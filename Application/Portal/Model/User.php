<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Core\Password;

/**
 * Description of User
 *
 * @author doug
 */
class User extends BaseModel {
    
    protected $password;
    
    public function __construct() {
        parent::__construct();
        $this->password = new Password();
    }

    public function getAllByClient($id) {
        return array_map('iterator_to_array', iterator_to_array($this->prod_portal->mast_users->where("client", $id)));
    }

    public function getOneUserById($id) {
        return $this->prod_portal->mast_users->limit(1)->where("client LIKE ?", "%" . $id);
    }

    public function getUserById($id) {
        return $this->prod_portal->mast_users[array("id" => $id)];
    }

    public function getUser($id) {
        return $this->prod_portal->mast_users->where("id", $id);
    }

    public function getOneUserByUsernameAndClient($username, $client) {
        return $this->prod_portal->mast_users->where("username", $username)->and("client", $client)->or("email", $username)->and("client", $client);
    }

    public function getOneUserByUsername($username) {
        return $this->prod_portal->mast_users->where("username", $username)->or("email", $username);
    }

    public function setActivity($user, $client, $newexpiry, $hash, $address) {

        $user1 = str_pad($user['id'], 11, 0, STR_PAD_LEFT);
        
        $count = count($this->prod_portal->user_activity->where("user_id", $user1));
        $activity = $this->prod_portal->user_activity->where("user_id", $user1);

        if ($count > 0) {

            $activity->update(array(
                "hash" => $hash,
                "terminal_addr" => $address,
                "expiry" => $newexpiry
            ));
        } else {
           $this->prod_portal->user_activity->insert(array(               
                
                "user_id" => $user['id'],
                "client" => $user['client'],
                "terminal_addr" => $address,
                "expiry" => $newexpiry,
                "username" => $user['username'],
                "hash" => $hash,
                "name" => $user['name'],
                "userclient" => $user['client'],
                "company" => $client['company']
            ));
        }

        return;
    }

    public function setNewPassword($user) {
        $check = $this->prod_portal->mast_users[array("username" => $user['username'], "client" => $user['client'])];
        if ($check) {
            $check->update(array(
                "password" => $user['password']
            ));
        }
        return;
    }

    public function getUserRole($id) {
//        error_log("getuserrole id = ". $id,0);
        return $this->prod_audit->ACLRoleMembers[array("Users_id" => $id)];
    }

    public function getRoleRights($id) {
        return $this->prod_audit->ACLRoleRights->where("ACLRoles_id", $id);
    }

    public function setNew($request, $client, $current) {
        return $this
                        ->prod_portal
                        ->mast_users
                        ->insert(
                                $this->userArray($request, $client, $current
                                )
        );
    }

    public function setUpdate($request, $client, $id) {

        $user = $this->prod_portal->mast_users[array('id' => $id)];
        if ($user) {

            $user->update(array(
                "name" => $request['name'],
                "email" => $request['email'],
                "jobtitle" => $request['jobtitle'],
                "telephone" => $request['telephone']
            ));
        }
        return;
    }
    
    public function setActivateUser($userId, $password){
        $user = $this->prod_portal->mast_users[$userId];
        if($user){
            $user->update(array(
               "retired" => 0,
                "password" => $this->password->encodePassword($password)
            ));
        }
    }

    public function userArray($request, $client, $current) {
        return array(
            "client" => $client,
            "username" => $request['username'],
            "password" => md5(\time()),
            "name" => $request['name'],
            "email" => $request['email'],
            "retired" => 1,
            "createdby_id" => $current,
            "timestamp" => \time(),
            "jobtitle" => $request['jobtitle'],
            "telephone" => $request['telephone'],
            "siglogo" => ""
        );
    }

    public function setKey($key, $userid) {

        return $this->prod_audit->NewUserKey->insert(array(
                    "privatekey" => $key,
                    "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                    "MastUsers_id" => $userid
        ));
    }
    
    public function retireKey($key){
       $result = $this->prod_audit->NewUserKey[array("privatekey" => $key)];
       if($result){
           $result->update(array(
               "retired" => 1,
               "used_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
           ));
           return true;
       }else{
           return "Key does not exist.";
       }
    }

    public function getUserByKey($key){
        $NewUserKey = $this->prod_audit->NewUserKey[array("privatekey" => $key)];
        return $this->prod_portal->mast_users[$NewUserKey['MastUsers_id']];
    }
    
    
    // Function to check the time is less than 7 days ago. Mihgt need a fiddle depnding n the times we acutally want to use in the future
    public function checkTime($time) {
        $created_at = new \DateTime($time);
        $today = new \DateTime();
        if ($today->modify("-1 week") < $created_at) {
            return true;
        } else {
            return false;
        }
    }

    public function checkKey($key) {
        $check = $this->prod_audit->NewUserKey[array("privatekey" => $key)];      
        if ($check && $check['retired'] == 0 && $check['used_at'] === NULL && $this->checkTime($check['created_at'])) {
            return true;
        } else if (!$check || $check == null) {
            return "The activation Key supplied does not match an existing record.";
        } else if ($check['retired'] == 1) {
            return "The activation Key supplied has previously been retired.";
        } else if ($check['used_at'] !== NULL) {
            return "The activation Key supplied has already been used.";
        } else if ($this->checkTime($check['created_at']) != true) {
            return "The activation Key supplied has expired.";
        } else {
            return "Unknown Error";
        }
    }
    
    

}
