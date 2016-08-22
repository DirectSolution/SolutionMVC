<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller;

class IndexController extends Controller {

    /**
     * PAGE: index
     * This method handles what happens when you move to http://portal.solutionhost.co.uk/apps2/public OR http://portal.solutionhost.co.uk/apps2/public/portal/home
     */
    public function indexAction() {
        if ($this->getToken()) {
            echo $this->twig->render("Portal/index.html.twig", array(
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
               "errors" => "You need to be logged in first",
                "project" => "Portal/",
                "controller" => "index/",
                "action" => "index"
            ));
        }
    }

}
