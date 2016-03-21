<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of AuditType
 *
 * @author dhayward
 */
class AuditType extends BaseModel {

    public function getAllAuditTypes($id) {
        return $this->orm->AuditTypes->where("client_id", array("000", $id));
    }
    
    public function allAuditTypesArray($id){
        foreach ($this->getAllAuditTypes($id) AS $key => $auditType) {

            $auditOptions[$key] = array(
                "name" => $auditType['name'],
                "id" => $auditType['id']
            );
        }
        return $auditOptions;
    }

}
