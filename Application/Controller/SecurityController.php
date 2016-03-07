<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Controller;

/**
 * Description of SecurityController
 *
 * @author doug
 */
class SecurityController extends Controller {

    public function login($post){
        return json_encode($post);

    }
    
    
    public function getUser($username, $client) {
        try {

            $model = new \SolutionMvc\Model\User();
            $user = $model->getOneUserByUsernameAndClient($username, $client);
            if (count($user) > 1) {
                throw new \Exception("Multiple Users Found");
            } else if (count($user) < 1) {
                throw new \Exception("No Users Found");
            } else {
                print "<PRE>";
                print_r(json_encode($user));
                print "</PRE>";
            }
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}
