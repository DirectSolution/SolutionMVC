<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller;

class DocumentController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function indexAction() {
        if ($this->isAuth(44)) {
            echo $this->twig->render('HealthSafety/Documents/index.html.twig');
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Document",
                "action" => ""
            ));
        }
    }

}
