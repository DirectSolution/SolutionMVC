<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Response,
    SolutionMvc\Library\Helper,
    SolutionMvc\Portal\Model\User,
    SolutionMvc\Model\Client,
    SolutionMvc\Model\TransPasswordResetRequest;

/**
 * Description of SecurityController
 *
 * @author doug
 */
class SecurityController extends Controller {

    protected $security;
    protected $response;
    protected $user;
    protected $client;
    protected $request;
    protected $helper;
    protected $passwordResetRequest;

    public function __construct() {
        parent::__construct();
        $this->security = new Security();
        $this->response = new Response();
        $this->user = new User();
        $this->client = new Client();
        $this->request = $this->requestObject();
        $this->helper = new Helper();
        $this->passwordResetRequest = new TransPasswordResetRequest();
    }

    public function loginAction($_POST) {
        $this->request = $_POST;
        $username = $this->request['_username'];
        if ($this->request['_client'] != null) {
            $users = $this->user->getOneUserByUsernameAndClient($username, $this->request['_client']);
        } else {
            $users = $this->user->getOneUserByUsername($username);
        }

        if (count($users) === 0) {
            $this->response->setStatus("error");
            $this->response->setMessage("User '$username' not found.");
        } else if (count($users) > 1) {
            $this->response->setStatus("error");
            $this->response->setMessage("Multiple users found for details supplied. Please try again entering your Client ID, Username and Password");
        } else if (count($users) === 1) {

            foreach ($users as $user) {
                if ($this->security->checkPassword($this->request['_password'], $user['password']) === true) {

                    $client = $this->client->getClientById($user['client']);

                    $this->response->setStatus("success");
                    $this->response->setToken($this->security->EncodeSecurityToken($user, $client));
                    $this->setLoginActivity($user, $client, $this->request['_address']);

                    //$this->response->headers = header("Location: http://doug.portal.solutionhost.co.uk/portalmain.php");
                } else {
                    $this->response->setStatus("error");
                    $this->response->setMessage("Incorrect password given");
                }
            }
        }

        return json_encode($this->response);
    }

    public function setLoginActivity($user, $client, $address) {
        $newexpiry = date('U') + 14400;
        $hash = $this->security->hasherAction($user['username']);
        $this->user->setActivity($user, $client, $newexpiry, $hash, $address);
        return;
//        return $this->helper->setCookies($user, $client);
    }

    public function switchclientAction() {
        $request = $this->requestObject();
        $token = $this->getToken();
        $user = $this->user->getUserById($token->user->id);



        $this->setCookieAction($this->security->EncodeSecurityTokenSwitch($user, $request['client']), "ClientSwitch");
    }

    public function setCookieAction($encodedToken, $redirect = null) {
        $replaceRedirect = strtr($redirect, array('-' => '/'));
        //Sets cookies and session in one go. S legacy apps, php apps and js 
        //apps can all use the same function, needs localstorage adding but not needed quite yet.
        $token = $this->security->DecodeSecurityToken($encodedToken);

//        die(print_R($encodedToken));
        //Legacy
        setcookie("shostuk", $token->user->id, "0", "/");
        setcookie("shostukclient", str_pad($token->user->client, 5, 0, STR_PAD_LEFT) . ":" . $token->user->company, strtotime("+5 years"), "/");
        setcookie("token", $encodedToken, "0", "/");
        //New
        $_SESSION['token'] = $encodedToken;

        if ($replaceRedirect == null || $replaceRedirect == '') {
            header('Location: ' . SERVER_ROOT . '/portalmain.php');
        } else if ($redirect == "ClientSwitch") {
            header('Location: ' . SERVER_ROOT . '/apps2/Portal/User');
        } else {
            header('Location: ' . SERVER_ROOT . '/apps2/' . $replaceRedirect);
        }
    }

    public function resetrequestAction() {
        $client = $this->request['_client'];
        $username = $this->request['_username'];
        $password = $this->request['_password'];
        $password_second = $this->request['_password_second'];
        $address = $this->request['_address'];
        if ($password !== $password_second) {
            $this->response->status = "error";
            $this->response->message = "The passwords you entered do not match!";
        } else if (trim(strtolower($username)) === "admin") {
            $this->response->status = "error";
            $this->response->message = "You cannot change an Admin account password! Please email technical@hsdirect.co.uk for further assistance.";
        } else {
            if ($client != null) {
                $users = $this->user->getOneUserByUsernameAndClient($username, $client);
            } else {
                $users = $this->user->getOneUserByUsername($username);
            }
            if (count($users) === 0) {
                $this->response->status = "error";
                $this->response->message = "User '$username' not found.";
            } else if (count($users) > 1) {
                $this->response->status = "error";
                $this->response->message = "Multiple users found for details supplied. Please try again entering your Client ID, Username and Password";
            } else if (count($users) === 1) {
                $user = $users->fetch();
                $email_details = $this->passwordResetRequest->setPasswordResetRequest($user, $password, $address);
                @$this->sendResetEmail($email_details);
                $this->response->status = "success";
                $this->response->message = "A reset password email has been sent, please check your emails now!";
            }
        }
        return print json_encode($this->response);
    }

    public function sendResetEmail($details) {
        $client = $details['client'];
        $user_id = $details['user_id'];
        $randomstring = $details['random_string'];
        $subject = "Your Password Reset Request.";

        $to = Array(
            Array(
                'name' => "",
//                'email' => $details['email'],
                'email' => 'doug@hsdirect.co.uk',
                'type' => 'to'
            ),
        );

        $from = Array('email' => 'noreply@solutionhost.co.uk', 'name' => 'Portal.Solutionhost.co.uk');

        $vars = Array(
            'TITLE' => $subject,
            'USERNAME' => $details['username'],
            'USERID' => $details['user_id'],
            'CLIENT' => $details['client'],
            'EMAIL' => $details['email'],
            //THis will be the new one once its made...
            'RANDOMSTRING' => SERVER_ROOT . "/apps2/public/portal/Login/resetpassword/$randomstring",
                //LIVE
                //'RANDOMSTRING' => "https://portal.solutionhost.co.uk/apps/Security/Forgot-My-Password/Reset-Password?id=$randomstring",
                //Sandbox
        );
        $mandril = new \SolutionMvc\Library\Mandril;
        $mandril->SendMandrillEmail('PasswordResetEmail', $to, $client, $user_id, $vars, 'sales', $subject, null, '', $from);
        return;
    }

}
