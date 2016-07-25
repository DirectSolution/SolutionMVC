<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller;

class PolicyController extends Controller {

    public function __construct() {
        parent::__construct();
    }

    public function indexAction() {
        echo $this->twig->render('HealthSafety/Documents/Policy/index.html.twig', array(
            "data" => "get data"
        ));
    }

    public function viewAction($id) {
        echo $this->twig->render('HealthSafety/Documents/Policy/view.html.twig', array(
            "data" => "get data"
        ));
    }

    public function createAction() {
        echo $this->twig->render('HealthSafety/Documents/Policy/create.html.twig', array(
            "data" => "get data"
        ));
    }

    public function updateAction($id) {
        echo $this->twig->render('HealthSafety/Documents/Policy/update.html.twig', array(
            "data" => "get data"
        ));
    }

    public function retireAction($id) {
        
    }

    public function getDocumentAction($id) {
        
    }

}
