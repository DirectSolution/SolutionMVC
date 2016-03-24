<?php

namespace SolutionMvc\Model\Fixtures;
/**
 * Description of AuditFixtures
 *
 * @author dhayward
 */
class AuditFixtures {

    public function optionValues(){
        $response = '{}';
    }
    
    public function answerTypeFixtures(){
        $response = '{}';
    }
    
    public function auditTypeFixtures(){
        $response = '{}';
    }
    
    public function auditFixtures($id) {
        if ($id == 1) {
            $response = '                
                    {
                    "name": "Audit Sample Number 1",
                    "description": "A description",
                    "auditType": {"id": 3},
                    "groups": [
                        {
                            "id" : 1,
                            "name": "Group 1",
                            "questions": [
                                {
                                    "id":1,
                                    "question": "Question 1.1",
                                    "answerType": {"id": 2},
                                    "answerRequired": 1,
                                    "addEvidence": 0,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                },
                                {
                                "id":2,
                                    "question": "Question 1.2",
                                    "answerType": {"id": 3},
                                    "answerRequired": 0,
                                    "addEvidence": 0,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                },
                                {
                                "id":3,
                                    "question": "Question 1.3",
                                    "answerType": {"id": 1},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                }
                            ]
                        },
                        {
                            "id" : 2,
                            "name": "Group 2",
                            "questions": [
                                {
                                "id":4,
                                    "question": "Question 2.1",
                                    "answerType": {"id": 2},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 0,
                                    "addExpiry": 0,
                                    "expiryRequired": 0
                                },
                                {
                                "id":5,
                                    "question": "Question 2.2",
                                    "answerType": {"id": 3},
                                    "answerRequired": 0,
                                    "addEvidence": 0,
                                    "evidenceRequired": 0,
                                    "addExpiry": 0,
                                    "expiryRequired": 0
                                },
                                {
                                "id":6,
                                    "question": "Question 2.3",
                                    "answerType": {"id": 1},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                }
                            ]
                        }
                    ]
                }
                ';
        } else if ($id == 2) {

            $response = '
                    {
                    "name": "Second Audit Example",
                    "description": "A description 2",
                    "auditType": {"id": 1},
                    "groups": [
                        {
                            "id" : 1,
                            "name": "Group 1",
                            "questions": [
                                {
                                "id": 3,
                                    "question": "Question 1.1",
                                    "answerType": {"id": 2},
                                    "answerRequired": 1,
                                    "addEvidence": 0,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                },
                                {
                                "id": 4,
                                    "question": "Question 1.2",
                                    "answerType": {"id": 3},
                                    "answerRequired": 0,
                                    "addEvidence": 0,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 0
                                },
                                {
                                "id": 5,
                                    "question": "Question 1.3",
                                    "answerType": {"id": 1},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                },
                                {
                                "id": 6,
                                    "question": "Question 1.4",
                                    "answerType": {"id": 1},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                }
                            ]
                        },
                        {
                            "id" : 2,
                            "name": "Group 2",
                            "questions": [
                                {
                                "id": 7,
                                    "question": "Question 1",
                                    "answerType": {"id": 2},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 1,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                },
                                {
                                "id": 8,
                                    "question": "Question 2",
                                    "answerType": {"id": 3},
                                    "answerRequired": 0,
                                    "addEvidence": 0,
                                    "evidenceRequired": 0,
                                    "addExpiry": 0,
                                    "expiryRequired": 0
                                },
                                {
                                "id": 9,
                                    "question": "Question 3",
                                    "answerType": {"id": 1},
                                    "answerRequired": 1,
                                    "addEvidence": 1,
                                    "evidenceRequired": 0,
                                    "addExpiry": 1,
                                    "expiryRequired": 1
                                }
                            ]
                        }
                    ]
                }
               ';
        }
        return $response;
    }

}
