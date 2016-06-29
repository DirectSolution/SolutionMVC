<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of Assignments
 *
 * @author dhayward
 */
class Assignment extends BaseModel {
    
    public function __construct() {
        parent::__construct();
    }
    

    public function setAssetAssignments($data) {
        foreach ($data->data->assets as $asset) {
            $this->prod_audit->Assignments->insert(array(
                "Audits_id" => $data->data->Audits_id,
                "Assets_id" => $asset->id,
                "assigned_by" => $data->data->assigned_by,
                "client_id" => $data->data->client_id,
                "assigned_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
                    )
            );
        }
        return "success";
    }

    public function setAuditAssignments($data) {
        foreach ($data->data->audits as $audit) {
            $this->prod_audit->Assignments->insert(array(
                "Audits_id" => $audit->Audits_id,
                "Assets_id" => $data->data->Assets_id,
                "assigned_by" => $data->data->assigned_by,
                "client_id" => $data->data->client_id,
                "assigned_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
                    )
            );
        }
        return "success";
    }

    public function setAssignment($audit, $asset, $user, $client) {
        foreach($audit as $a){
            $this->prod_audit->Assignments->insert(array(
                    "Audits_id" => $a,
                    "Assets_id" => $asset,
                    "assigned_by" => $user,
                    "client_id" => $client,
                    "assigned_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
                        )
        );
        }
        return;
    }

    public function setAssignmentAuditToAssets($audit, $assets, $user, $client) {
        foreach($assets as $a){
            $this->prod_audit->Assignments->insert(array(
                    "Audits_id" => $audit,
                    "Assets_id" => $a,
                    "assigned_by" => $user,
                    "client_id" => $client,
                    "assigned_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
                        )
        );
        }
        return;
    }

    public function getAuditInUseList($assetID){
        $audits = array();
        foreach ($this->prod_audit->Assignments->where("Assets_id", (int)$assetID)->and("retired", 0) as $audit) {
          $a = $this->prod_audit->AuditDatas[array("Audits_id" => $audit['Audits_id'])];
            $audits[] = array(
                "id" => $audit['Audits_id'],
                "name" => $a['name']
                    );
        }                       
        return $audits;
    }

    public function getAssetsInUseList($auditID){
        $assets = array();
        foreach ($this->prod_audit->Assignments->where("Audits_id", (int)$auditID)->and("retired", 0) as $asset) {
          $a = $this->prod_audit->Assets[array("id" => $asset['Assets_id'])];
            $assets[] = array(
                "id" => $asset['Assets_id'],
                "name" => $a['forename'] . " " . $a['surname']
                    );
        }                       
        return $assets;
    }
    
    public function getAssetAssignmentsByAsset($assets) {
        $audits = array();
        foreach ($this->prod_audit->Assignments->where("Assets_id", "$assets")->and("retired", 0) as $asset) {
            $a = $this->prod_audit->AuditDatas[array("Audits_id" => $asset['Audits_id'])];
            $audits[] = array(
                "id" => $asset['Audits_id'],
                "name" => $a['name']
            );
        }
        return $audits;
    }

    public function getAssetAssignmentsByAudit($audits) {
        $a = $this->prod_audit->Assignments->where("Audits_id", "$audits");
        $assets = array();
        foreach ($a as $audit) {
            $assets[] = $audit['Assets_id'];
        }
        return $assets;
    }

    public function getAssignmentByAssetAuditClient($asset, $audit, $client) {
        return $this->prod_audit->Assignments[array("Assets_id" => "$asset", "Audits_id" => "$audit", "client_id" => "$client", "retired" => 0)];
    }

}
