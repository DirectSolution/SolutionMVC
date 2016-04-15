<?php

namespace SolutionMvc\Model;

use SolutionMvc\Model\BaseModel;

/**
 * Description of Asset
 *
 * @author dhayward
 */
class Asset extends BaseModel {

    public function getAll($client) {       
        return $this->orm->Assets->where("client_id", $client)->where("retired", 0);
    }

    public function getAllByType($id, $client) {
        return $this->orm->Assets->where("client_id", $client)->where("AssetTypes_id", $id)->and("retired", 0);
    }
    
    public function getAllByGroup($id, $client) {
        return $this->orm->Assets->where("client_id", $client)->where("AssetGroups_id", $id)->and("retired", 0);
    }

    public function getAllByTypeArray($id, $client) {
        foreach ($this->getAllByType($id, $client) AS $key => $asset) {
            $assets[$key] = $this->assetArray($asset);
        }
        return $assets;
    }
    public function getAllByGroupArray($id, $client) {
        foreach ($this->getAllByGroup($id, $client) AS $key => $asset) {
            $assets[$key] = $this->assetArray($asset);
        }
        return $assets;
    }

    public function getAllArray($client) {
                
        foreach ($this->getAll($client) AS $key => $asset) {
            $assets[$key] = $this->assetArray($asset);
        }
        return $assets;
    }

    public function getOneByID($id) {
        return $this->orm->Assets[$id];
    }

    public function getOneByIdArray($id) {
        return $this->assetArray($this->getOneByID($id));
    }

    public function assetArray($asset) {
        return array(
            "id" => (int) $asset['id'],
            "forename" => $asset['forename'],
            "middlename" => $asset['middlename'],
            "surname" => $asset['surname'],
            "unique_ref_code" => $asset['unique_ref_code'],
            "address1" => $asset['address1'],
            "address2" => $asset['address2'],
            "county" => $asset->Counties['name'],
            "country" => $asset->Countries['name'],
            "postcode" => $asset['postcode'],
            "dob" => $asset['dob'],
            "start_date" => $asset['start_date'],
            "created_date" => $asset['created_date'],
            "updated_date" => $asset['updated_date'],
            "client_id" => (int) $asset['client_id'],
            "group_id" => $asset['AssetGroups_id'],
            "group_name" => $asset->AssetGroups['name'],
            "type_id" => $asset['AssetTypes_id'],
            "type_name" => $asset->AssetTypes['name'],
            "image" => $asset['image']
        );
    }

    public function setAsset($asset, $client) {
        $this->orm->Assets->insert($this->buildInsertAssetArray($asset, $client));
    }
    


    public function buildInsertAssetArray($asset, $client) {
                
        return array(
            "forename" => $asset->forename,
            "middlename" => $asset->middlename,
            "surname" => $asset->surname,
            "unique_ref_code" => $asset->unique_ref_code,
            "address1" => $asset->address1,
            "address2" => $asset->address2,
            "Counties_id" => (int) $asset->county,
            "Countries_id" => (int) $asset->country->id,
            "postcode" => $asset->postcode,
            "dob" => $asset->dob,
            "start_date" => $asset->start_date,
            "created_date" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "client_id" => (int) $client,
            "AssetGroups_id" => (int) $asset->AssetGroups_id->id,
            "AssetTypes_id" => (int) $asset->AssetTypes_id->id,
            "image" => $asset->filename,
        );
    }
    
    
        public function retire($id) {
        $asset = $this->orm->Assets[$id];
        if ($asset) {
            $asset->update(array("retired" => 1));
            return "success";
        } else {
            return "failure";
        }
    }

    public function unRetire($id) {
        //Check the audit exists first.
        $asset = $this->orm->Assets[$id];
        if ($asset) {
            $asset->update(array("retired" => 0));
            return "success";
        } else {
            return "failure";
        }
    }

}
