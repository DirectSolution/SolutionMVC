<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of Asset
 *
 * @author dhayward
 */
class ReportSettings extends BaseModel {
    
   public function getReportSettingsByClient($id){       
       if($this->prod_audit->ReportSettings[array("client_id" => $id)]){
           return $this->prod_audit->ReportSettings[array("client_id" => $id)];
       }else{
           return $this->prod_audit->ReportSettings[array("client_id" => 0)];
       }
   }
   
   public function getLogo($client){
       $a = $this->prod_healthandsafety->conf_settings[array("client" => $client)];
//       return "/var/www/html/Filestore/images/$client/". $a['noteslogo'];
       return "/home/git/htmlp/html/doug/portal.solutionhost.co.uk/web/apps/Audit/Filestore/images/$client/". $a['noteslogo'];
   }
   
   public function getClient($client){
       return $this->prod_portal->mast_clients[array("client"=> $client)];
   }
    
}
