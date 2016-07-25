<?php

namespace SolutionMvc\Ffc\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Ffc\Model\Sectors;

class AdminworksectorController extends Controller {

    protected $sectors;
    protected $token;

    public function __construct() {
        parent::__construct();
        $this->sectors = new Sectors();
        $this->token = $this->getToken();
    }

    public function indexAction() {
        if ($this->isAuth(43)) {
//        if (in_array(43, $this->token->auth->Auth)) {
            echo $this->twig->render('FFC/Admin/Worksectors/index.html.twig', array(
                "data" => $this->sectors->getDefaultSectors()
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You are not authorised to access this page. Either you have not logged in or you do not have the correct privileges to access this area.",
                "project" => "Ffc/",
                "controller" => "Adminworksector/",
                "action" => "index"
            ));
        }
    }

    public function viewAction($id) {
        if ($this->isAuth(43)) {
            echo $this->twig->render('FFC/Admin/Worksectors/view.html.twig', array(
                "sector" => $this->sectors->getSector($id),
                "questions" => $this->sectors->getSectorQuestions($id)
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You are not authorised to access this page. Either you have not logged in or you do not have the correct privileges to access this area.",
                "project" => "Ffc/",
                "controller" => "Adminworksector/",
                "action" => "index"
            ));
        }
    }

    public function createAction() {
        if ($this->isAuth(42) && $this->requestType() == "GET") {
            echo $this->twig->render('FFC/Admin/Worksectors/create.html.twig', array(
            ));
        } elseif ($this->isAuth(42) && $this->requestType() == "POST") {
            $sectorId = $this->sectors->setDefaultSector($this->requestObject(), $this->token->user->id);
            $this->sectors->setDefaultSectorQuestions($this->requestObject(), $this->token->user->id, $sectorId);
            header('Location: ' . HTTP_ROOT . 'Ffc/Adminworksector/view/' . $sectorId);
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You are not authorised to access this page. Either you have not logged in or you do not have the correct privileges to access this area.",
                "project" => "Ffc/",
                "controller" => "Adminworksector/",
                "action" => "create"
            ));
        }
    }

    public function updateAction($id) {
        if ($this->isAuth(42) && $this->requestType() == "GET") {
            echo $this->twig->render('FFC/Admin/Worksectors/edit.html.twig', array(
                "sector" => $this->sectors->getSector($id),
                "questions" => $this->sectors->getSectorQuestions($id)
            ));
        } elseif ($this->isAuth(42) && $this->requestType() == "POST") {
            //First retire old version
            $this->sectors->retireSector($id);
            $this->sectors->retireSectorQuestions($id);
            //Then insert new one
            $sectorId = $this->sectors->setDefaultSector($this->requestObject(), $this->token->user->id);
            $this->sectors->setDefaultSectorQuestions($this->requestObject(), $this->token->user->id, $sectorId);
            header('Location: ' . HTTP_ROOT . 'Ffc/Adminworksector/view/' . $sectorId);
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "errors" => "You are not authorised to access this page. Either you have not logged in or you do not have the correct privileges to access this area.",
                "project" => "Ffc/",
                "controller" => "Adminworksector/",
                "action" => "update"
            ));
        }
    }

    public function retireAction() {
        if ($this->isAuth(42)) {
            $request = $this->requestObject();
            $id = $request['id'];
            $this->sectors->retireSector($id);
            $this->sectors->retireSectorQuestions($id);
            return print \json_encode(array(
                "status" => "success",
                "message" => "Work sector successfully retired."
            ));
        } else {
            return print \json_encode(array(
                "status" => "error",
                "message" => "You are not authorised to comeplete this action!"
            ));
        }
    }

}
