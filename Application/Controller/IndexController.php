<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Controller;


//This is the index page. This gets called when all other posibilities have failed, check Application.php for more info.
class IndexController extends Controller {

    /**
     * PAGE: index
     * This method handles what happens when you move to http://portal.solutionhost.co.uk/apps2/public OR http://portal.solutionhost.co.uk/apps2/public/portal/home
     */
    public function indexAction($message = null) {

        print "$message Loaded base index page";
    }  

}
