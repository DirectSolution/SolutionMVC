<?php

namespace SolutionMvc\Core;

/**
 * Description of Response
 *
 * @author doug
 */
//Currently all this does is define a set of default reposnse objects, simply to 
//encurage the same standard to be used across the portal. Eventually this could
// probably be developed into something stronger. Maybe like this:
// http://api.symfony.com/3.1/Symfony/Component/HttpFoundation.html
class Response {

    public $headers;
    public $status;
    public $data;
    public $token;
    public $message;

    function getHeaders() {
        return $this->headers;
    }

    function getStatus() {
        return $this->status;
    }

    function getData() {
        return $this->data;
    }

    function getToken() {
        return $this->token;
    }

    function getMessage() {
        return $this->message;
    }

    function setHeaders($headers) {
        $this->headers = $headers;
    }

    function setStatus($status) {
        $this->status = $status;
    }

    function setData($data) {
        $this->data = $data;
    }

    function setToken($token) {
        $this->token = $token;
    }

    function setMessage($message) {
        $this->message = $message;
    }

//        public function __construct() {
//        $this->data = new \stdClass;
//        $this->token = new \stdClass;
//        $this->headers = new \stdClass;
//        $this->status = new \stdClass;
//        
//    }





}
