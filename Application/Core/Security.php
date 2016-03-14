<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Core;

/**
 * Description of Security
 *
 * @author doug
 */
class Security {

    protected $secret = "There cAn bE onLy ONE";

    public function encodePassword($password) {
        return md5($this->secret . $password);
    }
    
    public function checkPassword($passwordGiven, $passwordActual){
        if($this->encodePassword($passwordGiven) === $passwordActual){
            return true;
        }else{
            return false;
        }
    }

}
