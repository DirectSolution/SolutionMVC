<?php

namespace SolutionMvc\Core;

use Twig_Loader_Filesystem,
    Twig_Environment,
    Twig_Extension_Debug,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Response;

class Controller Extends Security {

    protected $loader;
    protected $twig;
    protected $security;
    protected $response;
    protected $setSession;

//    protected $response;
//    protected $request;

    public function __construct() {
        //$this->security = new Security();        
        parent::__construct();
        $this->response = new Response();
//        Twig_Autoloader::register();
        $this->loadTwig();

//        $this->getSecurity();
//        $this->responseObject();
        $this->requestObject();
        $this->requestType();
    }

    protected function loadTwig() {
        $loader = new Twig_Loader_Filesystem(APP . 'View');
        $this->twig = new Twig_Environment($loader, array(
            'cache' => APP . 'ViewCache',
            'debug' => true,
            'auto_reload' => true
        ));
        $this->twig->addExtension(new Twig_Extension_Debug());
        $this->twig->addGlobal("root", APP);
        if (isset($_SESSION['token'])) {
            $token = $this->DecodeSecurityToken($_SESSION['token']);
            $this->twig->addGlobal("token", $token);
        }
        $this->twig->addGlobal("session", $this->viewSession());
        $this->twig->addGlobal("public_base", HTTP_ROOT);
        $this->twig->addGlobal("asset_base", HTTP_ROOT."public/");
    }

    protected function getSecurity() {
        $this->security = new Security();
    }

    public function viewSession() {
        if (isset($_SESSION)) {
            $sess = $_SESSION;
            if (isset($_SESSION['success'])) {
                unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
                unset($_SESSION['error']);
            }
            return $sess;
        } else {
            return "No session set, use this for mobile requests";
        }
    }

    public function setSession($key, $value) {
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER ['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER ['HTTP_USER_AGENT'], 0, 4))) {
            return "Session not useable, need to think of a way to handle this nicely, will likely be using localStorage (JS) and sending in _POST/json";
        } else {
            $_SESSION[$key] = $value;
        }
    }

//    public function setResponse($response){
//        $this->response = $response;
//    }
//    public function getResponse(){
//        $this->response = new Response();
//    }
//    
//    protected function responseObject() {
//        $this->response = new Response();
//    }


    public function requestType() {
        $method = $_SERVER['REQUEST_METHOD'];
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            return "ajax";
        } else {

            if ($method == "OPTIONS") {
                return;
            } else if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == "application/json") {
                return "JSON";
            } else if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $_SERVER ['HTTP_USER_AGENT']) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($_SERVER ['HTTP_USER_AGENT'], 0, 4))) {
                return "MOBILE";
            } else if ($method == "GET") {
                return "GET";
            } else if ($method == "POST") {
                return "POST";
            } else {
                return "http";
            }
        }
    }

    protected function requestObject() {
        $method = $_SERVER['REQUEST_METHOD'];


        if ($method == "OPTIONS") {
            return;
        } else if ($method == "GET") {
            return $_GET;
        } else if ($method == "FILE") {
            return $_FILES;
        } else if ($method == "POST") {
            return $_POST;
        } else if ($_SERVER['CONTENT_TYPE'] == "application/json") {
            return json_decode(file_get_contents("php://input"));
        }
    }

    public function redirect($location, $message = null, $type = "success") {

        if($this->requestType() == 'ajax'){
            return print json_encode(array(
                "location" => $location,
                "message" => $message,
                "type" => $type,
            ));
        }elseif($this->requestType() != "JSON" && $this->requestType() != "MOBILE") {
            if ($message != null) {
                $this->setSession($type, $message);
            }
            header('Location:' . HTTP_ROOT . $location);
        } else {
            print "do something with json/mobile requests";
        }
    }

    function debug_to_console($data) {
        if (is_array($data)) {
            $output = "<script>console.log( 'Debug Objects: " . implode(',', $data) . "' );</script>";
        } else if (is_object($data)){
            $output = "<script>console.log( 'Debug Objects: " . print_r($data) . "' );</script>";
        } else {        
            $output = "<script>console.log( 'Debug Objects: " . $data . "' );</script>";
        }
        echo $output;
    }

}
