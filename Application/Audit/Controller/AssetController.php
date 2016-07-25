<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Core\Response,
    SolutionMvc\Audit\Controller\AssettypeController,
    SolutionMvc\Audit\Controller\AssetgroupController,
    SolutionMvc\Audit\Model\Asset,
    SolutionMvc\Audit\Model\Audit,
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
    protected $audit;
    protected $settings;

    public function __construct() {

        parent::__construct();
        $this->settings = new Settings();
        $this->response = new Response();
        $this->assets = new Asset();
        $this->helpers = new Helper();
        $this->assetType = new AssettypeController();
        $this->assetGroup = new AssetgroupController();
        $this->assignments = new Assignment();
        $this->token = $this->getToken();
        $this->security = new Security();
        $this->audit = new Audit();
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

    public function getMisc() {
        return array(
            "Groups" => $this->assetGroup->getGroupsWithTypesOptAction($this->token->user->client),
            "Countries" => $this->helpers->getCountries(),
            "Counties" => $this->helpers->getCounties(),
            "Audits" => $this->audit->arrayMapAudits($this->token->user->client)
        );
    }

    public function createAction() {
        if ($this->security->getToken()) {
            $this->response->setHeaders(http_response_code(200));
            $this->response->setData($this->getMisc());
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

    public function viewAction($id) {
        $this->response->headers = http_response_code(200);
        $return = array();
        $return['asset'] = $this->assets->getOneByIdArray($id, $this->token->user->client);
        $return['audits'] = $this->assignments->getAssetAssignmentsByAsset($id);
        $this->response->setData($return);
        echo $this->twig->render("Audit/Asset/view.html.twig", array(
            "data" => $this->response
        ));
//        
//        return print_r(json_encode($this->response));
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

    public function updateAction($id) {
        if ($this->security->getToken()) {

            if ($this->requestType() == 'GET') {
                $this->response->setHeaders(http_response_code(200));
                $return['misc'] = $this->getMisc();
                $return['asset'] = $this->assets->getOneByID($id);
                $this->response->setData($return);
                echo $this->twig->render("Audit/Asset/update.html.twig", array(
                    "data" => $this->response->data
                ));
            } else if ($this->requestType() == 'POST') {
                $this->assets->update($this->requestObject(), $this->token->user->client, $id);
                $this->setSession("success", "Asset successfully updated.");
                $this->response->setHeaders(header('Location: http://doug.portal.solutionhost.co.uk/apps2/public/Audit/Asset/View/'.$id));
                
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Asset/",
                "action" => "create"
            ));
        }
    }

    public function retireAction() {
        $request = $this->requestObject();
        $asset = $this->assets->getOneByID($request['asset']);
        if (in_array(9, $this->token->user->auth->Auth) && $asset['client_id'] === $this->token->user->client) {
            $this->assets->retire($request['asset']);
            return print json_encode("success");
        } else {
            return print json_encode("You are not authorised to complete this action");
        }
    }

    public function getAssetsNotInUseAction() {

        $this->response->assets = $this->assets->allAssetsNotInUseArray($this->token->user->client, $this->postdata->audit);
        return print json_encode($this->response);
    }

}
