<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Model\AssetGroup,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security;

/**
 * Description of AssetGroup
 *
 * @author dhayward
 */
class AssetgroupController Extends Controller {

    protected $assetGroups;
    protected $postdata;
    protected $token;
    protected $response;
    protected $security;

    public function __construct() {

        $this->response = new Response();
        $this->security = new Security();
        $this->assetGroups = new AssetGroup();
        //This may need moving, could cause if we ever want to use gets
//        $this->postdata = json_decode(file_get_contents("php://input"));
//        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
//        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function indexAction($client) {
        
       return $this->response->data = $this->assetGroups->getAllArray($client);        
//        return print_r(json_encode($this->response));
    }

    public function getAction() {
        
        $this->response->data = $this->assetGroups->getOneByIdArray($this->token->user->client);
        $this->response->headers = http_response_code(200);
        
        return print_r($this->response);
    }

    public function getGroupsWithTypesAction($client) {

        return $this->response->data = $this->assetGroups->getAllWithTypesArray($client);
//        $this->response->headers = http_response_code(200);
        
//        return print_r(json_encode($this->response));
    }
    
    public function getGroupsWithTypesOptAction($client){
         return $this->response->data = $this->assetGroups->assetGroupsWithTypes($client);
//        $this->response->headers = http_response_code(200);
        
//        return print_r(json_encode($this->response));
    }

    public function newAction() {
        
    }

    public function updateAction() {
        
    }

    public function deleteAction() {
        
    }

}
