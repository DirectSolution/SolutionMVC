<?php

namespace SolutionMvc\Healthsafety\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Portal\Model\User;

class Method extends BaseModel {

    protected $user;

    public function __construct() {
        parent::__construct();
        $this->user = new User();
    }

    // Start Getters

    public function getAllSheets() {
        return $this->prod_documents->MethodSheets->where("retired", 0);
    }

    public function getMethodStatements_MethodSheets($id) {
        return $this->prod_documents->MethodStatements_MethodSheets->where("MethodStatements_id", $id);
    }

    public function getMethodStatement($id) {
        return $this->prod_documents->MethodStatements[$id];
    }

    public function getMethods($id) {
        return $this->prod_documents->Methods->where("MethodStatements_id", $id);
    }

    public function getMethodStatements() {
        $statements = $this->prod_documents->MethodStatements->where("retired", 0);
        $return = array();
        foreach ($statements as $statement) {
            $return[] = array(
                "statement" => $statement,
                "author" => $this->user->getUserById($statement['created_by'])
            );
        }
        return $return;
    }

    // End Getters
    // Start Setters   

    public function setMethodStatement($request, $user) {
        return $this->prod_documents->MethodStatements->insert($this->methodStatementArray($request, $user));
    }

    public function setMethodSheets($request, $user, $msID) {
        foreach ($request['methodStatement']['sheets'] as $sheetId => $sheet) {
            foreach ($sheet['sections'] as $section) {
                $sectionId = $this->prod_documents->MethodSections->insert($this->methodSectionArray($section, $user));
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

    public function methodStatementArray($request, $user) {
        return array(
            "name" => $request['name'],
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user
        );
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
            "name" => $section['name'],
            "description" => $section['description'],
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user
        );
    }

    public function methodArray($method, $msID, $user) {
        return array(
            "name" => $method['name'],
            "description" => $method['description'],
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user,
            "MethodStatements_id" => $msID
        );
    }

    // End Insert Arrays
}
