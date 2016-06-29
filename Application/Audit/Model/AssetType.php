<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of AssetType
 *
 * @author dhayward
 */
class AssetType extends BaseModel {

    public function getAll($client) {
        return $this->prod_audit->AssetTypes->where("client_id", $client)->and("retired", 0)->or("client_id", 0)->and("retired", 0);
    }

    public function getAllByGroup($client, $id) {
        return $this->prod_audit->AssetTypes->where("client_id", $client)->and("retired", 0)->and("AssetGroups_id", $id)->or("client_id", 0)->and("retired", 0)->and("AssetGroups_id", $id);
    }

    public function getAllArray($client) {
        foreach ($this->getAll($client) AS $group) {
            $assetGroups[] = $this->assetTypeArray($group);
        }
        return $assetGroups;
    }

    public function getOneByID($id) {
        return $this->prod_audit->AssetTypes[$id];
    }

    public function getOneByIdArray($id) {
        return $this->assetTypeArray($this->getOneByID($id));
    }

    public function getAllByGroupArray($client, $id) {
        foreach ($this->getAllByGroup($client, $id) AS $type) {
            $assetTypes[] = $this->assetTypeArray($type);
        }
        return $assetTypes;
    }

    public function assetTypeArray($type) {
        return array(
            "id" => (int) $type['id'],
            "name" => $type['name'],
            "client_id" => $type['client_id'],
            "user_id" => $type['user_id'],
            "AssetGroups_id" => $type['AssetGroups_id'],
            "AssetGroups_name" => $type->AssetGroups['name']
        );
    }



}
