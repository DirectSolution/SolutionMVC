<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Healthsafety\Model\Coshh;

class CoshhController extends Controller {

    protected $coshh;
    protected $word;

    public function __construct() {
        parent::__construct();
        $this->coshh = new Coshh();
    }

    public function indexAction() {
        if (!$this->isAuth(44)) {
            $this->redirect("Portal/Login", "You are not allowed here!", "error");
        } else {
            echo $this->twig->render('HealthSafety/Documents/Coshh/index.html.twig', array(
                "data" => $this->coshh->getCoshhAssessments(),
                "awaiting" => $this->coshh->countAwaiting()
            ));
        }
    }

    public function viewAction($id) {
        if (!$id  || !$this->isAuth(44)) {
            $this->redirect("HealthSafety/Coshh", "Invalid Request", "error");
        }
        echo $this->twig->render('HealthSafety/Documents/Coshh/view.html.twig', array(
            "data" => $this->coshh->getAssessment($id),
            "init" => $this->buildInitialArray()
        ));
    }

    public function createAction() {
        if ($this->getToken()->user->client != 0) {
            $this->redirect("Portal/Login", "Invalid Request", "error");
        }
        if ($this->isAuth(45) && $this->requestType() == "GET") {
            echo $this->getCoshhCreate();
        } elseif ($this->isAuth(45) && $this->requestType() == "POST") {
            $coshhID = $this->postCoshhCreate($this->requestObject());
            $this->redirect("HealthSafety/coshh/View/$coshhID", "Successfully Created Coshh Assessment.");
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "HealthSafety/",
                "controller" => "Coshh/",
                "action" => "create"
            ));
        }
    }

    public function getCoshhCreate() {
        return $this->twig->render('HealthSafety/Documents/Coshh/create.html.twig', array(
                    "data" => $this->buildInitialArray()
        ));
    }

    public function postCoshhCreate($request) {

        $assessmentID = $this->coshh->setAssessment($request, $this->getToken()->user->id);
        $this->coshh->setCoshhMEL($request['mels'], $assessmentID);
        $this->coshh->setCoshhWEL($request['wels'], $assessmentID);
        $this->coshh->setCoshhOES($request['oess'], $assessmentID);
        $this->coshh->setCoshhRisks($request['risk_phrases'], $assessmentID);
        $this->coshh->setCoshhPPE($request['ppes'], $assessmentID);
        $this->coshh->setCoshhRoutes($request['route'], $assessmentID);
        $this->coshh->setCoshhPersons($request['person'], $assessmentID);
        $this->coshh->setCoshhSubstances($request['substances'], $assessmentID);
        return $assessmentID;
    }

    public function updateAction($id) {
        if (!$id || $this->getToken()->user->client != 0) {
            $this->redirect("HealthSafety/Coshh", "Invalid Request", "error");
        }
        if ($this->isAuth(45) && $this->requestType() == "GET") {
            echo $this->twig->render('HealthSafety/Documents/Coshh/edit.html.twig', array(
                "data" => $this->coshh->getAssessment($id),
                "init" => $this->buildInitialArray(),
            ));
        } else if ($this->isAuth(45) && $this->requestType() == "POST") {
            $this->retireAction($id);
            $coshhID = $this->postCoshhCreate($this->requestObject());
            $this->redirect("HealthSafety/coshh/View/$coshhID", "Successfully Updated Coshh Assessment.");
        }
    }

    public function acceptAction($id) {
        if (!$id || $this->getToken()->user->client != 0) {
            $this->redirect("HealthSafety/Coshh", "Invalid Request", "error");
        }
        if ($this->isAuth(47)) {
            $accept = $this->coshh->setAccept($id, $this->getToken()->user->id);
            if ($accept === true) {
                return print json_encode("Success");
            } else {
                return print json_encode("Something went wrong");
            }
        }
    }

    public function retireAction($id) {
        if (!$id || $this->getToken()->user->client != 0) {
            $this->redirect("HealthSafety/Coshh", "Invalid Request", "error");
        }
        if ($this->isAuth(47)) {
            $this->coshh->setRetire($id, $this->getToken()->user->id);
            if ($this->requestType() == "ajax") {
                return print json_encode(array("status" => "success"));
            } else {
                return;
            }
        }
    }

    public function documentByIdAction($id) {
        if (!$id || $this->getToken()->user->client != 0) {
            $this->redirect("HealthSafety/Coshh", "Invalid Request", "error");
        }
        if ($this->isAuth(44)) {
            $doc = new \SolutionMvc\Healthsafety\Controller\PhpWordTemplatesController();
            return $doc->coshh($this->coshh->getAssessment($id), $this->buildInitialArray());
        } else {
            $this->redirect("Portal/Login", "You are not authorised to view this area!", "error");
        }
    }

    public function documentAction($url) {
        if (!$url) {
            $this->redirect("HealthSafety/Coshh", "Invalid Request", "error");
        }
        $doc = new \SolutionMvc\Healthsafety\Controller\PhpWordTemplatesController();
        $id = $this->coshh->getCoshhAssessmentByUrl($url);
        if ($id) {
            return $doc->coshh($this->coshh->getAssessment($id), $this->buildInitialArray());
        } else {
            return print "Requested Document Does Not Exist";
        }
    }

    public function buildInitialArray() {
        $return = array();
        $return['persons_affected'] = $this->coshh->getPersonsAtRisk();
        $return['routes_of_entry'] = $this->coshh->getRouteEntries();
        $return['ppes'] = $this->coshh->getPPE();
        $return['phrases'] = $this->coshh->getRiskPhrases();
        $return['eh40s'] = $this->coshh->getEh40s();
        $return['amountsUsed'] = $this->coshh->getAmountsUsed();
        $return['timesPerDay'] = $this->coshh->getTimesPerDay();
        $return['durations'] = $this->coshh->getDurations();
        $return['substances'] = $this->coshh->getSubstances();
        return $return;
    }

    public function awaitingAction() {
        if ($this->isAuth(47)) {
            echo $this->twig->render('HealthSafety/Documents/Coshh/awaiting.html.twig', array(
                "data" => $this->coshh->getCoshhAssessmentsAwaitingReview()
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You need to be logged in, to access this area.",
                "project" => "HealthSafety/",
                "controller" => "Coshh/",
                "action" => "Awaiting"
            ));
        }
    }

}
