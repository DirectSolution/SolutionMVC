<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Audit\Model\Audit,
    SolutionMvc\Audit\Controller\AssetController,
    SolutionMvc\Audit\Controller\AssignmentController,
    SolutionMvc\Audit\Controller\AnswersController,
    SolutionMvc\Audit\Model\Asset,
    SolutionMvc\Audit\Model\ReviewFrequencies,
    SolutionMvc\Audit\Model\Settings,
    SolutionMvc\Audit\Model\AuditType,
    SolutionMvc\Audit\Model\QuestionType,
    SolutionMvc\Audit\Model\QuestionGroup,
    SolutionMvc\Audit\Model\QuestionTypeOption,
    SolutionMvc\Audit\Model\AuditGradings,
    SolutionMvc\Audit\Model\Assignment,
    SolutionMvc\Core\Response,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Controller,
    Fisharebest\PhpPolyfill\Php54;

/**
 * Description of AuditController
 *
 * @author doug
 */
class AuditController extends Controller {

    protected $response;
    protected $helpers;
    protected $audit;
    protected $auditTypes;
    protected $questionTypes;
    protected $questionGroups;
    protected $questionTypeOptions;
    protected $security;
    protected $assignment;
    protected $auditGradings;
    protected $settings;
    protected $answers;
    protected $asset;
    protected $assetCnt;
    protected $reviewFrquencies;

    public function __construct() {
        parent::__construct();

        $this->token = $this->getToken();
        $this->security = new Security();
        $this->answers = new AnswersController();
        $this->settings = new Settings();
        $this->assignment = new AssignmentController();
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->response = new Response();
        $this->audit = new Audit();
        $this->auditTypes = new AuditType();
        $this->questionTypes = new QuestionType();
        $this->questionTypeOptions = new QuestionTypeOption();
        $this->questionGroups = new QuestionGroup();
        $this->auditGradings = new AuditGradings();
        $this->asset = new Asset();
        $this->assetCnt = new AssetController();
        $this->reviewFrequencies = new ReviewFrequencies();
//        $this->postdata = json_decode(file_get_contents("php://input"));
//        $this->token = $this->security->DecodeSecurityToken($this->postdata->token);
//        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
//        $this->auth = $this->token->user->auth->Authorization;
    }

    public function getMessage($message) {
        switch ($message) {
            case "1":
                $m = "Audit succesffully created!";
                break;
            case "2":
                $m = "Audit succesffully deleted!";
                break;
            default:
                $m = NULL;
                break;
        }
        return $m;
    }

    public function indexAction($message = null) {
        if ($this->security->getToken()) {
            if (isset($message)) {
                $message = $this->getMessage($message);
            }
            $this->response->setHeaders(http_response_code(200));
            $return['default'] = (int) $this->settings->getDefault($this->token->user->client);
            $return['audits'] = $this->audit->allAuditsArray($this->token->user->client);
            $this->response->setData($return);
            echo $this->twig->render("Audit/Audit/index.html.twig", array(
                "data" => $this->response->data,
                "success" => $message
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Audit/",
                "action" => "index"
            ));
        }
    }

    public function getAuditsNotInUseAction() {
        $this->response->audits = $this->audit->allAuditsNotInUseArray($this->token->user->client, $this->postdata->asset);
        return print json_encode($this->response);
    }

    //This funciton is used t load the initial data required when creating 
    //a new audit (Question Type and Audit Type lists) add to this if you want
    // to provide more data to the "New Audit" pages. 
    public function initialNewAuditDataAction($client) {
//        $client = $this->token->user->client;

        $this->response->questionTypes = $this->questionTypes->allQuestionTypesArray($client);
        $this->response->questionTypeOptions = $this->questionTypeOptions->allQuestionTypeOptionsArray($client);
        $this->response->auditGradings = $this->auditGradings->allAuditGradingsReturnArray($client);
        $this->response->auditTypes = $this->auditTypes->allAuditTypesArray($client);
        $this->response->frequencies = $this->reviewFrequencies->getAll($client);
        return $this->response;
//        return\ print json_encode($this->response);
    }

    public function getTakeAuditAction($id) {
        $asset = $this->postdata->asset;
        $auditID = $this->postdata->audit;
        $client = $this->token->user->client;
        $a = $this->assignment->isValidAssignmentRequest($asset, $auditID, $client);
        if ($a) {
            $this->response = $this->getTakeAudit($auditID, $client);
        } else {
            return $this->response->headers = http_response_code(400);
        }
        return print json_encode($this->response);
    }

    public function getTakeAuditNoAssetAction($id) {
//        $auditID = $this->postdata->audit;
        $this->response = $this->getTakeAudit($id, $this->token->user->client);
        return print json_encode($this->response);
    }

    public function doPostAudit($id, $asset = null){
//        print "<PRE>";
//        print_R($this->requestObject());
//        print "</PRE>";
        if ($asset != null) { // Asset set so save the audit for it,
            
                $result = $this->answers->setAnswersAction($id, $asset);
            } else { // No asset so We'll have to create the audit first, create an assignment then finally save the aduti :/
                
                $result = $this->answers->setAnswersAndAssetAction($id);
            }
            if ($result !== false) {
                $this->setSession("success", "Successfully saved audit.");
                $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Audit/Reports/Report/' . $result));
            } else {
                $this->setSession("error", "Something went wrong while trying to save your audit.");
                $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Audit/Asset/'));
            }
    }
    public function doGetAudit($id, $asset){
            $audit = $this->getTakeAudit($id, $this->token->user->client);
            if ($audit != 404) {                
                if ($asset != null) {//Get asset data
                    if ($this->asset->getOneByIdArray($asset) != false) {
                        $assetArray = $this->asset->getOneByIdArray($asset);
                    }else{
                        $this->setSession("error", "The asset you requested does not exist.");
                        return $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Audit/Asset/'));
                    }
                }
                $this->response->setData($audit);
            } else {
                $this->response->setHeaders(http_response_code(404));
                $this->setSession("error", "Requested audit does not exist!");
                $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Audit/Asset/'));
            }
            if ($asset == null) { // No asset, so load audit with create asset form at top.                         
                echo $this->twig->render("Audit/Audit/take.html.twig", array("asset" => "new", "MISC" => $this->assetCnt->getMisc(), "data" => $this->response->data));
            } else {// Asset set so load the standard audit page.                               
                echo $this->twig->render("Audit/Audit/take.html.twig", array("asset" => $assetArray, "data" => $this->response->data));
            }
    }
    
    
    public function takeAuditAction($id, $asset = null) {
        if ($this->requestType() == 'POST' && $this->getToken()) { //Is POST so Save the audit               
            $this->doPostAudit($id, $asset);                       
        } else if ($this->requestType() == 'GET' && $this->getToken()) { // Is GET so Load the audit for taking
            $this->doGetAudit($id, $asset);
        } else { //Or direct to login            
            echo $this->twig->render("Portal/Login/login.html.twig", array("project" => "Audit/", "controller" => "Audit/", "action" => "TakeAudit/$id"));
        }
    }

    public function getTakeAudit($auditID, $client) {
        $return = array();
        $audit = new Audit;
        $auditData = $audit->getAuditDatasById($auditID, $client);
        if ($auditData == null) {
            $return = 404;
        } else {
            $questionTypes = new QuestionType();
//        $this->response->questionTypes = $questionTypes->allQuestionTypesArray($client);
            $return['questionTypes'] = $questionTypes->allQuestionTypesArray($client);
            $questionGroups = new QuestionGroup();
            $return['auditID'] = $auditID;
            $return['audit'] = array(
//            $this->response->data = array(
                "name" => $auditData['name'],
                "description" => $auditData['description'],
                "auditTypes" => array(
                    "id" => $auditData['AuditTypes_id'],
                    "name" => $auditData->AuditTypes['name']
                ),
                "groups" => $questionGroups->getAllQuestionGroupsByAuditIdArray($auditData['id']),
            );
            $totalSum = array();
//            foreach ($this->response->data['groups'] as $gkey => $group) {
            foreach ($return['audit']['groups'] as $gkey => $group) {
                
                foreach ($group['questions'] as $qkey => $question) {
                    $question['answerType']['name'] = $this->helpers->searchForId($question['answerType']['id'], $this->questionTypes->allQuestionTypesArray($client));

                    $optionArray = array();
                    foreach ($this->questionTypeOptions->allQuestionTypeOptionsArray($client) as $option) {
                        if ($option['type_id'] == $question['answerType']['id']) {
                            $optionArray[] = $option;
                        }
                    }
                    $question['answerType']['options'] = $optionArray;
                    $question['answerType']['optionCount'] = count($optionArray);
                    $max = 0;
                    foreach ($optionArray AS $opt) {
                        if ($max < $opt['value']) {
                            $max = $opt['value'];
                        }
                    }
                    $question['answerType']['optionMax'] = $max;
                    if (count($optionArray) == 1) {
                        $question['answerType']['optionSteps'] = 1;
                    } else {
                        $question['answerType']['optionSteps'] = ceil(round($question['answerType']['optionMax'] / (count($optionArray) - 1), 1));
                    }
//                    $this->response->data['groups'][$gkey]['questions'][$qkey] = $question;
                    $return['audit']['groups'][$gkey]['questions'][$qkey] = $question;
                }
                $totalSum[] = $group['group_total_possible'];
                
            }
            $return['MaximumTotalScore'] = array_sum($totalSum);
        }
        return $return;
    }

    public function create() {

        $this->audit->insertNewAudit($this->requestObject(), $this->token->user);
        $this->response->result = "Audit Saved";
        $this->response->status = "success";
        return;
    }

    public function createAction() {
        if ($this->security->getToken()) {
            if ($this->requestType() == "POST") {
                $this->create();
                return header('Location: ' . HTTP_ROOT . 'Audit/audit/index/1');
            } else {
                $this->response->setHeaders(http_response_code(200));
                $this->response->setData($this->initialNewAuditDataAction($this->token->user->client));
                echo $this->twig->render("Audit/Audit/create.html.twig", array(
                    "data" => $this->response->data,
                ));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Audit/",
                "action" => "create"
            ));
        }
    }

    public function update() {
//        
//        print "<PRE>";
//        print_r($this->requestObject());
//        print "</PRE>";
//        die();
        
        $this->audit->updateAudit($this->requestObject(), $this->token->user);
        $this->response->result = "Audit Updated";
        $this->response->status = "success";
        return;
    }

    public function updateAction($id = null) {
        if ($this->security->getToken()) {
            if ($this->requestType() == "POST") {
                $this->update();
                return header('Location: ' . HTTP_ROOT . 'Audit/audit/index/1');
            } else {
                $this->getAudit($id);
                echo $this->twig->render("Audit/Audit/update.html.twig", array(
                    "data" => $this->response->getData(),
                    "errors" => $this->response->getMessage()
                ));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Audit/",
                "action" => "update/$id"
            ));
        }
    }

    public function copyAction($id = null) {
        if ($this->security->getToken()) {
            if ($this->requestType() == "POST") {
                $this->insert();
                return header('Location: ' . HTTP_ROOT . 'Audit/audit/index/1');
            } else {
                $this->getAudit($id);
                echo $this->twig->render("Audit/Audit/copy.html.twig", array(
                    "data" => $this->response->getData(),
                    "errors" => $this->response->getMessage()
                ));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Audit/",
                "action" => "copy/$id"
            ));
        }
    }

    public function viewAction($id = null) {
        if ($this->security->getToken()) {
            $this->getAudit($id);
            echo $this->twig->render("Audit/Audit/view.html.twig", array(
                "data" => $this->response->getData(),
                "errors" => $this->response->getMessage()
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Audit/",
                "action" => "view/$id"
            ));
        }
    }

    // Used to load all audit data (Questions, Question Types, Evidence, Expiry)
    // for Update and TakeAudit pages.
    public function getAudit($id) {
        $return['auditGradings'] = $this->auditGradings->allAuditGradingsReturnArray($this->token->user->client);
        $return['auditTypes'] = $this->auditTypes->allAuditTypesArray($this->token->user->client);
        $return['questionTypes'] = $this->questionTypes->allQuestionTypesArray($this->token->user->client);
        $return['frequencies'] = $this->reviewFrequencies->getAll($this->token->user->client);
        $auditData = $this->audit->getAuditById($id, $this->token->user->client);
//        if ($auditData === null || $auditData['retired'] == 1) {
//            $this->response->setHeaders(http_response_code(404));
//            $this->response->setMessage("Audit not found!");
//        } else {
            $this->response->setHeaders(http_response_code(200));
            $groups = $this->questionGroups->getAllQuestionGroupsByAuditIdArray($id);
                      
            $return['audit'] = array(
                "id" => $auditData['id'],
                "name" => $auditData['name'],
                "description" => $auditData['description'],
                "frequency" => $auditData['ReviewFrequencies_id'],
                "auditTypes" => array(
                    "id" => $auditData['AuditTypes_id'],
                    "name" => $auditData->AuditTypes['name'],
                ),
                "auditGradings" => array(
                    "Aid" => $auditData['AuditGradings_id'],
                    "name" => $auditData->AuditGradings['name'],
                ),
                "groups" => $groups,
                "MaximumTotal"=> $this->getMaximumTotal($groups)
            );
//        }
        $this->response->setData($return);
        return;
    }

    public function getMaximumTotal($groups){
        $total = 0;
        foreach($groups as $group){
            $total += $group['group_total_possible'];
        }
        return $total;
    }
    
    
    public function retireAction() {
        $request = $this->requestObject();
        if ($this->requestType() == "ajax" && $this->security->getToken() && $this->audit->retireAudit($request['id'], $this->token->user->client) == "success") {
            $this->response->setMessage("Audit successfully retired.");
            $this->response->setStatus("success");
            $this->response->setHeaders(http_response_code(200));
        } else {
            $this->response->setMessage("You are not authorized to retire this audit.");
            $this->response->setStatus("errors");
            $this->response->setHeaders(http_response_code(401));
        }
        return print json_encode($this->response);
    }

    public function setAssignmentAction() {
        try {
            $assignment = new Assignment();
            $this->response->status = $assignment->setAssetAssignments($this->postdata);
            $this->response->headers = http_response_code(201);
            return print_R(json_encode($this->response));
        } catch (Exception $ex) {
            return print_r(json_encode($ex));
        }
    }

}
