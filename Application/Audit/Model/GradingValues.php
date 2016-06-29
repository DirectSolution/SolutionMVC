<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of User
 *
 * @author doug
 */
class GradingValues extends BaseModel {

    public function getAllGradingValuesByAuditGradings($id) {
        return $this->prod_audit->GradingValues->where('AuditGradings_id', $id);
    }

    public function allGradingValuesReturnArray($id){
        $array = array();
        foreach($this->getAllGradingValuesByAuditGradings($id) AS $return){
            $array[] = array(
                    "id" => (int)$return['id'],
                    "name" => $return['name'],
                    "value" => $return['value'],
                    "AuditGradings_id" => $return['AuditGradings_id']                    
                );
        }
        return $array;
    }
}
