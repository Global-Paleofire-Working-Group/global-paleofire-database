<?php

abstract class WebAppRole {

    /**
     * Name of the webApp Role
     * */
    private $_wepAppRole_name;
    private $_webAppRole_permissions;
    private $_webAppRole_read;
    private $_webAppRole_write;
    private $_webAppRole_delete;

    /**
     * Constuct a WebAppRole
     * @param $login    login of the webApp user
     */
    public function __construct($role_name) {
        if ($role_name != NULL && $role_name != "") {
            $this->_wepAppRole_name = $role_name;
        } else {
            throw new Exception("The role's name can't be NULL");
        }
        $this->_webAppRole_permissions = array();
        $this->_webAppRole_read = 0;
        $this->_webAppRole_write = 0;
        $this->_webAppRole_delete = 0;
    }

    /**
     * 
     * @return boolean
     */
    public function is_webAppRole_read() {
        return $this->_webAppRole_read == 1;
    }

    public function is_webAppRole_write() {
        return $this->_webAppRole_write == 1;
    }

    public function is_webAppRole_delete() {
        return $this->_webAppRole_delete == 1;
    }

    public function set_webAppRole_read($_webAppRole_read) {
        $this->_webAppRole_read = $_webAppRole_read;
    }

    public function set_webAppRole_write($_webAppRole_write) {
        $this->_webAppRole_write = $_webAppRole_write;
    }

    public function set_webAppRole_delete($_webAppRole_delete) {
        $this->_webAppRole_delete = $_webAppRole_delete;
    }

    /**
     * @param $role_name name of the WebAppRole
     */
    public function setWebAppRoleName($role_name) {
        if (isset($role_name) && $role_name != NULL && $role_name != "") {
            $this->_wepAppRole_name = $role_name;
        } else {
            throw new Exception("The role's name can't be NULL");
        }
    }

    public function getWebAppRoleName() {
        return $this->_wepAppRole_name;
    }

    public function getIdentifier() {
        if (isset($this) && $this->getWebAppRoleName() != NULL) {
            return $this->getDatabaseId();
        } else {
            return null;
        }
    }

    public function addWebAppPermission($permission) {
        if ($permission != NULL) {
            $this->_webAppRole_permissions[$permission->getIdentifier()] = $permission;
        }
    }

    abstract protected function getDatabaseId();

    abstract public function getWebAppRoleNumber();
}
