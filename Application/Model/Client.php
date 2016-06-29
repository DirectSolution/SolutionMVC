<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

class Client extends BaseModel {

    public function getOneClientById($id) {
        return $this->prod_portal->mast_clients->limit(1)->where("client LIKE ?", "%" . $id);
    }
    
    public function getClientById($id){
                
        return $this->prod_portal->mast_clients[array("client" => $id)];
    }
    

}
