<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Model\AssetGroup;

/**
 * Description of AssetGroup
 *
 * @author dhayward
 */
class AssetgroupController {

    public $assetGroups;
    
    public function __construct() {
        $this->assetGroups = new AssetGroup();
    }
    
    public function indexAction() {
        return print_r(json_encode($this->assetGroups->getAllArray(000)));
    }

    public function getAction($id) {
        return print_r($this->assetGroups->getOneByIdArray($id));
    }

    public function getGroupsWithTypesAction($client = 0){
        
//        print "<pre>";
//        print_r ($this->assetGroups->getAllWithTypesArray($client));
//        print "</pre>";
        
        return print_r(json_encode($this->assetGroups->getAllWithTypesArray($client)));
    }
    
    
    public function newAction() {
        
    }

    public function updateAction() {
        
    }

    public function deleteAction() {
        
    }

}
