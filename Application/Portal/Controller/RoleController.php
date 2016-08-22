<?php

namespace SolutionMvc\Portal\Controller;

use SolutionMvc\Core\Controller,
    SolutionMvc\Portal\Model\Role;

class RoleController extends Controller {

    protected $role;
    protected $security;

    public function __construct() {
        parent::__construct();
        $this->role = new Role();
        $this->security = new \SolutionMvc\Core\Security();
        $this->token = $this->getToken();
    }

    public function indexAction() {
        if ($this->isAuth(39)) {

            echo $this->twig->render("Portal/Role/index.html.twig", array(
                "roles" => $this->role->getRoles($this->token->user->client)
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "Role/",
                "action" => "index"
            ));
        }
    }

    public function viewAction($id) {
        if ($this->isAuth(39)) {
            echo $this->twig->render("Portal/Role/view.html.twig", array(
                "role" => $this->role->getRole($id),
                "rights" => $this->role->getRoleRights($id)
            ));
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "Role/",
                "action" => "View/$id"
            ));
        }
    }

    public function createAction() {
        if ($this->isAuth(35)) {
            if ($this->requestType() == "GET") {

                $permissions = array();

                foreach ($this->role->getAllPermissions($this->getToken()->user->client) as $permission) {
                    $permissions[$permission->ACLPermissionGroups['name']][] = array($permission);
                }

                echo $this->twig->render("Portal/Role/create.html.twig", array(
//                    "permissions" => $this->role->getAllPermissions()
                    "permissions" => $permissions
                ));
            } elseif ($this->requestType() == "POST") {

                $roleID = $this->role->setNewRole($this->requestObject(), $this->token->user->client);
                $this->role->setRoleRights($this->requestObject(), $roleID);
                $this->setSession("success", "Successfully added role.");
                $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Portal/Role/View/' . $roleID));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "Role/",
                "action" => "Create"
            ));
        }
    }

    public function updateAction($id) {
        if ($this->isAuth(38)) {
            if ($this->requestType() == "GET") {
                $rights = array();
                foreach ($this->role->getAllCurrentRights($id) AS $right) {
                    $rights[] = $right['ACLPermissions_id'];
                }
                foreach ($this->role->getAllPermissionsNotAssigned($rights, $this->getToken()->user->client) as $permission) {
                    $permissions[$permission->ACLPermissionGroups['name']][] = array($permission);
                }
                echo $this->twig->render("Portal/Role/update.html.twig", array(
                    "permissions" => $permissions,
                    "role" => $this->role->getRole($id),
                    "currentPermissions" => $this->role->getAllCurrentRights($id)
                ));
            } elseif ($this->requestType() == "POST") {

                $this->role->updateRole($this->requestObject(), $id);
                $this->role->updateRolePermission($this->requestObject(), $id);
                $this->setSession("success", "Successfully edited role.");
                $this->response->setHeaders(header('Location: ' . HTTP_ROOT . 'Portal/Role/View/' . $id));
            }
        } else {
            echo $this->twig->render("Portal/Login/login.html.twig", array(
                "project" => "Portal/",
                "controller" => "Role/",
                "action" => "Update"
            ));
        }
    }

    public function retireRoleAction() {
        $request = $this->requestObject();
        if ($this->isAuth(36)) {
            $this->role->retireRole($request['id'], $this->token->user->client);
            return print json_encode("success");
        } else {
            return print json_encode("You are not authorised to complete this action");
        }
        
    }

}
