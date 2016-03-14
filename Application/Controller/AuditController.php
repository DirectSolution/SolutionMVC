<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Controller;

/**
 * Description of AuditController
 *
 * @author doug
 */
class AuditController {

    public function indexAction() {
        return print '
            [
                {
                "id":"1",
                "name":"Some audit",
                "created_at":"11/11/16"
                },
                {
                "id":"2",
                "name":"Some audit 2",
                "created_at":"09/01/16"
                }
            ]            
            ';
    }

    public function newAction() {
        
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata);

        $response = new \SolutionMvc\Core\Response();
        $response->result = "Password Correct";
        $response->status = "success";
        $response->username = "dhayward";
        $response->data = $request;

        return print json_encode($response);
    }

    public function getAction($id = "1") {
        switch ($id) {
            case "1";
                $response = '                
                {
                "name": "Some audit",
                "description": "A description",
                "auditType": {"id": "1"},
                "groups": [
                    {
                        "name": "Group 1",
                        "questions": [
                            {
                                "question": "Question 1",
                                "answerType": {"id": "2"},
                                "answerRequired": "1",
                                "addEvidence": "0",
                                "evidenceRequired": "1",
                                "addExpiry": "1",
                                "expiryRequired": "1"
                            },
                            {
                                "question": "Question 2",
                                "answerType": {"id": "3"},
                                "answerRequired": "0",
                                "addEvidence": "0",
                                "evidenceRequired": "0",
                                "addExpiry": "1",
                                "expiryRequired": "0"
                            },
                            {
                                "question": "Question 3",
                                "answerType": {"id": "1"},
                                "answerRequired": "1",
                                "addEvidence": "1",
                                "evidenceRequired": "0",
                                "addExpiry": "0",
                                "expiryRequired": "1"
                            }
                        ]
                    },
                    {
                        "name": "Group 2",
                        "questions": [
                            {
                                "question": "Question 1",
                                "answerType": {"id": "2"},
                                "answerRequired": "1",
                                "addEvidence": "1",
                                "evidenceRequired": "1",
                                "addExpiry": "1",
                                "expiryRequired": "1"
                            },
                            {
                                "question": "Question 2",
                                "answerType": {"id": "3"},
                                "answerRequired": "0",
                                "addEvidence": "0",
                                "evidenceRequired": "0",
                                "addExpiry": "0",
                                "expiryRequired": "0"
                            },
                            {
                                "question": "Question 3",
                                "answerType": {"id": "1"},
                                "answerRequired": "1",
                                "addEvidence": "1",
                                "evidenceRequired": "0",
                                "addExpiry": "1",
                                "expiryRequired": "1"
                            }
                        ]
                    }
                ]
            }
                ';
                break;
            case "2";
                $response = '
                {
                "name": "Some audit 2",
                "description": "A description 2",
                "auditType": {"id": "1"},
                "groups": [
                    {
                        "name": "Group 1",
                        "questions": [
                            {
                                "question": "Question 1",
                                "answerType": {"id": "2"},
                                "answerRequired": "1",
                                "addEvidence": "0",
                                "evidenceRequired": "1",
                                "addExpiry": "1",
                                "expiryRequired": "1"
                            },
                            {
                                "question": "Question 2",
                                "answerType": {"id": "3"},
                                "answerRequired": "0",
                                "addEvidence": "0",
                                "evidenceRequired": "0",
                                "addExpiry": "1",
                                "expiryRequired": "0"
                            },
                            {
                                "question": "Question 3",
                                "answerType": {"id": "1"},
                                "answerRequired": "1",
                                "addEvidence": "1",
                                "evidenceRequired": "0",
                                "addExpiry": "0",
                                "expiryRequired": "1"
                            }
                        ]
                    },
                    {
                        "name": "Group 2",
                        "questions": [
                            {
                                "question": "Question 1",
                                "answerType": {"id": "2"},
                                "answerRequired": "1",
                                "addEvidence": "1",
                                "evidenceRequired": "1",
                                "addExpiry": "1",
                                "expiryRequired": "1"
                            },
                            {
                                "question": "Question 2",
                                "answerType": {"id": "3"},
                                "answerRequired": "0",
                                "addEvidence": "0",
                                "evidenceRequired": "0",
                                "addExpiry": "0",
                                "expiryRequired": "0"
                            },
                            {
                                "question": "Question 3",
                                "answerType": {"id": "1"},
                                "answerRequired": "1",
                                "addEvidence": "1",
                                "evidenceRequired": "0",
                                "addExpiry": "1",
                                "expiryRequired": "1"
                            }
                        ]
                    }
                ]
            }
                ';
                break;
        }
        return print $response;
    }

    public function deleteAuditAction() {
        
    }

}
