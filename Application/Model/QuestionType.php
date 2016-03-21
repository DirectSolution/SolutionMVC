<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of QuestionType
 *
 * @author dhayward
 */
class QuestionType extends BaseModel {

    public function getAllQuestionTypes($id) {
        return $this->orm->QuestionTypes->where("client_id", array("000", $id));
    }

    public function allQuestionTypesArray($client) {
        foreach ($this->getAllQuestionTypes($client) AS $key => $questionType) {
            $questionOptions[$key] = array(
                "name" => $questionType['name'],
                "id" => $questionType['id']
            );
        }
        
        return $questionOptions;
        
    }

}
