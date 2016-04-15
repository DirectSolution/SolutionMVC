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
        return $this->orm->AuditDatas[$id];
//        return $this->orm->Audits->where("id", $id)->and("client_id", $client);
    }

    public function getAllAudits($client) {
        return $this->orm->AuditDatas->where("client_id", $client)->where("retired", 0);
    }

    /**
     * 
     * @param integer $id
     * @description Get one Audit by $id
     */
    public function auditByIdArray($id, $client) {

        $audit = $this->getAuditById($id, $client);
        if ($audit['client_id'] === $client) {
//            foreach ($audits as $audit) {
            $auditReturn = array(
                "id" => (int) $audit['id'],
                "name" => $audit['name'],
                "description" => $audit['description'],
                "created_at" => $audit['created_at'],
                "client_id" => (int) $audit['client_id'],
                "user_id" => (int) $audit['user_id'],
                "audit_type_id" => $audit['AuditTypes_id'],
                "audit_type_name" => $audit->AuditTypes['name'],
                "Audits_id" => (int) $audit['Audits_id']
//                    "audit_type_name" => "string",
            );
//            }
            return $auditReturn;
        } else {
            return null;
        }
    }

    public function allAuditsArray($client) {
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
                "Audits_id" => (int) $audit['Audits_id']
            );
        }
        return $audits;
    }

//    public function insertNewAudit($audit, $user) {
//        $auditData = $this->auditArray($audit, $user);
//        $auditReturn = $this->orm->Audits->insert($auditData);
//        foreach ($audit->groups as $group) {
//            $groupData = $this->groupArray($group, $user, $auditReturn);
//            $groupReturn = $this->orm->QuestionGroups->insert($groupData);
//            foreach ($group->questions AS $question) {
//                $questionData = $this->questionArray($question, $user, $groupReturn, $auditReturn);
//                $this->orm->Questions->insert($questionData);
//            }
//        }
//        return;
//    }

    public function insertNewAudit($audit, $user) {
//        $auditData = ;
        $auditReturn = $this->orm->Audits->insert(
                $this->auditArray($user)
        );

        $auditDataReturn = $this->orm->AuditDatas->insert(
                $this->auditDatasArray(
                        $audit, $user, $auditReturn['id']
                )
        );

        foreach ($audit->groups as $group) {
            $groupData = $this->groupArray($group, $user, $auditDataReturn);
            $groupReturn = $this->orm->QuestionGroups->insert($groupData);
            foreach ($group->questions AS $question) {
                $questionData = $this->questionArray($question, $user, $groupReturn, $auditDataReturn);
                $this->orm->Questions->insert($questionData);
            }
        }
        return;
    }

    public function updateAudit($audit, $user) {
//        die(print_r($audit));
//        die(print_r($audit));
        // Retired the old version and set date when it happened.        
        // $audit->data->id is the Original / Parent id 
        $auditExists = $this->orm->AuditDatas[$audit->id];
        if ($auditExists) {
            $auditExists->update(array(
                "retired" => 1,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            ));
        }

        // Insert the new audit.
//        $auditData = $this->auditDatasArray($audit, $user, $audit->Audits_id);
//        die(print_r($auditData));
        $auditReturn = $this->orm->AuditDatas->insert(
                $this->auditDatasArray(
                        $audit, $user, $audit->Audits_id
                )
        );

//        die($auditReturn);
        foreach ($audit->groups as $group) {
            $groupData = $this->groupArray($group, $user, $auditReturn);
            $groupReturn = $this->orm->QuestionGroups->insert($groupData);
            foreach ($group->questions AS $question) {
                $questionData = $this->questionArray($question, $user, $groupReturn, $auditReturn);
                $this->orm->Questions->insert($questionData);
            }
        }
        return;
//         return "success";
    }

    public function auditDatasArray($audit, $user, $id) {
        return $auditData = array(
            "name" => $audit->name,
            "description" => $audit->description,
            "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "client_id" => $user->client,
            "user_id" => $user->id,
            "AuditTypes_id" => (int) $audit->auditTypes->id,
//            "audit_type_name" => $audit->auditTypes->name,
            "Audits_id" => (int) $id,
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
            "name" => $group->name,
            "audit_id" => $auditReturn['id'],
            "client_id" => $user->client,
        );
    }

    public function questionArray($question, $user, $groupReturn, $auditReturn) {
        return $questionData = array(
            "question" => $question->question,
            "answer_required" => (int) $question->answerRequired,
            "add_evidence" => (int) $question->addEvidence,
            "evidence_required" => (int) $question->evidenceRequired,
            "add_expiry" => (int) $question->addExpiry,
            "expiry_required" => (int) $question->expiryRequired,
            "type_id" => (int) $question->answerType->id,
            "group_id" => (int) $groupReturn['id'],
            "client_id" => $user->client,
            "audit_id" => (int) $auditReturn['id']
        );
    }

//    public function updateAudit($audit) {
//        $auditExists = $this->orm->Audits[$audit->data->id];
//
//        if ($auditExists) {
//            $auditData = $this->auditArray($audit);
//            $auditReturn = $auditExists->update($auditData);
//            foreach ($audit->data->groups as $group) {
//                $groupExists = $this->orm->QuestionGroups($group->id);
//                if ($groupExists) {
//                    $groupData = $this->groupArray($group, $auditReturn);
//                    $groupReturn = $groupExists->update($groupData);
//                    foreach ($group->questions AS $question) {
//                        $questionExists = $this->orm->Questions($question->id);
//                        if ($questionExists) {
//                            $questionData = $this->questionArray($question, $groupReturn, $auditReturn);
//                            $questionExists->update($questionData);
//                        }else{
//                            
//                        }
//                    }
//                }else{
//                    $groupData = $this->groupArray($group, $auditReturn);
//                    
//                }
//            }
//        }
//        return;
//    }

    public function retireAudit($id) {
        $audit = $this->orm->Audits[$id];
        if ($audit) {
            $audit->update(array("retired" => 1));
            return "success";
        } else {
            return "failure";
        }
    }

    public function unRetireAudit($id) {
        //Check the audit exists first.
        $audit = $this->orm->Audits[$id];
        if ($audit) {
            $audit->update(array("retired" => 0));
            return "success";
        } else {
            return "failure";
        }
    }

}
