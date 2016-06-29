<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of Client
 *
 * @author doug
 */
class Client extends BaseModel {

    public function getOneClientById($id) {
        return $this->prod_portal->mast_clients->limit(1)->where("client LIKE ?", "%" . $id);
    }
    

}
