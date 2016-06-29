<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Audit\Model\Audit,
    SolutionMvc\Audit\Model\Answer,
    SolutionMvc\Audit\Model\AnswersSet,
    SolutionMvc\Audit\Model\QuestionTypeOption,
    SolutionMvc\Audit\Controller\AssignmentController,
    SolutionMvc\Audit\Controller\AssetController,
    SolutionMvc\Core\Response,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Controller,
    Fisharebest\PhpPolyfill\Php54;

//Use this controller for saving answers to audits. Audit controller was getting to big so have split it into its own.

class AnswersController extends Controller {

    protected $assignment;
    protected $answer;
    protected $asset;
    protected $answerSet;
    protected $helpers;
    protected $security;
    protected $response;
    protected $questionTypeOption;
    protected $token;

    public function __construct() {
        parent::__construct();
        $this->questionTypeOption = new QuestionTypeOption();
        $this->assignment = new AssignmentController();
        $this->answer = new Answer();
        $this->asset = new AssetController();
        $this->answerSet = new AnswersSet();
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->security = new Security();
        $this->response = new Response();
        $this->token = $this->getToken();
//        $this->postdata = json_decode(file_get_contents("php://input"));
//        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
//        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function getAnswersAction() {
        
    }

    public function setAnswersAction($audit, $asset) {
        
//        die(print_r($this->requestObject()));
        $this->post = $this->requestObject();
        
        $client = $this->token->user->client;
        $assignment = $this->assignment->isValidAssignmentRequest($asset, $audit, $client);
        if ($assignment) {
            $answerSetId = $this->answerSet->setAnswersSet($assignment['id'], $client);
            foreach ($this->post['takeAudit']['answers'] AS $question => $answer) {
                if (key($answer) !== 'points') {
                    $aVal = $this->questionTypeOption->getOne($answer);
                } else {
                    $aVal = array(
                        "name" => ucfirst(key($answer)),
                        "value" => $answer->points);
                }
                $this->answer->insertOne($question, $answer, $answerSetId, $assignment, $this->token->user, $aVal);
            }
            return $answerSetId;
        }else{
            return false;
        }
        
    }
    
    public function setAnswersAndAssetAction() {

        //INSERT ASSET
       
        $asset = $this->asset->setAsset($this->postdata->asset, $this->token->user->client);
        
        //THEN INSERT ASSIGNMENT
        $assignment = $this->assignment->setAssignment($this->postdata->audit, $asset, $this->token->user->id, $this->token->user->client);
        
        //THEN INSERT ANSWERS
        
        
//        $client = $this->token->user->client;
//        $assignment = $this->assignment->isValidAssignmentRequest($this->postdata->asset, $this->postdata->audit, $client);
//        if ($assignment) {
            $answerSetId = $this->answerSet->setAnswersSet($assignment, $this->token->user->client);
            foreach ($this->postdata->data->answers AS $question => $answer) {
                if (key($answer) !== 'points') {
                    $aVal = $this->questionTypeOption->getOne($answer);
                } else {
                    $aVal = array(
                        "name" => ucfirst(key($answer)),
                        "value" => $answer->points);
                }
                $this->answer->insertOne($question, $answer, $answerSetId, $assignment, $this->token->user, $aVal);
            }
            $this->response->headers = http_response_code(200);
            $this->response->message = "WOOP WORKED FIRST TIME";
            return print (json_encode($this->response));
//        } else {
//            $this->response->headers = http_response_code(404);
//        }
    }

}
