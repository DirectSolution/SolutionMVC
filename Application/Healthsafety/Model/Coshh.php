<?php

namespace SolutionMvc\Healthsafety\Model;

use SolutionMvc\Model\BaseModel;

class Coshh extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getBasicAssessment(){
        return $this->prod_documents->CoshhAssessments->where("retired", 0);
    }
    
    public function getAssessment($id) {
        
        $assessment = array();
        $assessment['assessment'] = $this->prod_documents->CoshhAssessments[$id];
        $assessment['mel'] = $this->prod_documents->Coshh_Mel->where("CoshhAssessments_id", $id);
        $assessment['oes'] = $this->prod_documents->Coshh_Oes->where("CoshhAssessments_id", $id);
        $assessment['wel'] = $this->prod_documents->Coshh_Wel->where("CoshhAssessments_id", $id);
        $assessment['persons_affected'] = $this->prod_documents->Coshh_PersonsAffected->where("CoshhAssessments_id", $id);
        $assessment['ppes'] = $this->prod_documents->Coshh_Ppes->where("CoshhAssessments_id", $id);
        $assessment['risk_phrases'] = $this->prod_documents->Coshh_RiskPhrases->where("CoshhAssessments_id", $id);
        $assessment['routes_of_entry'] = $this->prod_documents->Coshh_RouteEntrys->where("CoshhAssessments_id", $id);
        $assessment['substances'] = $this->prod_documents->Coshh_Substances->where("CoshhAssessments_id", $id);
        
        return $assessment;
    }

    public function setAssessment($data, $user) {
        return $this->prod_documents->CoshhAssessments->insert($this->assessmentArray($data, $user));
    }

    public function assessmentArray($data, $user) {
        return array(
            "name" => isset($data['name']) ? $data['name'] : null,
            "reference" => isset($data['reference']) ? $data['reference'] : null,
            "supplied_by" => isset($data['supplied_by']) ? $data['supplied_by'] : null,
            "assessor" => isset($data['assessor']) ? $data['assessor'] : null,
            "supervisor" => isset($data['supervisor']) ? $data['supervisor'] : null,
            "description" => isset($data['description']) ? $data['description'] : null,
            "method_of_use" => isset($data['method_of_use']) ? $data['method_of_use'] : null,
            "location" => isset($data['location']) ? $data['location'] : null,
            "AmountUseds_id" => isset($data['amountUseds']) ? $data['amountUseds'] : null,
            "TimePerDays_id" => isset($data['time']) ? $data['time'] : null,
            "Durations_id" => isset($data['duration']) ? $data['duration'] : null,
            "general_precautions" => isset($data['general_precautions']) ? $data['general_precautions'] : null,
//            "first_aid_measures" => isset($data['first_aid_measures'])? $data['first_aid_measures'] : null,
            "further_controls" => isset($data['further_controls']) ? $data['further_controls'] : null,
            "responsibility" => isset($data['responsibility']) ? $data['responsibility'] : null,
            "by_when" => isset($data['by_when']) ? $data['by_when'] : null,
//            "date_done" => isset($data['date_done'])? $data['date_done'] : null,
            "spillage_procedure" => isset($data['spillage_procedure']) ? $data['spillage_procedure'] : null,
            "fire_prevention" => isset($data['fire_prevention']) ? $data['fire_prevention'] : null,
            "handling_storage" => isset($data['handling_storage']) ? $data['handling_storage'] : null,
            "disposal_procedure" => isset($data['disposal_procedure']) ? $data['disposal_procedure'] : null,
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user,
            "comments" => isset($data['comments']) ? $data['comments'] : null
        );
    }

    public function setCoshhMEL($mels, $assessmentID) {
        $melArray = array();
        foreach ($mels as $mel) {
            $melArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "Eh40s_id" => $mel
            );
        }
        $this->prod_documents->Coshh_Mel->insert_multi($melArray);
        return;
    }

    public function setCoshhOES($oess, $assessmentID) {
        $oesArray = array();
        foreach ($oess as $oes) {
            $oesArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "Eh40s_id" => $oes
            );
        }
        $this->prod_documents->Coshh_Oes->insert_multi($oesArray);
        return;
    }

    public function setCoshhWEL($wels, $assessmentID) {
        $welArray = array();
        foreach ($wels as $wel) {
            $welArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "Eh40s_id" => $wel
            );
        }
        $this->prod_documents->Coshh_Wel->insert_multi($welArray);
        return;
    }

    public function setCoshhPersons($persons, $assessmentID) {
        $personArray = array();
        foreach ($persons as $person) {
            $personArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "PersonRisks_id" => $person
            );
        }
        $this->prod_documents->Coshh_PersonsAffected->insert_multi($personArray);
        return;
    }

    public function setCoshhPPE($ppes, $assessmentID) {
        $ppeArray = array();
        foreach ($ppes as $ppe) {
            $ppeArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "Ppes_id" => $ppe
            );
        }
        $this->prod_documents->Coshh_Ppes->insert_multi($ppeArray);
        return;
    }

    public function setCoshhRisks($risks, $assessmentID) {
        $riskArray = array();
        foreach ($risks as $risk) {
            $riskArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "RiskPhrases_id" => $risk
            );
        }
        $this->prod_documents->Coshh_RiskPhrases->insert_multi($riskArray);
        return;
    }

    public function setCoshhRoutes($routes, $assessmentID) {
        $routeArray = array();
        foreach ($routes as $route) {
            $routeArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "RouteEntrys_id" => $route
            );
        }
        $this->prod_documents->Coshh_RouteEntrys->insert_multi($routeArray);
        return;
    }

    public function setCoshhSubstances($substances, $assessmentID) {
        $substanceArray = array();
        foreach ($substances as $substance) {
            $substanceArray[] = array(
                "CoshhAssessments_id" => $assessmentID,
                "Substances_id" => $substance
            );
        }
        $this->prod_documents->Coshh_Substances->insert_multi($substanceArray);
        return;
    }

    public function getPPE() {
        return $this->prod_documents->Ppes->where("retired", 0);
    }

    public function getRiskPhrases() {
        return $this->prod_documents->RiskPhrases->where("retired", 0);
    }

    public function getRouteEntries() {
        return $this->prod_documents->RouteEntrys->where("retired", 0);
    }

    public function getDurations() {
        return $this->prod_documents->Durations->where("retired", 0);
    }

    public function getAmountsUsed() {
        return $this->prod_documents->AmountUseds->where("retired", 0);
    }

    public function getControlMeasures() {
        return $this->prod_documents->ControlMeasures->where("retired", 0);
    }

    public function getFirstAidMeasures() {
        return $this->prod_documents->FirstAidMeasures->where("retired", 0);
    }

//    public function getMel(){
//        return $this->prod_documents->MaximumExposureLimits->where("retired", 0);
//    }
//    
//    public function getOes(){
//        return $this->prod_documents->OccupationalExposureStandards->where("retired", 0);
//    }
//    
//    public function getWel(){
//        return $this->prod_documents->WorkplaceExposureLimits->where("retired", 0);
//    }

    public function getEh40s() {
        return $this->prod_documents->Eh40s->where("retired", 0);
    }

    public function getPersonsAtRisk() {
        return $this->prod_documents->PersonRisks->where("retired", 0);
    }

    public function getSubstances() {
        return $this->prod_documents->Substances->where("retired", 0);
    }

    public function getTimesPerDay() {
        return $this->prod_documents->TimePerDays->where("retired", 0);
    }

}
