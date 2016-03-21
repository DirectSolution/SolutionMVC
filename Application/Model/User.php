<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of User
 *
 * @author doug
 */
class User extends BaseModel {

    public function getOneUserById($id) {
        return $this->orm->mast_users->limit(1)->where("client LIKE ?", "%" . $id);
    }
    
    public function getOneUserByUsernameAndClient($username, $client) {
        return $this->orm->{"prod_portal.mast_users"}->where("username LIKE ?", "%" . $username)->and("client LIKE ?", "%".$client)->fetch();
       
    }
    

}
