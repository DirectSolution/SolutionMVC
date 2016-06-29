<?php

namespace SolutionMvc\Model;

use PDO,
    SolutionORM\SolutionORM,
    Symfony\Component\Yaml\Parser;

/**
 * Description of BaseModel
 *
 * @author doug
 */
class BaseModel {

    /**
     * @var null Database Connection
     */
    public $db;
    public $prod_portal;
    public $prod_audit;
    public $prod_healthandsafety;
    /** @var string */
    private $tableName;
    private $config;
    /**
     * @var null Model
     */
    public $model = null;
    /**
     * Whenever controller is created, open a database connection too and load "the model".
     */
    public function __construct() {
        //Yaml because its easier to read for lists of configs.       
        $this->yaml = new Parser();        
        //Configs stored in one YAML file, so its easier to find them and when
        // changing we only need to chnage in one place instead of looking through millions of files
        $this->config = $this->yaml->parse(file_get_contents("../Application/Config/Config.yml"));
        $this->db = $this->config['db'];
        //These are each table in the db, or will be. Should probably be moved else where but no urgent.
        $this->getProdAudit();
        $this->getProdPortal();
        $this->getProdHS();
        //Haven't used this yet but it loads a table based on the class name so class mast_users{} would load the mast_users table.
        $this->tableName = $this->tableNameByClass(get_class($this));
    }
    
    public function getProdPortal(){        
        $this->prod_portal = $this->openDatabaseConnection("prod_portal");
    }
    public function getProdAudit(){        
        $this->prod_audit = $this->openDatabaseConnection("prod_audit");        
    }
    public function getProdHS(){
        $this->prod_healthandsafety = $this->openDatabaseConnection("prod_healthandsafety");
    }
    
    //Start connection
    private function openDatabaseConnection($database) {
        $options = array(PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ, PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING);
        $this->dbcon = new PDO($this->db['type'] . ':host=' . $this->db['host'] . ';dbname=' . $database . ';charset=' . $this->db['charset'], $this->db['user'], $this->db['pass'], $options);
         return new SolutionORM($this->dbcon);
       }

    /**
     * Determine table by class name
     * @param string
     * @return string
     * @result:Pages => pages, ArticleTag => article_tag
     */
    private function tableNameByClass($className) {
        $tableName = explode("\\", $className);
        $tableName = lcfirst(array_pop($tableName));

        $replace = array(); // A => _a
        foreach (range("A", "Z") as $letter) {
            $replace[$letter] = "_" . strtolower($letter);
        }

        return strtr($tableName, $replace);
    }

}