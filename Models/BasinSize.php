<?php

/* 
 * fichier \Models\BasinSize.php
 * 
 */

require_once 'ObjectPaleofire.php';

class BasinSize extends ObjectPaleofire {

    const TABLE_NAME = 'tr_basin_size';
    const ID = 'ID_BASIN_SIZE';
    const NAME = 'BASIN_SIZE_DESC';
    const BASIN_SIZE_CODE="BASIN_SIZE_CODE";
    const UNKNOWN_VALUE='not known';
    const UNKNOWN_ID = 4;
    
    private $_basin_size_code;
    protected static $_allObjectsByID = null;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_basin_size_code=null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        
    }
    
    private function setBasinSizeCode($basin_code){
        $this->_basin_size_code = $basin_code;
        $this->addOrUpdateFieldToGetId(self::BASIN_SIZE_CODE, $basin_code);
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
            throw new Exception("BASIN SIZE :: ERROR TO SELECT ALL INFORMATION ABOUT BASIN SIZE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setBasinSizeCode($values[self::BASIN_SIZE_CODE]);
        }
    }
    
    public static function getRepartitionSites(){
            return DistributionStatistique::getRepartitionSites(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID);
    }
    
    public static function getDataQuality(){
        return DistributionStatistique::getDataQuality(self::TABLE_NAME, self::ID, self::UNKNOWN_ID);
    }
}
