<?php

/* 
 * fichier \Models\CalibrationMethod.php
 * 
 */

require_once 'ObjectPaleofire.php';

class CalibrationMethod extends ObjectPaleofire {

    const TABLE_NAME = 'tr_calibration_method';
    const ID = 'ID_CALIBRATION_METHOD';
    const NAME = 'CALIBRATION_METHOD_TYPE';
    
    public  $_calibration_method_code;
    protected static $_allObjectsByID = null;
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->calibration_method_code=null;
    }

    public function setCalibrationMethodCode($_calibration_method_code) {
        $this->_calibration_method_code = $_calibration_method_code;
        $this->addOrUpdateFieldToGetId(self::ID, $_calibration_method_code);
    }

    public function create() {
        return array();
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CALIBRATION METHOD :: ERROR TO SELECT ALL INFORMATION ABOUT AGE MODEL METHOD ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);            
        }
    }
}
