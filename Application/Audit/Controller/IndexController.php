<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Audit\Controller\AssetController;

class indexController extends Controller {

    public function indexAction() {
        $assetIndex = new AssetController();
        print_r($assetIndex->indexAction());
        
    }

}
