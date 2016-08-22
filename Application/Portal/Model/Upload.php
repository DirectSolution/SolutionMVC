<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Library\Helper,
    SolutionMvc\Core\Security;

/**
 * Description of Audit
 *
 * @author dhayward
 */
class Upload extends BaseModel {

    var $helper;

    public function __construct() {
        $this->helper = new Helper();
    }

    /**
     * 
     * @param integer $id
     * @return object
     * @description Save an image, Supply: $location1 = Location of fileserver e.g /var/www/html/Filestore/ $location2 = Location of fileserver e.g /images/clientid/ $FILES = $_FILES form post data Return either a JSON Encoded 3 part array (ServerURL, FileLocation, FileName (encoded)) or an error (JSON Object (String)).
     */
    public function image($location1, $location2, $files) {
//print_r($FILES);
        
        foreach ($files AS $FILES) {
//            if (isset($FILES['file'])) {
                //The error validation could be done on the javascript client side.
                $errors = array();
                $return = array();
//            $explode =  explode(".", $FILES['file']['name']);
//            $ext = end($explode);
                error_log("DollarFILES here is " . json_encode($FILES));
                $file_name = $this->helper->encodeFileName($FILES['name']);
                error_log("File name here is " . $file_name);
                $file_size = $FILES['size'][0];
                $file_tmp = $FILES['tmp_name'][0];
                error_log("FILE SIZE IS " .  $FILES['size'][0]);
//            die(print_r($FILES));

                $file_ext = strtolower(pathinfo($FILES['name'][0], PATHINFO_EXTENSION));

                $extensions = array("jpeg", "jpg", "png", "zip");
                if (in_array($file_ext, $extensions) === false) {
                    $errors[] = "image extension not allowed, please choose a JPEG or PNG file.";
                }
                if ($file_size > 20971520) {
                    $errors[] = 'File size cannot exceed 2 MB';
                }
                if (empty($errors) == true) {
                    if (!is_dir($location1 . $location2)) {
                        mkdir($location1 . $location2, 0777, true);
                    }
                    move_uploaded_file($file_tmp, $location1 . $location2 . "/" . $file_name);
                    return print_r(
                            json_encode(
                                    $return['fileData'] = array(
                                "ServerURL" => $location1,
                                "FileLocation" => $location2,
                                "FileName" => $file_name
                                    )
                            )
                    );
                } else {
                    return print_r(json_encode($errors));
                }
//            }
        }
    }

}
