<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Core\Security;
/**
 * Description of User
 *
 * @author doug
 */
class User extends BaseModel {
    protected $security;
    public function __construct() {
        parent::__construct();
        $this->security = new Security();
    }
//    public function getOneUserById($id) {
//        return $this->orm->mast_users->limit(1)->where("client LIKE ?", "%" . $id);
//    }
    
    public function getUserById($id){
        foreach($this->orm->{"prod_portal.mast_users"}->where("id", $id) as $a){
            return $a;
        }
    }    
    
    public function getOneUserByUsernameAndClient($username, $client) {
        return $this->orm->{"prod_portal.mast_users"}->where("username LIKE ?", $username)->and("client LIKE ?", "%".$client)->fetch();
    }
    
    public function getAllUsers($id){
        return $this->orm->{"prod_portal.mast_users"}->where("client", $id)->and("retired", 0);
    }
    
    public function getOneUserById($id){
        return $this->orm->{"prod_portal.mast_users"}[$id];
    }
    
    public function getUserRole($id){
        return $this->prodAudit->ACLRoleMembers[array("Users_id" => $id)];
    }

    public function getRoleRights($id){
        return $this->prodAudit->ACLRoleRights->where("ACLRoles_id", $id);
    }
    

}
