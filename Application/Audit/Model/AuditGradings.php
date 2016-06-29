<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Audit\Model\GradingValues;

/**
 * Description of User
 *
 * @author doug
 */
class AuditGradings extends BaseModel {

    protected $gradingValues;

    public function __construct() {
        parent::__construct();
        $this->gradingValues = new GradingValues();
    }

    public function getAuditGradingsByID($id) {
        return $this->prod_audit->AuditGradings[$id];
    }

    public function getAllAuditGradings($client) {
        return $this->prod_audit->AuditGradings->where('client_id', $client)->or('client_id', 0);
    }

    public function allAuditGradingsReturnArray($client) {
        $array = array();
        foreach ($this->getAllAuditGradings($client) AS $grading) {
            $array[] = array(
                "Aid" => $grading['id'],
                "name" => $grading['name'],
                "values" => $this->gradingValues->allGradingValuesReturnArray($grading['id'])
            );
        }
        return $array;
    }

}
