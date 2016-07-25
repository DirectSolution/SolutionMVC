<?php

namespace SolutionMvc\Ffc\Controller;

use SolutionMvc\Core\Controller;

class AdminindexController extends Controller {

    protected $token;

    public function __construct() {
        parent::__construct();
        $this->token = $this->getToken();
    }

    public function indexAction() {
        if($this->isAuth(43, $this->getToken())){
            echo $this->twig->render('FFC/Admin/index.html.twig', array(
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You are not authorised to access this page. Either you have not logged in or you do not have the correct privileges to access this area.",
                "project" => "Ffc/",
                "controller" => "Adminindex/",
                "action" => "index"
            ));
        }
    }

}
