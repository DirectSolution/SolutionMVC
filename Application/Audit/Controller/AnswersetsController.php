<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Audit\Model\Audit,
    SolutionMvc\Audit\Model\AnswersSet,
    SolutionMvc\Audit\Model\AuditType,
    SolutionMvc\Core\Response,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Controller,
    Fisharebest\PhpPolyfill\Php54;

/**
 * Description of AssignmentController
 *
 * @author doug
 */
class AnswersetsController extends Controller {

    protected $response;
    protected $helpers;
    protected $security;
    protected $AnswerSet;

    public function __construct() {
        $this->security = new Security();
        $this->AnswerSet = new AnswersSet();
        //Most controllers require this as it is used to instantiate a Response 
        //object which you add to before encoding and sending to the frontend. 
        //Initiate it here so each action doesnt have to do it again and again.             
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->response = new Response();

        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }
    
    public function getAnswerSetsAction(){
        return $this->AnswerSet->getAnswersSet($this->postdata->data->assignment_id);
    }
        
}