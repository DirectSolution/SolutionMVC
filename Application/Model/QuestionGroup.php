<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Model\Question;

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
        return $this->orm->QuestionGroups->where("audit_id", $id);
    }

    public function getAllQuestionGroupsByAuditIdArray($id) {
        
        $questions = new Question();
        foreach ($this->getAllQuestionGroupsByAuditId($id) AS $key => $group) {
            $QuestionGroups[$key] = array(
                "id" => $group['id'],
                "name" => $group['name'],
                "audit_id" => $group['audit_id'],
                "client_id" => $group['client_id'],
                "questions" => $questions->getQuestionsByGroupIdArray($group['id'])
            );
        }
        return $QuestionGroups;
    }
    
}
