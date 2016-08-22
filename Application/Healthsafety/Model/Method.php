<?php

namespace SolutionMvc\Healthsafety\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Portal\Model\User,
    SolutionMvc\Library\Helper;

class Method extends BaseModel {

    protected $user;
    protected $helpers;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
        $this->helpers = new Helper();
    }

    // Start Getters

    public function getAllSheets() {
        return $this->prod_documents->MethodSheets->where("retired", 0)->order("name");
    }

    public function getMethodStatements_MethodSheets($id) {
        return $this->prod_documents->MethodStatements_MethodSheets->where("MethodStatements_id", $id);
    }

    public function getMethodStatement($id) {
        return $this->prod_documents->MethodStatements[$id];
    }

    public function getMethodStatementByUrl($url) {
        $a = $this->prod_documents->MethodStatements[array("url_code" => $url)];
        if ($a) {
            return $a['id'];
        } else {
            return false;
        }
    }

    public function getMethods($id) {
        return $this->prod_documents->Methods->where("MethodStatements_id", $id);
    }

    public function countAwaiting() {
        return count($this->prod_documents->MethodStatements->where("retired", 0)->and("awaiting_review", 1)->and("url_code", NULL));
    }

    public function getMethodStatements() {
        $statements = $this->prod_documents->MethodStatements->where("retired", 0)->and("awaiting_review", 0)->order("name");
        $return = array();
        foreach ($statements as $statement) {
            $return[] = array(
                "statement" => $statement,
                "author" => $this->user->getUserById($statement['created_by'])
            );
        }
        return $return;
    }

    public function getMethodStatementsAwaitingReview() {
        $statements = $this->prod_documents->MethodStatements->where("retired", 0)->and("awaiting_review", 1)->and("url_code", NULL);
        $return = array();
        foreach ($statements as $statement) {
            $return[] = array(
                "statement" => $statement,
                "author" => $this->user->getUserById($statement['created_by']),
            );
        }
        return $return;
    }

    // End Getters
    // Start Setters   

    public function setMethodStatement($request, $user) {
        return $this->prod_documents->MethodStatements->insert($this->methodStatementArray($request, $user));
    }

    public function setMethodSheets($request, $user, $msID, $parent = null) {
        foreach ($request['methodStatement']['sheets'] as $sheetId => $sheet) {
            foreach ($sheet['sections'] as $section) {
                $sectionId = $this->prod_documents->MethodSections->insert($this->methodSectionArray($section, $user, $parent));
                $this->prod_documents->MethodStatements_MethodSheets->insert($this->methodStatements_MethodSheetsArray($msID, $sheetId, $sectionId));
            }
        }
        return;
    }

    public function setMethods($request, $msID, $user) {
        $insertArray = array();
        foreach ($request['methodStatement']['methods'] as $method) {
            $insertArray[] = $this->methodArray($method, $msID, $user);
        }


        $this->prod_documents->Methods->insert_multi($insertArray);
        return;
    }

    public function setRetire($id, $user) {
        $methodStatement = $this->prod_documents->MethodStatements[$id];
        if ($methodStatement) {
            $methodStatement->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        } else {
            print "non";
        }
        $msms = $this->prod_documents->MethodStatements_MethodSheets->where("MethodStatements_id", $id);
        foreach ($msms as $section) {
            $section->MethodSections->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        }
        $methods = $this->prod_documents->Methods->where("MethodStatements_id", $id);
        foreach ($methods as $method) {
            $method->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        }
    }

    // End Setters
    // Start Inser Arrays
//            "retired" => ($parent)? 1 : 0,
//            "awaiting_review" => ($activate)? 0 : 1,
//            "url_code" => ($activate)? $activate['url_code'] : null,
//            "reviewed_by" => ($activate) ? $user : null,
//            "MethodStatements_id" => ($parent) ? $parent : null


    public function methodStatementArray($request, $user, $parent = null) {
        return array(
            "name" => $request['name'],
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user,
            "awaiting_review" => 1,
            "MethodStatements_id" => ($parent) ? $parent : null
        );
    }

    public function setAccept($id, $user) {
        $method = $this->prod_documents->MethodStatements[array("id" => $id, "awaiting_review" => 1)];
        if ($method) {
            if ($method->MethodStatements != null) {
                $url = $method->MethodStatements['url_code'];
                $method->MethodStatements->update(array(
                    "retired" => 1,
                    "url_code" => null
                ));
            }
            $method->update(array(
                "awaiting_review" => 0,
                "url_code" => ($url) ? $url : \time(),
                "reviewed_by" => $user
            ));
            return true;
        } else {
            return false;
        }
    }

    public function methodStatements_MethodSheetsArray($ms, $sheet, $section) {
        return array(
            "MethodStatements_id" => $ms,
            "MethodSheets_id" => $sheet,
            "MethodSections_id" => $section
        );
    }

    public function methodSectionArray($section, $user) {
        return array(
            "name" => $this->helpers->wordSanitizer($section['name']),
            "description" => $this->helpers->wordSanitizer($section['description']),
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user
        );
    }

    public function methodArray($method, $msID, $user) {
        return array(
            "name" => $this->helpers->wordSanitizer($method['name']),
            "description" => $this->helpers->wordSanitizer($method['description']),
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user,
            "MethodStatements_id" => $msID
        );
    }

    // End Insert Arrays
}
