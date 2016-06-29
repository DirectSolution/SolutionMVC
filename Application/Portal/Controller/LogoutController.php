<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller;

class LogoutController Extends Controller {

    public function indexAction() {
        session_destroy();
        unset($_COOKIE['token']);
        echo $this->twig->render("/Portal/Login/login.html.twig");
    }

}
