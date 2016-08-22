<?php

namespace SolutionMvc\Core;

use Symfony\Component\Yaml\Parser,
    SolutionMvc\Core\Response,
    SolutionMvc\Portal\Model\User,
    SolutionMvc\Audit\Model\Settings,
    Firebase\JWT\JWT,
    SolutionMvc\Core\Password;

/**
 * Description of Security
 *
 * @author doug
 */
class Security {

    private $yaml;
    protected $jwt;
    protected $config;
    protected $response;
    protected $user;
    protected $getToken;
    protected $password;

    public function __construct() {
        //Json Web Token, Using this instead of sessions. It's stateless, prettier and the modern way.
        //On successfull login, we build an array of Auth data, expiration date, etc. Then serialize it and send it to the front end.
        //This packet then gets sent backwards and forwards. If the unique key is ever altered then the auth will fail.
        //Check https://github.com/firebase/php-jwt or for more info on the backend. And
        //https://github.com/auth0/angular-jwt for the front. And https://jwt.io/ for more general info.
        $this->jwt = new JWT();
        $this->response = new Response();
        //YAML, because it's nicer to look at than a PHP array or XML.
        $this->yaml = new Parser();
        $this->config = $this->yaml->parse(file_get_contents(APP . "Config/Config.yml"));
        $this->user = new User();
        $this->password = new Password();
    }

    public function isAuth($auth) {
        $token = $this->getToken();
        if (isset($token)) {
            if (in_array($auth, $token->user->auth->Auth)) {
                return true;
            } else {
                return null;
            }
        } else {
            return null;
        }
    }

    public function randomKeyGen() {
        return $this->password->randomKeyGen();
    }

    public function encodePassword($password) {
        return $this->password->encodePassword($password);
    }

    public function hasherAction($password) {
        return $this->password->hasherAction($password);
    }

    public function checkPassword($passwordGiven, $passwordActual) {
        return $this->password->checkPassword($passwordGiven, $passwordActual);
    }

    public function getToken($token = null) {
        if ($token != null) {
            return $this->validateToken($this->DecodeSecurityToken($token));
        } else if (isset($_SESSION['token'])) {
            return $this->validateToken($this->DecodeSecurityToken($_SESSION['token']));
        } else {
            return null;
        }
    }

    public function validateToken($token) {
        if ($token->status == "Invalid Token") {
            return null;
        } else {
            $this->refreshToken($token);
            return $token;
        }
    }

    public function EncodeSecurityTokenSwitch($user, $client) {
        $key = $this->config['key']['secret_key'];
        $time = \time();
        $token = array(
            "iss" => "https://portal.solutionhost.co.uk",
            "user" => array(
                "id" => $user['id'],
                "username" => $user['username'],
                "client" => ($client == 0) ? (string) $client : ltrim($client),
                "level" => "admin",
                "company" => "nothing",
                "auth" => $this->getAuthObject($user['id'])
            ),
            "status" => "success",
            //Created at "now"
            "iat" => $time,
            //Not before now - 10 incase of slight variance.
            "nbf" => $time,
            //Set maximum Expiry time currently half hour, this gets refreshed everytime they make a call to the backend.
            "exp" => $time + (60 * 60)
        );
        return $this->jwt->encode($token, $key);
    }

    public function EncodeSecurityToken($user, $client) {
        $key = $this->config['key']['secret_key'];
        $time = \time();



        $token = array(
            "iss" => "https://portal.solutionhost.co.uk",
            "user" => array(
                "id" => $user['id'],
                "username" => $user['username'],
                "client" => (string)ltrim($user['client'], 0),
                "level" => "admin",
                "company" => $client['company'],
                "auth" => $this->getAuthObject($user['id'])
            ),
            "status" => "success",
            //Created at "now"
            "iat" => $time,
            //Not before now - 10 incase of slight variance.
            "nbf" => $time,
            //Set maximum Expiry time currently half hour, this gets refreshed everytime they make a call to the backend.
            "exp" => $time + (60 * 30)
        );
        return $this->jwt->encode($token, $key);
    }

    public function DecodeSecurityToken($token) {
        try {
            $jwt = new JWT();
            return $jwt->decode($token, $this->config['key']['secret_key'], array('HS256'));
        } catch (\Exception $e) {
            $this->response->setHeaders(\http_response_code(200));
            $this->response->setStatus("Invalid Token");
            //$this->response->headers = \http_response_code(401);
//            $this->response->status = "Invalid Token";

            return $this->response;
        }
    }

    public function refreshToken($token) {

        if ($token->user->client == 0) {
            $client = (string)"000";
        } else {
            $client = (string)$token->user->client;
        }

        $user = array("id" => $token->user->id, "username" => $token->user->username, "client" => (string) $client);

//        print_R($user);

        $client = array("company" => $token->user->company);
        return $_SESSION['token'] = $this->EncodeSecurityToken($user, $client);
    }

    public function getAuthObject($id) {
        $userRoleGroup = $this->user->getUserRole($id);
        return array(
            "Role" => @$userRoleGroup->ACLRoles['name'],
            "Auth" => @$this->getAuthPermissions($userRoleGroup->ACLRoles['id'])
        );
    }

    public function getAuthPermissions($id) {
        $array = array();
        foreach ($this->user->getRoleRights($id) AS $permission) {
            $array[] = (int) $permission->ACLPermissions['id'];
        }
        return $array;
    }

    public function isAuthorized($key, $authObject) {
        if (array_key_exists($key, $authObject)) {
            return true;
        } else {
            return "You are not authorized to use this function.";
        }
    }

}
