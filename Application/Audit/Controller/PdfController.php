<?php

namespace SolutionMvc\Audit\Controller;

use SolutionMvc\Audit\Model\Audit,
    SolutionMvc\Audit\Model\Answer,
    SolutionMvc\Audit\Model\AnswersSet,
    SolutionMvc\Audit\Model\QuestionTypeOption,
    SolutionMvc\Audit\Model\User,
    SolutionMvc\Core\Response,
    TCPDF;

class PdfController {

    protected $assignment;
    protected $answer;
    protected $answerSet;
    protected $questionTypeOptions;
    protected $audit;
    protected $helpers;
    protected $security;
    protected $response;
    protected $user;
    protected $pdf;

    public function __construct() {
        $this->user = new User();
        $this->response = new Response();
        $this->questionTypeOptions = new QuestionTypeOption();
        $this->answer = new Answer();
        $this->answerSet = new AnswersSet();
        $this->pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    public function getReportAction($id) {
        $reportID = $id;
        $qaReturn = array();
        $high = 0;
        $score = 0;
        foreach ($this->answer->getAnswers($reportID) AS $answer) {

            $high += $this->questionTypeOptions->getHighestById($answer->Questions['QuestionTypes_id']);

            $score += $answer['answer_value'];
            $qaReturn[$answer->Questions->QuestionGroups['name']][] = array(
                "Question" => $answer->Questions['question'],
                "Answer" => ($answer['answer_text'] === 'Points') ? null : $answer['answer_text'],
                "AnswerScore" => $answer['answer_value'],
                "AnswerEvidence" => $answer['evidence']
            );
        }
        //Php <= 5.3 :(      
        $user = $this->user->getUserById((int) $answer['answered_by']);
        $miscReturn = array(
            //PHP >= 5.4  :)
            // "Auditor" => $this->user->getUserById((int)$answer['answered_by'])['name'],
            "Auditor" => $user['name'],
            "id" => $reportID,
            "CompletedAt" => $answer['answered_at'],
            "AuditName" => $answer->Questions->AuditDatas['name'],
            "PassLevel" => $answer->Questions->AuditDatas['Pass_level'],
            "TotalScore" => $score,
            "Possible" => $high,
            "PercentScore" => ($score / ($high)) * 100, 2,
            "PassFail" => ($score > $answer->Questions->AuditDatas['Pass_level']) ? "Pass" : "Fail",
            "code" => md5($reportID)
        );



        $this->response->data->QuestionAnswer = $qaReturn;
        $this->response->data->misc = $miscReturn;
//        $this->response->headers = http_response_code(200);
//        $this->response->status = "success";
//        $this->pdf->buildPdfAction($this->response->data);
        //return $this->pdf->buildPdfAction("123 here i am");

        return $this->response;
    }

    public function buildPdfAction($id, $code) {

        if (md5($id) !== $code) {
            Print "You aint allowed here";
        } else {

            $report = $this->getReportAction($id);

// set document information
            $this->pdf->SetCreator($report->data->misc['Auditor']);
            $this->pdf->SetAuthor($report->data->misc['Auditor']);
            $this->pdf->SetTitle($report->data->misc['AuditName']);
            $this->pdf->SetSubject($report->data->misc['AuditName']);
            $this->pdf->SetKeywords($report->data->misc['CompletedAt']);
// set default header data
            $this->pdf->SetHeaderData(null, null, $report->data->misc['AuditName'], $report->data->misc['CompletedAt'], array(0, 64, 255), array(0, 64, 128));
            $this->pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
// set header and footer fonts
            $this->pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $this->pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
// set default monospaced font
            $this->pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
// set margins
            $this->pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $this->pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
// set auto page breaks
            $this->pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $this->pdf->setFontSubsetting(true);

            $this->pdf->SetFont('dejavusans', '', 14, '', true);
            $this->pdf->AddPage();
            $this->pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

//       $this->ColoredTable(array($report->data->QuestionAnswer));
//        
            $html = "";

            foreach ($report->data->QuestionAnswer AS $key => $qas) {
// set default font subsetting mode
                $this->pdf->setFontSubsetting(false);

                $this->pdf->SetFont('helvetica', 'B', 20);

                $this->pdf->Write(0, $key, '', 0, 'L', 1, 0, false, false, 0);
                
                foreach ($qas AS $qa) {
                $this->pdf->SetFont('helvetica', 'B', 10);
                $this->pdf->Write(0, "Question: " . $qa['Question'], '', 0, 'L', 1, 0, false, false, 0);
                $this->pdf->Write(0, "Answer: " . $qa['Answer'] . "Scored: " . $qa['AnswerScore'] . "Evidence: " . $qa['AnswerEvidence'], '', 0, 'L', 1, 0, false, false, 100);
                //$this->pdf->Write(0, $qa['AnswerEvidence'], '', 0, 'L', 1, 0, false, false, 0);
//                    $html .= "Question = " . $qa['Question'] . "<BR>";
//                    $html .= "Answer = " . $qa['Answer'] . "<BR>";
//                    $html .= "Score = " . $qa['AnswerScore'] . "<BR>";
//                    $html .= "Evidence = " . $qa['AnswerEvidence'] . "<BR>";
                }
            }

//
//// Print text using writeHTMLCell()
//            $this->pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
            $this->pdf->Output('file.pdf', 'D');
        }
//============================================================+
// END OF FILE
//============================================================+
    }

    public function ColoredTable($data) {
        
    }

}
