<?php

/* 
 * fichier \Models\user\WebAppRoleGCD.php
 * 
 */

include_once(REP_LIB . "WebAppUser/WebAppRole.php");


class WebAppRoleGCD extends WebAppRole {

    const TABLE_NAME = BDD_USER_ROLETABLE;
    //const TABLE_NAME = 'gcd_paleofire_webapp.paleofire_role';
    //const TABLE_NAME = 'paleofire_role';
    const NAMEFIELD = "NAME_ROLE";
    const IDENTIFIER = "ID_ROLE";
    
    const VISITOR = 6;
    const CONTRIBUTOR = 5;
    const ADMINISTRATOR = 2;
    const SUPERADMINISTRATOR = 1 ;
    
    // à quoi ils servent ??
    //const MAINTENANCE = 3;
    //const WEBMASTER = 4;

    /**
     * 
     * @return ID of WebAppRoleEA or NULL
     */
    protected function getDatabaseId() {
        $result_get_id = getFieldOfObjectFromFieldValue(self::IDENTIFIER, self::TABLE_NAME, self::NAMEFIELD, $this->getWebAppRoleName());
        if (getNumRows($result_get_id) > 0) {
            $tab_get_id = fetchAssoc($result_get_id);
            return $tab_get_id[self::IDENTIFIER];
        }
        return NULL;
    }

    /**
     * 
     * @return type
     */
    public function getWebAppRoleNumber() {
        $role_number = self::VISITOR;
        switch ($this->getWebAppRoleName()) {
            case "SUPERADMINISTRATOR":  $role_number = self::SUPERADMINISTRATOR;
                break;
            case "ADMINISTRATOR": $role_number = self::ADMINISTRATOR;
                break;
            case "CONTRIBUTOR": $role_number = self::CONTRIBUTOR;
                break;
            //case "MANAGER": $role_number = self::MANAGER;
             //   break;
            //case "NETWORK_DOCTOR": $role_number = self::NETWORK_DOCTOR;
             //   break;
            //case "WEBMASTER": $role_number = self::WEBMASTER;
             //   break;
            //case "MAINTENANCE": $role_number = self::MAINTENANCE;
            //    break;
            default : $role_number = self::VISITOR;
                break;
        }
        return $role_number;
    }

    /**
     * 
     * @return type
     */
    public static function getIdentifierName() {
        return self::IDENTIFIER;
    }

    /**
     * 
     * @return string
     */
    public static function getRelationNameBetWeenRolePermission() {
        return "gcd_paleofire_webapp.r_has_perm";
    }
    
    protected static $_allObjectsByID = null;
    public static function getStaticList() {
        if (self::$_allObjectsByID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allObjectsByID = null;
                foreach($tab_get_values as $row){
                    self::$_allObjectsByID[$row[self::IDENTIFIER]] = $row;
                }
            } else {
                self::$_allObjectsByID = NULL;
            }
        }
        return self::$_allObjectsByID;
    }
}
