<?php

namespace SolutionMvc\Core;

use Symfony\Component\Yaml\Parser;
/**
 * Description of Password
 *
 * @author dhayward
 */
class Password {

    private $yaml;
    protected $config;

    public function __construct() {
        $this->yaml = new Parser();
        $this->config = $this->yaml->parse(file_get_contents(APP . "Config/Config.yml"));
    }

    public function randomKeyGen() {
        return md5(uniqid($_SERVER['SERVER_ADDR']));
    }

    public function encodePassword($password) {
        return md5($this->config['key']['password'] . $password);
    }

    public function hasherAction($password) {
        return md5($password . $this->config['key']['password']);
    }

    public function checkPassword($passwordGiven, $passwordActual) {
        if ($this->encodePassword($passwordGiven) === $passwordActual) {
            return true;
        } else {
            return false;
        }
    }

}
