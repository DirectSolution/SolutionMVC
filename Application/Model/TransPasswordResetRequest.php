<?php

namespace SolutionMvc\Model;

use SolutionMvc\Core\Security;

class TransPasswordResetRequest extends BaseModel {

    protected $security;

    public function __construct() {
        parent::__construct();
        $this->security = new Security();
    }

    public function getResetRequest($string) {
        return $this->prod_portal->trans_password_reset_request[array("randomstring" => $string)];
    }

    public function setRetireRequest($id) {
        $check = $this->prod_portal->trans_password_reset_request[$id];
        if ($check) {
            $check->update(array("retired" => 1));
        }
        return;
    }

    public function setPasswordResetRequest($user, $password, $address) {

        $password_hashed = $this->security->encodePassword($password);
        $random_string = $this->security->hasherAction(rand() . $user['username']);


        $this->prod_portal->trans_password_reset_request->insert(array(
            "username" => $user['username'],
            "password" => $password_hashed,
            "client" => $user['client'],
            "randomstring" => $random_string,
            "date_requested" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "email" => $user['email'],
            "ip" => $address
        ));
        return array(
            "username" => $user['username'],
            "user_id" => $user['id'],
            "client" => $user['client'],
            "email" => $user['email'],
            "random_string" => $random_string,
        );
    }


}
