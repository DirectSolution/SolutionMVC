<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Model\AssetType;

/**
 * Description of AssetGroup
 *
 * @author dhayward
 */
class AssetGroup extends BaseModel {

    var $assetType;
    
    public function __construct() {
        parent::__construct();
        $this->assetType = new AssetType();
    }
    
    public function getAll($client) {
        return $this->orm->AssetGroups->where("client_id", $client)->where("retired", 0);
    }

    public function getAllWithTypes($client){
        return $this->orm->AssetGroups->where("client_id", $client)->where("retired", 0);
    }

    public function getAllWithTypesArray($client){
        foreach ($this->getAllWithTypes($client) AS $group) {
            $assetGroups[] = array(
            "id" => (int) $group['id'],
            "name" => $group['name'],
            "client_id" => $group['client_id'],
            "user_id" => $group['user_id'],
            "forename_label"=>$group['forename_label'],
            "middlename_label"=>$group['middlename_label'],
            "surname_label"=>$group['surname_label'],
            "dob_label"=>$group['dob_label'],
            "start_label"=>$group['start_label'],
            "uniquecode_label"=>$group['uniquecode_label'],
            "AssetTypes" => $this->assetType->getAllByGroupArray($client, $group['id']),
            );
        }
        return $assetGroups;        
    }
    
    public function getAllArray($client) {
        foreach ($this->getAll($client) AS $group) {
            $assetGroups[] = $this->assetGroupArray($group);
        }
        return $assetGroups;
    }

    public function getOneByID($id) {
        return $this->orm->AssetGroups[$id];
    }

    public function getOneByIdArray($id) {
        return $this->assetGroupArray($this->getOneByID($id));
    }

    public function assetGroupArray($group) {
        return array(
            "id" => (int) $group['id'],
            "name" => $group['name'],
            "client_id" => $group['client_id'],
            "user_id" => $group['user_id']
        );
    }

}
