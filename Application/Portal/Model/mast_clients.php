<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Core\BaseModel;

class mast_clients extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getClient($id) {
        return $this->prod_portal->mast_clients[array("client" =>$id)];
    }
    
    

}
