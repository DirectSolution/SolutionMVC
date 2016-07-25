<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Portal\Model\User,
    SolutionMvc\Portal\Model\Role;

class UserController Extends Controller {

    protected $users;
    protected $security;
    protected $roles;

    public function __construct() {
        parent::__construct();
        $this->users = new User();
        $this->token = $this->getToken();
        $this->security = new \SolutionMvc\Core\Security();
        $this->roles = new Role();
    }

    public function indexAction() {
        if ($this->getToken()) {
            $users = array();
            foreach ($this->users->getAllByClient($this->token->user->client) as $user) {
                $users[] = array(
                    "user" => $user,
                    "auth" => $this->users->getUserRole($user['id'])
                );
            }
            echo $this->twig->render("Portal/User/index.html.twig", array(
                "users" => $users
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "User/",
                "action" => "index"
            ));
        }
    }

    public function viewAction($id) {
        if (in_array(13, $this->token->user->auth->Auth) && $this->getToken()) {

            $userArray = $this->userArray($id);
            echo $this->twig->render("Portal/User/view.html.twig", array(
                "user" => $userArray
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "User/",
                "action" => "View/$id"
            ));
        }
    }

    public function createAction() {
        if (in_array(15, $this->token->user->auth->Auth) && $this->getToken()) {
            if ($this->requestType() == 'GET') {
                echo $this->twig->render("Portal/User/create.html.twig", array(
                    "roles" => $this->roles->getRoles($this->token->user->client)
                ));
            } elseif ($this->requestType() == 'POST') {
                $user = $this->users->setNew($this->requestObject(), $this->token->user->client, $this->token->user->id);
                if ($user != null) {
                    $email = new \SolutionMvc\Portal\Controller\EmailController();                    
                    $this->roles->setUserRole($user, $this->requestObject());
                    $this->setSession("success", "Successfully added user.");
                    $email->newUserEmail($user, $this->token, $this->requestObject());
                    $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Portal/User/'));
                } else {
                    $this->setSession("error", "Sorry, something went wrong while trying to create a new user.");
                    $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Portal/User/'));
                }
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "User/",
                "action" => "Create"
            ));
        }
    }

    public function updateAction($id) {
        if (in_array(14, $this->token->user->auth->Auth) && $this->getToken()) {
            if ($this->requestType() == 'GET') {
                $userArray = $this->userArray($id);
                echo $this->twig->render("Portal/User/update.html.twig", array(
                    "user" => $userArray,
                    "roles" => $this->roles->getRoles($this->token->user->client)
                ));
            } else if ($this->requestType() == 'POST') {
                $this->users->setUpdate($this->requestObject(), $this->token->user->client, $id);
                $this->roles->setUpdate($this->requestObject(), $id);
                $this->setSession("success", "Successfully updated user.");
                $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Portal/User/'));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "User/",
                "action" => "Update/$id"
            ));
        }
    }

    public function userArray($id) {
        $user = $this->users->getUser($id);
        $createdBy = $this->users->getUserById($user[$id]['createdby_id']);
        return array(
            "id" => $user[$id]['id'],
            "name" => $user[$id]['name'],
            "username" => $user[$id]['username'],
            "email" => $user[$id]['email'],
            "client" => ltrim($user[$id]['client'], 0),
            "jobtitle" => $user[$id]['jobtitle'],
            "telephone" => $user[$id]['telephone'],
            "timestamp" => $user[$id]['timestamp'],
            "siglogo" => $user[$id]['siglogo'],
            "createdby_id" => $createdBy['username'],
        );
    }
    
    
    public function activateAction($key){
        
         echo $this->twig->render("Portal/User/activate.html.twig", array(
                
            ));
        
    }


}
