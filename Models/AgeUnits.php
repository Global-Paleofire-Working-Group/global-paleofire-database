<?php

/* 
 * fichier \Models\AgeUnits.php
 * 
 */

require_once 'ObjectPaleofire.php';

class AgeUnits extends ObjectPaleofire {

    const TABLE_NAME = 'tr_age_units';
    const ID = 'ID_AGE_UNITS';
    const NAME = 'AGE_UNITS_TYPE';
    const AGE_UNITS_CODE="AGE_UNITS_CODE";
    const AGE_UNITS_CALORNOT = "AGE_UNITS_CALORNOT";
    
    public $_age_units_calornot;
    protected static $_allObjectsByID = null;
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
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
            throw new Exception("AGE UNITS :: ERROR TO SELECT ALL INFORMATION ABOUT AGE UNITS ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_age_units_calornot = $values[self::AGE_UNITS_CALORNOT];
        }
    }
}
