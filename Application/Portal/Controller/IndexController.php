<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller;

class IndexController extends Controller {

    /**
     * PAGE: index
     * This method handles what happens when you move to http://portal.solutionhost.co.uk/apps2/public OR http://portal.solutionhost.co.uk/apps2/public/portal/home
     */
    public function indexAction() {

        print "Loaded Homepage";
    }  

}