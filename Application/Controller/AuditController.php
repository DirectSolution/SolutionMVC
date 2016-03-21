<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Model\Audit,
        SolutionMvc\Model\AuditType,
        SolutionMvc\Model\QuestionType,
        SolutionMvc\Core\Response;

/**
 * Description of AuditController
 *
 * @author doug
 */
class AuditController {
    
    protected $response;


    public function __construct() {
        $this->response = new Response();
    }

    public function indexAction() {
        return print '
            [
                {
                "id":"1",
                "name":"Some audit",
                "created_at":"11/11/16"
                },
                {
                "id":"2",
                "name":"Some audit 2",
                "created_at":"09/01/16"
                }
            ]            
            ';
    }

    public function initialNewAuditDataAction($client = 000) {
        $auditTypes = new AuditType();
        $questionTypes = new QuestionType();
        
        $this->response->auditTypes = $auditTypes->allAuditTypesArray($client);
        $this->response->questionTypes = $questionTypes->allQuestionTypesArray($client);

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

    public function getAuditAction($id = "1") {
        $this->response->questionTypes = $questionTypes->allQuestionTypesArray($client);
        $audit = new Audit;
        return print ($audit->testDataForAudit($id));
    }

    public function deleteAuditAction() {
        
    }

}
