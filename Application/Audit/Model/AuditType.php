<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of AuditType
 *
 * @author dhayward
 */
class AuditType extends BaseModel {

    public function getAllAuditTypes($id) {
        return $this->prod_audit->AuditTypes->where("client_id", array("000", $id));
    }

    public function allAuditTypesArray($id) {
        foreach ($this->getAllAuditTypes($id) AS $key => $auditType) {

            $auditOptions[] = array(
                "name" => $auditType['name'],
                "id" => $auditType['id']
            );
        }
        return $auditOptions;
    }

    public function setNewType($client, $name) {
        return $this->prod_audit->AuditTypes->insert(
                        array(
                            "name" => $name,
                            "client_id" => $client
                        )
        );
    }

}
