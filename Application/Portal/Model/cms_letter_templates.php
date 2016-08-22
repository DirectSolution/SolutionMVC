<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Core\BaseModel;

/**
 * Description of cms_letter_templates
 *
 * @author dhayward
 */
class cms_letter_templates extends BaseModel {
    // Get one template by ID, and turn them into a pretty array. Easier to read on the other end but can't be used to access joins.
    public function get($id) {
        return array_map('iterator_to_array', iterator_to_array(
                        $this->prod_contacts->cms_letter_templates
                                ->where('id', $id)
                                ->where('retired', 0)
                                ->order('name DESC')
                )
        );
    }

    // Return one template by ID, contains all data replated to the row including any FK Table data
    public function getOne($id) {
        return $this->prod_contacts->cms_letter_templates[$id];
    }

    // Get all template, and turn them into a pretty array. Easier to read on the other end but can't be used to access joins.
    public function getAll() {
        return array_map('iterator_to_array', iterator_to_array(
                        $this->prod_contacts->cms_letter_templates
                                ->where('retired', 0)
                                ->order('name DESC')
                )
        );
    }

    // Get one header by ID
    public function getHeader($id) {
        return $this->prod_contacts->cms_letter_headers[$id];
    }

    // Get all active headers.
    public function getHeaders() {
        return $this->prod_contacts->cms_letter_headers->where("retired", 0);
    }

    // Get one footer by ID.
    public function getFooter($id) {
        return $this->prod_contacts->cms_letter_footers[$id];
    }

    // Get all active footers.
    public function getFooters() {
        return $this->prod_contacts->cms_letter_footers->where("retired", 0);
    }

    // Get one token by ID.
    public function getToken($id) {
        return $this->prod_contacts->cms_letter_template_tokens[$id];
    }

    //  Get all active tokens.
    public function getTokens() {
        return $this->prod_contacts->cms_letter_template_tokens->where("retired", 0);
    }

    // Save a new header.
    public function setHeader($request) {
        return $this->prod_contacts->cms_letter_headers->insert(array(
                    "name" => $request['name'],
                    "html" => $request['html']
        ));
    }

    // Save a new footer.
    public function setFooter($request) {
        return $this->prod_contacts->cms_letter_footers->insert(array(
                    "name" => $request['name'],
                    "address" => $request['html']
        ));
    }

    // Save a new token.
    public function setToken($request) {
        return $this->prod_contacts->cms_letter_template_tokens->insert(array(
                    "name" => $request['name']
        ));
    }

    // Save a new template.
    public function set($request, $html, $user) {
        return $this->prod_contacts->cms_letter_templates->insert(array(
                    "name" => ($request['name']) ? $request['name'] : "name",
                    "body" => $html,
                    "created_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                    "created_by" => $user,
                    "cms_letter_headers_id" => $request['header_type'],
                    "cms_letter_footers_id" => $request['footer_type']
        ));
    }

    // Update template by ID. 
    public function update($id, $request, $user) {
        $template = $this->prod_contacts->cms_letter_templates[$id];
        if ($template) {
            return array(
                "status" => "success",
                "data" => $template->update(
                        array(
                            "name" => $request['name'],
                            "body" => $request['body'],
                            "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "updated_by" => $user
            )));
        } else {
            return array(
                "error" => "The template you are trying to update does not exist!",
                "status" => "fail"
            );
        }
    }

    // Retire template by ID. 
    public function retire($id, $user) {
        $template = $this->prod_contacts->cms_letter_templates[$id];
        if ($template) {
            return array(
                "status" => "success",
                "data" => $template->update(
                        array(
                            "retired" => 1,
                            "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                            "updated_by" => $user
                ))
            );
        } else {
            return array(
                "error" => "The template you are trying to retire does not exist!",
                "status" => "fail"
            );
        }
    }

    // Un Retire Template.
    public function unretire($id, $user) {
        $template = $this->prod_contacts->cms_letter_templates[$id];
        if ($template) {
            $template->update(array(
                "retired" => 0,
                "updated_at" => new \SolutionORM\Controllers\LiteralController("NOW()"),
                "updated_by" => $user
            ));
        } else {
            return array(
                "error" => "The template you are trying to un-retire does not exist!",
                "status" => "fail"
            );
        }
    }

}
