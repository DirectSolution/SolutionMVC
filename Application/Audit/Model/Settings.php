<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

class Settings Extends BaseModel {

    
    public function getSettingsByClient($id) {
               
       $settings = $this->prod_audit->Settings->where("client_id", (int)$id);
       foreach($settings AS $setting){
           return array(
               "default" => (int)$setting['Audits_id']
           );
       }
       
    }
    
    public function getDefault($client){
        $setting = $this->prod_audit->Settings[array("client_id" => $client)];
        return $setting['Audits_id'];
    }
    
    public function setDefault($id, $client){
              
        
        $setting = $this->prod_audit->Settings[array("client_id" => $client)];
        if ($setting) {
            $setting->update(array("Audits_id" => (int)$id));
            return "success";
        } else {
            return "failure";
        }
    }

}
