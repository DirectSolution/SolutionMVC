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
        return $this->orm->QuestionTypeOptions->where("client_id", array("000", $id))->order("value ASC");
    }

    public function allQuestionTypeOptionsArray($client) {
        foreach ($this->getAllQuestionTypeOptions($client) AS $key => $questionType) {
            $questionTypeOptions[$key] = array(
                "name" => $questionType['name'],
                "id" => $questionType['id'],
                "value" => $questionType['value'],
                "type_id" => $questionType['type_id']
            );
        }
        return $questionTypeOptions;
    }

}
