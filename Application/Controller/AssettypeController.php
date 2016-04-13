<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Model\AssetType;

/**
 * Description of AssetTypeController
 *
 * @author dhayward
 */
class AssettypeController {

    public $assetTypes;
    
    public function __construct() {
        $this->assetTypes = new AssetType();
    }
    
    public function indexAction() {
        return print_r(json_encode($this->assetTypes->getAllArray(000)));
    }

    public function getAction($id) {
        return print_r(json_encode($this->assetTypes->getOneByIdArray($id)));
    }

    public function getByGroupAction($id, $client = 0){
        return print_r(json_encode($this->assetTypes->getAllByGroupArray($client, $id)));
    }
    
    public function newAction() {
        
    }

    public function updateAction() {
        
    }

    public function deleteAction() {
        
    }

}
