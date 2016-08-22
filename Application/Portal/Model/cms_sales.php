<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Core\BaseModel;

class cms_sales extends BaseModel{
    public function __construct() { 
        parent::__construct();
    }
    public function getSaleByContactId($id){
        $a = $this->prod_contacts->cms_sales->where("contactid", $id)->order("timestamp")->limit(1);
        foreach($a as $b){
            return $b;
        }
    }
    public function getAllSalesByContact($id){
        return $this->prod_contacts->cms_sales->where("contactid", $id)->order("productcode");
    }
}
