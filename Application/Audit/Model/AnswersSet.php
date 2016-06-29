<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of User
 *
 * @author doug
 */
class AnswersSet extends BaseModel {

    public function setAnswersSet($assignmentID, $clientID) {
        return $this->prod_audit->AnswerSets->insert(
                array(
                    "Assignments_id" => $assignmentID,
                    "client_id" => $clientID,
                    "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()")
                )
        );
    }
    
    public function getAnswersSet($id) {
        return $this->prod_audit->AnswerSets->where("Assignments_id", $id);
    }

    public function getOneAnswersSet($id) {
        return $this->prod_audit->AnswerSets[$id];
    }

}
