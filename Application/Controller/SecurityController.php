<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Response;

/**
 * Description of SecurityController
 *
 * @author doug
 */
class SecurityController extends Controller {

    public function loginAction() {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $security = new Security;

        $user = $this->getUserAction($request->user->username, $request->user->client);

        $passwordCheck = $security->checkPassword($request->user->password, $user['password']);
        $response = new Response();
        
        if($passwordCheck === true){
//            $response->header = header('HTTP/1.1 200 Authorised', true, 200);
            $response->result = "Password Correct";
            $response->status = "success";
            $response->username = $user['username'];

        }else{
//            $response->header = header('HTTP/1.1 401 Authorised', true, 401);
            $response->result = "Password In-Correct";
            $response->status = "fail";
        }
        
        return print(json_encode($response));
        
    }

    public function getUserAction($username, $client) {
        try {

            $model = new \SolutionMvc\Model\User();
            $user = $model->getOneUserByUsernameAndClient($username, $client);
//            if (count($user) > 1) {
//                throw new \Exception("Multiple Users Found");
//            } else if (count($user) < 1) {
//                throw new \Exception("No Users Found");
//            } else {
                return $user;
//            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

}
