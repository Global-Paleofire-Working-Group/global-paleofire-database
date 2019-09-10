<?php

/* 
 * fichier \Models\CharcoalSize.php
 * 
 */

include_once("ObjectPaleofire.php");
include_once("DistributionStatistique.php");

class CharcoalSize extends ObjectPaleofire {

    const TABLE_NAME = 'tr_charcoal_size';
    const ID = 'ID_CHARCOAL_SIZE';
    const NAME = 'CHARCOAL_SIZE_DESC';
    const CHARCOAL_SIZE_CODE = 'CHARCOAL_SIZE_CODE';
    const CHARCOAL_CONV_COEF_FOR_1CM3 = 'CONV_COEF_FOR_1CM3';
    const UNKNOWN_ID = '37';

    private $_charcoal_size_code;
    private $_charcoal_conv_coef_for_1cm3;
    private static $_allCharcoalSizeByCHARCOAL_SIZE_CODE = null;
    protected static $_allObjectsByID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_charcoal_size_code = null;
        $this->_charcoal_conv_coef_for_1cm3 = null;
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
            throw new Exception("CHARCOAL SIZE :: ERROR TO SELECT ALL INFORMATION ABOUT CHARCOAL SIZE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_charcoal_size_code = $values[self::CHARCOAL_SIZE_CODE];
            $this->_charcoal_conv_coef_for_1cm3 = $values[self::CHARCOAL_CONV_COEF_FOR_1CM3];
        }
    }
    
    /**
     * @return null|\object_paleofire
     */
    public static function getAllCharcoalSizeByCHARCOAL_SIZE_CODE() {
        if (self::$_allCharcoalSizeByCHARCOAL_SIZE_CODE == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allCharcoalSizeByCHARCOAL_SIZE_CODE = null;
                foreach($tab_get_values as $row){
                    // pour l'import de données on récupère les site par le GCD_ACCESS_ID
                    //$object_paleo = new $class_paleo();
                    //$object_paleo->read($row);
                    self::$_allCharcoalSizeByCHARCOAL_SIZE_CODE[$row[self::CHARCOAL_SIZE_CODE]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allCharcoalSizeByCHARCOAL_SIZE_CODE = NULL;
            }
        }
        return self::$_allCharcoalSizeByCHARCOAL_SIZE_CODE;
    }

    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityForOneCore(self::TABLE_NAME, self::ID, self::UNKNOWN_ID, $idCore);
    }
    
    public function getConvertedValueFor1Cm3(){
        return $this->_charcoal_conv_coef_for_1cm3;
    }
}
