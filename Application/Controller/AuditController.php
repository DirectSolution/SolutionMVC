<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Model\Audit,
    SolutionMvc\Model\AuditType,
    SolutionMvc\Model\QuestionType,
    SolutionMvc\Model\QuestionGroup,
    SolutionMvc\Model\QuestionTypeOption,
    SolutionMvc\Core\Response,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Controller;

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
    protected $security;

    public function __construct() {
        $this->security = new Security();
        //Most controllers require this as it is used to instantiate a Response 
        //object which you add to before encoding and sending to the frontend. 
        //Initiate it here so each action doesnt have to do it again and again.             
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->response = new Response();

        $this->audit = new Audit();
        $this->auditTypes = new AuditType();
        $this->questionTypes = new QuestionType();
        $this->questionTypeOptions = new QuestionTypeOption();

        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
    }

    public function indexAction() {
        
        $this->response->audits = $this->audit->allAuditsArray($this->token->user->client);
        
//        foreach ($this->audit->allAuditsArray($this->token->user->client) AS $key => $audit) {
//
//            $audits[$key] = array(
//                "id" => $audit['id'],
//                "name" => $audit['name'],
//                "description" => $audit['description'],
//                "created_at" => $audit['created_at'],
//                "client_id" => $audit['client_id'],
//                "created_by" => $audit['created_by'],
//                "audit_type_id" => $audit['audit_type_id'],
//                "audit_type_name" => $audit['audit_type_name'],
////                "audit_type_name" => 
//            );
//        }
//        $this->response->audits = $audits;

        return print json_encode($this->response);
    }

    //This funciton is used t load the initial data required when creating 
    //a new audit (Question Type and Audit Type lists) add to this if you want
    // to provide more data to the "New Audit" pages. 
    public function initialNewAuditDataAction() {
        $client = $this->token->user->client;
        $this->response->auditTypes = $this->auditTypes->allAuditTypesArray($client);
        $this->response->questionTypes = $this->questionTypes->allQuestionTypesArray($client);
        $this->response->questionTypeOptions = $this->questionTypeOptions->allQuestionTypeOptionsArray($client);
        return print json_encode($this->response);
    }

    public function takeAuditAction($id) {
        $client = $this->token->user->client;
        
        $questionTypes = new QuestionType();
        $this->response->questionTypes = $questionTypes->allQuestionTypesArray($client);
        $audit = new Audit;
        $questionGroups = new QuestionGroup();
        $auditData = $audit->auditByIdArray($id, $client);

//      IF requested resource not found then return not found

//       return print "Audit Client". $auditData['client_id'] . "request client id". $client;
        
        
        if ($auditData === null) {
            
//            die("SHIT");
            $this->response->headers = http_response_code(404);
        } else {
            $this->response->headers = http_response_code(200);
            $this->response = array(
                "name" => $auditData['name'],
                "description" => $auditData['description'],
                "auditTypes" => array(
                    "id" => $auditData['audit_type_id'],
                    "name" => $auditData['audit_type_name']
                ),
                "groups" => $questionGroups->getAllQuestionGroupsByAuditIdArray($id),
            );

            foreach ($this->response['groups'] as $gkey => $group) {

                foreach ($group['questions'] as $qkey => $question) {
                    $question['answerType']['name'] = $this->helpers->searchForId($question['answerType']['id'], $this->questionTypes->allQuestionTypesArray($client));

                    $optionArray = array();
                    foreach ($this->questionTypeOptions->allQuestionTypeOptionsArray($client) as $key => $option) {
                        if ($option['type_id'] == $question['answerType']['id']) {
                            $optionArray[$key] = $option;
                        }
                    }

                    $question['answerType']['options'] = $optionArray;
                    $question['answerType']['optionCount'] = count($optionArray);

                    $max = 0;
                    foreach ($optionArray AS $opt) {
                        if ($max < $opt['value']) {
                            $max = $opt['value'];
                        }
                    }

                    $question['answerType']['optionMax'] = $max;
                    if (count($optionArray) == 1) {
                        $question['answerType']['optionSteps'] = 1;
                    } else {
                        $question['answerType']['optionSteps'] = round($question['answerType']['optionMax'] / (count($optionArray) - 1), 1);
                    }
                    $this->response['groups'][$gkey]['questions'][$qkey] = $question;
                }
            }
        }

        return print json_encode($this->response);
    }

    public function newAction() {      
        $this->audit->insertNewAudit($this->postdata->data, $this->token->user);
        $this->response->result = "Audit Saved";
        $this->response->status = "success";
        return print json_encode($this->response);
    }

    public function updateAction() {
        $this->audit->updateAudit($this->postdata->data, $this->token->user);
        $this->response->result = "Audit Updated";
        $this->response->status = "success";
        return print json_encode($this->response);
    }

    //Used to load all audit data (Questions, Question Types, Evidence, Expiry)
    // for Update and TakeAudit pages.  
    public function getAuditAction($id) {
        $client = $this->token->user->client;      
        
        $questionTypes = new QuestionType();
        $this->response->questionTypes = $questionTypes->allQuestionTypesArray($client);
        $audit = new Audit;
        $questionGroups = new QuestionGroup();
        $auditData = $audit->auditByIdArray($id, $client);
        if ($auditData === null) {
//                        die("SHIT");
            $this->response->headers = http_response_code(404);
        } else {
            $this->response->headers = http_response_code(200);
            //Start building the json object
            $this->response = array(
                "name" => $auditData['name'],
                "description" => $auditData['description'],
                "auditTypes" => array(
                    "id" => $auditData['audit_type_id'],
                    "name" => $auditData['audit_type_name'],
                ),
                "groups" => $questionGroups->getAllQuestionGroupsByAuditIdArray($id),
            );
        }

//        $auditData['groups'] = $questionGroups->getAllQuestionGroupsByAuditIdArray($id);
//        var_dump($questionGroups->getAllQuestionGroupsByAuditIdArray($id));
//        $this->response->groups = $questionGroups->getAllQuestionGroupsByAuditIdArray($id);
        //$this->response->groups = "THIS";
//        $this->response->data->name = $auditData->name;

        return print_r(json_encode($this->response));
    }

    public function retireAuditAction() {
        $postdata = json_decode(file_get_contents("php://input"));
        $this->response->status = $this->audit->retireAudit($postdata->id);
        return print json_encode($this->response);
    }

}
