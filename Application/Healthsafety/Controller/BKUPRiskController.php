<?php

//namespace SolutionMvc\Healthsafety\Controller;

//use SolutionMvc\Core\Controller,
//    SolutionMvc\Healthsafety\Model\Risk;
//
//class RiskController extends Controller {
//
//    protected $risk;
//    protected $word;
//
//    public function __construct() {
//        parent::__construct();
//        $this->risk = new Risk();
//        $this->token = $this->getToken();
//    }
//
//    public function indexAction() {
//        echo $this->twig->render('HealthSafety/Documents/RiskAssessment/index.html.twig', array(
//            "data" => $this->risk->getRiskAssessments()
//        ));
//    }
//
//    public function viewAction($id) {
//        echo $this->twig->render('HealthSafety/Documents/RiskAssessment/view.html.twig', array(
//            "Persons" => $this->risk->getRiskAssessments_PersonRisks($id),
//            "Risk" => $this->risk->getRiskAssessment($id),
//            "Hazards" => $this->risk->getRiskAssessments_Hazards($id)
//        ));
//    }
//
//    public function createAction() {
//        if ($this->requestType() == "GET") {
//            echo $this->twig->render('HealthSafety/Documents/RiskAssessment/create.html.twig', array(
//                "hazards" => $this->risk->getHazards(),
//                "persons" => $this->risk->getPersons()
//            ));
//        } elseif ($this->requestType() == "POST") {
//
//            $RiskAssID = $this->risk->setRiskAssessment($this->requestObject(), $this->token->user->id);
//            print "RiskASSID = " . $RiskAssID;
//            $this->risk->setRiskAssessments_PersonRisks($this->requestObject(), $RiskAssID);
//            $this->risk->setRiskAssessments_Hazards($this->requestObject(), $RiskAssID);
////            print "<pre>";
////            print_r($this->requestObject());
////            print "</pre>";
//        } else {
//            echo $this->twig->render("Portal/Login/login.html.twig", array(
//                "project" => "HealthSafety/",
//                "controller" => "Risk/",
//                "action" => "create"
//            ));
//        }
//    }
//
//    public function updateAction($id) {
//        echo $this->twig->render('HealthSafety/Documents/RiskAssessment/update.html.twig', array(
//            "data" => "get data"
//        ));
//    }
//
//    public function retireAction($id) {
//        
//    }
//
//    public function documentAction($id) {
//        $Persons = $this->risk->getRiskAssessments_PersonRisks($id);
//        $Risk = $this->risk->getRiskAssessment($id);
//        $Hazards = $this->risk->getRiskAssessments_Hazards($id);
//        $riskName = htmlspecialchars($Risk['name']);
//        //Load PhpWord
//        require_once APP . '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
//        \PhpOffice\PhpWord\Autoloader::register();
//        $this->word = new \PhpOffice\PhpWord\PhpWord();
////        $this->word->setDefaultFontName('Helvetica');
//        $this->word->setDefaultFontSize(10);
//
//        $tableStyle = array(
//            'width' => 100,
//            'borderColor' => '999999',
//            'borderSize' => 6,
//            'cellMargin' => 100,
//            "spaceAfter" => 150,
//        );
//
//        $red = "FF0000";
//        $blue = "0000FF";
//
//        $h2 = array('size' => 17, 'bold' => true);
//        $center = array('align' => "center", "spaceBefore" => 80, "spaceAfter" => 80);
//        $h3 = array('size' => 15, 'bold' => true);
//        $h3Red = array('size' => 15, 'bold' => true, 'color' => $red);
//        $h4Red = array('size' => 12, 'bold' => true, 'color' => $red);
//        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
//        $headPara = array("align" => "center");
//        $headCell = array("valign" => "center");
//        $yellowCell = array("valign" => "center", "bgColor" => "fcf8e3");
//        $label = array('size' => 10, 'bold' => true);
//        $pRed = array('size' => 10, 'color' => 'ff0000');
//        $pRedBold = array('size' => 10, 'color' => 'ff0000', "bold" => true);
//        $p = array('size' => 10, 'color' => '000000');
//        $pBold = array('size' => 10, "bold" => true, 'color' => '000000');
//        $labelP = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 80);
//        $paragraphStyle = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 150);
//        $rowStyle = array('cantSplit' => true);
//
//        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
////        $this->word->addTableStyle('HeadTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('AssessorTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('FooterTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('KeyTable', $tableStyle, $styleBlueHead);
//
//        $section = $this->word->addSection();
//        $this->header($section, $riskName);
//        $this->footer($section);
//
//        $section->addText("RISK ASSESSMENT INFORMATION", $h3Red, $headPara);
//        $section->addText("Risk Assessment Examples", $pRed, $paragraphStyle);
//        $section->addText("The following document is an example of a risk assessment carried out for a specific task.", $pRed, $paragraphStyle);
//        $section->addText("Please now carefully examine the risk assessment and ensure that you add any further significant hazards associated with the task you are carrying out, these hazards must then be controlled and risk ranked to ensure that the risks of injury and ill health are reduced to a tolerable level.", $pRed, $paragraphStyle);
//        $section->addText("Please remove any hazards and control procedures that do not apply to your task.  Remember, you will be expected to implement all control procedures listed in your risk assessment.", $pRed, $paragraphStyle);
//        $section->addText("This document should then be used in conjunction with the associated method statement to provide a comprehensive set of safety documentation for the task you are carrying out.", $pRed, $paragraphStyle);
//        $section->addText("Please read the document RA_training_183.pdf supplied with this risk assessment or go to <a href='https://www.hsdirect.co.uk/free-info/risk-assessment.html'>www.hsdirect.co.uk/free-info/risk-assessment.html</a> for the web version.  This document will give you comprehensive information on how to complete a risk assessment including risk ranking.", $pRed, $paragraphStyle);
//        $section->addText("If you are still unsure and/or you are dealing with tasks of a very hazardous nature we advise that you seek further professional advice.", $pRed, $paragraphStyle);
//        $section->addText("The logo, Task Description, Location, Date of review, Company address, etc are in the document header.", $pRed, $paragraphStyle);
//        $section->addText("To change these details simply double click over the top of the logo which will open the header and footer for editing.", $pRed, $paragraphStyle);
//        $section->addText("The last page of this document should be used to fill in any other hazards and control procedures that become apparent on site.", $pRed, $paragraphStyle);
//        $section->addText("Disclaimer", $h4Red, $paragraphStyle);
//        $section->addText("All information and advice is given in good faith. We cannot accept any responsibility for your subsequent acts or omissions. If you have any doubts queries or concerns, you should refer to the relevant regulations and take further professional advice.", $pRedBold, $paragraphStyle);
//        $section->addText("Please delete all red text prior to use.", $pRedBold, $paragraphStyle);
//
//        $assessorTable = $section->addTable("AssessorTable");
//        $assessorTable->addRow(400);
//        $assessorTable->addCell(3066, $yellowCell)->addText("Assessors Name", $pBold);
//        $assessorTable->addCell(3066, $yellowCell)->addText("Assessors Signature", $pBold);
//        $assessorTable->addCell(3066, $yellowCell)->addText("Persons Affected By This Risk Assessment", $pBold);
//        $assessorTable->addRow();
//        $assessorTable->addCell(3066);
//        $assessorTable->addCell(3066);
//        $personCell = $assessorTable->addCell(3066);
//        foreach ($Persons as $person) {
//            $personCell->addListItem($person->PersonRisks['name']);
//        }
//
//        $section->addPageBreak();
//        $section->addText();
//        foreach ($Hazards as $hazard) {
//            $this->word->addTableStyle(trim(htmlspecialchars(str_replace(" ", "", $hazard['hazard']['name']))), $tableStyle, $styleBlueHead);
//            $hazardTable = $section->addTable(trim(htmlspecialchars(str_replace(" ", "", $hazard['hazard']['name']))));
//            $hazardTable->addRow(100, $rowStyle);
//            $hazardTable->addCell(2200, $yellowCell)->addText("Hazard / Consequences", $pBold);
//            $hazardTable->addCell(2500, $yellowCell)->addText("Control Procedures", $pBold);
//            $hazardTable->addCell(1500, $yellowCell)->addText("Likelihood (a)", $pBold);
//            $hazardTable->addCell(1500, $yellowCell)->addText("Severity (b)", $pBold);
//            $hazardTable->addCell(1500, $yellowCell)->addText("Risk Ranking (a x b)", $pBold);
//            $i = 1;
//            foreach ($hazard['controls'] as $control) {
//                $hazardTable->addRow(100, $rowStyle);
//                if ($i == 1) {
//                    $hazConq = $hazardTable->addCell(2200);
//                    $hazConq->getStyle()->setVMerge("restart")->setVAlign("center");
//                    $hazConq->addText($hazard['hazard']['name'], $pBold, $center);
//                } else {
//                    $hazConq = $hazardTable->addCell(2200);
//                    $hazConq->getStyle()->setVMerge("continue")->setVAlign("center");
//                }
//                $hazardTable->addCell(2500)->addText($control->Controls['name']);
//                if ($i == 1) {
//                    $like = $hazardTable->addCell(1500);
//                    $like->getStyle()->setVMerge("restart")->setVAlign("center");
//                    $like->addText($hazard['hazard']['likelihood'], $pBold, $center);
//                    $sev = $hazardTable->addCell(1500);
//                    $sev->getStyle()->setVMerge("restart")->setVAlign("center");
//                    $sev->addText($hazard['hazard']['severity'], $pBold, $center);
//                    $riskRank = $hazardTable->addCell(1500, array("bgColor" => $this->getRiskColour($hazard['hazard']['risk_ranking'])));
//                    $riskRank->getStyle()->setVMerge("restart")->setVAlign("center");
//                    $riskRank->addText($hazard['hazard']['risk_ranking'], $pBold, $center);
//                } else {
//                    $like = $hazardTable->addCell(1500);
//                    $like->getStyle()->setVMerge("continue")->setVAlign("center");
//                    $sev = $hazardTable->addCell(1500);
//                    $sev->getStyle()->setVMerge("continue")->setVAlign("center");
//                    $riskRank = $hazardTable->addCell(1500);
//                    $riskRank->getStyle()->setVMerge("continue")->setVAlign("center");
//                }
//                $i++;
//            }
//            $section->addText();
//        }
//
//        $key = $section->addTable("KeyTable");
//        $key->addRow(100);
//        $key->addCell(500, $yellowCell)->addText();
//        $key->addCell(2566, $yellowCell)->addText("Likelihood", $pBold);
//        $key->addCell(500, $yellowCell)->addText();
//        $key->addCell(2566, $yellowCell)->addText("Severity", $pBold);
//        $key->addCell(500, $yellowCell)->addText();
//        $key->addCell(2566, $yellowCell)->addText("Priority", $pBold);
//        $key->addRow(100);
//        $key->addCell(500, $yellowCell)->addText(1);
//        $key->addCell(2566, $yellowCell)->addText("Highly Unlikely");
//        $key->addCell(500, $yellowCell)->addText(1);
//        $key->addCell(2566, $yellowCell)->addText("Trivial");
//        $key->addCell(500, $yellowCell)->addText(1);
//        $key->addCell(2566, array("valign" => "center", "bgColor" => "00ff00"))->addText("Very Low Priority– No Action required (Risk no 1)");
//        $key->addRow(100);
//        $key->addCell(500, $yellowCell)->addText(2);
//        $key->addCell(2566, $yellowCell)->addText("Unlikely");
//        $key->addCell(500)->addText(2);
//        $key->addCell(2566, $yellowCell)->addText("Minor Injury");
//        $key->addCell(500, $yellowCell)->addText(2);
//        $key->addCell(2566, array("valign" => "center", "bgColor" => "ffcc00"))->addText("Low Priority – Risk no (2 – 4)");
//        $key->addRow(100);
//        $key->addCell(500, $yellowCell)->addText(3);
//        $key->addCell(2566, $yellowCell)->addText("Possible");
//        $key->addCell(500, $yellowCell)->addText(3);
//        $key->addCell(2566, $yellowCell)->addText("Over 3 day Injury");
//        $key->addCell(500, $yellowCell)->addText(3);
//        $key->addCell(2566, array("valign" => "center", "bgColor" => "ff9933"))->addText("Medium Priority – (Risk no 5 – 9)");
//        $key->addRow(100);
//        $key->addCell(500, $yellowCell)->addText(4);
//        $key->addCell(2566, $yellowCell)->addText("Probable");
//        $key->addCell(500, $yellowCell)->addText(4);
//        $key->addCell(2566, $yellowCell)->addText("Major Injury");
//        $key->addCell(500, $yellowCell)->addText(4);
//        $key->addCell(2566, array("valign" => "center", "bgColor" => "ff3300"))->addText("High Priority – (Risk no 10 – 12)");
//        $key->addRow(100);
//        $key->addCell(500, $yellowCell)->addText(5);
//        $key->addCell(2566, $yellowCell)->addText("Certain");
//        $key->addCell(500, $yellowCell)->addText(5);
//        $key->addCell(2566, $yellowCell)->addText("Incapacity or Death");
//        $key->addCell(500, $yellowCell)->addText(5);
//        $key->addCell(2566, array("valign" => "center", "bgColor" => "ff0000"))->addText("Urgent action – (Risk no 15 – 25)");
//
//        $section->addPageBreak();
//
//        $section->addText("Further Information", $h2, $center);
//        $section->addText("The example document provided by HS Direct Ltd is supplied as a guide to enable you to complete your Risk Assessment.", $p, $paragraphStyle);
//        $section->addText("We strongly recommend that you thoroughly read, edit and change your document. Control procedures within the document make reference to the appropriate method statement, and also COSHH assessments. All editing should be made by a competent person. If you are not competent to carry out this task you should seek training or professional assistance to enable you to carry out a suitable and sufficient assessment.", $p, $paragraphStyle);
//        $section->addText("A method statement should be completed for each of the risk assessments you have, enabling you to provide employees with a written safe system of work.", $p, $paragraphStyle);
//        $section->addText("All chemicals used must have a corresponding COSHH assessment.", $p, $paragraphStyle);
//        $section->addText("If your organisation has 5 or more employees, that includes all staff, not just site workers, then you also require by law a written health and safety policy. ", $p, $paragraphStyle);
//        $section->addText("HS direct Ltd take great pride in providing help and assistance with all health and safety related paperwork for over 1500 clients throughout the UK using our Safety First Package. ", $p, $paragraphStyle);
//        $section->addText("To get further information on the entire range of products and services we can assist you with please do not hesitate to call us on 0114 2444461. Our offices are open 8am – 9pm Monday to Friday, and 9am - 5 pm at weekends. ", $p, $paragraphStyle);
//
//
//        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/MS/output/";
//        if (!file_exists($dir)) {
//            mkdir($dir, 0777);
//        }
//        // Produce file and download
//        $file = stripslashes(str_replace('/', "", $riskName)) . '.docx';
//        header("Content-Description: File Transfer");
//        header('Content-Disposition: attachment; filename="' . $file . '"');
//        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
//        header('Content-Transfer-Encoding: binary');
//        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
//        header('Expires: 0');
//        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->word, 'Word2007');
//        $xmlWriter->save("php://output");
//        $xmlWriter->save($dir . $file);
//    }
//
//    public function header($section, $riskName) {
//
//        $tableStyle = array(
//            'width' => 100,
//            'borderColor' => '999999',
//            'borderSize' => 6,
//            'cellMargin' => 100
//        );
//
//        $red = "FF0000";
//        $blue = "0000FF";
//        $h3 = array('size' => 15, 'bold' => true);
//        $h3Red = array('size' => 15, 'bold' => true, 'color' => $red);
//        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
//        $headPara = array("align" => "center");
//        $headCell = array("valign" => "center");
//        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
//        $this->word->addTableStyle('HeadTable', $tableStyle, $styleBlueHead);
//
//
//
//        $header = $section->addHeader();
////        $header->addText("COMPANY LOGO");
//        $headTable = $header->addTable("HeadTable");
//        $headTable->addRow(400);
//        $logoCell = $headTable->addCell(2600, $headCell);
//        $logoCell->getStyle()->setVMerge("restart");
//        $logoCell->addText("LOGO", $h3Blue, $headPara);
//        $headTable->addCell(6600, $headCell)->addText("INSERT COMPANY NAME", $h3Red, $headPara);
//        $headTable->addRow(400);
//        $cell2 = $headTable->addCell(2600, $headCell);
//        $cell2->getStyle()->setVMerge("continue");
//        $headTable->addCell(6600)->addText("RISK ASSESSMENT", $h3Blue, $headPara);
//        $headTable->addRow(400);
//        $logoCell3 = $headTable->addCell(2600, $headCell);
//        $logoCell3->getStyle()->setVMerge("continue");
//        $headTable->addCell(6600)->addText(htmlspecialchars($riskName), $h3, $headPara);
//        return $header;
//    }
//
//    public function footer($section) {
//
//        $headCell = array("valign" => "center");
//        $label = array('size' => 10, 'bold' => true);
//        $pRed = array('size' => 10, 'color' => 'ff0000');
//        $p = array('size' => 10, 'color' => '000000');
//        //Footer
//        $footer = $section->addFooter();
//        $footTable = $footer->addTable("FooterTable");
//        $footTable->addRow();
//        $footTable->addCell(1533, $headCell)->addText("Series", $p);
//        $footTable->addCell(1533, $headCell);
//        $footTable->addCell(1533, $headCell)->addText("Series and Number", $p);
//        $footTable->addCell(1533, $headCell)->addText("Number System", $pRed);
//        $footTable->addCell(1533, $headCell)->addText("Issue Date", $p);
//        $footTable->addCell(1533, $headCell)->addText("Enter Date", $pRed);
//        $footTable->addRow();
//        $footTable->addCell(1533)->addText("Revision Number", $p);
//        $footTable->addCell(1533);
//        $footTable->addCell(1533)->addText("Revision Date", $p);
//        $footTable->addCell(1533);
//        $pageNumberCell = $footTable->addCell(3066);
//        $pageNumberCell->getStyle()->setGridSpan(2);
//        $pageNumberCell->getStyle()->setValign("center");
//        $pageNumberCell->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'), $label, array('align' => 'center'));
//        return $footer;
//    }
//
//    public function getRiskColour($number) {
//        switch ($number) {
//            case $number < 2:
//                $colour = "00ff00";
//                break;
//            case $number >= 2 && $number <= 4:
//                $colour = "ffcc00";
//                break;
//            case $number >= 5 && $number <= 9:
//                $colour = "ff9933";
//                break;
//            case $number >= 10 && $number <= 14:
//                $colour = "ff3300";
//                break;
//            case $number >= 15;
//                $colour = "ff0000";
//                break;
//        }
//        return $colour;
//    }
//
//    public function getControlsAction() {
//        return print json_encode($this->risk->getControls());
//    }
//
//}
