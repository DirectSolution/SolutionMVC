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
    public function image($location1, $location2, $FILES) {
//print_r($FILES);
        if (isset($FILES['file'])) {
            //The error validation could be done on the javascript client side.
            $errors = array();
            $return = array();
//            $explode =  explode(".", $FILES['file']['name']);
//            $ext = end($explode);
            $file_name = $this->helper->encodeFileName($FILES['file']['name']);
            $file_size = $FILES['file']['size'];
            $file_tmp = $FILES['file']['tmp_name'];
            
//            die(print_r($FILES));
            
            $file_ext = strtolower(pathinfo($FILES['file']['name'], PATHINFO_EXTENSION));
            
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
        }
    }
    
    
}
