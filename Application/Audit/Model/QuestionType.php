<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of QuestionType
 *
 * @author dhayward
 */
class QuestionType extends BaseModel {

    public function getAllQuestionTypes($id) {
        return $this->prod_audit->QuestionTypes->where("client_id", array("000", $id));
    }

    public function allQuestionTypesArray($client) {
        foreach ($this->getAllQuestionTypes($client) AS $key => $questionType) {
            $questionOptions[$key] = array(
                "typeName" => \implode(" ", $this->getQuestionTypeOptions($questionType['id'])),
                "name" => $questionType['name'],
                "id" => $questionType['id']
            );
        }        
        return $questionOptions;        
    }
    
    public function getQuestionTypeOptions($id){
        $options = $this->prod_audit->QuestionTypeOptions->where('QuestionTypes_id', $id)->order('value ASC');
        $array = array();
        foreach($options as $option){
            $array[] = $option['name']. " (" . $option['value'] . ")";
        }
        return $array;
    }
    
    public function setNewType($client, $data){
        return $this->prod_audit->QuestionTypes->insert(array(
            "name" => $data->name,
            "client_id" => $client
        ));
    }

}
