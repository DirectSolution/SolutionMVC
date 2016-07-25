<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Audit\Model\Audit,
    SolutionMvc\Audit\Model\User,
    SolutionMvc\Audit\Model\GradingValues,
    SolutionMvc\Audit\Model\Answer,
    SolutionMvc\Audit\Model\AnswersSet,
    SolutionMvc\Audit\Model\QuestionTypeOption,
    SolutionMvc\Audit\Controller\AssignmentController,
    SolutionMvc\Audit\Controller\PdfController,
    SolutionMvc\Core\Response,
    SolutionMvc\Core\Security,
    SolutionMvc\Core\Controller,
    SolutionMvc\Audit\Model\ReportSettings,
    Fisharebest\PhpPolyfill\Php54;

//Use this controller for saving answers to audits. Audit controller was getting to big so have split it into its own.

class ReportsController extends Controller {

    protected $assignment;
    protected $answer;
    protected $answerSet;
    protected $questionTypeOptions;
    protected $audit;
    protected $helpers;
    protected $security;
    protected $response;
    protected $user;
//    protected $pdf;
    protected $gradingValues;

    public function __construct() {
        parent::__construct();
//        $this->pdf = new PdfController();
//        $this->user = new User();
        $this->gradingValues = new GradingValues();
        $this->assignment = new AssignmentController();
        $this->questionTypeOptions = new QuestionTypeOption();
        $this->answer = new Answer();
        $this->answerSet = new AnswersSet();
        $this->audit = new Audit();
        $this->helpers = new \SolutionMvc\Library\Helper();
        $this->security = new Security();
        $this->response = new Response();
//        $this->response = new Response();
//        $this->postdata = json_decode(file_get_contents("php://input"));
        $this->token = $this->getToken();
//        $this->response->token = $this->security->EncodeSecurityToken((array) $this->token->user);
    }

    public function reportAction($answerSet) {
//        $return = array();
//        $return['answerSet'] = $this->answerSet->getOneAnswersSet($answerSet);
//        $return['answers'] = $this->answer->getAnswers($answerSet);
//        foreach($this->answer->getAnswers($answerSet) AS $answer){
//            $return['answers'][] = $answer;
//        }
        if ($this->security->getToken()) {
            $this->response->setData($this->getReportAction($answerSet));

//        $this->response->setData($return);
            echo $this->twig->render("Audit/Report/view.html.twig", array(
                "data" => $this->response
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Reports/",
                "action" => "Report/$answerSet"
            ));
        }
    }

    public function getReportsByAssetAndAuditAction($id, $asset) {

        $series = array();
        $r = array();

        //$r[] = $passes;
        $audit = $this->audit->getAuditDatasById();
        $series[] = $audit['name'];

        $assignment = $this->assignment->getAssignment($asset, $id, $this->token->user->client);
        $ii = 0;
        foreach ($this->answerSet->getAnswersSet($assignment['id']) AS $answerSet) {
            $ii++;
            $high = 0;
            $answerTotal = 0;
            $answers = $this->answer->getAnswers($answerSet['id']);
            foreach ($answers AS $answer) {
                $high += $this->questionTypeOptions->getHighestById($answer->Questions['QuestionTypes_id']);
                $answerTotal += $answer['answer_value'];
                $q = $answer->Questions->AuditDatas;
            }

            $d = date("d/m/Y", strtotime($answerSet['created_at']));
            $s = number_format(($answerTotal / ($high)) * 100, 2);

            $report[] = $s;
            $labels[] = $d;
            $grades = $this->gradingValues->allGradingValuesReturnArray($q['AuditGradings_id']);
            $dataArray[] = array(
                "AnswerSetID" => (int) $answerSet['id'],
                "Score" => $s,
                "Date" => $d,
                // "PassRate" => $q['AuditGradings_id'],
                "PassFail" => $this->helpers->getClosest($s, $grades),
            );
        }

        $r[] = $report;

        foreach ($grades AS $grade) {
            $series[] = $grade['name'];
            $r[] = array_fill(0, $ii, $grade['value']);
        }


        $this->response->reportData = $dataArray;
        $this->response->auditName = $audit['name'];
        $this->response->series = $series;
        $this->response->scores = $r;
        $this->response->labels = $labels;
        return print json_encode($this->response);
    }

    public function getReportAction($id) {

        $qaReturn = array();
        $high = 0;
        $score = 0;
        $settings = new ReportSettings();
        $set = $settings->getReportSettingsByClient($this->token->user->client);

        foreach ($this->answer->getAnswers($id) AS $answer) {

            if ($answer['evidence'] != null && $answer['evidence'] != '' && $set['hide_images'] != 1) {

//                $path = "/var/www/html/Filestore/images/" . $answer->Assignments['client_id'] . "/Audits/" . $answer->Assignments['Audits_id'] . "/Assets/" . $answer->Assignments['Assets_id'] . "/" . $answer['evidence'];
                $path = FILESTORE . "images/" . $answer->Assignments['client_id'] . "/Audits/" . $answer->Assignments['Audits_id'] . "/" . $answer['evidence'];
//                $path = 'myfolder/myimage.png';
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $evidence = 'data:image/' . $type . ';base64,' . base64_encode($data);
            } else {
                $evidence = null;
            }
            $high += $this->questionTypeOptions->getHighestById($answer->Questions['QuestionTypes_id']);
            $score += $answer['answer_value'];
            $qaReturn[$answer->Questions->QuestionGroups['name']][] = array(
                "Question" => $answer->Questions['question'],
                "Answer" => ($answer['answer_text'] === 'Points') ? null : $answer['answer_text'],
                "AnswerScore" => $answer['answer_value'],
                "AnswerEvidence" => $evidence
            );
        }
        $answerset = $this->answerSet->getAssetByAnswerSet($id);
        $grades = $this->gradingValues->allGradingValuesReturnArray($answer->Questions->AuditDatas['AuditGradings_id']);
        //Php <= 5.3 :(      
        $user = $this->user->getUserById((int) $answer['answered_by']);
        $client = $settings->getClient($this->token->user->client);
        $miscReturn = array(
            //PHP >= 5.4  :)
            // "Auditor" => $this->user->getUserById((int)$answer['answered_by'])['name'],
            "Auditor" => $user['name'],
            "ClientLogo" => $this->helpers->base64image($settings->getLogo($this->token->user->client)),
            "Client" => array(
                "Company" => $client['company'],
                "Address1" => $client['address1'],
                "Address2" => $client['address2'],
                "City" => $client['city'],
                "County" => $client['county'],
                "Postcode" => $client['postcode'],
                "ContactTel" => $client['contacttel'],
                "Country" => $client['country'],
            ),
            "id" => $id,
            "CompletedAt" => $answer['answered_at'],
            "AuditName" => $answer->Questions->AuditDatas['name'],
            //"PassLevel" => $answer->Questions->AuditDatas['Pass_level'],
            "TotalScore" => $score,
            "Possible" => $high,
            "PercentScore" => $percent = $this->helpers->getPercent($score, $high),
            "PassFail" => $this->helpers->getClosest($percent, $grades),
//            "PassFail" => ($score > $answer->Questions->AuditDatas['Pass_level']) ? "Pass" : "Fail",
            "code" => md5($id),
        );


        $return['QuestionAnswer'] = $qaReturn;
        $return['Misc'] = $miscReturn;
        $return['Asset'] = $answerset;

//        $this->response->data->QuestionAnswer = $qaReturn;
//        $this->response->data->misc = $miscReturn;
//        $this->pdf->buildPdfAction($this->response->data);
        //return $this->pdf->buildPdfAction("123 here i am");
//        die(print_R($return));

        return $return;
//        return print json_encode($this->response);
    }

    public function historyAction($audit, $asset) {

        if ($this->security->getToken()) {
//            $this->response->setData($this->getReportAction());

//        $this->response->setData($return);
            echo $this->twig->render("Audit/Report/history-by-audit-asset.html.twig", array(
                "data" => $this->response
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Audit/",
                "controller" => "Reports/",
                "action" => "History/$audit/$asset"
            ));
        }
    }

}
