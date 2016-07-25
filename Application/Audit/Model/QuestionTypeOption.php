<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of QuestionTypeOption
 *
 * @author dhayward
 */
class QuestionTypeOption extends BaseModel {

    public function getHighestById($id) {
        $i = 0;
        $a = $this->prod_audit->QuestionTypeOptions->where('QuestionTypes_id', $id)->order('value DESC')->limit(1, 0);
        foreach ($a as $b) {
            $i += $b['value'];
        }
        return $i;
    }

    public function getAllQuestionTypeOptions($id) {
        return $this->prod_audit->QuestionTypeOptions->where("client_id", array("000", $id))->order("value ASC");
    }

    public function getOne($answer) {
        return $this->prod_audit->QuestionTypeOptions[$answer['score']];
    }

    public function getByQuestionType($id) {
        $result = $this->prod_audit->QuestionTypeOptions->where('QuestionTypes_id', $id)->and('retired', 0)->order("value ASC");
        $array = array();
        foreach ($result as $options) {
            $array[] = array(
                "id" => $options['id'],
                "name" => $options['name'],
                "value" => $options['value'],
                "QuestionTypes_id" => $options['QuestionTypes_id']
            );
        }
        return $array;
    }

    public function allQuestionTypeOptionsArray($client) {
        foreach ($this->getAllQuestionTypeOptions($client) AS $key => $questionType) {
            $questionTypeOptions[$key] = array(
                "name" => $questionType['name'],
                "id" => $questionType['id'],
                "value" => $questionType['value'],
                "type_id" => $questionType['QuestionTypes_id']
            );
        }
        return $questionTypeOptions;
    }

    public function setNewOption($client, $data, $id) {
        return $this->prod_audit->QuestionTypeOptions->insert(array(
                    "name" => $data->name,
                    "value" => $data->value,
                    "QuestionTypes_id" => $id,
                    "client_id" => $client
        ));
    }

}
