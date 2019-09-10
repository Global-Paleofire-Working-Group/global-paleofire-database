<?php

/* 
 * fichier \Models\CalibrationVersion.php
 * 
 */

require_once 'ObjectPaleofire.php';

class CalibrationVersion extends ObjectPaleofire {

    const TABLE_NAME = 'tr_calibration_version';
    const ID = 'ID_CALIBRATION_VERSION';
    const NAME = "CALIBRATION_CURVE_VERSION";
    
    public $_code_calibration_version;
    protected static $_allObjectsByID = null;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_code_calibration_version=null;
    }

    public function setCalibrationVersionCode($_code_calibration_version) {
        $this->_code_calibration_version = $_code_calibration_version;
        $this->addOrUpdateFieldToGetId(self::ID, $_code_calibration_version);
    }

    public function create() {
        return array();
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CALIBRATION VERSION :: ERROR TO SELECT ALL INFORMATION ABOUT AGE MODEL METHOD ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);            
        }
    }
}
