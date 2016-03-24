<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Model\Fixtures\AuditFixtures;

/**
 * Description of Audit
 *
 * @author dhayward
 */
class Audit extends BaseModel {
    public function testDataForAudit($id){
        
        $data = new AuditFixtures();
        return $data->auditFixtures($id);
        
    }
    
    public function getAllAudits($client) {
        
        return $this->orm->Audits->where("client_id", array($client));
    }
    
    public function allAuditsArray($client){
        foreach ($this->getAllAudits($client) AS $key => $audit) {

            $audits[$key] = array(
                "id" => $audit['id'],
                "name" => $audit['name'],
                "description" => $audit['description'],
                "created_at" => $audit['created_at'],
                "client_id" => $audit['client_id'],
                "created_by" => $audit['created_by'],
                "audit_type_id" => $audit['AuditType_id'],
            );
        }
        return $audits;
    }
    
    public function insertNewAudit($audit){
        
        $auditData = array(
            "name" => $audit->data->name,
            "description" => $audit->data->description,
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
//            "client_id" => $audit->data->description,
//            "created_by" => $audit->data->description,
            "client_id" => 000,
            "created_by" => 123,
            "AuditType_id" => $audit->data->auditType
            
        );
        
//        $auditReturn = $this->orm->Audits->insert($auditData);
        
//        $auditReturn['id'];
        
        print "<PRE>";
        
        
//        Loop groups, insert a group with an audit id then retrive its ID then loop its questions inserting questions with the group id
        
        foreach($audit->data->groups as $group){
          var_dump($group);  
        }
        
//        var_dump($audit);
        print "</PRE>";
        
        
        return;
    }
    
    
}
