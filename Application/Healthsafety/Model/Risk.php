<?php

namespace SolutionMvc\Healthsafety\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Portal\Model\User,
    SolutionMvc\Library\Helper;

class Risk extends BaseModel {

    protected $user;
    protected $helpers;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
        $this->helpers = new Helper();
    }

    public function getRiskHazards($id) {
        return $this->prod_documents->RiskHazards->where("RiskAssessments_id", $id);
    }

    public function getRiskAssessments() {
        $risks = $this->prod_documents->RiskAssessments->where("retired", 0)->and("awaiting_review", 0)->order("name");
        $return = array();
        foreach ($risks as $risk) {
            $return[] = array(
                "risks" => $risk,
                "author" => $this->user->getUserById($risk['created_by'])
            );
        }
        return $return;
    }

    public function countAwaiting() {
        return count($this->prod_documents->RiskAssessments->where("retired", 0)->and("awaiting_review", 1)->and("url_code", NULL));
    }

    public function getRiskAssessment($id) {
        return $this->prod_documents->RiskAssessments[$id];
    }

    public function getRiskAssessmentByUrl($url) {
        $a = $this->prod_documents->RiskAssessments[array("url_code" => $url)];
        if ($a) {
            return $a['id'];
        } else {
            return false;
        }
    }

    public function setRiskAssessment($request, $user, $parent = null) {
        return $this->prod_documents->RiskAssessments->insert(
                        array(
                            "name" => $this->helpers->wordSanitizer($request['name']),
                            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "created_by" => $user,
                            "awaiting_review" => 1,
                            "RiskAssessments_id" => ($parent) ? $parent : null
                        )
        );
    }

    public function setRiskHazards($request, $riskId, $user) {
        $insertArray = array();
        foreach ($request['hazards'] as $hazard) {
            $insertArray[] = array(
                "name" => $this->helpers->wordSanitizer($hazard['name']),
                "description" => $this->helpers->wordSanitizer($hazard['description']),
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

    public function setAccept($id, $user) {
        $method = $this->prod_documents->RiskAssessments[array("id" => $id, "awaiting_review" => 1)];
        if ($method) {
            $url = $method->RiskAssessments['url_code'];
            $method->RiskAssessments->update(array(
                "retired" => 1,
                "url_code" => null
            ));
            $method->update(array(
                "awaiting_review" => 0,
                "url_code" => ($url)?$url:\time(),
                "reviewed_by" => $user
            ));
            return true;
        } else {
            return false;
        }
    }

    public function getRiskAssessmentsAwaitingReview() {
        $assessments = $this->prod_documents->RiskAssessments->where("retired", 0)->and("awaiting_review", 1)->and("url_code", NULL);
        $return = array();
        foreach ($assessments as $ass) {
            $return[] = array(
                "assessment" => $ass,
                "author" => $this->user->getUserById($ass['created_by'])
            );
        }
        return $return;
    }

}
