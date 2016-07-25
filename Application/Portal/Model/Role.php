<?php

namespace SolutionMvc\Portal\Model;

use SolutionMvc\Model\BaseModel;

class Role extends BaseModel {

    public function __construct() {
        parent::__construct();
    }

    public function getRoles($client) {
        return $this->prod_audit->ACLRoles->where("Clients_id", (int) $client)->and("retired", 0)->or("Clients_id", 0)->and("retired", 0);
    }

    public function getRole($id) {
        return $this->prod_audit->ACLRoles[$id];
    }

    public function getRoleRights($id) {
        return $this->prod_audit->ACLRoleRights->where("ACLRoles_id", $id);
    }

    public function getAllPermissions() {
        return $this->prod_audit->ACLPermissions->where("non_client", 0);
    }

    public function getAllCurrentRights($id) {
        return $this->prod_audit->ACLRoleRights->where("ACLRoles_id", $id);
    }

    public function getAllPermissionsNotAssigned($rights) {

        return $this->prod_audit->ACLPermissions->where("NOT id ", $rights)->and("non_client", 0);
    }

    public function setUpdate($request, $id) {
        $role = $this->prod_audit->ACLRoleMembers[array("Users_id" => $id)];
        if ($role) {
            $role->update(array(
                "ACLRoles_id" => $request['role']
            ));
        } else {
            $this->prod_audit->ACLRoleMembers->insert(
                    array(
                        "Users_id" => $id,
                        "ACLRoles_id" => $request['role']
            ));
        }
        return;
    }

    public function setUserRole($id, $request) {
        return $this->prod_audit
                        ->ACLRoleMembers
                        ->insert(
                                array(
                                    "Users_id" => $id,
                                    "ACLRoles_id" => $request['role']
                                )
        );
    }

    public function setNewRole($request, $client) {
        return $this->prod_audit->ACLRoles->insert(array(
                    "name" => $request['name'],
                    "description" => $request['description'],
                    "Clients_id" => $client
        ));
    }

    public function setRoleRights($request, $role) {
        foreach ($request['permissions'] AS $permission) {
            $this->prod_audit->ACLRoleRights->insert(array(
                "ACLRoles_id" => $role,
                "ACLPermissions_id" => $permission
            ));
        }
        return;
    }

    public function updateRole($request, $id) {
        $role = $this->prod_audit->ACLRoles[$id];
        if ($role) {
            $role->update(array(
                "name" => $request['name'],
                "description" => $request['description'],
            ));
        }
        return;
    }

    public function updateRolePermission($request, $role) {
        $get = $this->prod_audit->ACLRoleRights->where("ACLRoles_id", $role);
        $get->delete();
        foreach ($request['permissions'] as $permission) {
            print(string)$this->prod_audit->ACLRoleRights->insert(array(
                "ACLRoles_id" => $role,
                "ACLPermissions_id" => $permission
            ));
        }
    }
    
    public function retireRole($request, $client){
        $role = $this->prod_audit->ACLRoles[array("id" =>$request['id'], "Clients_id" => $client)];
        if($role){
            $role->update(array('retired' => 1));
        }
        return "success";
    }

}
