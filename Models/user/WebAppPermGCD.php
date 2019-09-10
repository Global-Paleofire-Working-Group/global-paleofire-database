<?php

/* 
 * fichier \Models\user\WebAppPermGCD.php
 * 
 */

include_once(REP_LIB . "WebAppUser/WebAppPermission.php");

class WebAppPermGCD extends WebAppPermission {

    const TABLE_NAME = BDD_USER_PERMTABLE;
    //const TABLE_NAME = 'gcd_paleofire_webapp.paleofire_permission';
    //const TABLE_NAME = 'paleofire_permission';
    const NAMEFIELD = "NAME_PERM";
    const IDENTIFIER = "ID_PERM";
    const TRIGRAM = "TRI_PERM";

    protected function getDatabaseId() {
        $result_get_id = getFieldsFromTables(self::IDENTIFIER, self::TABLE_NAME, sql_equal(self::NAMEFIELD, $this->getWebAppPermissionName()));
        if (getNumRows($result_get_id) > 0) {
            $tab_get_id = fetchAssoc($result_get_id);
            return $tab_get_id[self::IDENTIFIER];
        }
        return NULL;
    }

    public function getPermAcronym() {
        $result_get_id = getFieldsFromTables(self::TRIGRAM, self::TABLE_NAME, sql_equal(self::NAMEFIELD, $this->getWebAppPermissionName()));
        if (getNumRows($result_get_id) > 0) {
            $tab_get_id = fetchAssoc($result_get_id);
            return $tab_get_id[self::TRIGRAM];
        }
        return NULL;
    }

    /**
     * 
     * @param type $role_id
     * @return \WebAppPermGCD
     */
    public static function getAllPermissionsFromRoleId($role_id) {
        //Select All Id In the table of the relation between Role and Permission
        $array_permission_ids = array();
        $result = getFieldsFromTables(self::IDENTIFIER, WebAppRoleGCD::getRelationNameBetWeenRolePermission(), sql_equal(WebAppRoleGCD::IDENTIFIER, $role_id));
        while ($tab_values = fetchAssoc($result)) {
            $array_permission_ids[] = $tab_values[WebAppPermGCD::IDENTIFIER];
        }
        freeResult($result);
        unset($result);

        //Select All corresponded Permission in the table to construct WebAppPermission
        $array_permissions = array();
        foreach ($array_permission_ids as $id_perm) {
            $clauses_where = array(
                WebAppPermGCD::IDENTIFIER . " = " . $id_perm
            );
            $result_get_permission = getFieldsFromTables(array(WebAppPermGCD::IDENTIFIER, WebAppPermGCD::NAMEFIELD), WebAppPermGCD::TABLE_NAME, $clauses_where);

            while ($tab_values = fetchAssoc($result_get_permission)) {
                $array_permissions[$tab_values[WebAppPermGCD::IDENTIFIER]] = new WebAppPermGCD($tab_values[WebAppPermGCD::NAMEFIELD]);
            }
            freeResult($result_get_permission);
            unset($clauses_where);
        }
        unset($array_permission_ids);
        return $array_permissions;
    }

}
