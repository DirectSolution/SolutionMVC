<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Model\AuditType,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security;

/**
 * Description of AssetTypeController
 *
 * @author dhayward
 */
class AudittypeController Extends Controller{

    protected $auditTypes;    
    protected $postdata;
    protected $token;
    protected $response;
    protected $security;
        
    public function __construct() {
        $this->auditTypes = new AuditType();        
        $this->response = new Response();
        $this->security = new Security();
        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);        
    }
    
    public function indexAction() {
    }

    public function getAction($id) {
        
    }
    
    public function newAction() {
        $insert = $this->auditTypes->setNewType(
                $this->token->user->client, $this->postdata->type
        );
        if ($insert !== false) {
            $this->response->type = $insert['name'];
            $this->response->headers = http_response_code(200);
            $this->response->status = $this->response->status = "success";
        } else {
            $this->response->headers = http_response_code(400);
            $this->response->status = "fail";
        }
        
        return print json_encode($this->response);
    }

    public function updateAction() {
        
    }

    public function deleteAction() {
        
    }

}
