<?php


/* 
 * fichier \Models\CharcoalUnits.php
 * 
 */

include_once("ObjectPaleofire.php");

/**
 * Class CharcoalUnits
 *
 */
class CharcoalUnits extends ObjectPaleofire {

    const TABLE_NAME = 'tr_charcoal_units';
    const ID = 'ID_CHARCOAL_UNITS';
    const NAME = 'CHARCOAL_UNITS_NAME';
    const CHARCOAL_UNITS_HIGH_LEVEL = "CHARCOAL_UNITS_HIGH_LEVEL";
    const CHARCOAL_UNITS_NUMBER = "CHARCOAL_UNITS_NUMBER";
    const CHARCOAL_UNITS_FIRST_LEVEL = "CHARCOAL_UNITS_FIRST_LEVEL";
    const UNKNOWN_ID = '139';
    
    public $_charcoal_high_level_code;
    private static $_allCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL = null;
    protected static $_allObjectsByID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        $field_names = array(self::ID, self::NAME, "1ST_LEVEL", "UNITS", "UNIT_CODE");
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME, $field_names);
        $this->_charcoal_high_level_code = null;
    }

    public static function getAllCharcoalUnits() {
        return getAllObjectsInTable(self::TABLE_NAME);
    }

    public static function getCharcoalUnits($id_CharcoalUnits) {
        $current_CharcoalUnits = new CharcoalUnits();
        $result_get_CharcoalUnits = getAllObjectFromId(self::TABLE_NAME, self::ID, $id_CharcoalUnits);

        $tab_values = array();
        if (fetchRow($result_get_CharcoalUnits)) {
            foreach ($current_CharcoalUnits->getFields() as $field_name) {
                $tab_values[$field_name] = getValueInResult($result_get_CharcoalUnits, $field_name);
            }
        }
        if ($tab_values != NULL && !empty($tab_values)) {
            $current_CharcoalUnits->setArrayValues($tab_values);
            return $current_CharcoalUnits;
        } else {
            return NULL;
        }
    }

    public function setHightLevelCode($code_value){
        $this->_charcoal_high_level_code = $code_value;
    }
    

    public function create() {
        $errors = array();
        $values = array();
        //Create a CharcoalUnits in the database
        insertIntoTableFieldsValues(self::TABLE_NAME, $values);

        return $errors;
    }

    public function getTableName() {
        return self::TABLE_NAME;
    }

    public function prepareAndSetFieldValue($field, $value) {
        parent::setFieldValue($field, $value);
    }

    public static function getDatabaseObjectID($name) {
        $result_get_CharcoalUnits = getObjectIdFromName(self::TABLE_NAME, self::ID, self::NAME, $name);
        fetchRow($result_get_CharcoalUnits);
        $current_id_CharcoalUnits = getValueInResult($result_get_CharcoalUnits, self::ID);
        return $current_id_CharcoalUnits;
    }

    /*public static function existsInDatabase($id_value) {
        return parent::objectExistsInDatabase(self::TABLE_NAME, self::ID, $id_value);
    }*/

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CHARCOAL UNITS :: ERROR TO SELECT ALL INFORMATION ABOUT CHARCOAL UNITS ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setHightLevelCode($values[self::CHARCOAL_UNITS_HIGH_LEVEL]);
        }
    }

    /**
     * @return null|\object_paleofire
     */
    public static function getAllCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL() {
        if (self::$_allCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL = null;
                foreach($tab_get_values as $row){
                    // pour l'import de données on récupère les site par le GCD_ACCESS_ID
                    //$object_paleo = new $class_paleo();
                    //$object_paleo->read($row);
                    self::$_allCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL[$row[self::CHARCOAL_UNITS_HIGH_LEVEL]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL = NULL;
            }
        }
        return self::$_allCharcoalUnitsByCHARCOAL_UNITS_HIGH_LEVEL;
    }
    
    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityForOneCore(self::TABLE_NAME, self::ID, self::UNKNOWN_ID, $idCore);
    }
    
    public static function getCharcoalUnitsHighLevel($id_CharcoalUnits) {//essai
    $result_get_CharcoalUnitsHighLevel = getAllObjectFromId(self::TABLE_NAME, self::ID, $id_CharcoalUnits);
    }
    
}
