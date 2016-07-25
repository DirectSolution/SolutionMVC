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
        $this->users = new Users();
    }

    public function newUserEmailAction($newUser = null, $currentUser = null, $request = null) {

//Test data
        
        $newUser['email'] = "doug@hsdirect.co.uk";
        $newUser['name'] = "Doug Hayward";
        $currentUser['name'] = "Dougl";
        $currentUser['companyname'] = "HsDirect";
        $currentUser['id'] = "13509";
        
        
        // End Test
        
        $key = $this->security->randomKeyGen();
        
        
        $this->users->setKey($key, $newUser['id']);

        $emailView = $this->twig->render("Email/Portal/new-user.email.twig", array(
                "user" => $newUser,
                "client" => $currentUser,
                "key" => $key
            ));
        
        $html = $emailView;
        //Send the email
        
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
        $result = $this->mandrill->messages->send($message, $async);

        print_r($result);
    }

}
