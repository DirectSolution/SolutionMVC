<?php

namespace SolutionMvc\Healthsafety\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Portal\Model\User,
    SolutionMvc\Library\Helper;

class Coshh extends BaseModel {

    protected $user;
    protected $helper;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
        $this->helper = new Helper();
    }

    public function getCoshhAssessments() {
        $assessments = $this->prod_documents->CoshhAssessments->where("retired", 0)->order("name")->and("awaiting_review", 0)->order("name");
        $return = array();
        foreach ($assessments as $assessment) {
            $return[] = array(
                "assessment" => $assessment,
                "author" => $this->user->getUserById($assessment['created_by'])
            );
        }
        return $return;
    }

    public function countAwaiting() {
        return count($this->prod_documents->CoshhAssessments->where("retired", 0)->and("awaiting_review", 1)->and("url_code", NULL));
    }

    public function getCoshhAssessmentByUrl($url) {
        $a = $this->prod_documents->CoshhAssessments[array("url_code" => $url)];
        if ($a) {
            return $a['id'];
        } else {
            return false;
        }
    }

    public function getCoshhAssessmentsAwaitingReview1() {
        $assessments = $this->prod_documents->CoshhAssessments->where("retired", 0)->and("awaiting_review", 1)->and("url_code", NULL);


        $return = array();
        foreach ($assessments as $assessment) {
            $return[] = array(
                "assessment" => $assessment,
                "author" => $this->user->getUserById($assessment['created_by']),
            );
        }
        return $return;
    }

    public function getCoshhAssessmentsAwaitingReview() {
        $assessments = $this->prod_documents->CoshhAssessments->where("retired", 0)->order("name")->and("awaiting_review", 1)->order("name");
        $return = array();
        foreach ($assessments as $assessment) {
            $return[] = array(
                "assessment" => $assessment,
                "author" => $this->user->getUserById($assessment['created_by'])
            );
        }
        return $return;
    }

    public function getAssessment($id) {
        $ass = $this->prod_documents->CoshhAssessments[$id];
        if ($ass['retired'] != 1) {
            $assessment = array();
            $assessment['assessment'] = $ass;
            $assessment['mel'] = $this->prod_documents->Coshh_Mel->where("CoshhAssessments_id", $id);
            $assessment['oes'] = $this->prod_documents->Coshh_Oes->where("CoshhAssessments_id", $id);
            $assessment['wel'] = $this->prod_documents->Coshh_Wel->where("CoshhAssessments_id", $id);
            $assessment['persons_affected'] = $this->prod_documents->Coshh_PersonsAffected->where("CoshhAssessments_id", $id);
            $assessment['ppes'] = $this->prod_documents->Coshh_Ppes->where("CoshhAssessments_id", $id);
            $assessment['risk_phrases'] = $this->prod_documents->Coshh_RiskPhrases->where("CoshhAssessments_id", $id);
            $assessment['routes_of_entry'] = $this->prod_documents->Coshh_RouteEntrys->where("CoshhAssessments_id", $id);
            $assessment['substances'] = $this->prod_documents->Coshh_Substances->where("CoshhAssessments_id", $id);

            return $assessment;
        } else {
            return null;
        }
    }

    public function setAssessment($data, $user) {
        return $this->prod_documents->CoshhAssessments->insert($this->assessmentArray($data, $user));
    }

    public function assessmentArray($data, $user) {
        return array(
            "name" => isset($data['name']) ? $this->helper->wordSanitizer($data['name']) : null,
//            "reference" => isset($data['reference']) ? $data['reference'] : null,
            "supplied_by" => isset($data['supplied_by']) ? $this->helper->wordSanitizer($data['supplied_by']) : null,
            "assessor" => isset($data['assessor']) ? $this->helper->wordSanitizer($data['assessor']) : null,
            "supervisor" => isset($data['supervisor']) ? $this->helper->wordSanitizer($data['supervisor']) : null,
            "description" => isset($data['description']) ? $this->helper->wordSanitizer($data['description']) : null,
            "method_of_use" => isset($data['method_of_use']) ? $this->helper->wordSanitizer($data['method_of_use']) : null,
            "location" => isset($data['location']) ? $this->helper->wordSanitizer($data['location']) : null,
            "AmountUseds_id" => isset($data['amountUseds']) ? $this->helper->wordSanitizer($data['amountUseds']) : null,
            "TimePerDays_id" => isset($data['time']) ? $this->helper->wordSanitizer($data['time']) : null,
            "Durations_id" => isset($data['duration']) ? $this->helper->wordSanitizer($data['duration']) : null,
            "general_precautions" => isset($data['general_precautions']) ? $this->helper->wordSanitizer($data['general_precautions']) : null,
            "first_aid_measures" => isset($data['first_aid_measures']) ? $this->helper->wordSanitizer($data['first_aid_measures']) : null,
            "further_controls" => isset($data['further_controls']) ? $this->helper->wordSanitizer($data['further_controls']) : null,
            "responsibility" => isset($data['responsibility']) ? $this->helper->wordSanitizer($data['responsibility']) : null,
            "by_when" => isset($data['by_when']) ? $this->helper->wordSanitizer($data['by_when']) : null,
//            "date_done" => isset($data['date_done'])? $data['date_done'] : null,
            "spillage_procedure" => isset($data['spillage_procedure']) ? $this->helper->wordSanitizer($data['spillage_procedure']) : null,
            "fire_prevention" => isset($data['fire_prevention']) ? $this->helper->wordSanitizer($data['fire_prevention']) : null,
            "handling_storage" => isset($data['handling_storage']) ? $this->helper->wordSanitizer($data['handling_storage']) : null,
            "disposal_procedure" => isset($data['disposal_procedure']) ? $this->helper->wordSanitizer($data['disposal_procedure']) : null,
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user,
            "comments" => isset($data['comments']) ? $this->helper->wordSanitizer($data['comments']) : null
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

    public function setAccept($id, $user) {
        $coshh = $this->prod_documents->CoshhAssessments[array("id" => $id, "awaiting_review" => 1)];
        if ($coshh) {
            $url = $coshh->CoshhAssessments['url_code'];
            $coshh->CoshhAssessments->update(array(
                "retired" => 1,
                "url_code" => null
            ));
            $coshh->update(array(
                "awaiting_review" => 0,
                "url_code" => ($url)?$url:\time(),
                "reviewed_by" => $user
            ));
            return true;
        } else {
            return false;
        }
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

    public function setRetire($id, $user) {
        $coshh = $this->prod_documents->CoshhAssessments[$id];
        if ($coshh) {
            $coshh->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        }
        return;
    }

}
