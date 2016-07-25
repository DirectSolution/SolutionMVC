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

    public function setAnswers($assignment, $client, $request, $audit) {
        
        $answerSetId = $this->answerSet->setAnswersSet($assignment['id'], $client, $assignment['Audits_id'], $audit);
        $totalScore = array();

        
        foreach ($request['takeAudit']['answers'] AS $question => $answer) {
                   

            
            if (key($answer) !== 'points') {
                $aVal = $this->questionTypeOption->getOne($answer);
            } else {
                $aVal = array(
                    "name" => trim(ucfirst(key($answer))),
                    "value" => $answer->points);
            }
            $this->answer->insertOne($question, $answer, $answerSetId, $assignment, $this->token->user, $aVal);
//            die($aVal['value']);
            $totalScore[] = $aVal['value'];
        }
//        die((string)array_sum($totalScore));
        $this->answerSet->update($answerSetId, array_sum($totalScore));
//             die();   
        return $answerSetId;
    }

    public function setAnswersAction($audit, $asset) { 
        $assignment = $this->assignment->isValidAssignmentRequest($asset, $audit, $client = $this->token->user->client);
        if ($assignment) {
            return $this->setAnswers($assignment, $client, $this->requestObject(), $audit);
        } else {
            return false;
        }
    }

    public function setAnswersAndAssetAction($audit) {
        $request = $this->requestObject();
        //INSERT ASSET        
        $asset = $this->asset->setAsset($request['asset'], $this->token->user->client);               
        //THEN INSERT ASSIGNMENT

        
        $assignment = $this->assignment->setOneAssignment($audit, $asset, $this->token->user->id, $this->token->user->client);   
        

        
        return $this->setAnswers($assignment, $this->token->user->client, $this->requestObject(), $audit);
    }

}
