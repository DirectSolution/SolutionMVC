<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Audit\Model\Assignment,
    SolutionMvc\Audit\Model\QuestionTypeOption,
    SolutionMvc\Audit\Model\Settings;

/**
 * Description of Audit
 *
 * @author dhayward
 */
class Audit extends BaseModel {

    protected $questionTypeOptions;
    /**
     * 
     * @param integer $id
     * @return object
     * @description Get one Audit by $id
     */
    
    public function __construct() {
        parent::__construct();
        $this->questionTypeOptions = new QuestionTypeOption();
    }
    
    public function getAuditDatasById($audit, $client) {
        $result = $this->prod_audit->AuditDatas[array("Audits_id" => $audit, "client_id" => $client, "retired" => 0)];
        if($result == null){
            $result = $this->prod_audit->AuditDatas[array("Audits_id" => $audit, "client_id" => 0, "retired" => 0)];
        }
        return $result;
//        return $this->prod_audit->AuditDatas[array("Audits_id" => $audit, "client_id" => $client, "retired" => 0)];
    }

    public function getAllAuditDatasById($audit) {
        return $this->prod_audit->AuditDatas->where("Audits_id", $audit)->where("retired", 0);
    }

    public function getAuditById($id) {
        return $this->prod_audit->AuditDatas[$id];
    }

    public function getAllAudits($client) {
        return $this->prod_audit->AuditDatas->where("client_id", $client)->and("retired", 0)->or("client_id", 000)->and("retired", 0);
    }
    
    public function arrayMapAudits($client){
                return
                array_map('iterator_to_array', iterator_to_array(
                        $this->prod_audit->AuditDatas->where("client_id", $client)->and("retired", 0)->or("client_id", 000)->and("retired", 0)
                )
        );
    }

    public function getByAssetId($client) {
        return $this->prod_audit->AuditDatas->where("client_id", $client)->and("retired", 0)->or("client_id", 000)->and("retired", 0);
    }

    /**
     * 
     * @param integer $id
     * @description Get one Audit by $id
     */
    public function auditByIdArray($id, $client) {
        $audit = $this->getAuditById($id, $client);
        $auditReturn = array(
            "id" => (int) $audit['id'],
            "name" => $audit['name'],
            "description" => $audit['description'],
            "created_at" => $audit['created_at'],
            "client_id" => (int) $audit['client_id'],
            "user_id" => (int) $audit['user_id'],
            "audit_type_id" => $audit['AuditTypes_id'],
            "audit_type_name" => $audit->AuditTypes['name'],
            "Audits_id" => (int) $audit['Audits_id'],
            "AuditGradings_id" => (int) $audit['AuditGradings_id']
        );
        return $auditReturn;
    }

    public function allAuditsArray($client) {
        $settings = new Settings();
        $audits = array();
        foreach ($this->getAllAudits($client) AS $key => $audit) {
            $audits[] = array(
                "id" => (int) $audit['id'],
                "name" => $audit['name'],
                "description" => $audit['description'],
                "created_at" => $audit['created_at'],
                "client_id" => (int) $audit['client_id'],
                "user_id" => (int) $audit['user_id'],
                "audit_type_id" => (int) $audit['AuditTypes_id'],
                "audit_type_name" => $audit->AuditTypes['name'],
                "reviewFrequency" => $audit->ReviewFrequencies['name'],
                "Audits_id" => (int) $audit['Audits_id'],
                "AuditGradings_id" => (int) $audit['AuditGradings_id'],
                "Default" => ($settings->getDefault($client) == $audit['Audits_id']) ? "Default" : null
            );
        }
        return $audits;
    }

    public function allAuditsNotInUse($client, $list) {
        $ids = array();
        foreach($list as $l){
            $ids[] = $l['id'];
        }        
        $return = array();
        $audits = $this->prod_audit->AuditDatas->where("NOT Audits_id", $ids)->where("client_id", $client)->and("retired", 0)->or("NOT Audits_id", $ids)->and("retired", 0)->and("client_id", 0);
        foreach ($audits as $audit) {
            $return[] = array(
                "Name" => $audit['name']. " - " . $audit['description'],
                "Value" => $audit['Audits_id'] 
            );
        }
        return $return;
    }



    public function allAuditsNotInUseArray($client, $asset) {
        $audits = array();
        foreach ($this->allAuditsNotInUse($client, $asset) as $aud) {
            $audit = $this->getAllAuditDatasById($aud);
            foreach ($audit AS $audit1) {
                $audits[] = array(
                    "id" => (int) $audit1['id'],
                    "name" => $audit1['name'],
                    "description" => $audit1['description'],
                    "created_at" => $audit1['created_at'],
                    "client_id" => (int) $audit1['client_id'],
                    "user_id" => (int) $audit1['user_id'],
                    "audit_type_id" => (int) $audit1['AuditTypes_id'],
                    "audit_type_name" => $audit1->AuditTypes['name'],
                    "Audits_id" => (int) $audit1['Audits_id']
                );
            }
        }
        return $audits;
    }

    public function insertNewAudit($audit, $user) {

        $auditReturn = $this->prod_audit->Audits->insert(
                $this->auditArray($user)
        );
        $auditDataReturn = $this->prod_audit->AuditDatas->insert(
                $this->auditDatasArray(
                        $audit, $user, $auditReturn['id']
                )
        );
        $totalPossible = 0;
        foreach ($audit['groups'] as $group) {
            
            $groupData = $this->groupArray($group, $user, $auditDataReturn);
            $groupReturn = $this->prod_audit->QuestionGroups->insert($groupData);
            foreach ($group['questions'] AS $question) {
              $totalPossible += $this->questionTypeOptions->getHighestById($question['QuestionTypes_id']);
               
                $questionData = $this->questionArray($question, $user, $groupReturn, $auditDataReturn);
                $this->prod_audit->Questions->insert($questionData);
            }
        }
        
        $auditDataReturn['max_score_possible'] = $totalPossible;
        $auditDataReturn->update();
        
        
        return;
    }

    public function updateAudit($audit, $user) {
        $totalPossible = 0;
        // Retire the old version and set date when it happened.        
        // $audit->data->id is the Original / Parent id 
        $auditExists = $this->prod_audit->AuditDatas[$audit['id']];
        if ($auditExists) {
            $auditExists->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            ));
        }
        // Insert the new audit.
        $auditReturn = $this->prod_audit->AuditDatas->insert(
                $this->auditDatasArray(
                        $audit, $user, $auditExists['Audits_id']
                )
        );
        foreach ($audit['groups'] as $group) {
            $groupData = $this->groupArray($group, $user, $auditReturn);
            $groupReturn = $this->prod_audit->QuestionGroups->insert($groupData);
            foreach ($group['questions'] AS $question) {
                $totalPossible += $this->questionTypeOptions->getHighestById($question['QuestionTypes_id']);
                $questionData = $this->questionArray($question, $user, $groupReturn, $auditReturn);
                $this->prod_audit->Questions->insert($questionData);
            }
        }

        $auditReturn['max_score_possible'] = $totalPossible;
        $auditReturn->update();
        
        return;
    }

    public function auditDatasArray($audit, $user, $id) {
        return $auditData = array(
            "name" => $audit['name'],
            "description" => $audit['description'],
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "client_id" => $user->client,
            "user_id" => $user->id,
            "AuditTypes_id" => (int) $audit['auditTypes'],
//            "audit_type_name" => $audit->auditTypes->name,
            "Audits_id" => (int) $id,
            "AuditGradings_id" => (int) $audit['auditGradings'],
            "ReviewFrequencies_id" => (int) $audit['reviewFrequency']
        );
    }

    public function auditArray($user) {
        return array(
            "client_id" => $user->client,
            "user_id" => $user->id
        );
    }

    public function groupArray($group, $user, $auditReturn) {
        return $groupData = array(
            "name" => $group['name'],
            "audit_id" => $auditReturn['id'],
            "client_id" => $user->client,
        );
    }

    public function questionArray($question, $user, $groupReturn, $auditReturn) {
        return $questionData = array(
            "question" => $question['question'],
//            "answer_required" => (int) $question->answerRequired,
            "answer_required" => 1,
            "add_evidence" => (int) isset($question['add_evidence']) ? $question['add_evidence'] : 0,
            "evidence_required" => (int) isset($question['evidence_required']) ? $question['evidence_required'] : 0,
            "add_expiry" => (int) isset($question['add_expiry']) ? $question['add_expiry'] : 0,
            "expiry_required" => (int) isset($question['expiry_required']) ? $question['expiry_required'] : 0,
            "QuestionTypes_id" => (int) isset($question['QuestionTypes_id']) ? $question['QuestionTypes_id'] : 0,
            "QuestionGroups_id" => (int) $groupReturn['id'],
            "client_id" => $user->client,
            "AuditDatas_id" => (int) $auditReturn['id']
        );
    }

    public function retireAudit($id, $client) {
        $audit = $this->prod_audit->AuditDatas[$id];
        if ($audit) {
            if ($audit['client_id'] == $client) {
                $audit->Audits->update(array("retired" => 1));
                $audit->update(array("retired" => 1));
                return "success";
            } else {
                return "failure";
            }
        } else {
            return "failure";
        }
    }

    public function unRetireAudit($id) {
        //Check the audit exists first.
        $audit = $this->prod_audit->AuditDatas[$id];
        if ($audit) {
            $audit->Audits->update(array("retired" => 0));
            $audit->update(array("retired" => 0));
            return "success";
        } else {
            return "failure";
        }
    }

    
    
}
