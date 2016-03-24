<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Model\Audit,
    SolutionMvc\Model\AuditType,
    SolutionMvc\Model\QuestionType,
    SolutionMvc\Model\QuestionTypeOption,
    SolutionMvc\Core\Response;

/**
 * Description of AuditController
 *
 * @author doug
 */
class AuditController {

    protected $response;
    protected $helpers;
    protected $audit;
    protected $auditTypes;
    protected $questionsTypes;
    protected $questionsTypeOptions;

    public function __construct() {
        //Most controllers require this as it is used to instantiate a Response 
        //object which you add to before encoding and sending to the frontend. 
        //Initiate it here so each action doesnt have to do it again and again.
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->response = new Response();

        $this->audit = new Audit();
        $this->auditTypes = new AuditType();
        $this->questionTypes = new QuestionType();
        $this->questionTypeOptions = new QuestionTypeOption();
    }

    public function indexAction($client = 000) {

        foreach ($this->audit->getAllAudits($client) AS $key => $audit) {

            $audits[$key] = array(
                "id" => $audit['id'],
                "name" => $audit['name'],
                "description" => $audit['description'],
                "created_at" => $audit['created_at'],
                "client_id" => $audit['client_id'],
                "created_by" => $audit['created_by'],
                "audit_type_id" => $audit['AuditType_id'],
                "audit_type_name" => $this->helpers->searchForId($audit['AuditType_id'], $this->auditTypes->allAuditTypesArray($client))
            );
        }
        $this->response->audits = $audits;

        return print json_encode($this->response);
    }

    //This funciton is used t load the initial data required when creating 
    //a new audit (Question Type and Audit Type lists) add to this if you want
    // to provide more data to the "New Audit" pages. 
    public function initialNewAuditDataAction($client = 000) {

        $this->response->auditTypes = $this->auditTypes->allAuditTypesArray($client);
        $this->response->questionTypes = $this->questionTypes->allQuestionTypesArray($client);
        $this->response->questionTypeOptions = $this->questionTypeOptions->allQuestionTypeOptionsArray($client);


        return print json_encode($this->response);
    }

    public function takeAuditAction($id, $client = 000) {

        $this->response = json_decode($this->audit->testDataForAudit($id));
        $this->response->auditType->name = $this->helpers->searchForId($this->response->auditType->id, $this->auditTypes->allAuditTypesArray($client));

        foreach ($this->response->groups as $group) {

            foreach ($group->questions as $question) {

                $question->answerType->name = $this->helpers->searchForId($question->answerType->id, $this->questionTypes->allQuestionTypesArray($client));

                $optionArray = array();
                foreach ($this->questionTypeOptions->allQuestionTypeOptionsArray($client) as $key => $option) {
                    if ($option['type_id'] == $question->answerType->id) {
                        $optionArray[$key] = $option;
                    }
                }

                $question->answerType->options = $optionArray;
                $question->answerType->optionCount = count($optionArray);
                $question->answerType->optionMax = max(array_map(function($optionArray) {
                            return $optionArray['value'];
                        }, $optionArray));
                $question->answerType->optionSteps = round($question->answerType->optionMax / (count($optionArray) - 1) + 0.1, 1);
            }
        }

        return print json_encode($this->response);
    }

    public function saveAuditAction() {
        $postdata = file_get_contents("php://input");

        $this->audit->insertNewAudit(json_decode($postdata));    
        
        $this->response->result = "Audit Saved";
        $this->response->status = "success";
        $this->response->username = "dhayward";
        //$this->response->data = $request;

        return print json_encode($this->response);
    }

    public function newAuditAction() {

        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $this->response->result = "Password Correct";
        $this->response->status = "success";
        $this->response->username = "dhayward";
        $this->response->data = $request;


        return print json_encode($this->response);
    }

    //Used to load all audit data (Questions, Question Types, Evidence, Expiry)
    // for Update and TakeAudit pages.  
    public function getAuditAction($id = "1", $client = "000") {
        $questionTypes = new QuestionType();
        $this->response->questionTypes = $questionTypes->allQuestionTypesArray($client);
        $audit = new Audit;


        return print ($audit->testDataForAudit($id));
    }

    public function deleteAuditAction() {
        
    }

}
