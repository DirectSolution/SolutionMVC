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

    public function getHazards() {
        return $this->prod_documents->Hazards->where("retired", 0);
    }

    public function getControls() {

        return
                array_map('iterator_to_array', iterator_to_array(
                        $this->prod_documents->Controls->where("retired", 0)
                )
        );
    }

//    public function getRiskAssessments(){
//        return $this->prod_documents->RiskAssessments->where("retired" ,0);
//    }

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

    public function getPersons() {
        return $this->prod_documents->PersonRisks->where("retired", 0);
    }

    public function setRiskAssessment($request, $user) {
        return $this->prod_documents->RiskAssessments->insert(
                        array(
                            "name" => $request['name'],
                            "description" => "Not set",
                            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "created_by" => $user
                        )
        );
    }

    public function getRiskAssessments_PersonRisks($id) {
        return $this->prod_documents->RiskAssessments_PersonRisks->where("RiskAssessments_id", $id);
    }

    public function getRiskAssessments_Hazards($id) {
        $hazards = $this->prod_documents->RiskAssessments_Hazards->where("RiskAssessments_id", $id);
        $hazardArr = array();
        foreach ($hazards as $hazard) {
            $hazardArr[] = array(
                "hazard" => array(
                    "name" => $hazard->Hazards['name'],
                    "likelihood" => $hazard['likelihood'],
                    "severity" => $hazard['severity'],
                    "risk_ranking" => $hazard['risk_ranking']
                ),
                "controls" => $this->prod_documents->RiskAssessments_Hazards_Controls->where("RiskAssessments_Hazards_id", $hazard['id'])
            );
        }

        return $hazardArr;
    }

    public function setRiskAssessments_PersonRisks($request, $raID) {
        $personArray = array();
        foreach ($request['person'] as $person) {
            $personArray[] = array(
                "RiskAssessments_id" => $raID,
                "PersonRisks_id" => $person
            );
        }
        return $this->prod_documents->RiskAssessments_PersonRisks->insert_multi($personArray);
    }

    public function setRiskAssessments_Hazards($request, $raID) {

        foreach ($request['riskAssessment']['hazards'] as $hazard => $controls) {
            $ra_hID = $this->prod_documents->RiskAssessments_Hazards->insert(array(
                "RiskAssessments_id" => $raID,
                "Hazards_id" => $hazard,
                "likelihood" => $controls['likelihood'],
                "severity" => $controls['severity'],
                "risk_ranking" => ($controls['likelihood'] * $controls['severity'])
                    )
            );
            $controlsArray = array();
            foreach ($controls['controls'] as $control => $data) {
                $controlsArray[] = array(
                    "RiskAssessments_Hazards_id" => $ra_hID,
                    "Controls_id" => $control
                );
            }
            $this->prod_documents->RiskAssessments_Hazards_Controls->insert_multi($controlsArray);
        }
        return;
    }

    public function setRetire($id, $user) {
        $getRiskAss = $this->prod_documents->RiskAssessments[$id];
        if ($getRiskAss) {
            $getRiskAss->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        }
        $getHazards = $this->prod_documents->RiskHazards->where("RiskAssessments_id", $id);
        foreach ($getHazards as $hazard) {
            $hazard->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        }
        return;
    }

}
