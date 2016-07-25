<?php

namespace SolutionMvc\Healthsafety\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Healthsafety\Model\Method;

class MethodController extends Controller {

    protected $method;
    protected $token;
    protected $word;

    public function __construct() {
        parent::__construct();
        $this->token = $this->getToken();
        $this->method = new Method();
    }

    public function indexAction() {
        echo $this->twig->render('HealthSafety/Documents/MethodStatement/index.html.twig', array(
            "data" => $this->method->getMethodStatements()
        ));
    }

    public function viewAction($id) {
        if($this->getToken()){
        $methodStatementDatas = $this->method->getMethodStatements_MethodSheets($id);
        $returnArray = array();
        foreach ($methodStatementDatas as $data) {
            $returnArray[$data['MethodSheets_id']]['Sheet'] = $data->MethodSheets;
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
        }else{
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "view/$id"
            ));
        }
    }

    public function createAction() {
        if ($this->requestType() == "GET" && $this->getToken()) {
            echo $this->twig->render("HealthSafety/Documents/MethodStatement/create.html.twig", array(
                "sheets" => $this->method->getAllSheets(),
            ));
        } elseif ($this->requestType() == "POST" && $this->getToken()) {
            $msID = $this->method->setMethodStatement($this->requestObject(), $this->token->user->id);
            $this->method->setMethodSheets($this->requestObject(), $this->token->user->id, $msID);
            $this->method->setMethods($this->requestObject(), $msID, $this->token->user->id);
            header('Location: ' . HTTP_ROOT . 'HealthSafety/Method/view/' . $msID);
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "create"
            ));
        }
    }

    public function updateAction($id) {
        if($this->requestType() == 'GET' && $this->getToken()){
        $methodStatementDatas = $this->method->getMethodStatements_MethodSheets($id);
        $returnArray = array();
        foreach ($methodStatementDatas as $data) {
            $returnArray[$data['MethodSheets_id']]['Sheet'] = $data->MethodSheets;
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
        }elseif($this->requestType() == 'POST' && $this->getToken()){
            $this->retireAction($id);
            $msID = $this->method->setMethodStatement($this->requestObject(), $this->token->user->id);
            $this->method->setMethodSheets($this->requestObject(), $this->token->user->id, $msID);
            $this->method->setMethods($this->requestObject(), $msID, $this->token->user->id);
            header('Location: ' . HTTP_ROOT . 'HealthSafety/Method/view/' . $msID);
        }else{
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "HealthSafety/",
                "controller" => "Method/",
                "action" => "update/$id"
            ));
        }
    }

    public function retireAction($id) {
        if($this->getToken()){
            $this->method->setRetire($id, $this->token->user->id);
        }
    }

    public function documentAction($id) {
//Load PhpWord

        require_once APP . '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
        \PhpOffice\PhpWord\Autoloader::register();
        $this->word = new \PhpOffice\PhpWord\PhpWord();
        $this->word->setDefaultFontName('Helvetica');
        $this->word->setDefaultFontSize(10);

        $methodStatementDatas = $this->method->getMethodStatements_MethodSheets($id);
        $returnArray = array();
        foreach ($methodStatementDatas as $data) {
            $returnArray[$data['MethodSheets_id']]['Sheet'] = $data->MethodSheets;
            $returnArray[$data['MethodSheets_id']]['Sections'][] = array(
                "name" => $data->MethodSections['name'],
                "description" => $data->MethodSections['description']
            );
        }
        $methodStatement = $this->method->getMethodStatement($id);
        $sheets = $returnArray;
        $methods = $this->method->getMethods($id);

        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 100
        );

        $red = "FF0000";
        $blue = "0000FF";

        $h2 = array('size' => 17, 'bold' => true);
        $center = array('align' => "center", "spaceBefore" => 80, "spaceAfter" => 80);
        $h3 = array('size' => 13, 'bold' => true);
        $h3Red = array('size' => 13, 'bold' => true, 'color' => $red);
        $h4Red = array('size' => 12, 'bold' => true, 'color' => $red);
        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
        $headPara = array("align" => "center");
        $headCell = array("valign" => "center");
        $yellowCell = array("valign" => "center", "bgColor" => "cccccc");
        $label = array('size' => 10, 'bold' => true);
        $pRed = array('size' => 10, 'color' => 'ff0000');
        $p = array('size' => 10, 'color' => '000000');

        $labelP = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 80);
        $paragraphStyle = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 150);

        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
//        $this->word->addTableStyle('HeadTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('DetailsTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('SignOffTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('EmergencyTable', $tableStyle, $styleBlueHead);
        $this->word->addTableStyle('FooterTable', $tableStyle, $styleBlueHead);


        $section = $this->word->addSection();
//Header
//        $header = $section->addHeader();

        $this->header($section, $methodStatement['name']);
        $this->footer($section);


//        $addressSection = $this->word->addSection();

        $section->addText("Your company address", $label, $labelP);
        $section->addText("Address:", $label, $labelP);
        $section->addText("Tel:", $label, $labelP);
        $section->addText("Fax:", $label, $labelP);
        $section->addText("Mobile:", $label, $labelP);

//        $detailsSection = $this->word->addSection();
        $detailsTable = $section->addTable("DetailsTable");
        $detailsTable->addRow(400);
        $detailsTable->addCell(4600, $yellowCell)->addText("Project / Contract", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow(400);
        $detailsTable->addCell(4600, $yellowCell)->addText("Contractor", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow(400);
        $detailsTable->addCell(4600, $yellowCell)->addText("Site Address", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow(400);
        $detailsTable->addCell(4600, $yellowCell)->addText("Project Start Date", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow(400);
        $detailsTable->addCell(4600, $yellowCell)->addText("Expected Duration", $label);
        $detailsTable->addCell(4600);
        $detailsTable->addRow(400);
        $detailsTable->addCell(4600, $yellowCell)->addText("Projected Completed Date", $label);
        $detailsTable->addCell(4600);
        $section->addText();

//        $signOffSection = $this->word->addSection();
        $signOffTable = $section->addTable("SignOffTable");
        $signOffTable->addRow(400);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840, $yellowCell)->addText("Name", $label);
        $signOffTable->addCell(1840, $yellowCell)->addText("Title", $label);
        $signOffTable->addCell(1840, $yellowCell)->addText("Signature", $label);
        $signOffTable->addCell(1840, $yellowCell)->addText("Date", $label);
        $signOffTable->addRow(400);
        $signOffTable->addCell(1840, $yellowCell)->addText("Document Author", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addRow(400);
        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addRow(400);
        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addRow(400);
        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by (for Client)", $label);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $signOffTable->addCell(1840);
        $section->addText();

//        $emergencyContactSection = $this->word->addSection();
        $emergencyContactTable = $section->addTable("EmergencyTable");
        $emergencyContactTable->addRow();
        $emcTRow1 = $emergencyContactTable->addCell(9200, $yellowCell);
        $emcTRow1->getStyle()->setGridSpan(4);
        $emcTRow1->addText("Emergency Contact Details", $label);
        $emergencyContactTable->addRow(400);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addRow(400);
        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Contact", $label);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addRow(400);
        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Tel", $label);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addRow(400);
        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Mobile", $label);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);
        $emergencyContactTable->addCell(2300);

        $section->addText("Data Protection Statement", $h3, $center);
        $section->addText("The information and data provided herein shall not be duplicated, disclosed or disseminated by the recipient in whole or in part for any purpose whatsoever without the prior written permission from YOUR COMPANY", $p, $paragraphStyle);

        $section->addText("Disclaimer please delete before use", $h3Red, $center);
        $section->addText("The details provided in this example method statement are intended as a guide only, the hazards and control procedures listed are not a comprehensive list. You must ensure that you carry out a risk assessment to determine and control the significant hazards that will be present in your particular circumstance. All information and advice is given in good faith. We cannot accept any responsibility for your subsequent acts or omissions. If you have any doubts queries or concerns, you should refer to the relevant regulations and take further professional advice.", $pRed, $paragraphStyle);
        $section->addText("Please delete all red text prior to use.", $pRed, $paragraphStyle);
        $section->addPageBreak();
// End static Top bit
// Start Dynamic bit 
// SHeets

        foreach ($sheets as $sheet) {
            $section->addText(htmlspecialchars($sheet['Sheet']['name']), $h2, $center);
            \PhpOffice\PhpWord\Shared\Html::addHtml($section, htmlspecialchars(str_replace("&", "and", $sheet['Sheet']['description'])));
            foreach ($sheet['Sections'] as $sectionData) {
                if (strpos($sectionData['name'], "(")) {
                    $parts = explode("(", $sectionData['name']);

                    foreach ($parts as $key => $part) {
                        if ($key == 0) {
                            $section->addText(htmlspecialchars($part), $h3);
                        } else {
                            $section->addText(htmlspecialchars("(".$part), $h3Red);
                        }
                    }
                } else {
                    $section->addText(htmlspecialchars($sectionData['name']), $h3);
                }
                \PhpOffice\PhpWord\Shared\Html::addHtml($section, str_replace(array("&"), "and", trim(preg_replace('/\t+/', '', $sectionData['description']))));
            }
            $section->addPageBreak();
        }

        
        
        
//Methods    
//    <h1>Methods</h1>
//    {% for method in methods %}
//        <h3>{{method.name}}</h3>
//        <p>{{method.description | raw }}</p>
//    {% endfor %}
        $section->addText(htmlspecialchars("Methods"), $h2, $center);
        foreach ($methods as $method) {
            $section->addText(htmlspecialchars($method['name']), $h3);
//            $section->addText(htmlspecialchars($method['description']));

            \PhpOffice\PhpWord\Shared\Html::addHtml($section, str_replace(array("&"), "", trim(preg_replace('/\t+/', '', $method['description']))));
        }


// End Dynamic bit        
// Start build and download      
        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/MS/output/";
        if (!file_exists($dir)) {
            mkdir($dir, 0777);
        }
// Produce file and download
        $file = $methodStatement['name'] . '.docx';
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

    public function header($section, $msName) {

        $tableStyle = array(
            'width' => 100,
            'borderColor' => '999999',
            'borderSize' => 6,
            'cellMargin' => 100
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
//        $headTable->addRow(400);
//        $headTable->addCell(3060)->addText("INSERT COMPANY NAME", $h3Red, $headPara);
//        $headTable->addCell(3060)->addText("METHOD STATEMENT", $h3Blue, $headPara);
//        $headTable->addCell(3060)->addText(htmlspecialchars($msName), $h3, $headPara);
        $headTable->addRow(400);
        $logoCell = $headTable->addCell(2600, $headCell);
        $logoCell->getStyle()->setVMerge("restart");
        $logoCell->addText("LOGO", $h3Blue, $headPara);
        $headTable->addCell(6600, $headCell)->addText("INSERT COMPANY NAME", $h3Red, $headPara);
        $headTable->addRow(400);
        $cell2 = $headTable->addCell(2600, $headCell);
        $cell2->getStyle()->setVMerge("continue");
        $headTable->addCell(6600)->addText("METHOD STATEMENT", $h3Blue, $headPara);
        $headTable->addRow(400);
        $logoCell3 = $headTable->addCell(2600, $headCell);
        $logoCell3->getStyle()->setVMerge("continue");
        $headTable->addCell(6600)->addText(htmlspecialchars($msName), $h3, $headPara);
        return $header;
    }

    public function footer($section) {

        $headCell = array("valign" => "center");
        $label = array('size' => 10, 'bold' => true);
        $pRed = array('size' => 10, 'color' => 'ff0000');
        $p = array('size' => 10, 'color' => '000000');



//Footer
        $footer = $section->addFooter();
        $footTable = $footer->addTable("FooterTable");
        $footTable->addRow();
        $footTable->addCell(1533, $headCell)->addText("Series", $p);
        $footTable->addCell(1533, $headCell);
        $footTable->addCell(1533, $headCell)->addText("Series and Number", $p);
        $footTable->addCell(1533, $headCell)->addText("Number System", $pRed);
        $footTable->addCell(1533, $headCell)->addText("Issue Date", $p);
        $footTable->addCell(1533, $headCell)->addText("Enter Date", $pRed);
        $footTable->addRow();
        $footTable->addCell(1533)->addText("Revision Number", $p);
        $footTable->addCell(1533);
        $footTable->addCell(1533)->addText("Revision Date", $p);
        $footTable->addCell(1533);
        $pageNumberCell = $footTable->addCell(3066);
        $pageNumberCell->getStyle()->setGridSpan(2);
        $pageNumberCell->getStyle()->setValign("center");

        $pageNumberCell->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'), $label, array('align' => 'center'));
        return $footer;
    }

}
