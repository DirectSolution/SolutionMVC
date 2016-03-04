<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of User
 *
 * @author doug
 */
class Client extends BaseModel {

    public function getOneClientById($id) {
        return $this->orm->mast_clients->limit(1)->where("client LIKE ?", "%" . $id);
    }
    

}
