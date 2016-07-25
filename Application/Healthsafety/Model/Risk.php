<?php

namespace SolutionMvc\Healthsafety\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Portal\Model\User;

class Risk extends BaseModel {

    protected $user;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
    }

    public function getRiskHazards($id) {
        return $this->prod_documents->RiskHazards->where("RiskAssessments_id", $id);
    }



    public function getRiskAssessments() {
        $risks = $this->prod_documents->RiskAssessments->where("retired", 0);
        $return = array();
        foreach ($risks as $risk) {
            $return[] = array(
                "risks" => $risk,
                "author" => $this->user->getUserById($risk['created_by'])
            );
        }
        return $return;
    }

    public function getRiskAssessment($id) {
        return $this->prod_documents->RiskAssessments[$id];
    }

    
    

    public function setRiskAssessment($request, $user) {
        return $this->prod_documents->RiskAssessments->insert(
                        array(
                            "name" => $request['name'],
                            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "created_by" => $user,
                        )
        );
    }


    public function setRiskHazards($request, $riskId, $user){
        $insertArray = array();
        foreach($request['hazards'] as $hazard){
            $insertArray[] = array(
                "name" => $hazard['name'],
                "description" => $hazard['description'],
                "likelihood" => $hazard['likelihood'],
                "severity" => $hazard['severity'],
                "risk_ranking" => ($hazard['likelihood'] * $hazard['severity']),
                "created_by" => $user,
                "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "RiskAssessments_id" => $riskId
            );
        }
        $this->prod_documents->RiskHazards->insert_multi($insertArray);
        return;
    }
    
}
