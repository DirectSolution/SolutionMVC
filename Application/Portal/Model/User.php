<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Core\Security;

/**
 * Description of User
 *
 * @author doug
 */
class User extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getOneUserById($id) {
        return $this->prod_portal->mast_users->limit(1)->where("client LIKE ?", "%" . $id);
    }

    public function getUserById($id) {
        return $this->prod_portal->mast_users[array("id" => $id)];
    }

    public function getOneUserByUsernameAndClient($username, $client) {
        return $this->prod_portal->mast_users->where("username", $username)->and("client", $client);
    }

    public function getOneUserByUsername($username) {
        return $this->prod_portal->mast_users->where("username", $username)->or("email", $username);
    }

    public function setActivity($user, $client, $newexpiry, $hash, $address) {

        $user1 = str_pad($user['id'], 11, 0, STR_PAD_LEFT);
        $activity = $this->prod_portal->user_activity->where("user_id", $user1);

        if ($activity) {

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
        error_log("getuserrole id = ". $id,0);
        return $this->prod_audit->ACLRoleMembers[array("Users_id" => $id)];
    }

    public function getRoleRights($id) {
        return $this->prod_audit->ACLRoleRights->where("ACLRoles_id", $id);
    }

}
