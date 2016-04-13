<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Model\Helpers;

class HelperController {

    var $helpers;

    public function __construct() {
        $this->helpers = new Helpers();
    }

    public function getCountiesAction() {
        return print_r(json_encode($this->helpers->getCountiesArray()));
    }

    public function getCountriesAction() {
        return print_r(json_encode($this->helpers->getCountriesArray()));
    }

}
