<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Model\Fixtures\AuditFixtures,
    SolutionMvc\Model\Question;

/**
 * Description of Audit
 *
 * @author dhayward
 */
class Audit extends BaseModel {

    /**
     * 
     * @param integer $id
     * @return object
     * @description Get one Audit by $id
     */
    public function getAuditById($id) {
        return $this->orm->Audits[$id];
    }

    public function testDataForAudit($id) {

        $data = new AuditFixtures();
        return $data->auditFixtures($id);
    }

    public function getAllAudits($client) {

        return $this->orm->Audits->where("client_id", $client)->where("retired", 0);
    }

    /**
     * 
     * @param integer $id
     * @description Get one Audit by $id
     */
    public function auditByIdArray($id) {

        $audit = $this->getAuditById($id);

        $auditReturn = array(
            "id" => $audit['id'],
            "name" => $audit['name'],
            "description" => $audit['description'],
            "created_at" => $audit['created_at'],
            "client_id" => $audit['client_id'],
            "created_by" => $audit['created_by'],
            "audit_type_id" => $audit['AuditType_id'],
        );

        return $auditReturn;
    }

    public function allAuditsArray($client) {
        foreach ($this->getAllAudits($client) AS $key => $audit) {

            $audits[$key] = array(
                "id" => $audit['id'],
                "name" => $audit['name'],
                "description" => $audit['description'],
                "created_at" => $audit['created_at'],
                "client_id" => $audit['client_id'],
                "created_by" => $audit['created_by'],
                "audit_type_id" => $audit['AuditType_id'],
            );
        }
        return $audits;
    }

    public function insertNewAudit($audit) {

        $auditData = array(
            "name" => $audit->data->name,
            "description" => $audit->data->description,
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
//            "client_id" => $audit->data->client_id,
//            "created_by" => $audit->data->created_by,
            "client_id" => 000,
            "created_by" => 123,
            "AuditType_id" => $audit->data->auditType
        );

        $auditReturn = $this->orm->Audits->insert($auditData);
//        Loop groups, insert a group with an audit id then retrive its ID then loop its questions inserting questions with the group id

        foreach ($audit->data->groups as $group) {
            $groupData = array(
                "name" => $group->name,
                "audit_id" => $auditReturn['id'],
//                "client_id" => $audit->data->client_id,
                "client_id" => 000,
            );

            $groupReturn = $this->orm->QuestionGroups->insert($groupData);

            foreach ($group->questions AS $question) {
                $questionData = array(
                    "question" => $question->question,
                    "answer_required" => $question->answerRequired,
                    "add_evidence" => $question->addEvidence,
                    "evidence_required" => $question->evidenceRequired,
                    "add_expiry" => $question->addExpiry,
                    "expiry_required" => $question->expiryRequired,
                    "type_id" => $question->answerType,
                    "group_id" => $groupReturn['id'],
                    "client_id" => 000,
//                    "client_id" => $audit->data->client_id
                );
                $this->orm->Questions->insert($questionData);
            }
        }
        return;
    }

    public function retireAudit($id) {
        $audit = $this->orm->Audits[$id];
        if ($audit) {
            $data = array(
                "retired" => 1
            );
            $audit->update($data);
            return "success";
        }else{
            return "failure";
        }
    }

    public function unRetireAudit($id) {
        $audit = $this->orm->Audits[$id];
        if ($audit) {
            $data = array(
                "retired" => 0
            );
            return $audit->update($data);
        }
    }

}
