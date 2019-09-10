<?php

/* 
 * fichier \Models\CatchSize.php
 * 
 */

require_once 'ObjectPaleofire.php';

class CatchSize extends ObjectPaleofire {

    const TABLE_NAME = 'tr_catch_size';
    const ID = 'ID_CATCH_SIZE';
    const NAME = 'CATCH_SIZE_NAME';
    const CATCH_SIZE_CODE="CATCH_SIZE_CODE";
    const UNKNOWN_VALUE = 'not known';
    const UNKNOWN_ID = 4;
    
    private $_catch_size_code;
    protected static $_allObjectsByID = null;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_catch_size_code=null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        
    }
    
    private function setCatchSizeCode($cs_code){
        $this->_catch_size_code = $cs_code;
        $this->addOrUpdateFieldToGetId(self::CATCH_SIZE_CODE, $cs_code);
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
            throw new Exception("CATCH SIZE :: ERROR TO SELECT ALL INFORMATION ABOUT CATCH SIZE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setCatchSizeCode($values[self::CATCH_SIZE_CODE]);
        }
    }
    
    public static function getRepartitionSites(){
            return DistributionStatistique::getRepartitionSites(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID);
    }
    
    public static function getDataQuality(){
        return DistributionStatistique::getDataQuality(self::TABLE_NAME, self::ID, self::UNKNOWN_ID);
    }
}
