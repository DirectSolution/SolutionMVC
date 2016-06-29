<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Controller\AssettypeController,
    SolutionMvc\Audit\Controller\AssetgroupController,
    SolutionMvc\Audit\Model\Asset,
    SolutionMvc\Audit\Model\Settings,
    SolutionMvc\Library\Helper,
    SolutionMvc\Core\Controller,
    SolutionMvc\Core\Security,
    SolutionMvc\Audit\Model\Assignment;

/**
 * Description of AssetController
 *
 * @author dhayward
 */
class AssetController Extends Controller {

    public $assets;
    public $assetType;
    public $assetGroup;
    public $assignments;
    public $helpers;
    protected $settings;

//    public $response;
//    protected $settings;
//    protected $token;

    public function __construct() {

        parent::__construct();
        $this->settings = new Settings();
        $this->response = new Response();
        $this->assets = new Asset();
        $this->helpers = new Helper();
        $this->assetType = new AssettypeController();
        $this->assetGroup = new AssetgroupController();
        
//        $this->assignments = new Assignment();


        $this->token = $this->getToken();
        $this->security = new Security();
//        $this->settings = new Settings();
        //This may need moving, could cause if we ever want to use gets
//        $this->postdata = json_decode(file_get_contents("php://input"));
//        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
//        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function indexAction($message = null) {
        if ($this->security->getToken()) {
            
            
            $this->response->setHeaders(http_response_code(200));
            $return['assets'] = $this->assets->getAllArray($this->token->user->client);
            $return['default'] = $this->settings->getDefault($this->token->user->client);
            $this->response->setData($return);

            echo $this->twig->render("Audit/Asset/index.html.twig", array(
                "data" => $this->response->data,
                "success" => $message
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Asset/",
                "action" => "index"
            ));
        }
    }

    public function getMisc(){
     return array(
                        "Groups" => $this->assetGroup->getGroupsWithTypesOptAction($this->token->user->client),
//                        "Groups" => $this->assetGroup->indexAction($this->token->user->client),
//                        "Types" => $this->assetType->indexAction($this->token->user->client),
                        "Countries" => $this->helpers->getCountries(),
                        "Counties" => $this->helpers->getCounties(),
                    );   
    }
    
    public function createAction() {
        if ($this->security->getToken()) {
            $this->response->setHeaders(http_response_code(200));
            $this->response->setData( $this->getMisc());
            echo $this->twig->render("Audit/Asset/create.html.twig", array(
                "data" => $this->response->data
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Asset/",
                "action" => "create"
            ));
        }
    }

    public function getAction($id) {
        $this->response->headers = http_response_code(200);
        $this->response->asset = $this->assets->getOneByIdArray($id, $this->token->user->client);
        $this->response->audits = $this->assignments->getAssetAssignmentsByAsset($id);
        return print_r(json_encode($this->response));
    }

    public function getAllByTypeAction($id) {
        $this->response->headers = http_response_code(200);
        $this->response->data = $this->assets->getAllByTypeArray($id, $this->token->user->client);
        return print_r(json_encode($this->response));
    }

    public function getAllByGroupAction($id) {
        $this->response->headers = http_response_code(200);
        $this->response->data = $this->assets->getAllByGroupArray($id, $this->token->user->client);
        return print_r(json_encode($this->response));
    }

    public function newAction() {
        if ($this->security->getToken()) {
            if ($this->requestType() == "POST") {
                $this->setAsset($this->requestObject(), $this->token->user->client);
                $this->setSession("success", "Asset Successfully Created.");
                $this->response->setHeaders(header('Location: http://doug.portal.solutionhost.co.uk/apps2/public/Audit/Asset/'));                
            } else {            
                $this->setSession("error", "You can't access this page.");
                $this->response->setHeaders(header('Location: http://doug.portal.solutionhost.co.uk/apps2/public/Audit/Asset/'));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Asset/",
                "action" => "create",
                "status" => "error",
                "message" => "You need to be logged in before inserting assets!"
            ));
        }
    }

    public function setAsset($asset, $client) {
        return $this->assets->setAsset($asset, $client);
    }

    public function setAssignmentAction() {
        try {
            $assignment = new Assignment();
            $this->response->status = $assignment->setAuditAssignments($this->postdata);
            $this->response->headers = http_response_code(201);

            return print_R(json_encode($this->response));
        } catch (Exception $ex) {
            return print_r(json_encode($ex));
        }
    }

    public function updateAction() {
        
    }

    public function retireAction() {
        $this->response->status = $this->assets->retire($this->postdata->id);
        return print json_encode($this->response);
    }

    public function getAssetsNotInUseAction() {

        $this->response->assets = $this->assets->allAssetsNotInUseArray($this->token->user->client, $this->postdata->audit);
        return print json_encode($this->response);
    }

}
