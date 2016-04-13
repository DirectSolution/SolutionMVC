<?php

namespace SolutionMvc\Controller;

use SolutionMvc\Library\Helper;

class UploadController {

    var $helper;
    
    public function __construct() {
        $this->helper = new Helper();
    }
    
    public function AssetimageAction() {
        $location = "/var/www/html/Filestore/";

        if (isset($_FILES['file'])) {
            //The error validation could be done on the javascript client side.
            $errors = array();
            $file_name = $this->helper->encodeFileName($_FILES['file']['name'][0]);
            $file_size = $_FILES['file']['size'][0];
            $file_tmp = $_FILES['file']['tmp_name'][0];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $extensions = array("jpeg", "jpg", "png");
            if (in_array($file_ext, $extensions) === false) {
                $errors[] = "image extension not allowed, please choose a JPEG or PNG file.";
            }
            if ($file_size > 20971520) {
                $errors[] = 'File size cannot exceed 2 MB';
            }
            if (empty($errors) == true) {
                move_uploaded_file($file_tmp, $location."images/" . $file_name);
                return print($file_name);
            } else {
                return print_r(json_encode($errors));
            }
        }
    }

}
