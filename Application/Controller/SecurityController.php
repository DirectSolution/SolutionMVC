<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Response,
    SolutionMvc\Model\User;

/**
 * Description of SecurityController
 *
 * @author doug
 */
class SecurityController extends Controller {

    var $security;
    var $response;
    var $user;

    public function __construct() {
        $this->security = new Security();
        $this->response = new Response();
        $this->user = new User();
    }

    public function loginAction() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);
        $user = $this->getUserAction($request->user->username, $request->user->client, $request->user->password);
        if ($user['status'] == "success") {
            $passwordCheck = $this->security->checkPassword($request->user->password, $user['response']['password']);
            if ($passwordCheck == true) {
//                $this->response->headers = http_response_code(200);
                $this->response->result = "Password Correct";
                $this->response->status = "success";
                $this->response->username = $user['response']['username'];
                $this->response->token = $this->security->EncodeSecurityToken($user['response']);
            } else {
                $this->response->headers = http_response_code(401);
                $this->response->result = "Password Not Correct!";
                $this->response->status = 401;
            }
        } else {
            $this->response->result = $user['response'];
            $this->response->status = "fail";
        }
        return print(json_encode($this->response));
    }

    public function getUserAction($username, $client) {
        try {
            $user = $this->user->getOneUserByUsernameAndClient($username, $client);
            if (count($user['id']) > 1) {
                return array(
                    "status" => "fail",
                    "response" => "Multiple Users Found"
                );
            } else if (count($user['id']) < 1) {
                return array(
                    "status" => "fail",
                    "response" => "No User Found"
                );
            } else {
                return array(
                    "status" => "success",
                    "response" => $user
                );
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
