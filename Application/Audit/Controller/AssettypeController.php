<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Model\AssetType,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security;

/**
 * Description of AssetTypeController
 *
 * @author dhayward
 */
class AssettypeController Extends Controller {

    protected $assetTypes;
    protected $response;
    protected $security;

    public function __construct() {
        parent::__construct();
        $this->assetTypes = new AssetType();
        $this->response = new Response();
        $this->security = new Security();
        //This may need moving, could cause if we ever want to use gets
//        $this->postdata = json_decode(file_get_contents("php://input"));
//        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
//        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function indexAction($client) {        
      return $this->response->data = $this->assetTypes->getAllArray($client);
//        return print_r(json_encode($this->response));
    }

    public function getAction($id) {
        return print_r(json_encode($this->getOne($id)));
    }
    
    public function getOne($id){
        return $this->assetTypes->getOneByIdArray($id);
    }

    public function getByGroupAction($id) {

        $this->response->data = $this->assetTypes->getAllByGroupArray($this->token->user->client, $id);
        $this->response->headers = http_response_code(200);
        return print_r(json_encode($this->response));
    }

    public function newAction() {
        
    }

    public function updateAction() {
        
    }

    public function deleteAction() {
        
    }

}
