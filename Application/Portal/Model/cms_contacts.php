<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Core\BaseModel;

class cms_contacts extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getContact($id) {
        return $this->prod_contacts->cms_contacts[$id];
    }

}
