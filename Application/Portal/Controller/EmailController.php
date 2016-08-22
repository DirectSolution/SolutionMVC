<?php

namespace SolutionMvc\Portal\Controller;
require APP."../vendor/mandrill/mandrill/src/Mandrill.php";
use Symfony\Component\Yaml\Parser,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security,
    SolutionMvc\Portal\Model\User;

class EmailController Extends Controller {

    protected $mandrill;
    protected $yaml;
    protected $config;
    protected $security;
    protected $users;

    public function __construct() {
        parent::__construct();
        $this->yaml = new Parser();
        $this->config = $this->yaml->parse(file_get_contents(APP . "Config/Config.yml"));
        $this->mandrill = new \Mandrill($this->config['mandrill']['api_key']);
        $this->security = new Security();
        $this->users = new User();
    }

    public function newUserEmail($newUser, $currentUser, $request) {
        
        
//        
////        print "<PRE>";
////        print_r($currentUser);
////        print "</PRE>";
//        print "<PRE>";
//        print_r($newUser);
//        print "</PRE>";
//        die();
        
        $key = $this->security->randomKeyGen();                
        $this->users->setKey($key, $newUser['id']);
        $html = $this->twig->render("Email/Portal/new-user.email.twig", array(
                "user" => $newUser,
                "clientusername" => $currentUser->user->username,
                "clientname" => $currentUser->user->company,
                "clientnumber" => $currentUser->user->client,
                "key" => $key
            ));
                        
        $message = array(
            'html' => $html,
            'subject' => 'User Account Activation',
            'from_email' => 'support@solutionhost.co.uk',
            'from_name' => "Portal.SolutionHost.co.uk",
            'to' => array(
                array(
                    'email' => $newUser['email'],
                    'name' => $newUser['name'],
                    'type' => 'to'
                )
            ),
            'track_opens' => true,
            'track_clicks' => true
        );
        $async = true;
        return $this->mandrill->messages->send($message, $async);
    }

}
