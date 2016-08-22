<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Healthsafety\Model\Method;

class MethodController extends Controller {

    protected $method;
    protected $token;

    public function __construct() {
        parent::__construct();
        $this->token = $this->getToken();
        $this->method = new Method();
    }

    public function indexAction() {
        if ($this->isAuth(44)) {
            echo $this->twig->render('HealthSafety/Documents/MethodStatement/index.html.twig', array(
                "data" => $this->method->getMethodStatements(),
                "awaiting" => $this->method->countAwaiting()
            ));
        }else{
         echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Method",
                "action" => ""
            ));   
        }
    }

    public function viewAction($id) {
        if(!$id){ $this->redirect("HealthSafety/Method", "Invalid Request", "error"); }
        if ($this->isAuth(44)) {
            $methodStatementDatas = $this->method->getMethodStatements_MethodSheets($id);
            $returnArray = array();
            foreach ($methodStatementDatas as $data) {
                $returnArray[$data['MethodSheets_id']]['Sheet'] = (array)$data->MethodSheets;
                $returnArray[$data['MethodSheets_id']]['Sections'][] = array(
                    "name" => $data->MethodSections['name'],
                    "description" => $data->MethodSections['description']
                );
            }
            echo $this->twig->render('HealthSafety/Documents/MethodStatement/view.html.twig', array(
                "methodStatement" => $this->method->getMethodStatement($id),
                "sheets" => $returnArray,
                "methods" => $this->method->getMethods($id)
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "view/$id"
            ));
        }
    }

    public function createAction() {
        if ($this->requestType() == "GET" && $this->isAuth(45)) {
            echo $this->twig->render("HealthSafety/Documents/MethodStatement/create.html.twig", array(
                "sheets" => $this->method->getAllSheets(),
            ));
        } elseif ($this->requestType() == "POST" && $this->getToken()) {

            $msID = $this->method->setMethodStatement($this->requestObject(), $this->token->user->id);
            $this->method->setMethodSheets($this->requestObject(), $this->token->user->id, $msID);
            $this->method->setMethods($this->requestObject(), $msID, $this->token->user->id);
//            $this->method->setMethods($this->requestObject(), 1, $this->token->user->id);
            $this->redirect("HealthSafety/Method/", "You method statement has been successfully saved and has now joined the awaiting review queue.");
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "create"
            ));
        }
    }

    public function updateAction($id) {
        if(!$id){ $this->redirect("HealthSafety/Method", "Invalid Request", "error"); }
        if ($this->requestType() == 'GET' && $this->isAuth(45)) {
            $methodStatementDatas = $this->method->getMethodStatements_MethodSheets($id);
            $returnArray = array();
            foreach ($methodStatementDatas as $data) {
                $returnArray[$data['MethodSheets_id']]['Sheet'] = (array)$data->MethodSheets;
                $returnArray[$data['MethodSheets_id']]['Sections'][] = array(
                    "name" => $data->MethodSections['name'],
                    "description" => $data->MethodSections['description']
                );
            }
            echo $this->twig->render('HealthSafety/Documents/MethodStatement/edit.html.twig', array(
                "methodStatement" => $this->method->getMethodStatement($id),
                "initsheets" => $this->method->getAllSheets(),
                "sheets" => $returnArray,
                "methods" => $this->method->getMethods($id)
            ));
        } elseif ($this->requestType() == 'POST' && $this->getToken()) {
            $msID = $this->method->setMethodStatement($this->requestObject(), $this->token->user->id, $id);
            $this->method->setMethodSheets($this->requestObject(), $this->token->user->id, $msID);
            $this->method->setMethods($this->requestObject(), $msID, $this->token->user->id);
            $this->redirect("HealthSafety/Method/View/$msID", "Successfully Updated Method Statement, your revision has been added to the awaiting review list.");
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "update/$id"
            ));
        }
    }

    public function retireAction($id) {
        if(!$id){ $this->redirect("HealthSafety/Method", "Invalid Request", "error"); }
        if ($this->isAuth(46)) {
            $this->method->setRetire($id, $this->token->user->id);
            if ($this->requestType() == "ajax") {
                return print json_encode(array("status" => "success"));
            } else {
                return;
            }
        }
    }

    public function documentByIdAction($id) {
        if(!$id){ $this->redirect("HealthSafety/Method", "Invalid Request", "error"); }
        if ($this->isAuth(44)) {
            $doc = new \SolutionMvc\Healthsafety\Controller\PhpWordTemplatesController();
            return $doc->method(
                            $this->method->getMethodStatement($id), $this->method->getMethods($id), $this->method->getMethodStatements_MethodSheets($id)
            );
        } else {
            $this->redirect("Portal/Login", "You are not authorised to view this area!", "error");
        }
    }

    public function documentAction($url) {
        if(!$url){ $this->redirect("HealthSafety/Method", "Invalid Request", "error"); }
        $doc = new \SolutionMvc\Healthsafety\Controller\PhpWordTemplatesController();
        $id = $this->method->getMethodStatementByUrl($url);
        if ($id) {
            return $doc->method($this->method->getMethodStatement($id), $this->method->getMethods($id), $this->method->getMethodStatements_MethodSheets($id)
            );
        } else {
            return print "Requested Document Does Not Exist";
        }
    }

    public function awaitingAction() {
        if ($this->isAuth(47)) {
            echo $this->twig->render('HealthSafety/Documents/MethodStatement/awaiting.html.twig', array(
                "data" => $this->method->getMethodStatementsAwaitingReview()
            ));
        }else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "Awaiting"
            ));
        }
    }

    public function acceptAction($id) {
        if ($this->isAuth(47)) {
            $accept = $this->method->setAccept($id, $this->token->user->id);
            if ($accept === true) {
                return print json_encode("Success");
            } else {
                return print json_encode("Something went wrong");
            }
        }
    }

}
