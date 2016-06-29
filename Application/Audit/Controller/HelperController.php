<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Model\Helpers;

class HelperController {

    var $helpers;
    protected $response;

    public function __construct() {
        $this->helpers = new Helpers();
        $this->response = new Response();
    }

    public function getCountiesAction() {
        $this->response->data = $this->helpers->getCountiesArray();
        return print_r(json_encode($this->response));
    }

    public function getCountriesAction() {
        $this->response->data = $this->helpers->getCountriesArray();
        return print_r(json_encode($this->response));
    }

}
