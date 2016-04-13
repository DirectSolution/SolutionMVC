<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

class Helpers extends BaseModel {

    public function getCounties() {
        return $this->orm->Counties;
    }

    public function getCountiesArray() {
        foreach ($this->getCounties() AS $county) {
            $counties[] = array(
                "id" => (int)$county['id'],
                "name" => $county['name']
            );
        }
        return $counties;
    }

    public function getCountries() {
        return $this->orm->Countries->where('enabled', 1)->order("name ASC");
    }

    public function getCountriesArray() {
        foreach ($this->getCountries() AS $country) {
            $countries[] = array(
                "id" => (int)$country['id'],
                "country_code" => $country['country_code'],
                "name" => $country['name']
            );
        }
        return $countries;
    }

}
