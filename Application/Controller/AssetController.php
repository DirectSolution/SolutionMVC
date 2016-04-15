<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Model\Asset,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security;

/**
 * Description of AssetController
 *
 * @author dhayward
 */
class AssetController {

    public $assets;
    public $response;

    public function __construct() {
        $this->response = new Response();
        $this->assets = new Asset();
        $this->security = new Security();
        //This may need moving, could cause if we ever want to use gets
        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function indexAction() {
        $this->response->assets = $this->assets->getAllArray($this->token->user->client);
        return print_r(json_encode($this->response));
    }

    public function getAction($id) {
        $this->response->assets = $this->assets->getOneByIdArray($id, $this->token->user->client);
        return print_r(json_encode($this->response));
    }

    public function getAllByTypeAction($id) {
        $this->response->assets = $this->assets->getAllByTypeArray($id, $this->token->user->client);
        return print_r(json_encode($this->response));
    }

    public function getAllByGroupAction($id) {
        $this->response = $this->assets->getAllByGroupArray($id, $this->token->user->client);
        return print_r(json_encode($this->response));
    }

    public function newAction() {

        try {
            $this->assets->setAsset($this->postdata->asset, $this->token->user->client);
            $this->response->status = "success";
            return print_r(json_encode($this->response));
        } catch (Exception $e) {
            return print_r(json_encode($e));
        }


        return print(json_encode("Record inserted"));
    }

    public function updateAction() {
        
    }

    public function retireAction() {
        $this->response->status = $this->assets->retire($this->postdata->data->id);
        return print json_encode($this->response);
    }

}
