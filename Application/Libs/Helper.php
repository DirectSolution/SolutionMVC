<?php

namespace SolutionMvc\Library;

class Helper {

    /**
     * @param string
     * @return array
     * @description Explode a filename, retaining its file extension. returns an array with the filename and the extension eg: array("fileName" => Somefile, "fileExtension" => .jpg);
     */
    public function explodeOnLastDot($file) {
        //Explode on "." delimiter
        $makeArray = explode(".", $file);
        //Get the last instance which should be followed by a file extension
        $fileExtension = end($makeArray);
        //Get the Filename by exploding on extension
        $fileName = explode("." . $fileExtension, $file);
        //Return both parts
        return array(
            "fileName" => $fileName[0],
            "fileExtension" => "." . $fileExtension);
    }

    /**
     * 
     * @param type string
     * @return string
     * @description Pass in full filename, returns a string containing an encoded filename while retaining the extension eg Pass in SomeFile.jpg and expect 120jdhhriwquehdf.jpg back, use this to avoid clashing filenames.
     */
    public function encodeFileName($file) {
        //Get an array of the filename + extension
        $split = $this->explodeOnLastDot($file);
        $encodeFileName = md5(urldecode($split['fileName'] . time()));
        return $encodeFileName . $split['fileExtension'];
    }

    public function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['id'] == $id) {
                return $val['name'];
            }
        }
        return null;
    }

    /**
     * debugPDO
     *
     * Shows the emulated SQL query in a PDO statement. What it does is just extremely simple, but powerful:
     * It combines the raw query and the placeholders. For sure not really perfect (as PDO is more complex than just
     * combining raw query and arguments), but it does the job.
     * 
     * @param string $raw_sql
     * @param array $parameters
     * @return string
     */
    static public function debugPDO($raw_sql, $parameters) {

        $keys = array();
        $values = $parameters;

        foreach ($parameters as $key => $value) {

            // check if named parameters (':param') or anonymous parameters ('?') are used
            if (is_string($key)) {
                $keys[] = '/' . $key . '/';
            } else {
                $keys[] = '/[?]/';
            }

            // bring parameter into human-readable format
            if (is_string($value)) {
                $values[$key] = "'" . $value . "'";
            } elseif (is_array($value)) {
                $values[$key] = implode(',', $value);
            } elseif (is_null($value)) {
                $values[$key] = 'NULL';
            }
        }

        /*
          echo "<br> [DEBUG] Keys:<pre>";
          print_r($keys);

          echo "\n[DEBUG] Values: ";
          print_r($values);
          echo "</pre>";
         */

        $raw_sql = preg_replace($keys, $values, $raw_sql, 1, $count);

        return $raw_sql;
    }

    function getClosest($search, $arr) {
        $closest = array();
        foreach ($arr as $item) {
            if (empty($closest) || abs($search - $closest['value']) > abs($item['value'] - $search)) {
                $closest = $item;
            }
        }
        return $closest;
    }

    /**
     * GetPercent
     *
     * Returns a percent value when passed Score and High
     * 
     * @param string $small
     * @param string $large
     * @return string
     */
    public function getPercent($small, $large) {
        return($small / ($large)) * 100;
    }

    public function base64image($path) {
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        return 'data:image/' . $type . ';base64,' . base64_encode($data);
    }

    public function passwordHasherAction($password) {
        return md5($this->secret . $password);
    }
    
    
    public function getCountries(){
       $helpers = new \SolutionMvc\Audit\Model\Helpers();
       return $helpers->getCountriesArray();
    }

    public function getCounties(){
       $helpers = new \SolutionMvc\Audit\Model\Helpers();
       return $helpers->getCountiesArray();
    }

    
    public function convertDayMonthYearToMysqlDataTime($date = null){                
        if($date != null){        
            $d = \DateTime::createFromFormat('!d/m/Y', $date);
            return $d->format('Y-m-d H:i:s');       
        }else{
            return null;
        }
    }
    
}
