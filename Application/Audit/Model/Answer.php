<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of User
 *
 * @author doug
 */
class Answer extends BaseModel {

    //Gets all answers related to specific Audit Assignment
    public function getAnswers($id) {
        return $this->prod_audit->Answers->where("AnswerSets_id", $id);
    }

    public function setAnswers($answers_array) {
        return $this->prod_audit->Answers->insert(array($answers_array));
    }

    public function setSingleAnswer($answer_array) {
        return $this->prod_audit->Answers->insert($answer_array);
    }

    public function insertOne($question, $answer, $answerSetId, $assignment, $token, $aVal) {
//        die(print "<pre>" . print_r($aVal['value']) . print "</pre>");
        return $this->prod_audit->Answers->insert(
                        array(
//                            "answer_value" => $answer->score,
//                            "answer_text" => $answer->text,
                            "answer_value" => $aVal['value'],
                            "answer_text" => $aVal['name'],
                            "expiry" => isset($answer['expires']) ? $answer['expires'] : null,
                            "evidence" => isset($answer['evidence']) ? $answer['evidence'] : null,
                            "Assignments_id" => $assignment['id'],
                            "client_id" => $token->client,
                            "Questions_id" => $question,
                            "answered_by" => $token->id,
                            "answered_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "notes" => isset($answer['notes']) ? $answer['notes'] : null,
                            "AnswerSets_id" => $answerSetId['id']
                        )
        );
    }
    

}
