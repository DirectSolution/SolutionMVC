<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Model\Asset,
    SolutionMvc\Core\Controller;

/**
 * Description of AssetController
 *
 * @author dhayward
 */
class AssetController{

    public $assets;
    public $response;

    public function __construct() {
        $this->response = new Response();
        $this->assets = new Asset();
    }

    public function indexAction() {
        return print_r(json_encode($this->assets->getAllArray(000)));
    }

    public function getAction($id) {
        return print_r(json_encode($this->assets->getOneByIdArray($id)));
    }

    public function getAllByTypeAction($id, $client = 0) {
        return print_r(json_encode($this->assets->getAllByTypeArray($id, $client)));
    }

    public function newAction() {
        $post = json_decode(file_get_contents("php://input"));

        try {
            $this->assets->setAsset($post->asset);
            $this->response->status = "success";
            return print_r(json_encode($this->response));
        } catch (Exception $e) {
            return print_r(json_encode($e));
        }


        return print(json_encode("Record inserted"));
    }

    public function updateAction() {
        
    }

    public function deleteAction() {
        
    }

    public function retireAction() {
        $postdata = json_decode(file_get_contents("php://input"));
        $this->response->status = $this->assets->retire($postdata->id);
        return print json_encode($this->response);
    }

}
