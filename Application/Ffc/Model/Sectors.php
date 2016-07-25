<?php

namespace SolutionMvc\Ffc\Model;

use SolutionMvc\Model\BaseModel;

class Sectors extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getDefaultSectors() {
        return $this->prod_humanresources->mast_sectors_default->where("retired", 0);
    }

    public function getSector($id) {
        return $this->prod_humanresources->mast_sectors_default[$id];
    }

    public function getSectorQuestions($id) {
        return $this->prod_humanresources->mast_worksector_default_questions->where("ws_id", $id)->and("retired", 0);
    }

    public function setDefaultSector($request, $user) {
        return $this->prod_humanresources->mast_sectors_default->insert(array(
                    "client" => 0,
                    "description" => $request['description'],
                    "retired" => 0,
                    "created_by" => $user
        ));
    }

    public function setDefaultSectorQuestions($request, $user, $wsID) {
        $order = 1;
        $insertArray = array();
        foreach ($request['sector']['questions'] as $question) {
            $insertArray[] = array(
                "field_name" => $question['field_name'],
                "field_type" => $question['field_type'],
                "field_req" => $question['field_req'],
                "automatic_unapprove" => $question['automatic_unapprove'],
                "add_expiry_date" => $question['add_expiry_date'],
                "expiry_required" => $question['expiry_required'],
                "add_evidence" => $question['add_evidence'],
                "evidence_required" => $question['evidence_required'],
                "`order`" => $order++,
                "retired" => "0",
                "ws_id" => $wsID['id']
            );
        }
        $this->prod_humanresources->mast_worksector_default_questions->insert_multi($insertArray);
    }

    public function retireSector($id) {
        $exists = $this->prod_humanresources->mast_sectors_default[$id];
        if($exists){
            $exists->update(array(
                "retired" => 1
            ));
        }
        return;
    }

    public function retireSectorQuestions($id) {
        $exists = $this->prod_humanresources->mast_worksector_default_questions->where("ws_id", $id);
        foreach($exists as $existing){
            $existing->update(array(
                "retired" => 1
            ));
        }
        return;
    }

}
