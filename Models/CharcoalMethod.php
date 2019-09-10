<?php

/* 
 * fichier \Models\CharcoalMethod.php
 * 
 */

include_once("ObjectPaleofire.php");

class CharcoalMethod extends ObjectPaleofire {

    const TABLE_NAME = 'tr_charcoal_method';
    const ID = 'ID_CHARCOAL_METHOD';
    const NAME = 'CHARCOAL_METHOD_NAME';
    const CHARCOAL_METHOD_CODE = 'CHARCOAL_METHOD_CODE';
    const CHARCOAL_METHOD_NUMBER='CHARCOAL_METHOD_NUMBER';
    const UNKNOWN_ID = '7';
    
    private $_charcoal_method_code;
    private $_charcoal_method_number;
    private static $_allCharcoalMethodByCHARCOAL_METHOD_CODE;
    protected static $_allObjectsByID = null;
    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_charcoal_method_code = null;
        $this->_charcoal_method_number = null;
    }

    /**
     * @return null|\object_paleofire
     */
    public static function getAllCharcoalMethodByCHARCOAL_METHOD_CODE() {
        if (self::$_allCharcoalMethodByCHARCOAL_METHOD_CODE == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allCharcoalMethodByCHARCOAL_METHOD_CODE = null;
                foreach($tab_get_values as $row){
                    self::$_allCharcoalMethodByCHARCOAL_METHOD_CODE[$row[self::CHARCOAL_METHOD_CODE]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allCharcoalMethodByCHARCOAL_METHOD_CODE = NULL;
            }
        }
        return self::$_allCharcoalMethodByCHARCOAL_METHOD_CODE;
    }
    
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function create() {
        return array();
    }



    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CHARCOAL METHOD :: ERROR TO SELECT ALL INFORMATION ABOUT CHARCOAL METHOD ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_charcoal_method_code = $values[self::CHARCOAL_METHOD_CODE];
            $this->_charcoal_method_number = $values[self::CHARCOAL_METHOD_NUMBER];
        }
    }

    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityForOneCore(self::TABLE_NAME, self::ID, self::UNKNOWN_ID, $idCore);
    }
}
