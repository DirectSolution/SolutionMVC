<?php

//namespace SolutionMvc\Healthsafety\Controller;
//
//use SolutionMvc\Core\Controller,
//    SolutionMvc\Healthsafety\Model\Method;
//
//class MethodController extends Controller {
//
//    protected $method;
//    protected $token;
//    protected $word;
//
//    public function __construct() {
//        parent::__construct();
//        $this->token = $this->getToken();
//        $this->method = new Method();
//    }
//
//    public function indexAction() {
//        echo $this->twig->render('HealthSafety/Documents/MethodStatement/index.html.twig', array(
//            "data" => $this->method->getMethodStatements()
//                
//        ));
//    }
//
//    public function viewAction($id) {
//        echo $this->twig->render('HealthSafety/Documents/MethodStatement/view.html.twig', array(
//            "MS" => $this->method->getMethodStatement($id),
//            "Data" => $this->method->getStatementsSheets($id),
//            "Methods" => $this->method->getMethods($id)
//        ));
//    }
//
//    public function createAction() {
//        if ($this->requestType() == "GET" && $this->getToken()) {
//            
//            $array = array(
//                "Sheets" => $this->method->getAllSheets()
//            );            
//            echo $this->twig->render('HealthSafety/Documents/MethodStatement/create.html.twig', array(
//                "SectionTypes" => $this->method->getMethodSectionTypes(),
//                    "Data" => $array
//            ));
//        } elseif ($this->requestType() == "POST" && $this->getToken()) {
//            $request = $this->requestObject();
//            $msID = $this->method->setMethodStatement($this->requestObject(), $this->token->user->id);
//         
//            foreach($request['methodStatement']['sheets'] as $sheetKey => $sheet){
//                
//                $ss_id = $this->method->setSheet($sheetKey, $msID);
//                foreach($sheet['sections'] as $sectionKey => $section){
//                    $sect_id = $this->method->setSection($ss_id, $sectionKey);
//                    foreach($section['headers'] as $header){
//                        $this->method->setHeader($sect_id, $header);
//                    }
//                }
//            }
//            foreach($request['methodStatement']['methods'] as $method){
//                $this->method->setMethod($method, $msID, $this->token->user->id);
//            }
//            header('Location: ' . HTTP_ROOT . 'HealthSafety/Method/view/' . $msID);
//        }else{
//            echo $this->twig->render("Portal/Login/login.html.twig", array(
//                "project" => "HealthSafety/",
//                "controller" => "Method/",
//                "action" => "create"
//            ));
//        }
//    }
//
//    public function getSheetDataAction($id){
//        return print_r(json_encode(array(
//            "sheet" => $this->method->getSheet($id),
//            "sections" => $this->method->getAllSections()
//        )));
//    }
//    
//    public function getSectionDataAction($id){
//        return print_r(json_encode(array(
//            "section" =>  $this->method->getSection($id),
//            "headings" => $this->method->getHeadings($id)
//        )));
//    }
//    
//    
//    
//    public function updateAction($id) {
//        echo $this->twig->render('HealthSafety/Documents/MethodStatement/update.html.twig', array(
//            "data" => "get data"
//        ));
//    }
//
//    public function retireAction($id) {
//        
//    }
//
//    public function documentAction($id) {
//        //Load PhpWord
//        require_once APP . '../vendor/phpoffice/phpword/src/PhpWord/Autoloader.php';
//        \PhpOffice\PhpWord\Autoloader::register();
//        $this->word = new \PhpOffice\PhpWord\PhpWord();
//        $this->word->setDefaultFontName('Helvetica');
//        $this->word->setDefaultFontSize(10);
//        $ms = $this->method->getMethodStatement($id);
//        $msName = $ms['name'];
//        $Data = $this->method->getStatementsSheets($id);
//        $Methods = $this->method->getMethods($id);
//
//        
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
//
//        $h2 = array('size' => 17, 'bold' => true);
//        $center = array('align' => "center", "spaceBefore" => 80, "spaceAfter" => 80);
//        $h3 = array('size' => 15, 'bold' => true);
//        $h3Red = array('size' => 15, 'bold' => true, 'color' => $red);
//        $h4Red = array('size' => 12, 'bold' => true, 'color' => $red);
//        $h3Blue = array('size' => 15, 'bold' => true, 'color' => $blue);
//        $headPara = array("align" => "center");
//        $headCell = array("valign" => "center");
//        $yellowCell = array("valign" => "center", "bgColor" => "cccccc");
//        $label = array('size' => 10, 'bold' => true);
//        $pRed = array('size' => 10, 'color' => 'ff0000');
//        $p = array('size' => 10, 'color' => '000000');
//
//        $labelP = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 80);
//        $paragraphStyle = array("widowControl" => false, "spaceBefore" => 80, "spaceAfter" => 150);
//
//        $styleBlueHead = array('valign' => 'center', 'borderBottomSize' => 8, 'borderBottomColor' => 'aad7ee', 'bgColor' => 'd9edf7', 'align' => 'center');
////        $this->word->addTableStyle('HeadTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('DetailsTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('SignOffTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('EmergencyTable', $tableStyle, $styleBlueHead);
//        $this->word->addTableStyle('FooterTable', $tableStyle, $styleBlueHead);
//
//
//        $section = $this->word->addSection();
//        //Header
////        $header = $section->addHeader();
//
//        $this->header($section, $msName);
//        $this->footer($section);
//
//
////        $addressSection = $this->word->addSection();
//
//        $section->addText("Your company address", $label, $labelP);
//        $section->addText("Address:", $label, $labelP);
//        $section->addText("Tel:", $label, $labelP);
//        $section->addText("Fax:", $label, $labelP);
//        $section->addText("Mobile:", $label, $labelP);
//
////        $detailsSection = $this->word->addSection();
//        $detailsTable = $section->addTable("DetailsTable");
//        $detailsTable->addRow(400);
//        $detailsTable->addCell(4600, $yellowCell)->addText("Project / Contract", $label);
//        $detailsTable->addCell(4600);
//        $detailsTable->addRow(400);
//        $detailsTable->addCell(4600, $yellowCell)->addText("Contractor", $label);
//        $detailsTable->addCell(4600);
//        $detailsTable->addRow(400);
//        $detailsTable->addCell(4600, $yellowCell)->addText("Site Address", $label);
//        $detailsTable->addCell(4600);
//        $detailsTable->addRow(400);
//        $detailsTable->addCell(4600, $yellowCell)->addText("Project Start Date", $label);
//        $detailsTable->addCell(4600);
//        $detailsTable->addRow(400);
//        $detailsTable->addCell(4600, $yellowCell)->addText("Expected Duration", $label);
//        $detailsTable->addCell(4600);
//        $detailsTable->addRow(400);
//        $detailsTable->addCell(4600, $yellowCell)->addText("Projected Completed Date", $label);
//        $detailsTable->addCell(4600);
//        $section->addText();
//
////        $signOffSection = $this->word->addSection();
//        $signOffTable = $section->addTable("SignOffTable");
//        $signOffTable->addRow(400);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Name", $label);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Title", $label);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Signature", $label);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Date", $label);
//        $signOffTable->addRow(400);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Document Author", $label);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addRow(400);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by", $label);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addRow(400);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by", $label);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addRow(400);
//        $signOffTable->addCell(1840, $yellowCell)->addText("Authorised by (for Client)", $label);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $signOffTable->addCell(1840);
//        $section->addText();
//
////        $emergencyContactSection = $this->word->addSection();
//        $emergencyContactTable = $section->addTable("EmergencyTable");
//        $emergencyContactTable->addRow();
//        $emcTRow1 = $emergencyContactTable->addCell(9200, $yellowCell);
//        $emcTRow1->getStyle()->setGridSpan(4);
//        $emcTRow1->addText("Emergency Contact Details", $label);
//        $emergencyContactTable->addRow(400);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addRow(400);
//        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Contact", $label);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addRow(400);
//        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Tel", $label);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addRow(400);
//        $emergencyContactTable->addCell(2300, $yellowCell)->addText("Mobile", $label);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//        $emergencyContactTable->addCell(2300);
//
//        $section->addText("Data Protection Statement", $h3, $center);
//        $section->addText("The information and data provided herein shall not be duplicated, disclosed or disseminated by the recipient in whole or in part for any purpose whatsoever without the prior written permission from YOUR COMPANY", $p, $paragraphStyle);
//
//        $section->addText("Disclaimer please delete before use", $h3Red, $center);
//        $section->addText("The details provided in this example method statement are intended as a guide only, the hazards and control procedures listed are not a comprehensive list. You must ensure that you carry out a risk assessment to determine and control the significant hazards that will be present in your particular circumstance. All information and advice is given in good faith. We cannot accept any responsibility for your subsequent acts or omissions. If you have any doubts queries or concerns, you should refer to the relevant regulations and take further professional advice.", $pRed, $paragraphStyle);
//        $section->addText("Please delete all red text prior to use.", $pRed, $paragraphStyle);
//        $section->addPageBreak();
//        foreach ($Data['sheets'] as $sheet) {
//            //Show header
//            $section->addText($sheet['name'], $h2, $center);
//            //Show Description
//            foreach ($sheet['sections'] as $sect) {
//                //Show sect header
//                $explodeHeader = preg_match('#<span[^<>]*>(.+).*?</span>#', $sect['name'], $matches);
//                if ($matches > 0) {
//                    $section->addText(htmlspecialchars(preg_replace("#<span[^<>]*>(.+).*?<\/span>#", "", $sect['name'])), $h3);
//                    foreach ($matches as $key => $redHead) {
//                        if ($key != 0) {
//                            $section->addText(htmlspecialchars($redHead), $h4Red);
//                        }
//                    }
//                }
//                if (count($sect['headers']) > 1) {
//                    $array = array();
//                    $array[] = "<ul>";
//                    foreach ($sect['headers'] as $header) {
//                        $array[] = "<li>" . $header . "</li>";
//                    }
//                    $array[] = "</ul>";
//                    $list = implode(null, $array);
//                    \PhpOffice\PhpWord\Shared\Html::addHtml($section, htmlspecialchars(str_replace("&", "and", $list)));
//                } else {
//                    foreach ($sect['headers'] as $header) {
//                        $replace1 = str_replace('<b>', '</w:t></w:r><w:r><w:rPr><w:b/></w:rPr><w:t xml:space="preserve"> ', $header);
//                        $replace2 = str_replace('</b>', '</w:t></w:r><w:r><w:t>', $replace1);
//                        $section->addText($replace2, $p, $paragraphStyle);          
//                    }
//                }
//            }
//            $section->addPageBreak();
//        }
//        
//        $section->addText(htmlspecialchars("Method of work."), $h2, $center);
//        foreach ($Methods as $key => $method) {
//            $this->word->addNumberingStyle("list$key", array("format" => "decimal", "restart" => true));
//            //Show header
//            //Show description
//            $section->addText(htmlspecialchars($method['name']), $h3);
//            $output = str_replace(array("<li>", "<\li>"), "", $method['description']);
//            $array = explode("<p>", $output);
//            unset($array[0]);
//            foreach ($array as $arr) {
//                $section->addListItem(trim(htmlspecialchars(str_replace(array("</li>", "</p>", "</ol>"), "", $arr))), 0, null, "list" . $key);
//            }
//        }
//        // Download it
////        \PhpOffice\PhpWord\Settings::setPdfRendererPath(APP . '../vendor/dompdf/dompdf/autoload.inc.php');
////        \PhpOffice\PhpWord\Settings::setPdfRendererName('DomPDF');
//        $dir = "/var/www/html/doug/portal.solutionhost.co.uk/web/apps2/public/HealthSafety/Documents/MS/output/";
//        if (!file_exists($dir)) {
//            mkdir($dir, 0777);
//        }
//        // Produce file and download
//        $file = $msName . '.docx';
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
//    public function header($section, $msName) {
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
////        $headTable->addRow(400);
////        $headTable->addCell(3060)->addText("INSERT COMPANY NAME", $h3Red, $headPara);
////        $headTable->addCell(3060)->addText("METHOD STATEMENT", $h3Blue, $headPara);
////        $headTable->addCell(3060)->addText(htmlspecialchars($msName), $h3, $headPara);
//        $headTable->addRow(400);
//        $logoCell = $headTable->addCell(2600, $headCell);
//        $logoCell->getStyle()->setVMerge("restart");
//        $logoCell->addText("LOGO", $h3Blue, $headPara);
//        $headTable->addCell(6600, $headCell)->addText("INSERT COMPANY NAME", $h3Red, $headPara);
//        $headTable->addRow(400);
//        $cell2 = $headTable->addCell(2600, $headCell);
//        $cell2->getStyle()->setVMerge("continue");
//        $headTable->addCell(6600)->addText("METHOD STATEMENT", $h3Blue, $headPara);
//        $headTable->addRow(400);
//        $logoCell3 = $headTable->addCell(2600, $headCell);
//        $logoCell3->getStyle()->setVMerge("continue");
//        $headTable->addCell(6600)->addText(htmlspecialchars($msName), $h3, $headPara);
//        return $header;
//    }
//
//    public function footer($section) {
//
//        $headCell = array("valign" => "center");
//        $label = array('size' => 10, 'bold' => true);
//        $pRed = array('size' => 10, 'color' => 'ff0000');
//        $p = array('size' => 10, 'color' => '000000');
//
//
//
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
//
//        $pageNumberCell->addPreserveText(htmlspecialchars('Page {PAGE} of {NUMPAGES}.'), $label, array('align' => 'center'));
//        return $footer;
//    }
//
//}
