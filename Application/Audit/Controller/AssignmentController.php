<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Audit\Model\Audit,
    SolutionMvc\Audit\Model\Asset,
    SolutionMvc\Audit\Model\Assignment,
    SolutionMvc\Audit\Model\AuditType,
    SolutionMvc\Core\Response,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Controller,
    Fisharebest\PhpPolyfill\Php54;

/**
 * Description of AssignmentController
 *
 * @author doug
 */
class AssignmentController extends Controller {

    protected $response;
    protected $helpers;
    protected $security;
    protected $assignment;
    protected $token;
    protected $audit;
    protected $asset;

    public function __construct() {
        parent::__construct();
        $this->token = $this->getToken();
        $this->security = new Security();
        $this->assignment = new Assignment();
        $this->asset = new Asset();
        $this->audit = new Audit();

        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->response = new Response();
    }

    public function indexAction() {
        $this->response->audits = $this->audit->allAuditsArray($this->token->user->client);
        return print json_encode($this->response);
    }

    public function isValidAssignmentRequest($asset, $audit, $client) {
        $a = $this->assignment->getAssignmentByAssetAuditClient($asset, $audit, $client);
        if ($a) {
            return $a;
        } else {
            return false;
        }
    }

    public function getAssignment($asset, $audit, $client) {
        return $this->assignment->getAssignmentByAssetAuditClient($asset, $audit, $client);
    }

    public function setAssignment($audit, $asset, $user, $client) {
        return $this->assignment->setAssignment($audit, $asset, $user, $client);
    }

    public function setOneAssignment($audit, $asset, $user, $client) {
        return $this->assignment->setOneAssignment($audit, $asset, $user, $client);
    }

    public function setAssignmentsAction() {
        $request = $this->requestObject();
        $this->assignment->setAssignment($request['Audits'], $request['Asset'], $this->token->user->id, $this->token->user->client);
        $this->response->setStatus(200);
        $this->response->setMessage("Succesfully saved assignments");
        return print json_encode($this->response);
    }

    public function setAssignmentsAuditToAssetsAction() {
        $request = $this->requestObject();
        $this->assignment->setAssignmentAuditToAssets($request['Audit'], $request['Assets'], $this->token->user->id, $this->token->user->client);
        $this->response->setStatus(200);
        $this->response->setMessage("Succesfully saved assignments");
        return print json_encode($this->response);
    }

    public function getAuditsNotAssignedAction($assetID) {
        $return = array();
        $list = $this->assignment->getAuditInUseList($assetID);
        $return["inUse"] = $list;
        $return["notIn"] = $this->audit->allAuditsNotInUse($this->token->user->client, $list);

        $this->response->setData($return);
        return print json_encode($this->response);
    }

    public function getAssetsNotAssignedAction($auditID) {
        $return = array();
        $list = $this->assignment->getAssetsInUseList($auditID);
//        die(print_r($list));
        $return["inUse"] = $list;
        $return["notIn"] = $this->asset->allAssetsNotInUse($this->token->user->client, $list);

        $this->response->setData($return);
        return print json_encode($this->response);
    }

    public function retireAssignmentAction() {
        $request = $this->requestObject();
        $assignment = $this->assignment->getAssignmentByID($request['id']);
        
//        die(print_R($this->token));
        
        if (in_array(1123, $this->token->user->auth->Auth) && $assignment['client_id'] === $this->token->user->client) {
            $this->assignment->setRetireOne($request['id']);
            if ($this->assignment->setRetireOne($request['id']) == true) {
                return print json_encode("success");
            } else {
                return print json_encode("Assignment does not exist");
            }
        } else {
            return print json_encode("You are not authorised to complete this action");
        }
    }

}
