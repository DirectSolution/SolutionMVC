<?php

namespace SolutionMvc\Audit\Model;

use SolutionMvc\Model\BaseModel,
    SolutionMvc\Library\Helper,
    SolutionMvc\Audit\Controller\AssettypeController;

/**
 * Description of Asset
 *
 * @author dhayward
 */
class Asset extends BaseModel {

    protected $assetType;
    protected $helpers;

    public function __construct() {
        parent::__construct();
        $this->helpers = new Helper();
//        $this->assetType = new AssettypeController();
    }

    public function getAll($client) {
        return $this->prod_audit->Assets->where("client_id", $client)->and("retired", 0);
    }

    public function getAllByType($id, $client) {
        return $this->prod_audit->Assets->where("client_id", $client)->where("AssetTypes_id", $id)->and("retired", 0);
    }

    public function getAllByGroup($id, $client) {
        return $this->prod_audit->Assets->where("client_id", $client)->where("AssetGroups_id", $id)->and("retired", 0);
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
        return $this->prod_audit->Assets[$id];
    }

    public function getAssetsById($id) {
        return $this->prod_audit->Assets->where("id", $id)->where("retired", 0);
    }

    public function getOneByIdArray($id) {
        return $this->assetArray($this->getOneByID($id));
    }

    public function assetArray($asset) {

        $assignment = new \SolutionMvc\Audit\Model\Assignment();
        if ($asset) {
            return array(
                "id" => (int) $asset['id'],
                "forename" => isset($asset['forename']) ? $asset['forename'] : null,
                "middlename" => isset($asset['middlename']) ? $asset['middlename'] : null,
                "surname" => isset($asset['surname']) ? $asset['surname'] : null,
                "unique_ref_code" => isset($asset['unique_ref_code']) ? $asset['unique_ref_code'] : null,
                "address1" => isset($asset['address1']) ? $asset['address1'] : null,
                "address2" => isset($asset['address2']) ? $asset['address2'] : null,
                "county" => isset($asset->Counties['name']) ? $asset->Counties['name'] : null,
                "country" => isset($asset->Countries['name']) ? $asset->Countries['name'] : null,
                "postcode" => isset($asset['postcode']) ? $asset['postcode'] : null,
                "dob" => isset($asset['dob']) ? $asset['dob'] : null,
                "start_date" => isset($asset['start_date']) ? $asset['start_date'] : null,
                "created_date" => isset($asset['created_date']) ? $asset['created_date'] : null,
                "updated_date" => isset($asset['updated_date']) ? $asset['updated_date'] : null,
                "client_id" => (int) isset($asset['client_id']) ? $asset['client_id'] : null,
                "group_id" => isset($asset['AssetGroups_id']) ? $asset['AssetGroups_id'] : null,
                "group_name" => isset($asset->AssetGroups['name']) ? $asset->AssetGroups['name'] : null,
                "type_id" => isset($asset['AssetTypes_id']) ? $asset['AssetTypes_id'] : null,
                "contact_number" => isset($asset['contact_number']) ? $asset['contact_number'] : null,
                "contact_email" => isset($asset['contact_email']) ? $asset['contact_email'] : null,
                "type_name" => isset($asset->AssetTypes['name']) ? $asset->AssetTypes['name'] : null,
                "default" => isset($asset['Audits_id']) ? $asset['Audits_id'] : null,
//            "image" => "http://localhost/Filestore/images/".$asset['client_id']."/Assets/".$asset['image']
                "image" => $this->checkForImage($asset['client_id'], $asset['image']),
                "Audits" => $assignment->getAssetAssignmentsByAsset($asset['id'])
            );
        } else {
            return false;
        }
    }

    public function checkForImage($client, $image = null) {
        if ($image != null) {
            return "https://portal.solutionhost.co.uk/Filestore/images/" . $client . "/Assets/" . $image;
        } else {
            return "https://portal.solutionhost.co.uk/Filestore/images/no-image.jpg";
        }
    }

    public function setAsset($asset, $client) {

        return $this->prod_audit->Assets->insert($this->buildInsertAssetArray($asset, $client));
    }

    public function update($assetArray, $client, $id){        
        $asset = $this->prod_audit->Assets[$id];        
        if($asset){
            $asset->update($this->buildInsertAssetArray($assetArray, $client));
            return "success";
        }else{
            return "failure";
        }
        
      
        
    }
    
    public function buildInsertAssetArray($asset, $client) {
        $this->assetType = new AssettypeController();
//die(print_r($asset));
        $assetType = $this->assetType->getOne($asset['AssetTypes_id']);

        if (isset($asset['contact'])) {
            if (strpos($asset['contact'], "/") !== false) {
                $both = explode("/", $asset['contact']);
                if (strpos($both[0], '@') !== false) {
                    $asset['contact_email'] = $both[0];
                    $asset['contact_number'] = $both[1];
                } else {
                    $asset['contact_email'] = $both[1];
                    $asset['contact_number'] = $both[0];
                }
            } else if (strpos($asset['contact'], '@') !== false) {
                $asset['contact_email'] = $asset['contact'];
            } else {
                $asset['contact_number'] = $asset['contact'];
            }
        }
//die(print_R($asset));
        return array(
            "forename" => $asset['forename'],
            "middlename" => isset($asset['middlename']) ? $asset['middlename'] : null,
            "surname" => isset($asset['surname']) ? $asset['surname'] : null,
            "unique_ref_code" => isset($asset['unique_ref_code']) ? $asset['unique_ref_code'] : null,
            "address1" => isset($asset['address1']) ? $asset['address1'] : null,
            "address2" => isset($asset['address2']) ? $asset['address2'] : null,
            "Counties_id" => ((int) isset($asset['Counties_id'])) ? $asset['Counties_id'] : 89,
            "Countries_id" => ((int) isset($asset['Countries_id'])) ? (int) $asset['Countries_id'] : 229,
            "postcode" => isset($asset['postcode']) ? $asset['postcode'] : null,
            "dob" => isset($asset['dob']) ? $this->helpers->convertDayMonthYearToMysqlDataTime($asset['dob']) : null,
            "start_date" => isset($asset['start_date']) ? $this->helpers->convertDayMonthYearToMysqlDataTime($asset['start_date']) : null,
            "created_date" => new \SolutionORM\Controllers\LiteralController("NOW()"),
            "client_id" => $client,
            "AssetGroups_id" => (int) isset($assetType['AssetGroups_id']) ? $assetType['AssetGroups_id'] : null,
            "AssetTypes_id" => (int) isset($asset['AssetTypes_id']) ? $asset['AssetTypes_id'] : null,
            "contact_name" => isset($asset['contactname'])? $asset['contactname'] : null,
            "contact_number" => isset($asset['contact_number']) ? $asset['contact_number'] : null,
            "contact_email" => isset($asset['contact_email']) ? $asset['contact_email'] : null,
            "Audits_id" => isset($asset['AuditDefault_id']) ? $asset['AuditDefault_id'] : null,
//            "image" => ($asset->filename->FileName) ? $asset->filename->FileName : null
        );
    }

//    public function buildInsertAssetArray($asset, $client) {
//
//        return array(
//            "forename" => $asset->forename,
//            "middlename" => $asset->middlename,
//            "surname" => $asset->surname,
//            "unique_ref_code" => $asset->unique_ref_code,
//            "address1" => $asset->address1,
//            "address2" => $asset->address2,
//            "Counties_id" => (int) $asset->county->id,
//            "Countries_id" => (int) $asset->country->id,
//            "postcode" => $asset->postcode,
//            "dob" => $asset->dob,
//            "start_date" => $asset->start_date,
//            "created_date" => new \SolutionORM\Controllers\LiteralController("NOW()"),
//            "client_id" => (int) $client,
//            "AssetGroups_id" => (int) $asset->AssetGroups_id->id,
//            "AssetTypes_id" => (int) $asset->AssetTypes_id->id,
//            "image" => $asset->filename->FileName,
//        );
//    }

    public function retire($id) {
        $asset = $this->prod_audit->Assets[$id];
        if ($asset) {
            $asset->update(array("retired" => 1));
            return "success";
        } else {
            return "failure";
        }
    }

    public function unRetire($id) {
        //Check the audit exists first.
        $asset = $this->prod_audit->Assets[$id];
        if ($asset) {
            $asset->update(array("retired" => 0));
            return "success";
        } else {
            return "failure";
        }
    }

    public function allAuditsNotInUse($client, $audit) {
        $assignments = new Assignment();
        $assigned = $assignments->getAssetAssignmentsByAudit($audit);
        return $this->prod_audit->Assets->where("client_id", $client)->and("NOT id", $assigned);
    }

    public function allAssetsNotInUseArray($client, $audit) {
        $assets = array();
        foreach ($this->allAuditsNotInUse($client, $audit) as $ass) {
            foreach ($this->getAssetsById($ass['id']) as $asset) {
                $assets[] = array(
                    "id" => (int) $asset['id'],
                    "name" => $asset['forename'] . " " . $asset['middlename'] . " " . $asset['surname'],
//                    "middlename" => $asset['middlename'],
//                    "surname" => $asset['surname']
                );
            }
        }
        return $assets;
    }

    public function allAssetsNotInUse($client, $list) {
        $ids = array();

//        die(var_dump($list));

        foreach ($list as $l) {
            $ids[] = (int) $l['id'];
        }
        $return = array();


        $assets = $this->prod_audit->Assets->where("NOT id", $ids)->and("client_id", $client)->and("retired", 0);
//        print (string)$this->prod_audit->Assets->where("NOT id", $ids)->and("client_id", $client)->and("retired", 0);
//        die();
        foreach ($assets as $asset) {
            $return[] = array(
                "Name" => $asset['forename'] . " " . $asset['surname'],
                "Value" => $asset['id']
            );
        }
        return $return;
    }

}
