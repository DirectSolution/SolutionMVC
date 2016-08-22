<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Core\BaseModel;

/**
 * Description of AuditEvidence
 *
 * @author dhayward
 */
class AuditEvidence extends BaseModel{
    
    public function setEvidence($id, $evidenceArray, $user){
        $insert = array();
        foreach($evidenceArray as $evidence){
            $insert[] = array(
                "name" => $evidence,
                "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "created_by" => $user,
                "Answers_id" => $id['id']
            );
        }
        return $this->prod_audit->AuditEvidence->insert_multi($insert);        
    }
    
    public function getEvidence($id){
        return $this->prod_audit->AuditEvidence->where("Answers_id", $id);
    }
    
    
    //put your code here
}
