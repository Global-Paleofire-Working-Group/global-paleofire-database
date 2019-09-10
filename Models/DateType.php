<?php

/* 
 * fichier \Models\DateType.php
 * 
 */

require_once 'ObjectPaleofire.php';

class DateType extends ObjectPaleofire {

    const TABLE_NAME = 'tr_date_type';
    const ID = 'ID_DATE_TYPE';
    const NAME = 'DATE_TYPE_NAME';
    const DATE_TYPE_CODE="DATE_TYPE_CODE";
    const DATE_TYPE_NUMBER="DATE_TYPE_NUMBER";
    const UNKNOWN_ID = 20;
    
    
    private $_date_type_code;
    private $_date_type_number;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_date_type_code=null;
        $this->_date_type_number=null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    
    public function setDateTypeCode($code_value){
        $this->_date_type_code=$code_value;
    }
    
    public function setDateTypeNumber($number_value){
        $this->_date_type_number=$number_value;
    }

    public function create() {
        $object_exists = $this->exists();
        if ($object_exists) {
            $this->getIdValue();
            $this->read();
        } else {
            //
        }
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("DATE TYPE :: ERROR TO SELECT ALL INFORMATION ABOUT DATE TYPE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setDateTypeCode($values[self::DATE_TYPE_CODE]);
            $this->setDateTypeNumber($values[self::DATE_TYPE_NUMBER]);
        }
    }
    
    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionDateInfoForOneCore(self::TABLE_NAME, self::ID, self::NAME, null, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityDateInfoForOneCore(self::TABLE_NAME, self::ID, null, $idCore);
    }
}
