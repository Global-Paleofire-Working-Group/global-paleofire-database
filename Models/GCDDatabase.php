<?php

/* 
 * fichier \Models\GCDDatabase.php
 * 
 */

require_once 'ObjectPaleofire.php';

class GCDDatabase extends ObjectPaleofire {

    const TABLE_NAME = 'tr_database';
    const ID = 'ID_DATABASE';
    const NAME = 'GCD_ACCESS_VERSION';
    const DATABASE_PUB_DATE = 'DATABASE_PUB_DATE';
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    

    public function create() {
        $land_exists = $this->exists();
        if ($land_exists) {
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
            throw new Exception("GCD DATABASE :: ERROR TO SELECT ALL INFORMATION ABOUT GCD DATABASE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
        }
    }
}
