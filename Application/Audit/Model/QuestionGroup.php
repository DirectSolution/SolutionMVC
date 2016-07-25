<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Audit\Model\Question;

/**
 * Description of QuestionGroup
 *
 * @author dhayward
 */
class QuestionGroup extends BaseModel {

    /**
     * @param integer $id
     * @return array
     * @Description Pass in group_id, returns all questions by related group.
     */
    public function getAllQuestionGroupsByAuditId($id) {
        return $this->prod_audit->QuestionGroups->where("audit_id", $id);
    }

    public function sumQuestionGroup($groups) {
        $valArray = array();
        foreach ($groups as $group) {
            foreach ($group['answerType']['values'] as $value) {
                $valArray[] = $value['value'];
            }
            $groupMax[] = max($valArray);
        }
        return array_sum($groupMax);
    }

    public function getAllQuestionGroupsByAuditIdArray($id) {
        $questions = new Question();
        $QuestionGroups = array();
        
        foreach ($this->getAllQuestionGroupsByAuditId($id) AS $group) {
            $questionResult = $questions->getQuestionsByGroupIdArray($group['id']);
            $groupSum = $this->sumQuestionGroup($questionResult);            
            $QuestionGroups[] = array(
                "id" => $group['id'],
                "name" => $group['name'],
                "audit_id" => $group['audit_id'],
                "client_id" => $group['client_id'],
                "questions" => $questionResult,
                "group_total_possible" => $groupSum,
            );
            
        }
        
        return $QuestionGroups;
    }

}
