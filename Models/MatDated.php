<?php

/* 
 * fichier \Models\MatDated.php
 * 
 */

require_once 'ObjectPaleofire.php';

class MatDated extends ObjectPaleofire {

    const TABLE_NAME = 'tr_mat_dated';
    const ID = 'ID_MAT_DATED';
    const NAME = 'MAT_DATED_TYPE';
    const MAT_DATED_STANDARD_LEVEL="MAT_DATED_STANDARD_LEVEL";
    const MAT_DATED_HIGH_LEVEL="MAT_DATED_HIGH_LEVEL";
    const UNKNOWN_ID = 15;
    

    private $_mat_dated_standard_level;
    private $_mat_dated_high_level;
    
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_mat_dated_standard_level=null;
        $this->_mat_dated_high_level=null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    
    public function setMatDatedStandardLevel($mat_dated_standard_level) {
        $this->_mat_dated_standard_level = $mat_dated_standard_level;
    }

    public function setMatDatedHighLevel($mat_dated_high_level) {
        $this->_mat_dated_high_level = $mat_dated_high_level;
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
            throw new Exception("MAT DATED :: ERROR TO SELECT ALL INFORMATION ABOUT MAT DATED ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setMatDatedStandardLevel($values[self::MAT_DATED_STANDARD_LEVEL]);
            $this->setMatDatedHighLevel($values[self::MAT_DATED_HIGH_LEVEL]);
        }
    }
    
    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionDateInfoForOneCore(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityDateInfoForOneCore(self::TABLE_NAME, self::ID, self::UNKNOWN_ID, $idCore);
    }
}
