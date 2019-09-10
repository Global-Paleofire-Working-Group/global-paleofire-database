<?php

/* 
 * fichier \Models\LandsDesc.php
 * 
 */

require_once 'ObjectPaleofire.php';

class LandsDesc extends ObjectPaleofire {

    const TABLE_NAME = 'tr_lands_desc';
    const ID = 'ID_LANDS_DESC';
    const NAME = 'LANDS_DESC_NAME';
    const GCD_CODE = 'LANDS_DESC_CODE';
    const UNKNOWN_ID = 12;
    
    private $_lands_desc_code;
    protected static $_allObjectsByID = null;
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_lands_desc_code=null;
    }
    
    public function setLandGCDCode($gcd_code_value){
        $this->_lands_desc_code=$gcd_code_value;
         $this->addOrUpdateFieldToGetId(self::GCD_CODE, $gcd_code_value);
    }
    
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function create() {
               $obj_exists = $this->exists();
        if ($obj_exists) {
            $this->getIdValue();
            $this->read();
        } else {
//BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->getIdValue() != NULL) {
                $column_values[self::ID_SITE] = $this->getIdValue();
            } 

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = sql_varchar($this->getName());
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            }

            //If no error, commit transaction !
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
// END TRANSACTION
        }
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("LANDS DESC :: ERROR TO SELECT ALL INFORMATION ABOUT LANDS DESC ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setLandGCDCode($values[self::GCD_CODE]);
        }
    }
    
    protected function getSetValues(){
        $array_values=array();
        $array_values[]=$this->_lands_desc_code;
        $array_values = array_merge($array_values, parent::getSetValues());
        return $array_values;
    }
    
    public static function getRepartitionSites(){
            return DistributionStatistique::getRepartitionSites(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID);
    }
    
    public static function getDataQuality(){
        return DistributionStatistique::getDataQuality(self::TABLE_NAME, self::ID, self::UNKNOWN_ID);
    }
}
