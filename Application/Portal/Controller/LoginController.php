<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Core\Response,
    SolutionMvc\Portal\Model\User;

class LoginController Extends Controller {

    protected $user;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
        $this->response = new Response();
    }

    public function getRedirect($project = null, $controller = null, $action = null, $item = null){
        if (isset($project)) {
            if (isset($project)) { $redirect = $project . "-"; }
            if (isset($controller)) { $redirect .= $controller . "-"; }
            if (isset($action)) { $redirect .= $action . "-"; }
            if (isset($item)) { $redirect .= $item."-"; }
        }else{
            $redirect = null;           
        }
        return $redirect;
    }
    
    public function indexAction($requestType = null, $project = null, $controller = null, $action = null, $item = null) {
        $dash = null;       
        if ($this->requestType() != 'GET' && !$this->getToken()) {
            $fire = $this->loginFire();

            if ($requestType == "ajax") {
                if ($fire->status == "success") {
                    return print json_encode(array(
                        "status" => "success",
                        "url" => HTTP_ROOT . "portal/security/setcookie/" . $fire->token . "/".$this->getRedirect($project, $controller, $action, $item)
                    ));
                } else {
                    return print json_encode(array(
                        "status" => "error",
                        "message" => $fire->message
                    ));
                }
            } else {
//                $conFire = json_decode(json_encode($fire), true);
                if ($fire->status == "success") {
                    $this->redirect("portal/security/setcookie/" . $fire->token . "/".$this->getRedirect($project, $controller, $action, $item));
                } else {
                    echo $this->twig->render('Portal/Login/login.html.twig', array(
                        "errors" => $fire->message,
                        "form" => $this->requestObject()));
                }
            }
        }elseif ($this->getToken()){
            $this->redirect("Portal/Index", "You are already logged in!");
        } else {
            echo $this->twig->render('Portal/Login/login.html.twig');
        }
    }

    public function loginFire() {
        $security = new \SolutionMvc\Portal\Controller\SecurityController();
                $_POST['_address'] = $_SERVER['REMOTE_ADDR'];
               
        return json_decode($security->loginAction($_POST));
        
        
        
        
//        $url = 'http://doug.portal.solutionhost.co.uk/apps2/public/portal/security/login';
////        $url = "http:" . HTTP_ROOT . 'portal/security/login';
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
//        return $result = json_decode(curl_exec($ch), true);
        
        
//        $_POST['_address'] = $_SERVER['REMOTE_ADDR'];
//        $url = "http:" . HTTP_ROOT . 'portal/security/login';
//        $ch = curl_init($url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
//        return $result = json_decode(curl_exec($ch), true);
    }

    public function ForgottenpasswordAction($requestType = null) {
        $fire = $this->forgotFire();
        if ($this->requestObject() != $_GET) {
            if ($requestType == "ajax") {
                return print json_encode(array(
                    "status" => $fire['status'],
                    "message" => $fire['message'],
                    "form" => $this->requestObject()
                ));
            } else {
                if ($fire['status'] == 'error') {
                    echo $this->twig->render('Portal/ForgotPassword/forgotten-password.html.twig', array(
                        "errors" => $fire['message'],
                        "form" => $this->requestObject()
                    ));
                    return;
                } else {
                    echo $this->twig->render('Portal/ForgotPassword/forgotten-password.html.twig', array(
                        "success" => $fire['message'],
                        "form" => $this->requestObject()
                    ));
                    return;
                }
            }
        } else {
            echo $this->twig->render('Portal/ForgotPassword/forgotten-password.html.twig');
        }
    }

    public function forgotFire() {
        $_POST['_address'] = $_SERVER['REMOTE_ADDR'];
        $url = "http:" . HTTP_ROOT . 'portal/security/resetrequest/';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
        return $result = json_decode(curl_exec($ch), true);
    }

    public function resetpasswordAction($hash) {
        $tprr = new \SolutionMvc\Model\TransPasswordResetRequest();
        $request = $tprr->getResetRequest($hash);
        if (!$request) {
            echo $this->twig->render('Portal/ForgotPassword/forgotten-password.html.twig', array(
                "errors" => "Invalid link supplied!"
            ));
        } else if ($request['retired'] == 1) {
            echo $this->twig->render('Portal/ForgotPassword/forgotten-password.html.twig', array(
                "errors" => "This password reset request has been retired, please submit a new one!"
            ));
        } else if ($request['date_requested'] > \date("Y-m-d h:i:s", \strtotime("-30 minutes"))) {
            $this->user->setNewPassword($request);
            $tprr->setRetireRequest($request['id']);
            echo $this->twig->render('Portal/Login/login.html.twig', array(
                "success" => "Reset password complete, you may now log in using your new password!"
            ));
        } else if ($request['date_requested'] < \date("Y-m-d h:i:s", \strtotime("-30 minutes"))) {
            $tprr->setRetireRequest($request['id']);
            echo $this->twig->render('Portal/ForgotPassword/forgotten-password.html.twig', array(
                "errors" => "Reset request has expired please submit a new one!"
            ));
        } else {
            die("system ERROR");
        }
    }

}
