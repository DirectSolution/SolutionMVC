<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Model\User,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Response,
    Fisharebest\PhpPolyfill\Php54;

/**
 * Description of AuditController
 *
 * @author doug
 */
class UsersController extends Controller {

    protected $users;
    protected $response;
    protected $token;
    protected $postdata;
    protected $security;

    public function __construct() {
        $this->security = new Security();
        $this->users = new User();
        $this->response = new Response();
        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function indexAction() {
        $array = array();
        foreach ($this->users->getAllUsers($this->token->user->client) AS $user) {
            $array[] = $this->userArray($user);
        }
        return print json_encode($array);
    }

    public function getOneUserAction(){
        return $this->userArray($this->model->getOneUserById($this->postdata->UserId));
    }
    
    public function userArray($user) {
        return array(
            "id" => $user['id'],
            "username" => $user['username'],
            "name" => $user['name'],
            "email" => $user['email'],
            "jobtitle" => $user['jobtitle'],
            "telephone" => $user['telephone'],
            "signature" => $user['siglogo']
        );
    }

}
