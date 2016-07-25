<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Healthsafety\Model\Coshh;

class CoshhController extends Controller {

    protected $coshh;
    protected $token;
    protected $word;

    public function __construct() {
        parent::__construct();
        $this->coshh = new Coshh();
        $this->token = $this->getToken();
    }

    public function indexAction() {
        echo $this->twig->render('HealthSafety/Documents/Coshh/index.html.twig', array(
            "data" => $this->coshh->getBasicAssessment()
        ));
    }

    public function viewAction($id) {
        echo $this->twig->render('HealthSafety/Documents/Coshh/view.html.twig', array(
            "data" => $this->coshh->getAssessment($id),
            "init" => $this->buildInitialArray()
        ));
    }

    public function createAction() {
        if ($this->getToken() && $this->requestType() == "GET") {
            echo $this->getCoshhCreate();
        } elseif ($this->getToken() && $this->requestType() == "POST") {
            $this->postCoshhCreate($this->requestObject());
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

        $assessmentID = $this->coshh->setAssessment($request, $this->token->user->id);
        $this->coshh->setCoshhMEL($request['mels'], $assessmentID);
        $this->coshh->setCoshhWEL($request['wels'], $assessmentID);
        $this->coshh->setCoshhOES($request['oess'], $assessmentID);
        $this->coshh->setCoshhRisks($request['risk_phrases'], $assessmentID);
        $this->coshh->setCoshhPPE($request['ppes'], $assessmentID);
        $this->coshh->setCoshhRoutes($request['route'], $assessmentID);
        $this->coshh->setCoshhPersons($request['person'], $assessmentID);
        $this->coshh->setCoshhSubstances($request['substances'], $assessmentID);

        print "Success, Assessment ID : $assessmentID";
    }

    public function updateAction($id) {
        echo $this->twig->render('HealthSafety/Documents/Coshh/update.html.twig', array(
            "data" => "get data"
        ));
    }

    public function retireAction($id) {
        
    }

    public function getDocumentAction($id) {
        
    }

    public function buildInitialArray() {
        $return = array();

        $return['persons_affected'] = $this->coshh->getPersonsAtRisk();
        $return['routes_of_entry'] = $this->coshh->getRouteEntries();
        $return['ppes'] = $this->coshh->getPPE();
        $return['phrases'] = $this->coshh->getRiskPhrases();
//        $return['oess'] = $this->coshh->getOes();
//        $return['mels'] = $this->coshh->getMel();
//        $return['wels'] = $this->coshh->getWel();
        $return['eh40s'] = $this->coshh->getEh40s();
        $return['amountsUsed'] = $this->coshh->getAmountsUsed();
        $return['timesPerDay'] = $this->coshh->getTimesPerDay();
        $return['durations'] = $this->coshh->getDurations();
        $return['substances'] = $this->coshh->getSubstances();
        return $return;
    }

    public function coshhDocAction($id) {
        //Load PhpWord
        require_once APP . '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
        $this->word = new \PhpOffice\PhpWord\PhpWord();
        $this->word->setDefaultFontName('Arial');
        $this->word->setDefaultFontSize(12);
        //Load required data
        $data = $this->coshh->getAssessment($id);
        $init = $this->buildInitialArray();

        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 100
        );

        $h2 = array('size' => 14, 'bold' => true, 'align' => 'center');
        $h3 = array('size' => 11, 'bold' => true, 'align' => 'center');

//        $tableStyle = array('cellMarginTop' => 200);
        $p = array('size' => 9);
        $label = array('size' => 9, 'bold' => true, 'align' => 'center');


        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead4 = array("gridspan" => 4, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead8 = array("gridspan" => 8, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead3 = array("gridspan" => 3, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead2 = array("gridspan" => 2, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleYellowHead = array('valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'fcf8e3', 'align' => 'center');
        $styleYellowHead6 = array("gridspan" => 6, 'valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'fcf8e3', 'align' => 'center');
        $styleYellowHead9 = array("gridspan" => 9, 'valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'fcf8e3', 'align' => 'center');
        $styleWhiteCell = array('valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'ffffff', 'align' => 'center');
        $styleWhiteCell3 = array("gridspan" => 3, 'valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'ffffff', 'align' => 'center');

        //Section 1 - General Substance Details
        $section1 = $this->word->addSection();
        $this->word->addTableStyle('general', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('hazardsandppe', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('risksafe', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('frequencyandduration', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('SubstanceProperties', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('ControlMeasures', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('Control', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('FireSpill', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('comments', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('SignOffSheet', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('SignOffSheet2', $tableStyle, $styleBlueHead);

        $this->word->addParagraphStyle("leftRight", array("tabs" => array(
                new \PhpOffice\PhpWord\Style\Tab("right", 8500)
        )));


        $checked = '<w:sym w:font="Wingdings" w:char="F0FE"/>';


        //Section 1 - Table
        $table1 = $section1->addTable("general");
        $table1->addRow(600);
        $table1->addCell(9200, $styleBlueHead4)->addText("General Substance Details", $h2);
        $table1->addRow(600);
        $table1->addCell(1800, $styleYellowHead)->addText("Substance Name", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['name'], $p);
        $table1->addCell(1800, $styleYellowHead)->addText("COSHH Reference", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['reference'], $p);
        $table1->addRow(600);
        $table1->addCell(1800, $styleYellowHead)->addText("Supplied By", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['supplied_by'], $p);
        $table1->addCell(1800, $styleYellowHead)->addText("Date of Assessment", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['created_at'], $p);
        $table1->addRow(100);
        $table1->addCell(1800, $styleYellowHead)->addText("Persons at Risk", $label);
        $personCell = $table1->addCell(2800, $styleWhiteCell);
        $innerPerson = $personCell->addTable()->addRow()->addCell();
        foreach ($init['persons_affected'] as $person) {
            foreach ($data['persons_affected'] as $c_people) {
                $ppl[] = $c_people['id'];
            }
            $textrun = $innerPerson->addTextRun();
            $textrun->addText($person['name'], $p);
            if (in_array($person['id'], $ppl)) {
                $textrun->addFormField('checkbox')->setValue($checked);
            } else {
                $textrun->addFormField('checkbox')->setValue(false);
            }
        }

        $table1->addCell(1800, $styleYellowHead)->addText("Review Date", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText();
        $table1->addRow(600);
        $table1->addCell(1800, $styleYellowHead)->addText("Assessor", $label);
        $assCell = $table1->addCell(2800, $styleWhiteCell);
        $assCell->addText("Print:", $p);
        $assCell->addText("Signed:", $p);
        $table1->addCell(1800, $styleYellowHead)->addText("Supervisor", $label);
        $supCell = $table1->addCell(2800, $styleWhiteCell);
        $supCell->addText("Print:", $p);
        $supCell->addText("Signed:", $p);
        $table1->addRow(600);
        $table1->addCell(1800, $styleYellowHead)->addText("Description", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText(strip_tags($data['assessment']['description']), $p);
        $table1->addCell(1800, $styleYellowHead)->addText("Method of Use", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText(strip_tags($data['assessment']['method_of_use']), $p);

//        $section1->addTextBreak();
//        $section2 = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
        $table2 = $section1->addTable("hazardsandppe");
        $table2->addRow(600);
        $table2->addCell(9200, $styleBlueHead8)->addText("Hazards Identification and PPE", $h2);
        $table2->addRow(700);
        $table2->addCell(1250, $styleYellowHead)->addText("Routes of Entry", $h3);
        $table2->addCell(6800, $styleYellowHead6)->addText("Personal Protective Equipment (Tick Required Boxes)", $h3);
        $table2->addCell(1150, $styleYellowHead)->addText("Tick here for none", $label);
        $table2->addRow(1300);
        $routesListCell = $table2->addCell(1250, $styleWhiteCell);
        foreach ($init['routes_of_entry'] as $route) {
            foreach ($data['routes_of_entry'] as $c_route) {
                $routeAr[] = $c_route['RouteEntrys_id'];
            }
            $routesListCellRun = $routesListCell->addTextRun();
            $routesListCellRun->addText($route['name'] . " ", $p);
            if (in_array($route['id'], $routeAr)) {
                $routesListCellRun->addFormField('checkbox')->setValue($checked);
            } else {
                $routesListCellRun->addFormField('checkbox')->setValue(false);
            }



//            $routesListCell->addText($route['name'], $p);
        }

        foreach ($init['ppes'] AS $ppe) {
            $table2->addCell(1135, $styleWhiteCell)->addImage("http://doug.portal.solutionhost.co.uk/apps2/public/assets/HealthSafety/Coshh/images/ppes/" . $ppe['image'], array('width' => 45, 'height' => 45));
        }

        $table2->addRow(600);
        $table2->addCell(1250, $styleWhiteCell)->addText("Location", $label);

        foreach ($init['ppes'] AS $ppe) {
            $table2->addCell(1135, $styleWhiteCell)->addText($ppe['name'], $label);
        }
        $table2->addRow(600);
        $table2->addCell(1250, $styleWhiteCell)->addText($data['assessment']['location'], $p);

        foreach ($init['ppes'] AS $ppe) {
            foreach ($data['ppes'] as $peep) {
                $peeps[] = $peep['Ppes_id'];
            }
            if (in_array($ppe, $peeps)) {
                $table2->addCell(1135, $styleWhiteCell)->addFormField('checkbox')->setValue($checked);
            } else {
                $table2->addCell(1135, $styleWhiteCell)->addFormField('checkbox')->setValue(false);
            }
            //$table2->addCell(1135, $styleWhiteCell)->addText("tick or cross", $p);
        }
//        $section1->addTextBreak();
// Frequency & Duration of Exposure
//        $section3 = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
        $table3 = $section1->addTable("frequencyandduration");
        $table3->addRow(600);
        $table3->addCell(9200, $styleBlueHead3)->addText("Frequency and Duration of Exposure", $h2);
        $table3->addRow(700);
        $table3->addCell(3066, $styleYellowHead)->addText("Amount Used", $h3);
        $table3->addCell(3066, $styleYellowHead)->addText("How many times per day", $h3);
        $table3->addCell(3066, $styleYellowHead)->addText("Duration", $h3);
        $table3->addRow(600);
        $table3->addCell(3066, $styleWhiteCell)->addText($data['assessment']->AmountUseds['name'], $p);
        $table3->addCell(3066, $styleWhiteCell)->addText($data['assessment']->TimePerDays['name'], $p);
        $table3->addCell(3066, $styleWhiteCell)->addText($data['assessment']->Durations['name'], $p);


        $sectionHazPpe = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
        $tableRiskSafe = $sectionHazPpe->addTable("risksafe");
        $tableRiskSafe->addRow(600);
        $tableRiskSafe->addCell(9200, $styleBlueHead2)->addText("Data Sheet", $h2);
        $tableRiskSafe->addRow(600);
        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Risk Phrases/ Safety Phrases", $h3);
        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Occupational Exposure Standard (OES)", $h3);

        $tableRiskSafe->addRow(300);
        $phrasesCell = $tableRiskSafe->addCell(2300, $styleWhiteCell);
        foreach ($data['risk_phrases'] as $phrase) {
            $phrasesCell->addText($phrase->RiskPhrases['code'], $label);
            $phrasesCell->addText($phrase->RiskPhrases['description'], $p);
            $phrasesCell->addText("");
        }
        $oesCell = $tableRiskSafe->addCell(4600, $styleWhiteCell);
        foreach ($data['oes'] as $oes) {
            $this->whatLimits($oes->Eh40s, $label, $p, $oesCell);
        }
        $tableRiskSafe->addRow(300);

        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Maximum Exposure Limits (MEL)", $h3);
        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Workplace Exposure Limits (WEL)", $h3);
        $tableRiskSafe->addRow(600);
        $melCell = $tableRiskSafe->addCell(4600, $styleWhiteCell);
        foreach ($data['mel'] as $mel) {
            $this->whatLimits($mel->Eh40s, $label, $p, $melCell);
        }
        $welCell = $tableRiskSafe->addCell(4600, $styleWhiteCell);
        foreach ($data['wel'] as $wel) {
            $this->whatLimits($wel->Eh40s, $label, $p, $welCell);
        }

        //SubstanceProperties
//        $section4 = $this->word->addSection();
        //SubstanceProperties
        $table4 = $sectionHazPpe->addTable("SubstanceProperties");
        $table4->addRow(300);
        $table4->addCell(9200, $styleYellowHead9)->addText("Substance Properties", $h3);
        $table4->addRow(600);
        foreach ($init['substances'] as $substance) {
            $table4->addCell(1022, $styleWhiteCell)->addImage("http://doug.portal.solutionhost.co.uk/apps2/public/assets/HealthSafety/Coshh/images/substance_properties/" . $substance['image'], array("width" => 35, "height" => 35));
        }
        $table4->addRow(300);
        foreach ($init['substances'] as $substance) {
            $table4->addCell(1022, $styleYellowHead)->addText($substance['name'], array("bold" => true, "size" => 7));
        }
        $table4->addRow();
        foreach ($init['substances'] as $substance) {

            foreach ($data['substances'] as $sub) {
                $subs[] = $sub['Substances_id'];
            }
            if (in_array($substance, $subs)) {
                $table4->addCell(1022, $styleWhiteCell)->addFormField('checkbox')->setValue($checked);
            } else {
                $table4->addCell(1022, $styleWhiteCell)->addFormField('checkbox')->setValue(false);
            }
        }

        // Frequency & Duration of Exposure
        $section5 = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
        $table5 = $section5->addTable("ControlMeasures");
        $table5->addRow(600);
        $table5->addCell(9200, $styleBlueHead2)->addText("Control Measures", $h2);
        $table5->addRow(600);
        $table5->addCell(4600, $styleYellowHead)->addText("Gerneral Precautions", $h3);
        $table5->addCell(4600, $styleYellowHead)->addText("First Aid Measures", $h3);
        $table5->addRow();
        $table5->addCell(4600, $styleWhiteCell)->addText(strip_tags($data['assessment']['general_precautions']), $p);
        $table5->addCell(4600, $styleWhiteCell)->addText(strip_tags($data['assessment']['first_aid_measures']), $p);
        // Frequency & Duration of Exposure
//        $sectionControl = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
//        $tableControl = $sectionControl->addTable("Control");
        $table5->addRow();
        $table5->addCell(4600, $styleYellowHead)->addText("Further Controls Required", $h3);
        $table5->addCell(4600, $styleYellowHead)->addText("Responsibility", $h3);
        $table5->addRow();
        $table5->addCell(4600, $styleWhiteCell)->addText(strip_tags($data['assessment']['general_precautions']), $p);
        $table5->addCell(4600, $styleWhiteCell)->addText($data['assessment']['responsibility'], $p);
        $table5->addRow();
        $table5->addCell(4600, $styleYellowHead)->addText("By When", $h3);
        $table5->addCell(4600, $styleYellowHead)->addText("Date Done", $h3);
        $table5->addRow();
        $table5->addCell(4600, $styleWhiteCell)->addText($data['assessment']['by_when'], $p);
        $table5->addCell(4600, $styleWhiteCell)->addText();
        // Frequency & Duration of Exposure
        $sectionSpillFire = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
        $tableSpillFire = $sectionSpillFire->addTable("FireSpill");
        $tableSpillFire->addRow(600);
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Spillage Procedure", $h3);
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Fire Prevention", $h3);
        $tableSpillFire->addRow();
        $tableSpillFire->addCell(4600, $styleWhiteCell)->addText(strip_tags("spill section " . $data['assessment']['spillage_procedure']), $p);
        $tableSpillFire->addCell(4600, $styleWhiteCell)->addText(strip_tags("fire section " . $data['assessment']['fire_prevention']), $p);
        $tableSpillFire->addRow(600);
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Handling and Storage", $h3);
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Disposal Considerations", $h3);
        $tableSpillFire->addRow();
        $tableSpillFire->addCell(4600, $styleWhiteCell)->addText(strip_tags("handling section " . $data['assessment']['handling_storage']), $p);
        $tableSpillFire->addCell(4600, $styleWhiteCell)->addText(strip_tags("disposal section " . $data['assessment']['disposal_procedure']), $p);

        // COSHH Assessment Comments
        $section6 = $this->word->addSection();
        //COSHH Assessment Comments
        $table6 = $section6->addTable("comments");
        $table6->addRow(600);
        $table6->addCell(9200, $styleBlueHead)->addText("COSHH Assessment Comments", $h2);
        $table6->addRow();
        $table6->addCell(9200, $styleWhiteCell)->addText(strip_tags($data['assessment']['comments']), $p);
        $section6->addPageBreak();
        $signOffSection = $this->word->addSection();
        $signOffTable = $signOffSection->addTable("SignOffSheet");
        $signOffTable->addRow(500);
        $signOffTable->addCell(9200, $styleBlueHead3)->addText("Sign off Sheet", $h2);
        $signOffTable->addRow(500);
        $signOffTable->addCell(4700, $styleYellowHead)->addText("Assessor Summary", $h3);
        $signOffTable->addCell(1000, $styleYellowHead)->addText("Yes/No", $label);
        $signOffTable->addCell(3500, $styleYellowHead)->addText("Further Action", $label);
        $signOffTable->addRow(500);
        $signOffTable->addCell(4700, $styleYellowHead)->addText("1. Has the assessment considered all factors pertinent to the use of the substance? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow(500);
        $signOffTable->addCell(4700, $styleYellowHead)->addText("2. Has the assessment considered the practicability of preventing exposure? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow(500);
        $signOffTable->addCell(4700, $styleYellowHead)->addText("3. Has the assessment considered the steps to be taken to achieve and maintain adequate control of exposure where prevention is not reasonably practicable? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow(500);
        $signOffTable->addCell(4700, $styleYellowHead)->addText("4. Has the assessment considered the need for monitoring exposure to the substance? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow(500);
        $signOffTable->addCell(4700, $styleYellowHead)->addText("5. Has the assessment identified all action required to comply with regulations? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
//        $signOffTable2 = $signOffSection->addTable("SignOffSheet2");
        $signOffTable->addRow(500);
        $signOffTable->addCell(4100, $styleYellowHead)->addText("COSHH Assessment", $h3);
        $signOffTable->addCell(1000, $styleYellowHead)->addText("Please Tick", $p);
        $signOffTable->addCell(4100, $styleYellowHead)->addText();
        $signOffTable->addRow();
        $signOffTable->addCell(4100, $styleYellowHead)->addText("The task is safe to be carried out with current control procedures.", $p);
        $signOffTable->addCell(1000, $styleWhiteCell)->addText();
        $signOffTable->addCell(4100, $styleYellowHead)->addText("Tick if no further action required", $p);
        $signOffTable->addRow();
        $signOffTable->addCell(4100, $styleYellowHead)->addText("The task is safe to be carried out subject to actions listed", $p);
        $signOffTable->addCell(1000, $styleWhiteCell)->addText();
        $signOffTable->addCell(4100, $styleYellowHead)->addText("Tick if use of the substance is not causing significant problems but requires some action to bring it within COSHH guidelines, Action should be prioritised and specific dates set for completion", $p);
        $signOffTable->addRow();
        $signOffTable->addCell(4100, $styleYellowHead)->addText("Task Substance is Unsafe, significant non-compliance with Health and Safety standards", $p);
        $signOffTable->addCell(1000, $styleWhiteCell)->addText();
        $signOffTable->addCell(4100, $styleYellowHead)->addText("Tick if the task or substance has potential to cause significant problems to users, use of substance to be discontinued until problems have been rectified", $p);
        $signOffTable->addRow();
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("The Task process should be re-assessed on a regular basis either annually, or if there are significant changes to the task or process or if there is a significant change in personnel who carry it out it e.g. young/inexperienced workers, pregnancy, workers with pre existing conditions such as asthma, dermatitis etc", $p);
        $signOffTable->addRow(150);
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("Signed Assessor:", $label);
        $signOffTable->addRow(150);
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("Date:", $label);
        $signOffTable->addRow();
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("This Assessment has been discussed with the user their line manager and action agreed", $p);
        $signOffTable->addRow(150);

        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("Signed Assessor:", $label);
        $signOffTable->addRow(150);
//        $innerRun0->addTextBreak(4, null, array("lineHeight"=> 10));
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("Signed User:", $label);
        $signOffTable->addRow(150);
//        $innerRun1->addTextBreak(4, null, array("lineHeight"=> 10));
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("Signed Line Manager:", $label);
        $signOffTable->addRow(150);
//        $innerRun2->addTextBreak(4, null, array("lineHeight"=> 10));
        $signOffTable->addCell(9200, $styleWhiteCell3)->addText("Date:", $label);
//        $innerRun3->addTextBreak(4, null, array("lineHeight"=> 10));
//        $signTBL->addRow();

        \PhpOffice\PhpWord\Settings::setPdfRendererPath(APP . '../vendor/dompdf/dompdf/autoload.inc.php');
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/Coshh/output/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }

//        / Produce file and download
        $file = $data['assessment']['name'] . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');


        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($this->word, 'Word2007');
        $xmlWriter->save("php://output");
        $xmlWriter->save($dir . $file);
    }

    public function personsAffectedTable($persons) {
        $section = $this->word->addSection();
//        
        $table = $section->addTable();


        return $section;
    }

    public function whatLimits($eh40, $label, $p, $cell) {
        $cell->addText($eh40['substance'], $label);
        if ($eh40['cas_number'] != null) {
            $cell->addText(strip_tags($eh40['cas_number']), $label);
        }
        if ($eh40['long_ppm'] != null || $eh40['long_mgm3'] != null) {
            $longppm = ($eh40['long_ppm'] != null) ? $eh40['long_ppm'] . "ppm" : null;
            $longmgm3 = ($eh40['long_mgm3'] != null) ? strip_tags($eh40['long_mgm3'] . "mgm-³ ") : null;
            $cell->addText("Long: " . $longppm . " " . $longmgm3, $p);
        }
        if ($eh40['short_ppm'] != null || $eh40['short_mgm3'] != null) {
            $shortppm = ($eh40['short_ppm'] != null) ? $eh40['short_ppm'] . "ppm" : null;
            $shortmgm3 = ($eh40['short_mgm3'] != null) ? strip_tags($eh40['short_mgm3'] . "mgm-³ ") : null;
            $cell->addText("Short: " . $shortppm . " " . $shortmgm3, $p);
        }
        $cell->addText("", $p);
        return $cell;
    }

}
