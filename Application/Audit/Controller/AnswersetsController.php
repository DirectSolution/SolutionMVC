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
        parent::__construct();
        $this->AnswerSet = new AnswersSet();          
        $this->response = new Response();
        $this->token = $this->getToken();
    }
    
    public function getAnswerSetsAction(){
        return $this->AnswerSet->getAnswersSet($this->postdata->data->assignment_id);
    }
       
    
    public function overUnderDueAuditsAction(){
        return print json_encode($this->AnswerSet->getOverUnder($this->token->user->client));
    }
}