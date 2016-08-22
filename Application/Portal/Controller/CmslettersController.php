<?php

/**
 * Description of CMSLettersController
 *
 * @author dhayward
 */

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller, // Core controller where all the reuseable controllery bits are stored
    SolutionMvc\Portal\Model\cms_letter_templates, // Where the "SQL" is (its not actually sql)
    \SolutionMvc\Portal\Model\mast_clients, // So we can get the data from prod_portal>mast_clients
    SolutionMvc\Library\Helper; // A collection of helper functions .. For this class we need the html stripper

require_once "../vendor/tecnickcom/tcpdf/tcpdf.php";

class MYPDF extends \TCPDF {

    protected $letter_templates;

    public function __construct($orientation = 'P', $unit = 'mm', $format = 'A4', $unicode = true, $encoding = 'UTF-8', $diskcache = false, $pdfa = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->letter_templates = new cms_letter_templates();
    }

    public function urlSplitter() {
        //URL Keys = 0 - 4. Un-Used
        //           5. Docuement ID
        //           6. $price
        //           7. $client
        //           8. $userid
        //           9. $last
        //           10. $product        
        $urlParts = explode("/", $_SERVER["REQUEST_URI"]);
        // For ease of reading I'm setting the Key name now:
        return array(
            "id" => $urlParts[5],
            "price" => isset($urlParts[6]) ? $urlParts[6] : null,
            "client" => isset($urlParts[7]) ? $urlParts[7] : null,
            "userid" => isset($urlParts[8]) ? $urlParts[8] : null,
            "last" => isset($urlParts[9]) ? $urlParts[9] : null,
            "product" => isset($urlParts[10]) ? $urlParts[10] : null
        );
    }

    //Page header
    public function Header() {
        $url = $this->urlSplitter();
        $header = $this->letter_templates->getOne($url['id']);
        $this->writeHTML($header->cms_letter_headers['html'], true, 0, true, 0);
    }

    //Page footer
    public function Footer() {
        $url = $this->urlSplitter();
        $footer = $this->letter_templates->getOne($url['id']);
        $this->writeHTML($footer->cms_letter_footers['html'], true, 0, true, 0);
    }

}

class CmslettersController extends Controller {

    protected $template; // $this->template;
    protected $helper; // The helper class
    protected $pdf; // The pdf generator $this->pdf;
    protected $cms_sales;

    public function __construct() { // Construct loads re-used components through the class.
        parent::__construct(); // parent::constuct runs the __construct defined in SolutionMvc\Core\Controller these are used throughout the system so stored in one place and loaded once
        $this->template = new cms_letter_templates(); // This is the model, where we do the "SQL"
        $this->helper = new Helper();
        $this->pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    }

    public function indexAction() {
        // Check if the token is set AND the auth token holds the correct credentials which are set when they log in to minimise needless DB calls, this function is found in Controller.
        if ($this->isAuth(1)) { // These auth levels will need changing I haven't added anything to the ACL yet for them
            echo $this->twig->render("Portal/CmsLetters/index.html.twig", array(// Load the template and pass in any variables we wish to use in it.
                "data" => $this->template->getAll() // Ask the model to get us all the templates, then we pass it to the view.
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(// Load the template and pass in any variables we wish to use in it.
                "errors" => "You are not authorised to access this area, please log in first.", // Ask the model to get us all the templates, then we pass it to the view.
                "project" => "Portal/",
                "controller" => "Cmsletters/",
                "action" => "index"
            ));
            //$this->redirect("Portal/Login", "You are not authorised to access this area!", "error");
            // Redirecter is defined in the base controller, this is so if we ever 
            // need to handle mobile or ajax requests (where header(location) is useless 
            // we can write a function in one place and itll work for ALL redirect.
        }
    }

    public function addressBlock() {
        return "<strong><p>Example Address</p><p>Line 1</p><p>Line 2</p><p>Postcode</p><p>County</p><p>Country</p></strong>";
    }

    public function buildAddressString($client) {
        $string = "<strong>";
        $string .= ($client['address1']) ? $client['address1'] : null;
        $string .= ($client['address2']) ? ",<br>" . $client['address2'] : null;
        $string .= ($client['city']) ? ",<br>" . $client['city'] : null;
        $string .= ($client['county']) ? ",<br>" . $client['county'] : null;
        $string .= ($client['postcode']) ? ",<br>" . $client['postcode'] : null;
        $string .= ($client['country']) ? ",<br>" . $client['country'] : null;
        $string .= ".</strong>";
        return $string;
    }

    public function tokenReplacer($string, $price = null, $client = null, $mast_client = null, $user = null, $product = null) {
        if (strpos($string, "[[TABLE-ROWS-RENEWALS-DATA]]")) {
            $replacement = "";
            $productTypeArray = array(
                1 => "SFP",
                29 => "FFE",
                25 => "FFH",
                21 => "FFC"
            );
            if ($product == 'SFP') {
                $theprod = '1';
            }
            if ($product == 'FFE') {
                $theprod = '29';
            }
            if ($product == 'FFH') {
                $theprod = '25';
            }
            if ($product == 'FFC') {
                $theprod = '21';
            }
            $this->cms_sales = new \SolutionMvc\Portal\Model\cms_sales();
            $i = 0;
            foreach ($this->cms_sales->getAllSalesByContact($client) as $sale) {
                
                foreach ($productTypeArray as $keyType => $typeValue) {



                    if ($typeValue == $sale['productcode']) {
                        $replacement .= "<table><tr><td>$typeValue</td></tr></table>";
                    } else {
                        $replacement .= "<tr><td>$keyType</td></tr>";
                        $replacement .= "<tr><td>$keyType</td></tr>";
                        $replacement .= "<tr><td>$keyType</td></tr>";
                        $replacement .= "<tr><td>two</td></tr>";
                        $replacement .= "<tr><td>two</td></tr>";
                    }



//                $replacement .= "<tr>
//                                    <td>$i</td>
//                                    <td></td>
//                                    <td></td>
//                                    <td></td>
//                                    <td></td>
//                                    <td></td>
//                                    <td></td>
//                                    <td></td>
//                                    <td></td>
//                                </tr>";
                }
            }

            $string = str_replace("[[TABLE-ROWS-RENEWALS-DATA]]", $replacement, $string);
        }

        $cms_sales = new \SolutionMvc\Portal\Model\cms_sales();
        $sales_array = $cms_sales->getSaleByContactId($client);

        $tokens = array();
        $variables = array();
        if ($price) {
            $tokens[] = "[[RENEWAL-PRICE]]";
            $tokens[] = "[[SUB-TOTAL]]";
            $tokens[] = "[[VAT-TOTAL]]";
            $tokens[] = "[[VAT]]";
            $tokens[] = "[[RENEWAL-TOTAL]]";
            $tokens[] = "[[TOTAL]]";
            $tokens[] = "[[PRODUCT-PRICE]]";
            $variables[] = number_format($price, 2); // [[RENEWAL-PRICE]]
            $variables[] = number_format($price, 2); // [[SUB=TOTAL]]
            $variables[] = number_format(($price * 0.2), 2); // [[VAT-TOTAL]]
            $variables[] = number_format(($price * 0.2), 2); // [[VAT]]
            $variables[] = number_format(($price * 1.2), 2); // [[RENEWAL-TOTAL]]
            $variables[] = number_format(($price * 1.2), 2); // [[TOTAL]]
            $variables[] = number_format(($price), 2); // [[PRODUCT-PRICE]]
        }

        $replacerArray = array(
            "[[TODAYS-DATE]]" => \date("d/m/Y"), //[[TODAYS-DATE]]
            "[[ADDRESS-BLOCK]]" => $this->buildAddressString($client), //[[ADDRESS-BLOCK]]
            "[[CURRENT-YEAR]]" => \date("y"),
            "[[NEWID]]" => str_pad($client['clientnumber'], 4, "0", STR_PAD_LEFT), // [[NEWID]] -- God knows what the names about, legcacy of some sort :/
            "[[CLIENT-NUMBER]]" => str_pad($client['clientnumber'], 4, "0", STR_PAD_LEFT), // [[NEWID]] -- God knows what the names about, legcacy of some sort :/
            "[[CLIENT-SALUTATION]]" => $client['salutation'], // [[CLIENT-SALUTATION]]
            "[[CLIENT-NAME]]" => $client['contactname'], // [[CLIENT-NAME]]
            "[[CLIENT-POSTCODE]]" => $client['postcode'], // [[CLIENT-POSTCODE]]
            "[[CLIENT-COMPANYNAME]]" => $client['companyname'], // [[CLIENT-COMPANYNAME]]
            "[[CLIENT-ADDRESS]]" => $this->buildAddressString($client), // [[CLIENT-ADDRESS]]
            "[[USER]]" => $user['name'], // [[USER]],
            "[[CLIENT-CARD-LAST4]]" => $client['l4'],
            "[[CARD-LAST4]]" => $client['l4'],
            "[[INCREASE-PRICE]]" => "THIS STILL NEED DOING!!!", //[[INCREASE-PRICE]]
            "[[ADDRESS-1]]" => $client['address1'], //[[ADDRESS-1]]
            "[[ADDRESS-2]]" => $client['address2'], //[[ADDRESS-2]]
            "[[COMPANY-NAME]]" => $client['companyname'],
            "[[CLIENT-CITY]]" => $client['city'],
            "[[CLIENT-JOBTITLE]]" => $client['jobtitle'],
            "[[CLIENT-PHONE]]" => $client['phone'],
            "[[CLIENT-MOBILE]]" => $client['mobile'],
            "[[CLIENT-EMAIL]]" => $client['email'],
            "[[CLIENT-COUNTY]]" => $client['county'],
            "[[CLIENT-FAX]]" => $client['fax'],
            "[[COMPANY-NUMBER]]" => $mast_client['regcompno'],
            "[[RENEWAL-DATE]]" => $client['expiry'],
            "[[PRODUCT-NAME]]" => $product,
            "[[SALES-NOTES]]" => $sales_array['salesnotes'],
            "[[COURSE-LOCATION]]" => $sales_array['location'],
            "[[DATE-LOCATION]]" => ($sales_array['coursedate']) ? \gmdate("d/m/Y", (int) $sales_array['coursedate']) : null,
            "[[PRICE-PAID]]" => $sales_array['paid'],
            "[[PRICE-QUOTED]]" => ($sales_array['price']) ? \number_format($sales_array['price'], 2) : null,
            "[[CLIENT-JOIN-DATE]]" => ($mast_client['timestamp']) ? \gmdate('d/m/Y', (int) $mast_client['timestamp']) : null
        );
        foreach ($replacerArray as $key => $replace) {
            $tarr[] = $key;
            $varr[] = $replace;
        }

        if ($product) {
            if ($product == 21 || $product == 22) {
                $productName = "First for Contractors";
            }
        }



        return str_replace(
                array_merge($tokens, $tarr), array_merge($variables, $varr), $string
        );
    }

    public function clientDetailsTokenReplacer($client) {
        
    }

    public function viewAction($id, $price = 123, $client = 4, $userid = 4, $last = 4, $product = null) {
        $user = new \SolutionMvc\Portal\Model\User();
        $cms_contacts = new \SolutionMvc\Portal\Model\cms_contacts();
        $mast_clients = new \SolutionMvc\Portal\Model\mast_clients();
        $temp = $this->template->get($id); // SOmething fucked up not sure why need to come back to this. // Once fixed don't forget to sort the same function in viewPdfAction()
        if ($this->isAuth(1)) { // These auth levels will need changing I haven't added anything to the ACL yet for them
            echo $this->twig->render("Portal/CmsLetters/view.html.twig", array(
                "data" => $temp[$id],
                "body" => $this->tokenReplacer(
                        $temp[$id]['body'], $price, $cms_contacts->getContact($client), $mast_clients->getClient($client), $user->getUserById($userid), $last, $product
                )
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(// Load the template and pass in any variables we wish to use in it.
                "errors" => "You are not authorised to access this area, please log in first.", // Ask the model to get us all the templates, then we pass it to the view.
                "project" => "Portal/",
                "controller" => "Cmsletters/",
                "action" => "view/$id"
            ));
            //$this->redirect("Portal/Login", "You are not authorised to access this area!", "error");
        }
    }

    public function viewPdfAction($id, $price = 123, $client = 4, $userid = 4, $last = 4, $product = null) {

        $user = new \SolutionMvc\Portal\Model\User();
        $cms_contacts = new \SolutionMvc\Portal\Model\cms_contacts();
        $mast_clients = new \SolutionMvc\Portal\Model\mast_clients();
        $temp = $this->template->get($id); // SOmething fucked up not sure why need to come back to this.
        $body = $this->tokenReplacer($temp[$id]['body'], $price, $cms_contacts->getContact($client), $mast_clients->getClient($client), $user->getUserById($userid), $last, $product);
        $data = $temp[$id];
//        print "<pre>";
//        print_r($data);
//        print "</pre>";   
        $style = array(
            'width' => 0.5, 'line' => 1000, 'color' => array(255, 0, 0));
        $this->pdf->SetCreator(PDF_CREATOR);
        $this->pdf->SetAuthor("Doug");
        $this->pdf->SetTitle($data['name']);
        $this->pdf->SetSubject($data['name']);
        $this->pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, $data['name'], null, array(0, 64, 255), array(0, 64, 128));
        $this->pdf->Line(5, 10, 95, 10, $style);
        //$this->pdf->setFooterData(array(0, 64, 0), array(0, 64, 128));
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
// set image scale factor
        $this->pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
// set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__) . '/lang/eng.php')) {
            require_once(dirname(__FILE__) . '/lang/eng.php');
            $this->pdf->setLanguageArray($l);
        }
// ---------------------------------------------------------
// set default font subsetting mode
        $this->pdf->setFontSubsetting(true);
// Set font
// dejavusans is a UTF-8 Unicode font, if you only need to
// print standard ASCII chars, you can use core fonts like
// helvetica or times to reduce file size.
        $this->pdf->SetFont('dejavusans', '', 9, '', true);
// Add a page
// This method has several options, check the source code documentation for more information.
        $this->pdf->AddPage();
// set text shadow effect
        $this->pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));
// Set some content to print
        $html = $body;
// Print text using writeHTMLCell()
        $this->pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
// ---------------------------------------------------------
// Close and output PDF document
// This method has several options, check the source code documentation for more information.
        $this->pdf->Output('example_001.pdf', 'I');
    }

    public function createAction() {

        if ($this->isAuth(1) && $this->requestType() == "GET") { // If "GET" display the form
            echo $this->twig->render("Portal/CmsLetters/create.html.twig", array(
                "tokens" => $this->template->getTokens(),
                "headers" => $this->template->getHeaders(),
                "footers" => $this->template->getFooters()
            ));
        } elseif ($this->requestType() == "ajax") { // But if "POST" we'll save the form. REST? :)
            $data = $this->requestObject();
            try {
                $id = $this->template->set($this->requestObject(), $this->helper->htmlStripper($data['content']), $this->getToken()->user->id);
                $this->redirect("Portal/Cmsletters/view/" . $id, "Successfully added a new template, you can see it below.");
            } catch (\Exception $ex) {
                $this->redirect("Portal/Login", "Something went wrong", "error");
            }
        } else {

            echo $this->twig->render("Portal/Login/login.html.twig", array(// Load the template and pass in any variables we wish to use in it.
                "errors" => "You are not authorised to access this area, please log in first.", // Ask the model to get us all the templates, then we pass it to the view.
                "project" => "Portal/",
                "controller" => "Cmsletters/",
                "action" => "create"
            ));
            // $this->redirect("Portal/Login", "You are not authorised to access this area!", "error");
        }
    }

    public function updateAction($id) {
        if ($this->isAuth(1) && $this->requestType() == "GET") { // If "GET" display the form
            echo $this->twig->render("Portal/CmsLetters/update.html.twig", array(
                "tokens" => $this->template->getTokens(), // These are all the tokens
                "headers" => $this->template->getHeaders(),
                "footers" => $this->template->getFooters(),
                "data" => array(
                    "template" => $this->template->get($id),
                    "tokens" => $this->template->getTokens($id) // These are the actual ones currently assigned to the template (we'll match them up in the view)
                )
            ));
        } elseif ($this->getToken() && $this->requestType() == "POST") { // But if "POST" we'll save the form. REST? :)
            try {
                $this->template->update($id, $this->requestObject(), $this->getToken()->user->id);
            } catch (\Exception $ex) {
                $this->redirect("Portal/Login", "Something went wrong", "error");
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(// Load the template and pass in any variables we wish to use in it.
                "errors" => "You are not authorised to access this area, please log in first.", // Ask the model to get us all the templates, then we pass it to the view.
                "project" => "Portal/",
                "controller" => "Cmsletters/",
                "action" => "update/$id"
            ));

            // $this->redirect("Portal/Login", "You are not authorised to access this area!", "error");
        }
    }

    public function retireAction($id) {
        if ($this->isAuth(1)) { // These auth levels will need changing I haven't added anything to the ACL yet for them
            $this->template->retire($id, $this->getToken()->user->id);
        } else {
            $this->redirect("Portal/Login", "You are not authorised to access this area!", "error");
        }
    }

    public function unRetireAction($id) {
        if ($this->isAuth(1)) { // These auth levels will need changing I haven't added anything to the ACL yet for them
            $this->template->unretire($id, $this->getToken()->user->id);
        } else {
            $this->redirect("Portal/Login", "You are not authorised to access this area!", "error");
        }
    }

    public function getHeaderAction() {
//        if ($this->requestType() == "POST") {
        $request = $this->requestObject();
        $temp = $this->template->getHeader($request['id']);
        print_r(
                json_encode(
                        array(
                            "id" => $temp['id'],
                            "html" => $temp['html']
                        )
                )
        );
//        }
    }

//
    public function getFooterAction() {
//        if ($this->requestType() == "POST") {
        $request = $this->requestObject();
        $temp = $this->template->getFooter($request['id']);
        print_r(
                json_encode(
                        array(
                            "id" => $temp['id'],
                            "html" => $temp['html']
                        )
                )
        );
//        }
    }

}
