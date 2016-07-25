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

    public function setMethodStatement($request, $user) {
        return $this->prod_documents->MethodStatements->insert($this->methodArray($request['methodStatement'], $user));
    }

    public function methodArray($request, $user) {
        return array(
            "name" => isset($request['name']) ? $request['name'] : null,
            "description" => isset($request['description']) ? $request['description'] : null,
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "created_by" => $user
        );
    }

    public function setMethod($method, $msID, $user) {
        return $this->prod_documents->Methods->insert(array(
                    "name" => $method['name'],
                    "description" => $method['description'],
                    "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                    "created_by" => $user,
                    "MethodStatements_id" => $msID
        ));
    }

    public function setSheet($sheetId, $msID) {
        return $this->prod_documents->MSStatements_Sheets->insert(array(
                    "MethodStatements_id" => $msID,
                    "MethodSheets_id" => $sheetId
        ));
    }

    public function setSection($ss_id, $sectionKey) {
        return $this->prod_documents->MSSheets_Sections->insert(array(
                    "Statements_Sheets_id" => $ss_id,
                    "Sections_id" => $sectionKey
        ));
    }

    public function setHeader($sect_id, $header) {
        return $this->prod_documents->MSSections_Headers->insert(array(
                    "MSSheets_Sections_id" => $sect_id,
                    "Headers_id" => $header
        ));
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

    public function getMethodStatementSections($id) {
//        return $this->prod_documents->MethodSections->where("MethodStatements_id", $id)->and("retired", 0);
        return $this->prod_documents->MethodStatement_MethodSections->where("MethodStatements_id", $id)->and("retired", 0);
    }

    public function getMethodSectionTypes() {
        return $this->prod_documents->MethodSectionTypes->where("retired", 0);
    }

    public function getMethodStatement($id) {
        return $this->prod_documents->MethodStatements[$id];
    }

    public function getStatementsSheets($id) {
        $a = $this->prod_documents->MSStatements_Sheets->where("MethodStatements_id", $id)->and("retired", 0);
        $returnArray = array();
        foreach ($a as $sheet) {
            $returnArray['sheets'][] = array(
                "name" => $sheet->MethodSheets['name'],
                "description" => $sheet->MethodSheets['description'],
                "sections" => $this->getSheetsSections($sheet['id'])
            );
        }
        return $returnArray;
    }

    public function getSheetsSections($id) {
        $a = $this->prod_documents->MSSheets_Sections->where("Statements_Sheets_id", $id)->and("retired", 0);
        $returnArray = array();
        foreach ($a as $sheetSection) {
            $returnArray[] = array(
                "name" => $sheetSection->Sections['name'],
                "headers" => $this->getSectionHeaders($sheetSection['Sections_id'])
            );
        }

        return $returnArray;
    }

    public function getSectionHeaders($id) {
        $a = $this->prod_documents->MSSections_Headers->where("MSSheets_Sections_id", $id)->and("retired", 0);
        $returnArray = array();
        foreach ($a as $header) {
            $returnArray[] = $header->Headers['description'];
        }
        return $returnArray;
    }

    public function getMethods($id) {
        return $this->prod_documents->Methods->where("MethodStatements_id", $id)->and("retired", 0);
    }

    public function getAllSheets() {
        $sheets = $this->prod_documents->MethodSheets->where("retired", 0);
        $returnArray = array();
        foreach ($sheets as $sheet) {
            $returnArray[] = array(
                "id" => $sheet['id'],
                "name" => $sheet['name'],
                "description" => $sheet['description']
            );
        }
        return $returnArray;
    }

    public function getAllSections() {
        $a = $this->prod_documents->Sections->where("retired", 0);
        $returnArray = array();
        foreach ($a as $section) {
            $returnArray[] = array(
                "id" => $section['id'],
                "name" => $section['short_name']
            );
        }
        return $returnArray;
    }

    public function getSheet($id) {
        $sheet = $this->prod_documents->MethodSheets[$id];
        return array(
            "id" => $sheet['id'],
            "description" => $sheet['description'],
            "name" => $sheet['name']
        );
    }

    public function getHeadings($id) {
        $headings = $this->prod_documents->Headers->where("Types_id", $id);
        $returnArray = array();
        foreach ($headings as $header) {
            $returnArray[] = array(
                "id" => $header['id'],
                "name" => $header['name'],
                "description" => $header['description']
            );
        }
        return $returnArray;
    }

    public function getSection($id) {
        $section = $this->prod_documents->Sections[$id];
        return array(
            "id" => $section['id'],
            "name" => $section['name']
        );
    }

}
