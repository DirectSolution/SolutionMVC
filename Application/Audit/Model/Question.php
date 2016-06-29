<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of Question
 *
 * @author dhayward
 */
class Question extends BaseModel {

    /**
     * @param integer $id
     * @return object
     * @Description Pass in group_id, returns all questions by related group.
     */
    public function getQuestionsByGroupId($id) {
        return $this->prod_audit->Questions->where("QuestionGroups_id", array($id));
    }

    /**
     * @param integer $id
     * @return array
     * @Description Pass in group_id, returns all questions by related group.
     */
    public function getQuestionsByGroupIdArray($id) {
        foreach ($this->getQuestionsByGroupId($id) AS $question) {
            $Questions[] = array(
                "id" => $question['id'],
                "question" => $question['question'],
                "answerRequired" => (int) $question['answer_required'],
                "addEvidence" => (int) $question['add_evidence'],
                "evidenceRequired" => (int) $question['evidence_required'],
                "addExpiry" => (int) $question['add_expiry'],
                "expiryRequired" => (int) $question['expiry_required'],
                "answerType" => array("id" => (int)$question['QuestionTypes_id']),
                "group_id" => (int) $question['QuestionGroups_id'],
                "client_id" => (int) $question['client_id'],
                "audit_id" => (int) $question['AuditDatas_id'],
            );

        }
        return $Questions;
    }

    /**
     * @param integer $id
     * @return array
     * @Description Pass in audit_id, returns all questions by related audit.
     */
    public function getQuestionsByAuditId($id) {
        return $this->prod_audit->Questions->where("AuditDatas_id", array($id));
    }

}
