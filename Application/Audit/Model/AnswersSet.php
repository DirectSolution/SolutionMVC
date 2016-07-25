<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Audit\Model\Answer;

/**
 * Description of User
 *
 * @author doug
 */
class AnswersSet extends BaseModel {

    protected $answers;

    public function __construct() {
        parent::__construct();
        $this->answers = new Answer();
    }

    public function setAnswersSet($assignmentID, $clientID, $audit, $auditDatas) {
        return $this->prod_audit->AnswerSets->insert(
                        array(
                            "Assignments_id" => $assignmentID,
                            "client_id" => $clientID,
                            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "Audits_id" => $audit,
                            "AuditDatas_id" => $auditDatas
                        )
        );
    }

    public function update($id, $result) {
//        die($result);
        $check = $this->prod_audit->AnswerSets[$id];
        if ($check) {
            $check->update(array('result_mark' => $result));
        }
        return;
    }

    public function getAnswersSet($id) {
        return $this->prod_audit->AnswerSets->where("Assignments_id", $id);
    }

    public function getOneAnswersSet($id) {
        return $this->prod_audit->AnswerSets[$id];
    }

    public function getAssetByAnswerSet($id) {
        $answerset = $this->prod_audit->AnswerSets[$id];
        return array(
            "Audits_id" => $answerset->Assignments['Audits_id'],
            "Assets_id" => $answerset->Assignments['Assets_id'],
            "forename" => $answerset->Assignments->Assets['forename'],
            "middlename" => $answerset->Assignments->Assets['middlename'],
            "surname" => $answerset->Assignments->Assets['surname'],
            "unique_ref_code" => $answerset->Assignments->Assets['unique_ref_code'],
            "address1" => $answerset->Assignments->Assets['address1'],
            "address2" => $answerset->Assignments->Assets['address2'],
            "county" => $answerset->Assignments->Assets->Counties['name'],
            "country" => $answerset->Assignments->Assets->Countries['name'],
            "postcode" => $answerset->Assignments->Assets['postcode'],
            "asset_type" => $answerset->Assignments->Assets->AssetTypes['name'],
            "asset_group" => $answerset->Assignments->Assets->AssetGroups['name'],
            "contact_number" => $answerset->Assignments->Assets['contact_number'],
            "contact_email" => $answerset->Assignments->Assets['contact_email'],
        );
    }
    
    
    //Get over and under.
    public function getOverUnder($id){
        $recent = $this->getMostRecentByClient($id);
        return $this->createOverUnderArray($recent);
    }
    
    // Function for finding which audits are in date and which are overdue and by hw much.
    public function createOverUnderArray($answerSetArray){
        $overDueArray = array();
        $inDateArray = array();
        foreach ($answerSetArray as $review) {
            $today = new \DateTime();
            $created = new \DateTime($review['created_at']);
            $nextReview = $created->modify("+ " . $review['review_freq']);
            if ($today >= $nextReview) {
                $overDueArray[] = array(
                    "Asset" => $review,
                    "ReviewDate" => $nextReview,
                    "OverdueBy" => \date_diff($nextReview, $today)
                );
            } else {
                $inDateArray[] = array(
                    "Asset" => $review,
                    "ReviewDate" => $nextReview,
                    "Due" => \date_diff($nextReview, $today)
                );
            }
        }
        return $reviewsArray[] = array(
            "over" => $overDueArray,
            "under" => $inDateArray
        );
    }
    
    
    //Get most recent by client
    public function getMostRecentByClient($id) {
        $answerSetArray = array();
        $answerSets = $this->prod_audit->AuditDatas->select("AuditDatas.* Aud, AnswerSets.* Ans")->where("AuditDatas.client_id", $id)->and('retired', 0)->order("AnswerSets:created_at ASC");
        foreach ($answerSets as $answerSet) {
            if ($answerSet['Assignments_id'] != null && $answerSet->Assignments->Assets['retired'] == 0) {
                $answerSetArray[$answerSet['Assignments_id']] = array(
                    "created_at" => $answerSet['created_at'],
                    "Audit" => array(
                        "id" => $answerSet['Audits_id'],
                        "name" => $answerSet->AuditDatas['name']
                        ),
                    "review_freq" => $answerSet->ReviewFrequencies['frequency'],
                    "Asset" => array(
                        "id" => $answerSet->Assignments->Assets['id'],
                        "forename" => $answerSet->Assignments->Assets['forename'],
                        "surname" => $answerSet->Assignments->Assets['surname']
                    ),
                );
            }
        }  
        return $answerSetArray;
       }

    public function getMostRecent($id) {

        return
                array_map('iterator_to_array', iterator_to_array(
                        $this->prod_audit->AnswerSets
                                ->where('Assignments_id', $id)
                                ->order('created_at DESC')
                                ->limit(1, 0)
                )
        );
    }

}
