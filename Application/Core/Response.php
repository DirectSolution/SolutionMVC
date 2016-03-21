<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Core;

/**
 * Description of Response
 *
 * @author doug
 */
class Response {
    
    public $header = "";
    public $status = "";
    public $result = "";
    public $username = "";
    public $data = "";
    
    public function __construct() {
        $this->token = md5($this->username);
    }
}
