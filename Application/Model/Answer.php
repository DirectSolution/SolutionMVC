<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of User
 *
 * @author doug
 */
class Answer extends BaseModel {

    //Gets all answers related to specific Audit Assignment
    public function getAnswers($assignment_id) {
        return $this->orm->Answers->where("assignment_id = ?", $assignment_id);
    }

    public function setAnswers($answers_array) {
        return $this->orm->Answers->insert_multi(array($answers_array));
    }

    public function setSingleAnswer($answer_array) {
        return $this->orm->Answers->insert($answer_array);
    }

}
