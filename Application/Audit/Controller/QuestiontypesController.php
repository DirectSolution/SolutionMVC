<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Model\QuestionType,
    SolutionMvc\Audit\Model\QuestionTypeOption,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security;

/**
 * Description of AssetGroup
 *
 * @author dhayward
 */
class QuestiontypesController Extends Controller {

    protected $questionType;
    protected $questionTypeOption;

    public function __construct() {

        $this->response = new Response();
        $this->security = new Security();
        $this->questionType = new QuestionType();
        $this->questionTypeOption = new QuestionTypeOption();
        //This may need moving, could cause if we ever want to use gets
        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function setNewAction() {
        $questionType = $this->questionType->setNewType($this->token->user->client, $this->postdata->data);
        foreach ($this->postdata->data->options AS $option) {
            $this->questionTypeOption->setNewOption($this->token->user->client, $option, $questionType['id']);
        }
        $this->response->status = "success";
        $this->response->header = http_response_code(200);
        return print json_encode($this->response);
    }

}
