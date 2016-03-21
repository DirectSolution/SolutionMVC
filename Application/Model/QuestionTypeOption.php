<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of QuestionTypeOption
 *
 * @author dhayward
 */
class QuestionTypeOption extends BaseModel {
    public function getAllQuestionTypeOptions($id) {
        return $this->orm->QuestionTypeOptions->where("type_id", $id)->order("value DESC");
    }
}
