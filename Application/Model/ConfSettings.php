<?php

namespace SolutionMvc\Model;

class ConfSettings extends BaseModel{
    
    public function getSettings($id){
        return $this->prod_healthandsafety->conf_settings[array("client" => $id)];
    }
    
    
}