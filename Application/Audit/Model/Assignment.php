<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Audit\Model\AnswersSet,
    SolutionMvc\Audit\Model\GradingValues,
    SolutionMvc\Audit\Model\ReviewFrequencies;

/**
 * Description of Assignments
 *
 * @author dhayward
 */
class Assignment extends BaseModel {

    protected $answerSet;
    protected $helpers;

    public function __construct() {
        parent::__construct();
        $this->answerSet = new AnswersSet();
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->gradingValues = new GradingValues();
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
        foreach ($audit as $a) {
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

    public function setOneAssignment($audit, $asset, $user, $client) {
        return $this->prod_audit->Assignments->insert(array(
                    "Audits_id" => $audit,
                    "Assets_id" => $asset,
                    "assigned_by" => $user,
                    "client_id" => $client,
                    "assigned_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
                        )
        );
    }

    public function setAssignmentAuditToAssets($audit, $assets, $user, $client) {
        foreach ($assets as $a) {
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

    public function getAuditInUseList($assetID) {
        $audits = array();
        foreach ($this->prod_audit->Assignments->where("Assets_id", (int) $assetID)->and("retired", 0) as $audit) {
            $a = $this->prod_audit->AuditDatas[array("Audits_id" => $audit['Audits_id'])];
            $audits[] = array(
                "id" => $audit['Audits_id'],
                "name" => $a['name']
            );
        }
        return $audits;
    }

    public function getAssetsInUseList($auditID) {
        $assets = array();
        foreach ($this->prod_audit->Assignments->where("Audits_id", (int) $auditID)->and("retired", 0) as $asset) {
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
        $assignments = $this->prod_audit->Assignments->where("Assets_id", "$assets")->and("retired", 0);
        foreach ( $assignments as $assignment) {
            $auditData = $this->prod_audit->AuditDatas[array("Audits_id" => $assignment['Audits_id'], "retired"=> 0)];
            $answerSet = array_shift($this->answerSet->getMostRecent($assignment['id'])); // Using array_map causes a multi index array to be made but we only want one element so using this to get the first
            @$percent = $this->helpers->getPercent($answerSet['result_mark'], $auditData['max_score_possible']);
            $grades = $this->gradingValues->allGradingValuesReturnArray($auditData['AuditGradings_id']);
            $date = new \DateTime($answerSet['created_at']);
            $dueDate = $date->modify("+".$auditData->ReviewFrequencies['frequency']);
            $audits[] = array(
                "id" => $assignment['id'],
                "Audits_id" => $assignment['Audits_id'],
                "name" => $auditData['name'],
                "description" => $auditData['description'],
                "max_score_possible" => $auditData['max_score_possible'],
                "auditType" => $auditData->AuditTypes['name'],
                "answerSet" => $answerSet,
                "next_due" => $dueDate->format('d/m/Y'),
                "grade" => $this->helpers->getClosest($percent, $grades),
                "Assignment_id" => $assignment['id']
            );
        }


//        print "<pre>";
//        print_r($audits);
//        print "</pre>";
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

    public function getAssignmentByID($id){
        return $this->prod_audit->Assignments[$id];
    }
    
    
    public function setRetireOne($id){
        $assignment = $this->prod_audit->Assignments[$id];
        if($assignment){
            $assignment->update(array("retired" => 1));
            return true;
        }else{
            return false;
        }
        
    }
    
    
}
