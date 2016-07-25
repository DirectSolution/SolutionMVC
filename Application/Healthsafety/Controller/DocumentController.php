<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller,
    PhpOffice\PhpWord\PhpWord;

class DocumentController extends Controller {

    public function __construct() {
        parent::__construct();
        require_once APP . '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
        $this->word = new \PhpOffice\PhpWord\PhpWord();
    }

    public function createCoshhAction() {
        echo $this->twig->render("HealthSafety/Documents/Coshh/create.html.twig", array(
        ));
    }

    public function indexAction() {

        echo $this->twig->render('HealthSafety/Documents/index.html.twig', array(
            "data" => "get data"
        ));
    }

}
