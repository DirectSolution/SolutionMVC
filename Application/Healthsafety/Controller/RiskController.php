<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Healthsafety\Model\Risk;

class RiskController extends Controller {

    protected $risk;
    protected $word;

    public function __construct() {
        parent::__construct();
        $this->risk = new Risk();
        $this->token = $this->getToken();
    }

    public function indexAction() {
        if ($this->token->user->client == 0 && $this->isAuth(44)) {
            echo $this->twig->render('HealthSafety/Documents/RiskAssessment/index.html.twig', array(
                "data" => $this->risk->getRiskAssessments(),
                "awaiting" => $this->risk->countAwaiting()
            ));
        } else {
            $this->redirect("Portal/Login", "Invalid Request", "error");
        }
    }

    public function viewAction($id) {
        if (!$id || $this->getToken()->user->client != 0 || !$this->isAuth(44)) {
            $this->redirect("HealthSafety/Risk", "Invalid Request", "error");
        }
        echo $this->twig->render('HealthSafety/Documents/RiskAssessment/view.html.twig', array(
            "riskAssessment" => $this->risk->getRiskAssessment($id),
            "hazards" => $this->risk->getRiskHazards($id),
        ));
    }

    public function createAction() {
        if ($this->isAuth(45) && $this->requestType() == "GET") {
            echo $this->twig->render('HealthSafety/Documents/RiskAssessment/create.html.twig', array(
            ));
        } elseif ($this->isAuth(45) && $this->requestType() == "POST") {
            $riskId = $this->risk->setRiskAssessment($this->requestObject(), $this->token->user->id);
            $this->risk->setRiskHazards($this->requestObject(), $riskId, $this->token->user->id);
            header('Location: ' . HTTP_ROOT . 'HealthSafety/Risk/view/' . $riskId);
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "HealthSafety/",
                "controller" => "Risk/",
                "action" => "create"
            ));
        }
    }

    public function updateAction($id) {
        if ($this->isAuth(45) && $this->requestType() == 'GET') {
            echo $this->twig->render('HealthSafety/Documents/RiskAssessment/edit.html.twig', array(
                "riskAssessment" => $this->risk->getRiskAssessment($id),
                "hazards" => $this->risk->getRiskHazards($id)
            ));
        } elseif ($this->isAuth(45) && $this->requestType() == 'POST') {
            //Save it      
            $riskId = $this->risk->setRiskAssessment($this->requestObject(), $this->token->user->id, $id);
            $this->risk->setRiskHazards($this->requestObject(), $riskId, $this->token->user->id);
            $this->redirect('HealthSafety/Risk/view/' . $riskId, $this->requestType(), "Successfully Updated Method Statement, your revision has been added to the awaiting review list.");
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "HealthSafety/",
                "controller" => "Risk/",
                "action" => "update/$id"
            ));
        }
    }

    public function retireAction($id) {
        if ($this->isAuth(46)) {
            $this->risk->setRetire($id, $this->token->user->id);
            if ($this->requestType() == "ajax") {
                return print json_encode(array("status" => "success"));
            } else {
                return;
            }
        }
    }

    public function documentByIdAction($id) {
        if (!$id) {
            $this->redirect("HealthSafety/Risk", "Invalid Request", "error");
        }
        if ($this->isAuth(44)) {
            $doc = new \SolutionMvc\Healthsafety\Controller\PhpWordTemplatesController();
            return $doc->risk($this->risk->getRiskAssessment($id), $this->risk->getRiskHazards($id));
        } else {
            $this->redirect("Portal/Login", "You are not authorised to view this area!", "error");
        }
    }

    public function documentAction($url) {
        if (!$url) {
            $this->redirect("HealthSafety/Method", "Invalid Request", "error");
        }
        $doc = new \SolutionMvc\Healthsafety\Controller\PhpWordTemplatesController();
        $id = $this->risk->getRiskAssessmentByUrl($url);
        if ($id) {
            return $doc->risk($this->risk->getRiskAssessment($id), $this->risk->getRiskHazards($id)
            );
        } else {
            return print "Requested Document Does Not Exist";
        }
    }

    public function getControlsAction() {
        return print json_encode($this->risk->getControls());
    }

    public function awaitingAction() {
        if ($this->isAuth(47)) {
            echo $this->twig->render('HealthSafety/Documents/RiskAssessment/awaiting.html.twig', array(
                "data" => $this->risk->getRiskAssessmentsAwaitingReview()
            ));
        } else {
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
            $accept = $this->risk->setAccept($id, $this->token->user->id);
            if ($accept === true) {
                return print json_encode("Success");
            } else {
                return print json_encode("Something went wrong");
            }
        }
    }

}
