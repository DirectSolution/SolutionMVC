<?php

namespace SolutionMvc\Healthsafety\Controller;

/**
 * Description of PhpWordTemplatesController
 *
 * @author dhayward
 */
class PhpWordTemplatesController {

    protected $word;
    protected $coshh;

    public function __construct() {
        require_once APP . '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
        $this->word = new \PhpOffice\PhpWord\PhpWord();
        $this->word->setDefaultFontName('Helvetica');
        $this->word->setDefaultFontSize(9);
        $this->word->setDefaultParagraphStyle(array("keepLines" => true, "spaceBefore" => 50, "spaceAfter" => 50, "widowControl" => true, "lineHeight" => "1.1",));
        $this->coshh = new \SolutionMvc\Healthsafety\Model\Coshh();
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

    public function coshh($datain, $initin) {

        $init = $initin;
        $data = $datain;

        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 50
        );

        $h2 = array('size' => 13, 'bold' => true, 'align' => 'center');
        $h3 = array('size' => 10, 'bold' => true, 'align' => 'center');

//        $tableStyle = array('cellMarginTop' => 200);
        $p = array('size' => 9);
        $label = array('size' => 9, 'bold' => true, 'align' => 'center');
        $labelSm = array('size' => 8, 'bold' => true, 'align' => 'center');


        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead4 = array("gridspan" => 4, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead8 = array("gridspan" => 8, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead3 = array("gridspan" => 3, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleBlueHead2 = array("gridspan" => 2, 'valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $styleYellowHead = array('valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'fcf8e3', 'align' => 'center');
        $styleYellowHead6 = array("gridspan" => 6, 'valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'fcf8e3', 'align' => 'center');
        $styleYellowHead9 = array("gridspan" => 9, 'valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'fcf8e3', 'align' => 'center');
        $styleWhiteCell = array('valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'ffffff', 'align' => 'center');
        $styleWhiteTextCell = array('borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'ffffff', 'align' => 'center');
        $styleWhiteCell3 = array("gridspan" => 3, 'valign' => 'center', 'borderBottomSize' => 5, 'borderBottomColor' => '000000', 'bgColor' => 'ffffff', 'align' => 'center');

        //Section 1 - General Substance Details
        $section = $this->word->addSection();

        $this->header($section, $data['assessment']['name'], "COSHH");
        $this->footer($section, "COSHH");

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
        $this->word->addTableStyle('FooterTable', $tableStyle, $styleBlueHead);


        $this->word->addParagraphStyle("leftRight", array("tabs" => array(
                new \PhpOffice\PhpWord\Style\Tab("right", 8500)
        )));


        $checked = '<w:sym w:font="Wingdings" w:char="F0FE"/>';
        $date = new \DateTime($data['assessment']['created_at']);

        //Section 1 - Table
        $table1 = $section->addTable("general");
        $table1->addRow();
        $table1->addCell(9200, $styleBlueHead4)->addText("General Substance Details", $h2);
        $table1->addRow();
        $table1->addCell(1800, $styleYellowHead)->addText("Substance Name", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['name'], $p);
        $table1->addCell(1800, $styleYellowHead)->addText("COSHH Reference", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['reference'], $p);
        $table1->addRow();
        $table1->addCell(1800, $styleYellowHead)->addText("Supplied By", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($data['assessment']['supplied_by'], $p);

        $table1->addCell(1800, $styleYellowHead)->addText("Persons at Risk", $label);
        $personCell = $table1->addCell(2800, $styleWhiteCell);
        $innerPerson = $personCell->addTable()->addRow()->addCell();

        $inpertab = $innerPerson->addTable("thisone");


        foreach ($init['persons_affected'] as $person) {
            foreach ($data['persons_affected'] as $c_people) {
                $ppl[] = $c_people['PersonRisks_id'];
            }

            $inpertab->addRow();
            $inpertab->addCell(2000)->addText($person['name']);

//            $textrun = $innerPerson->addTextRun(array("align" => "right"));
//            $textrun->addText($person['name'] . " ", $p);

            if (in_array($person['id'], $ppl)) {
                $inpertab->addCell(400)->addFormField('checkbox', null, array("align" => "right"))->setValue($checked);
            } else {
                $inpertab->addCell(400)->addFormField('checkbox', null, array("indent" => 100, "align" => "right"))->setValue(false);
            }
        }



        $table1->addRow();

        $table1->addCell(1800, $styleYellowHead)->addText("Date of Assessment", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText($date->format("d/m/Y"), $p);
        $table1->addCell(1800, $styleYellowHead)->addText("Review Date", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText();
        $table1->addRow();
        $table1->addCell(1800, $styleYellowHead)->addText("Assessor", $label);
        $assCell = $table1->addCell(2800, $styleWhiteCell);
        $assCell->addText("Print:", $p);
        $assCell->addText("Signed:", $p);
        $table1->addCell(1800, $styleYellowHead)->addText("Supervisor", $label);
        $supCell = $table1->addCell(2800, $styleWhiteCell);
        $supCell->addText("Print:", $p);
        $supCell->addText("Signed:", $p);
        $table1->addRow();
        $table1->addCell(1800, $styleYellowHead)->addText("Description", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText(strip_tags($data['assessment']['description']), $p);
        $table1->addCell(1800, $styleYellowHead)->addText("Method of Use", $label);
        $table1->addCell(2800, $styleWhiteCell)->addText(strip_tags($data['assessment']['method_of_use']), $p);

        $section->addTextBreak();
        $section->addPageBreak();
//        $section2 = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
        $table2 = $section->addTable("hazardsandppe");
        $table2->addRow();
        $table2->addCell(9200, $styleBlueHead8)->addText("Hazards Identification and PPE", $h2);
        $table2->addRow();
        $table2->addCell(1804, $styleYellowHead)->addText("Routes of Entry", $h3);
        $table2->addCell(6339, $styleYellowHead6)->addText("Personal Protective Equipment (Tick Required Boxes)", $h3);
        $table2->addCell(1056, $styleYellowHead)->addText("Tick here for none", $label);
        $table2->addRow();
        $routesListCell = $table2->addCell(1804, $styleWhiteCell);
        $tblroutes = $routesListCell->addTable("anothertable");
        foreach ($init['routes_of_entry'] as $route) {
            foreach ($data['routes_of_entry'] as $c_route) {
                $routeAr[] = $c_route['RouteEntrys_id'];
            }
            $tblroutes->addRow();
            $tblroutes->addCell(1200)->addText($route['name'], $p);
//            $routesListCellRun = $routesListCell->addTextRun();
//            $routesListCellRun->addText($route['name'] . " ", $p);
            if (in_array($route['id'], $routeAr)) {
                $tblroutes->addCell(400)->addFormField('checkbox')->setValue($checked);
            } else {
                $tblroutes->addCell(400)->addFormField('checkbox')->setValue(false);
            }



//            $routesListCell->addText($route['name'], $p);
        }

        foreach ($init['ppes'] AS $ppe) {
            $table2->addCell(1059, $styleWhiteCell)->addImage("http://doug.portal.solutionhost.co.uk/apps2/public/assets/HealthSafety/Coshh/images/ppes/" . $ppe['image'], array('width' => 45, 'height' => 45));
        }

        $table2->addRow();
        $table2->addCell(1804, $styleWhiteCell)->addText("Location", $label);

        foreach ($init['ppes'] AS $ppe) {
            $table2->addCell(1059, $styleWhiteCell)->addText($ppe['name'], $labelSm);
        }
        $table2->addRow();
        $table2->addCell(1804, $styleWhiteCell)->addText($data['assessment']['location'], $p);

        foreach ($init['ppes'] AS $ppe) {
            foreach ($data['ppes'] as $peep) {
                $peeps[] = $peep['Ppes_id'];
            }
            if (in_array($ppe, $peeps)) {
                $table2->addCell(1059, $styleWhiteCell)->addFormField('checkbox')->setValue($checked);
            } else {
                $table2->addCell(1059, $styleWhiteCell)->addFormField('checkbox')->setValue(false);
            }
            //$table2->addCell(1135, $styleWhiteCell)->addText("tick or cross", $p);
        }

//// Frequency & Duration of Exposure
////        $section3 = $this->word->addSection();
//        //HAZARDS iDENTIFICATION AND PPE
        $section->addText();
        $table3 = $section->addTable("frequencyandduration");
        $table3->addRow();
        $table3->addCell(9200, $styleBlueHead3)->addText("Frequency and Duration of Exposure", $h2);
        $table3->addRow();
        $table3->addCell(3066, $styleYellowHead)->addText("Amount Used", $h3);
        $table3->addCell(3066, $styleYellowHead)->addText("How many times per day", $h3);
        $table3->addCell(3066, $styleYellowHead)->addText("Duration", $h3);
        $table3->addRow();
        $table3->addCell(3066, $styleWhiteCell)->addText($data['assessment']->AmountUseds['name'], $p);
        $table3->addCell(3066, $styleWhiteCell)->addText($data['assessment']->TimePerDays['name'], $p);
        $table3->addCell(3066, $styleWhiteCell)->addText(htmlspecialchars($data['assessment']->Durations['name']), $p);

        $table4 = $section->addTable("SubstanceProperties");
        $table4->addRow();
        $table4->addCell(9200, $styleYellowHead9)->addText("Substance Properties", $h3);
        $table4->addRow();
        foreach ($init['substances'] as $k => $substance) {
            if ($k <= 7) {
                $table4->addCell(1533, $styleWhiteCell)->addImage("http://doug.portal.solutionhost.co.uk/apps2/public/assets/HealthSafety/Coshh/images/substance_properties/" . $substance['image'], array("width" => 35, "height" => 35));
            }
        }
        $table4->addRow();
        foreach ($init['substances'] as $k => $substance) {
            if ($k <= 7) {
                $table4->addCell(1533, $styleYellowHead)->addText($substance['name'], array("bold" => true, "size" => 7));
            }
        }
        $table4->addRow();
        foreach ($init['substances'] as $k => $substance) {
            if ($k <= 7) {

                foreach ($data['substances'] as $sub) {
                    $subs[] = $sub['Substances_id'];
                }
                if (in_array($substance, $subs)) {
                    $table4->addCell(1533, $styleWhiteCell)->addFormField('checkbox', array("align" => "right"), array("align" => "right"))->setValue($checked);
                } else {
                    $table4->addCell(1533, $styleWhiteCell)->addFormField('checkbox', array("align" => "right"), array("align" => "right"))->setValue(false);
                }
            }
        }
        $table4->addRow();
        foreach ($init['substances'] as $k => $substance) {
            if ($k > 7) {
                $table4->addCell(1533, $styleWhiteCell)->addImage("http://doug.portal.solutionhost.co.uk/apps2/public/assets/HealthSafety/Coshh/images/substance_properties/" . $substance['image'], array("width" => 35, "height" => 35));
            }
        }
        $table4->addRow();
        foreach ($init['substances'] as $k => $substance) {
            if ($k > 7) {
                $table4->addCell(1533, $styleYellowHead)->addText($substance['name'], array("bold" => true, "size" => 7));
            }
        }
        $table4->addRow();
        foreach ($init['substances'] as $k => $substance) {
            if ($k > 7) {

                foreach ($data['substances'] as $sub) {
                    $subs[] = $sub['Substances_id'];
                }
                if (in_array($substance, $subs)) {
                    $table4->addCell(1533, $styleWhiteCell)->addFormField('checkbox')->setValue($checked);
                } else {
                    $table4->addCell(1533, $styleWhiteCell)->addFormField('checkbox')->setValue(false);
                }
            }
        }

        $section->addText();
//        $sectionHazPpe = $this->word->addSection();
//        $this->header($sectionHazPpe, $data['assessment']['name'], "COSHH");
//        $this->footer($sectionHazPpe);
        //HAZARDS iDENTIFICATION AND PPE
//        $section1->addPageBreak();
        $tableRiskSafe = $section->addTable("risksafe");
        $tableRiskSafe->addRow();
        $tableRiskSafe->addCell(9200, $styleBlueHead2)->addText("Data Sheet", $h2);
        $tableRiskSafe->addRow();
        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Risk Phrases/ Safety Phrases", $h3);
        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Occupational Exposure Standard (OES)", $h3);

        $tableRiskSafe->addRow();
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
        $tableRiskSafe->addRow();

        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Maximum Exposure Limits (MEL)", $h3);
        $tableRiskSafe->addCell(4600, $styleYellowHead)->addText("Workplace Exposure Limits (WEL)", $h3);
        $tableRiskSafe->addRow();
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
//        $section1->addPageBreak();
        // Frequency & Duration of Exposure
//        $section5 = $this->word->addSection();
        $section->addPageBreak();
//        $section5->addPageBreak();
//        $this->header($section5, $data['assessment']['name'], "COSHH");
//        $this->footer($section5);
        //HAZARDS iDENTIFICATION AND PPE
        $table5 = $section->addTable("ControlMeasures");
        $table5->addRow();
        $table5->addCell(9200, $styleBlueHead2)->addText("Control Measures", $h2);
        $table5->addRow();
        $table5->addCell(4600, $styleYellowHead)->addText("Gerneral Precautions", $h3);
        $table5->addCell(4600, $styleYellowHead)->addText("First Aid Measures", $h3);
        $table5->addRow();
        $table5->addCell(4600, $styleWhiteTextCell)->addText(strip_tags($data['assessment']['general_precautions']), $p);
        $table5->addCell(4600, $styleWhiteTextCell)->addText(strip_tags($data['assessment']['first_aid_measures']), $p);
        // Frequency & Duration of Exposure
//        $sectionControl = $this->word->addSection();
        //HAZARDS iDENTIFICATION AND PPE
//        $tableControl = $sectionControl->addTable("Control");
        $table5->addRow();
        $table5->addCell(4600, $styleYellowHead)->addText("Further Controls Required", $h3);
        $table5->addCell(4600, $styleYellowHead)->addText("Responsibility", $h3);
        $table5->addRow();
        $table5->addCell(4600, $styleWhiteTextCell)->addText(strip_tags($data['assessment']['general_precautions']), $p);
        $table5->addCell(4600, $styleWhiteTextCell)->addText($data['assessment']['responsibility'], $p);
        $table5->addRow();
        $table5->addCell(4600, $styleYellowHead)->addText("By When", $h3);
        $table5->addCell(4600, $styleYellowHead)->addText("Date Done", $h3);
        $table5->addRow();
        $table5->addCell(4600, $styleWhiteTextCell)->addText($data['assessment']['by_when'], $p);
        $table5->addCell(4600, $styleWhiteTextCell)->addText();
        // Frequency & Duration of Exposure
//        $section = $this->word->addSection();
//        $this->header($sectionSpillFire, $data['assessment']['name'], "COSHH");
//        $this->footer($sectionSpillFire);
        //HAZARDS iDENTIFICATION AND PPE
        $section->addPageBreak();
        $tableSpillFire = $section->addTable("FireSpill");
        $tableSpillFire->addRow();
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Spillage Procedure", $h3);
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Fire Prevention", $h3);
        $tableSpillFire->addRow();
        $c1 = $tableSpillFire->addCell(4600, $styleWhiteTextCell);
        \PhpOffice\PhpWord\Shared\Html::addHtml($c1, $data['assessment']['spillage_procedure']);
//        $tableSpillFire->addCell(4600, $styleWhiteTextCell)->addText($data['assessment']['spillage_procedure'], $p);
        $c2 = $tableSpillFire->addCell(4600, $styleWhiteTextCell);
        \PhpOffice\PhpWord\Shared\Html::addHtml($c2, $data['assessment']['fire_prevention']);
//        $tableSpillFire->addCell(4600, $styleWhiteTextCell)->addText($data['assessment']['fire_prevention'], $p);
        $tableSpillFire->addRow();
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Handling and Storage", $h3);
        $tableSpillFire->addCell(4600, $styleYellowHead)->addText("Disposal Considerations", $h3);
        $tableSpillFire->addRow();
        $c3 = $tableSpillFire->addCell(4600, $styleWhiteTextCell);
        \PhpOffice\PhpWord\Shared\Html::addHtml($c3, $data['assessment']['handling_storage']);
//        $tableSpillFire->addCell(4600, $styleWhiteTextCell)->addText($data['assessment']['handling_storage'], $p);
        $c4 = $tableSpillFire->addCell(4600, $styleWhiteTextCell);
        \PhpOffice\PhpWord\Shared\Html::addHtml($c4, $data['assessment']['disposal_procedure']);
//        $tableSpillFire->addCell(4600, $styleWhiteTextCell)->addText($data['assessment']['disposal_procedure'], $p);
        // COSHH Assessment Comments
//        $section6 = $this->word->addSection();
//        $this->header($section6, $data['assessment']['name'], "COSHH");
//        $this->footer($section6);
        //COSHH Assessment Comments
        $section->addText();
        $table6 = $section->addTable("comments");
        $table6->addRow();
        $table6->addCell(9200, $styleBlueHead)->addText("COSHH Assessment Comments", $h2);
        $table6->addRow();

        $c5 = $table6->addCell(9200, $styleWhiteCell);
        \PhpOffice\PhpWord\Shared\Html::addHtml($c5, $data['assessment']['comments']);

//        $table6->addCell(9200, $styleWhiteCell)->addText($data['assessment']['comments'], $p);
        $section->addPageBreak();
//        $signOffSection = $this->word->addSection();
//        $this->header($sectionSpillFire, $data['assessment']['name'], "COSHH");
//        $this->footer($signOffSection);
        $signOffTable = $section->addTable("SignOffSheet");
        $signOffTable->addRow();
        $signOffTable->addCell(9200, $styleBlueHead3)->addText("Sign off Sheet", $h2);
        $signOffTable->addRow();
        $signOffTable->addCell(4700, $styleYellowHead)->addText("Assessor Summary", $h3);
        $signOffTable->addCell(1000, $styleYellowHead)->addText("Yes/No", $label);
        $signOffTable->addCell(3500, $styleYellowHead)->addText("Further Action", $label);
        $signOffTable->addRow();
        $signOffTable->addCell(4700, $styleYellowHead)->addText("1. Has the assessment considered all factors pertinent to the use of the substance? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow();
        $signOffTable->addCell(4700, $styleYellowHead)->addText("2. Has the assessment considered the practicability of preventing exposure? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow();
        $signOffTable->addCell(4700, $styleYellowHead)->addText("3. Has the assessment considered the steps to be taken to achieve and maintain adequate control of exposure where prevention is not reasonably practicable? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow();
        $signOffTable->addCell(4700, $styleYellowHead)->addText("4. Has the assessment considered the need for monitoring exposure to the substance? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();
        $signOffTable->addRow();
        $signOffTable->addCell(4700, $styleYellowHead)->addText("5. Has the assessment identified all action required to comply with regulations? If NO please give details of further action required", $p);
        $signOffTable->addCell(1000, $styleYellowHead)->addText();
        $signOffTable->addCell(3500, $styleWhiteCell)->addText();

        $signOffTable->addRow();
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
        $section->addPageBreak();
        $signOffTable2 = $section->addTable("SignOffSheet2");

        $signOffTable2->addRow();
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("The Task process should be re-assessed on a regular basis either annually, or if there are significant changes to the task or process or if there is a significant change in personnel who carry it out it e.g. young/inexperienced workers, pregnancy, workers with pre existing conditions such as asthma, dermatitis etc", $p);
        $signOffTable2->addRow(150);
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("Signed Assessor:", $label);
        $signOffTable2->addRow(150);
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("Date:", $label);
        $signOffTable2->addRow();
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("This Assessment has been discussed with the user their line manager and action agreed", $p);
        $signOffTable2->addRow(150);

        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("Signed Assessor:", $label);
        $signOffTable2->addRow();
//        $innerRun0->addTextBreak(4, null, array("lineHeight"=> 10));
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("Signed User:", $label);
        $signOffTable2->addRow();
//        $innerRun1->addTextBreak(4, null, array("lineHeight"=> 10));
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("Signed Line Manager:", $label);
        $signOffTable2->addRow();
//        $innerRun2->addTextBreak(4, null, array("lineHeight"=> 10));
        $signOffTable2->addCell(9200, $styleWhiteCell3)->addText("Date:", $label);
//        $innerRun3->addTextBreak(4, null, array("lineHeight"=> 10));
//        $signTBL->addRow();

        \PhpOffice\PhpWord\Settings::setPdfRendererPath(APP . '../vendor/dompdf/dompdf/autoload.inc.php');
        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/Coshh/output/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }

//        / Produce file and download
        $file = str_replace(" ", "-", $data['assessment']['name']) . '.docx';
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

    public function method($methodStatement, $methods, $methodStatementDatas) {
        $returnArray = array();
        foreach ($methodStatementDatas as $data) {
            $returnArray[$data['MethodSheets_id']]['Sheet']['name'] = $data->MethodSheets['name'];
            $returnArray[$data['MethodSheets_id']]['Sheet']['description'] = $data->MethodSheets['description'];
            $returnArray[$data['MethodSheets_id']]['Sections'][] = array(
                "name" => $data->MethodSections['name'],
                "description" => $data->MethodSections['description']
            );
        }



        $sheets = $returnArray;
        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 50
        );

        $red = "FF0000";
        $blue = "0000FF";

        $h2 = array('size' => 17, 'bold' => true);
        $center = array('align' => "center", "spaceBefore" => 80, "spaceAfter" => 80);
        $h3 = array('size' => 13, 'bold' => true);
        $h2Red = array('size' => 17, 'bold' => true, 'color' => $red);
        $h3Red = array('size' => 13, 'bold' => true, 'color' => $red);
        $h4Red = array('size' => 12, 'bold' => true, 'color' => $red);
        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
        $headPara = array("align" => "center");
        $headCell = array("valign" => "center");
        $yellowCell = array("valign" => "center", "bgColor" => "cccccc");
        $label = array('size' => 10, "bold" => true);
        $pRed = array('size' => 10, 'color' => 'ff0000');
        $p = array('size' => 10, 'color' => '000000');

        $labelP = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 80);
        $paragraphStyle = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 150);

        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');

        $this->word->addTableStyle('DetailsTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('SignOffTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('EmergencyTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('FooterTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('SignOffSheet', $tableStyle, $styleBlueHead);


        $section = $this->word->addSection();

        $this->header($section, $methodStatement['name'], "METHOD STATEMENT");
        $this->footer($section, "METHOD STATEMENT");


        $section->addText(htmlspecialchars("Method Statement Information"), $h2Red, $center);
        $section->addText(htmlspecialchars("Disclaimer please delete before use"), $h3Red);
        $section->addText(htmlspecialchars("The details provided in this example method statement are intended as a guide only, the hazards and control procedures listed are not a comprehensive list.  You must ensure that you carry out a risk assessment to determine and control the significant hazards that will be present in your particular circumstance.  All information and advice is given in good faith. We cannot accept any responsibility for your subsequent acts or omissions. If you have any doubts queries or concerns, you should refer to the relevant regulations and take further professional advice."), $pRed, $paragraphStyle);
        $section->addText(htmlspecialchars("Please delete all red text prior to use."), $pRed, $paragraphStyle);
        $section->addPageBreak();

        $section->addText("Your company address", $label, $labelP);
        $section->addText("Address:", $label, $labelP);
        $section->addText("Tel:", $label, $labelP);
        $section->addText("Fax:", $label, $labelP);
        $section->addText("Mobile:", $label, $labelP);

        $detailsTable = $section->addTable("DetailsTable");
        $detailsTable->addRow();
        $detailsTable->addCell(4600, $yellowCell)->addText("Project / Contract", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow();
        $detailsTable->addCell(4600, $yellowCell)->addText("Contractor", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow();
        $detailsTable->addCell(4600, $yellowCell)->addText("Site Address", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow();
        $detailsTable->addCell(4600, $yellowCell)->addText("Project Start Date", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow();
        $detailsTable->addCell(4600, $yellowCell)->addText("Expected Duration", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow();
        $detailsTable->addCell(4600, $yellowCell)->addText("Projected Completed Date", $label);
        $detailsTable->addCell(4600);
        $section->addText();

        $signOffTable = $section->addTable("SignOffTable");
        $signOffTable->addRow();
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840, $yellowCell)->addText("Name", $label);
        $signOffTable->addCell(1840, $yellowCell)->addText("Title", $label);
        $signOffTable->addCell(1840, $yellowCell)->addText("Signature", $label);
        $signOffTable->addCell(1840, $yellowCell)->addText("Date", $label);
        $signOffTable->addRow();
        $signOffTable->addCell(1840, $yellowCell)->addText("Document Author", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addRow();
        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addRow();
        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addRow();
        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by (for Client)", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $section->addText();

        $emergencyContactTable = $section->addTable("EmergencyTable");
        $emergencyContactTable->addRow();
        $emcTRow1 = $emergencyContactTable->addCell(9200, $yellowCell);
        $emcTRow1->getStyle()->setGridSpan(4);
        $emcTRow1->addText("Emergency Contact Details", $label);
        $emergencyContactTable->addRow();
        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Contact", $label);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addRow();
        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Tel", $label);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addRow();
        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Mobile", $label);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);

        $section->addText(htmlspecialchars("Data Protection Statement"), $h3, $center);
        $section->addText(htmlspecialchars("The information and data provided herein shall not be duplicated, disclosed or disseminated by the recipient in whole or in part for any purpose whatsoever without the prior written permission from YOUR COMPANY"), $p, $paragraphStyle);
        $section->addText(htmlspecialchars("Use this page to highlight the significant hazards your staff and others will be exposed to, also highlight the most important preventative/control measures that must be taken, HAZARDS and CONTROL MEASURES will be taken from your RISK ASSESSMENT.  You can also highlight quality and environmental issues.  The following are examples; please delete/enter your own"), $pRed);

        $section->addPageBreak();
// End static Top bit
// Start Dynamic bit 
// SHeets

        foreach ($sheets as $sheet) {
            $pattern = "/<a.+href=['|\"]([^\"\']*)['|\"].*>(.+)<\/a>/";
            $replacement = '<br></br><strong style="color: #0000ff">\1</strong>';
            $section->addText(htmlspecialchars($sheet['Sheet']['name']), $h2, $center);
//            $repla = preg_replace($pattern, $replacement, $sheet['Sheet']['description']);
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, $sheet['Sheet']['description']);
            foreach ($sheet['Sections'] as $sectionData) {
//                print_r($sectionData['name']);
                if (strpos($sectionData['name'], "(")) {

                    $parts = explode("(", $sectionData['name']);
                    foreach ($parts as $key => $part) {
                        if ($key == 0) {
                            $section->addText(htmlspecialchars($part), $h3);
                        } else {
                            $section->addText(htmlspecialchars("(" . $part), $h3Red);
                        }
                    }
                } else {
                    $section->addText(htmlspecialchars($sectionData['name']), $h3);
                }
                $description = preg_replace($pattern, $replacement, $sectionData['description']);
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, trim($description));
            }
        }




//Methods    

        $section->addText(htmlspecialchars("Methods"), $h2, $center);
        foreach ($methods as $method) {
            $section->addText(htmlspecialchars($method['name']), $h3);
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, trim(preg_replace('/\t+/', '', $method['description'])));
        }

        $section->addPageBreak();
        //Sign off sheet

        $lineDeco = array('weight' => 1, 'width' => 150, 'height' => 1, 'color' => 635552, "dash" => "rounddot");

        $section->addText("Sign Off Sheet", $h2, $center);
        $section->addText(htmlspecialchars("I have read and understood the contents of this Method Statement. Anything I did not understand has been explained to me to my satisfaction. I agree to follow the Method Statement and understand that any instructions are provided for my safety and the safety of others."), $p, $paragraphStyle);
        $signoff = $section->addTable("SignOffSheet");
        $signoff->addRow();
        $signoff->addCell(3066)->addText("Print Name", $label, $center);
        $signoff->addCell(3066)->addText("Signed", $label, $center);
        $signoff->addCell(3066)->addText("Date", $label, $center);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addRow();
        $signoff->addCell(3066);
        $signoff->addCell(3066);
        $signoff->addCell(3066);



// End Dynamic bit        
// Start build and download      
        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/MS/output/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
// Produce file and download
        $file = str_replace(" ", "-", $methodStatement['name']) . '.docx';
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

    public function risk($riskAssessment, $hazards) {

        $fullWidth = 14000;

        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 100,
            "spaceAfter" => 150,
        );

        $red = "FF0000";
        $blue = "0000FF";

        $h2 = array('size' => 17, 'bold' => true);
        $center = array('align' => "center", "spaceBefore" => 80, "spaceAfter" => 80);
        $h3 = array('size' => 15, 'bold' => true);
        $h3Red = array('size' => 14, 'bold' => true, 'color' => $red);
        $h4Red = array('size' => 12, 'bold' => true, 'color' => $red);
        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
        $headPara = array("align" => "center");
        $headCell = array("valign" => "center");
        $yellowCell = array("valign" => "center", "bgColor" => "fcf8e3");
        $label = array('size' => 10, 'bold' => true);
        $pRed = array('size' => 10, 'color' => 'ff0000');
        $pRedBold = array('size' => 10, 'color' => 'ff0000', "bold" => true);
        $p = array('size' => 10, 'color' => '000000');
        $pBold = array('size' => 10, "bold" => true, 'color' => '000000');
        $labelP = array("widowControl" => false, "spaceBefore" => 40, "spaceAfter" => 40);
        $paragraphStyle = array("widowControl" => false, "spaceBefore" => 40, "spaceAfter" => 70);
        $rowStyle = array('cantSplit' => true);
        $rowStyleRepeat = array('cantSplit' => true, "tblHeader" => true);

        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');

        $this->word->addTableStyle('AssessorTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('FooterTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('KeyTable', $tableStyle, $styleBlueHead);

        $section = $this->word->addSection(array("orientation" => "landscape"));
        $this->header($section, htmlspecialchars($riskAssessment['name']), "RISK ASSESSMENT");
        $this->footer($section, "RISK ASSESSMENT");

        $section->addText("RISK ASSESSMENT INFORMATION", $h3Red, $headPara);
        $section->addText("Risk Assessment Examples", $pRed, $paragraphStyle);
        $section->addText("The following document is an example of a risk assessment carried out for a specific task.", $pRed, $paragraphStyle);
        $section->addText("Please now carefully examine the risk assessment and ensure that you add any further significant hazards associated with the task you are carrying out, these hazards must then be controlled and risk ranked to ensure that the risks of injury and ill health are reduced to a tolerable level.", $pRed, $paragraphStyle);
        $section->addText("Please remove any hazards and control procedures that do not apply to your task.  Remember, you will be expected to implement all control procedures listed in your risk assessment.", $pRed, $paragraphStyle);
        $section->addText("This document should then be used in conjunction with the associated method statement to provide a comprehensive set of safety documentation for the task you are carrying out.", $pRed, $paragraphStyle);
        $textrun = $section->addTextRun($paragraphStyle);
        $textrun->addText("Please read the document RA_training_183.pdf supplied with this risk assessment or go to ", $pRed, $paragraphStyle);
        $textrun->addLink('https://www.hsdirect.co.uk/free-info/risk-assessment.html', "www.hsdirect.co.uk/free-info/risk-assessment.html ");
        $textrun->addText("for the web version.  This document will give you comprehensive information on how to complete a risk assessment including risk ranking.", $pRed, $paragraphStyle);
        $section->addText("If you are still unsure and/or you are dealing with tasks of a very hazardous nature we advise that you seek further professional advice.", $pRed, $paragraphStyle);
        $section->addText("The logo, Task Description, Location, Date of review, Company address, etc are in the document header.", $pRed, $paragraphStyle);
        $section->addText("To change these details simply double click over the top of the logo which will open the header and footer for editing.", $pRed, $paragraphStyle);
        $section->addText("The last page of this document should be used to fill in any other hazards and control procedures that become apparent on site.", $pRed, $paragraphStyle);
        $section->addText("Disclaimer", $h4Red, $paragraphStyle);
        $section->addText("All information and advice is given in good faith. We cannot accept any responsibility for your subsequent acts or omissions. If you have any doubts queries or concerns, you should refer to the relevant regulations and take further professional advice.", $pRedBold, $paragraphStyle);
        $section->addText("Please delete all red text prior to use.", $pRedBold, $paragraphStyle);

        $assessorTable = $section->addTable("AssessorTable");
        $assessorTable->addRow();
        $assessorTable->addCell($fullWidth/3, $yellowCell)->addText("Assessor's Name", $pBold);
        $assessorTable->addCell($fullWidth/3, $yellowCell)->addText("Assessor's Signature", $pBold);
        $assessorTable->addCell($fullWidth/3, $yellowCell)->addText("Persons Affected By This Risk Assessment", $pBold);
        $assessorTable->addRow();
        $assessorTable->addCell($fullWidth/3);
        $assessorTable->addCell($fullWidth/3);
        $assessorTable->addCell($fullWidth/3);

//        $section->addPageBreak();
        $section->addText();
//
//
//        ///This is just to repeat the table headers on each page instead of having load of the same.
        $this->word->addTableStyle(htmlspecialchars("HazardsTable"), $tableStyle, $styleBlueHead);
        $hazardTable = $section->addTable("HazardsTable");
        $hazardTable->addRow(100, $rowStyleRepeat);
        $hazardTable->addCell(($fullWidth/10)*2, $yellowCell)->addText("Hazard / Consequences", $pBold, $center);
        $hazardTable->addCell(($fullWidth/10)*5, $yellowCell)->addText("Control Procedures", $pBold, $center);
        $cell1 = $hazardTable->addCell($fullWidth/10, $yellowCell);
            $cell1->addText("Likelihood", $pBold, $center);
            $cell1->addText("(a)", $pBold, $center);
        $cell2 = $hazardTable->addCell($fullWidth/10, $yellowCell);
            $cell2->addText("Severity", $pBold, $center);
            $cell2->addText("(b)", $pBold, $center);
        $cell3 = $hazardTable->addCell($fullWidth/10, $yellowCell); 
            $cell3->addText("Risk Ranking", $pBold, $center);
            $cell3->addText("(a x b)", $pBold, $center);

        foreach ($hazards as $hazard) {

            //This is for table header for every table
//            $this->word->addTableStyle(trim(htmlspecialchars(str_replace(" ", "", $hazard['name']))), $tableStyle, $styleBlueHead);
//            $hazardTable = $section->addTable(trim(htmlspecialchars(str_replace(" ", "", $hazard['name']))));
//            $hazardTable->addRow(100, $rowStyle);
//            $hazardTable->addCell(1800, $yellowCell)->addText("Hazard / Consequences", $pBold, $center);
//            $hazardTable->addCell(3700, $yellowCell)->addText("Control Procedures", $pBold, $center);
//            $hazardTable->addCell(1300, $yellowCell)->addText("Likelihood (a)", $pBold, $center);
//            $hazardTable->addCell(1200, $yellowCell)->addText("Severity (b)", $pBold, $center);
//            $hazardTable->addCell(1200, $yellowCell)->addText("Risk Ranking (a x b)", $pBold, $center);

            $hazardTable->addRow(100, $rowStyle);

            $hazCell = $hazardTable->addCell(($fullWidth/10)*2);
            $hazCell->getStyle()->setVAlign("center");
            $hazCell->addText(htmlspecialchars($hazard['name']), $pBold, $center);
            $consecell = $hazardTable->addCell(($fullWidth/10)*5);
            \PhpOffice\PhpWord\Shared\Html::addHtml($consecell, trim(preg_replace('/\t+/', '', $hazard['description'])));

            $likeCell = $hazardTable->addCell($fullWidth/10);
            $likeCell->getStyle()->setVAlign("center");
            $likeCell->addText($hazard['likelihood'], $pBold, $center);

            $sevCell = $hazardTable->addCell($fullWidth/10);
            $sevCell->getStyle()->setVAlign("center");
            $sevCell->addText($hazard['severity'], $pBold, $center);

            $riskRankCell = $hazardTable->addCell($fullWidth/10, array("bgColor" => $this->getRiskColour($hazard['risk_ranking'])));
            $riskRankCell->getStyle()->setVAlign("center");
            $riskRankCell->addText($hazard['risk_ranking'], $pBold, $center);
        }
        for($inc = 1; $inc <= 2; $inc++){
            $hazardTable->addRow(100, $rowStyle);
            $hazCell = $hazardTable->addCell(($fullWidth/10)*2);
            $hazCell->getStyle()->setVAlign("center");
            $hazCell->addText(null, $pBold, $center);
            $hazardTable->addCell(($fullWidth/10)*5)->addText("");
            

            $likeCell = $hazardTable->addCell($fullWidth/10);
            $likeCell->getStyle()->setVAlign("center");
            $likeCell->addText("", $pBold, $center);

            $sevCell = $hazardTable->addCell($fullWidth/10);
            $sevCell->getStyle()->setVAlign("center");
            $sevCell->addText("", $pBold, $center);

            $riskRankCell = $hazardTable->addCell($fullWidth/10);
            $riskRankCell->getStyle()->setVAlign("center");
            $riskRankCell->addText("", $pBold, $center);
        }
        
        
        $section->addText();
        $key = $section->addTable("KeyTable");
        $key->addRow(100);
        $key->addCell($fullWidth/12, $yellowCell)->addText();
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Likelihood", $pBold);
        $key->addCell($fullWidth/12, $yellowCell)->addText();
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Severity", $pBold);
        $key->addCell($fullWidth/12, $yellowCell)->addText();
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Priority", $pBold);
        $key->addRow(100);
        $key->addCell($fullWidth/12, $yellowCell)->addText(1, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Highly Unlikely");
        $key->addCell($fullWidth/12, $yellowCell)->addText(1, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Trivial");
        $key->addCell($fullWidth/12, $yellowCell)->addText(1, $label, $center);
        $key->addCell(($fullWidth/12)*3, array("valign" => "center", "bgColor" => "00ff00"))->addText("Very Low Priority– No Action required (Risk no 1)");
        $key->addRow(100);
        $key->addCell($fullWidth/12, $yellowCell)->addText(2, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Unlikely");
        $key->addCell($fullWidth/12, $yellowCell)->addText(2, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Minor Injury");
        $key->addCell($fullWidth/12, $yellowCell)->addText(2, $label, $center);
        $key->addCell(($fullWidth/12)*3, array("valign" => "center", "bgColor" => "ffcc00"))->addText("Low Priority – Risk no (2 – 4)");
        $key->addRow(100);
        $key->addCell($fullWidth/12, $yellowCell)->addText(3, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Possible");
        $key->addCell($fullWidth/12, $yellowCell)->addText(3, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Over 3 day Injury");
        $key->addCell($fullWidth/12, $yellowCell)->addText(3, $label, $center);
        $key->addCell(($fullWidth/12)*3, array("valign" => "center", "bgColor" => "ff9933"))->addText("Medium Priority – (Risk no 5 – 9)");
        $key->addRow(100);
        $key->addCell($fullWidth/12, $yellowCell)->addText(4, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Probable");
        $key->addCell($fullWidth/12, $yellowCell)->addText(4, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Major Injury");
        $key->addCell($fullWidth/12, $yellowCell)->addText(4, $label, $center);
        $key->addCell(($fullWidth/12)*3, array("valign" => "center", "bgColor" => "ff3300"))->addText("High Priority – (Risk no 10 – 12)");
        $key->addRow(100);
        $key->addCell($fullWidth/12, $yellowCell)->addText(5, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Certain");
        $key->addCell($fullWidth/12, $yellowCell)->addText(5, $label, $center);
        $key->addCell(($fullWidth/12)*3, $yellowCell)->addText("Incapacity or Death");
        $key->addCell($fullWidth/12, $yellowCell)->addText(5, $label, $center);
        $key->addCell(($fullWidth/12)*3, array("valign" => "center", "bgColor" => "ff0000"))->addText("Urgent action – (Risk no 15 – 25)");

        $section->addPageBreak();

        $section->addText("Further Information", $h2, $center);
        $section->addText("The example document provided by HS Direct Ltd is supplied as a guide to enable you to complete your Risk Assessment.", $p, $paragraphStyle);
        $section->addText("We strongly recommend that you thoroughly read, edit and change your document. Control procedures within the document make reference to the appropriate method statement, and also COSHH assessments. All editing should be made by a competent person. If you are not competent to carry out this task you should seek training or professional assistance to enable you to carry out a suitable and sufficient assessment.", $p, $paragraphStyle);
        $section->addText("A method statement should be completed for each of the risk assessments you have, enabling you to provide employees with a written safe system of work.", $p, $paragraphStyle);
        $section->addText("All chemicals used must have a corresponding COSHH assessment.", $p, $paragraphStyle);
        $section->addText("If your organisation has 5 or more employees, that includes all staff, not just site workers, then you also require by law a written health and safety policy. ", $p, $paragraphStyle);
        $section->addText("HS direct Ltd take great pride in providing help and assistance with all health and safety related paperwork for over 1500 clients throughout the UK using our Safety First Package. ", $p, $paragraphStyle);
        $section->addText("To get further information on the entire range of products and services we can assist you with please do not hesitate to call us on 0114 2444461. Our offices are open 8am – 9pm Monday to Friday, and 9am - 5 pm at weekends. ", $p, $paragraphStyle);


        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/MS/output/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
        // Produce file and download
        $file = stripslashes(str_replace('/', "", str_replace(" ", "-", $riskAssessment['name']))) . '.docx';
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

    public function header($section, $docName, $type) {

        if ($type == "RISK ASSESSMENT") {
            $fullWidth = 14000;
        } else {
            $fullWidth = 9200;
        }

        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 50
        );

        $red = "FF0000";
        $blue = "0000FF";
        $h3 = array('size' => 15, 'bold' => true);
        $h3Red = array('size' => 15, 'bold' => true, 'color' => $red);
        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
        $headPara = array("align" => "center");
        $headCell = array("valign" => "center");
        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
        $this->word->addTableStyle('HeadTable', $tableStyle, $styleBlueHead);



        $header = $section->addHeader();
//        $header->addText("COMPANY LOGO");
        $headTable = $header->addTable("HeadTable");
        $headTable->addRow();
        $logoCell = $headTable->addCell($fullWidth / 3, $headCell);
        $logoCell->getStyle()->setVMerge("restart");
        $logoCell->addText("LOGO", $h3Blue, $headPara);
        $headTable->addCell(($fullWidth / 3) * 2, $headCell)->addText("INSERT COMPANY NAME", $h3Red, $headPara);
        $headTable->addRow();
        $cell2 = $headTable->addCell($fullWidth / 3, $headCell);
        $cell2->getStyle()->setVMerge("continue");
        $headTable->addCell(($fullWidth / 3) * 2)->addText($type, $h3Blue, $headPara);
        $headTable->addRow();
        $logoCell3 = $headTable->addCell($fullWidth / 3, $headCell);
        $logoCell3->getStyle()->setVMerge("continue");
        $headTable->addCell(($fullWidth / 3) * 2)->addText(htmlspecialchars($docName), $h3, $headPara);
        $header->addText();
        return $header;
    }

    public function footer($section, $type) {

        $headCell = array("valign" => "center");
        $label = array('size' => 10, 'bold' => true);
        $pRed = array('size' => 10, 'color' => 'ff0000');
        $p = array('size' => 10, 'color' => '000000');


        if ($type == "RISK ASSESSMENT") {
            $fullWidth = 14000;
        } else {
            $fullWidth = 9200;
        }


//Footer
        $footer = $section->addFooter();
        $footTable = $footer->addTable("FooterTable");
        $footTable->addRow(50);
        $footTable->addCell($fullWidth/6, $headCell)->addText("Series", $p);
        $footTable->addCell($fullWidth/6, $headCell);
        $footTable->addCell($fullWidth/6, $headCell)->addText("Series and Number", $p);
        $footTable->addCell($fullWidth/6, $headCell)->addText("Number System", $pRed);
        $footTable->addCell($fullWidth/6, $headCell)->addText("Issue Date", $p);
        $footTable->addCell($fullWidth/6, $headCell)->addText("Enter Date", $pRed);
        $footTable->addRow(50);
        $footTable->addCell($fullWidth/6)->addText("Revision Number", $p);
        $footTable->addCell($fullWidth/6);
        $footTable->addCell($fullWidth/6)->addText("Revision Date", $p);
        $footTable->addCell($fullWidth/6);
        $pageNumberCell = $footTable->addCell(($fullWidth/6)*2);
        $pageNumberCell->getStyle()->setGridSpan(2);
        $pageNumberCell->getStyle()->setValign("center");

        $pageNumberCell->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'), $label, array('align' => 'center'));
        return $footer;
    }

    public function getRiskColour($number) {
        switch (true) {
            case $number < 2 || $number === null || $number === false || $number === 0 || $number === "0":
                $colour = "00ff00";
                break;
            case $number >= 2 && $number <= 4:
                $colour = "ffcc00";
                break;
            case $number >= 5 && $number <= 9:

                $colour = "ff9933";
                break;
            case $number >= 10 && $number <= 14:
                $colour = "ff3300";
                break;
            case $number >= 15;
                $colour = "ff0000";
                break;
            default:
                $colour = '000000';
                break;
        }
        return $colour;
    }

}
