<?php

/* 
 * fichier \Models\RefBiome.php
 * 
 */


require_once 'ObjectPaleofire.php';

class RefBiome extends ObjectPaleofire {

    const TABLE_NAME = 'tr_ref_biome';
    const ID = 'ID_REF_BIOME';
    const NAME = 'REF_BIOME_NAME';
    

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
            throw new Exception("REF BIOME :: ERROR TO SELECT ALL INFORMATION ABOUT REF BIOME ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
        }
    }
}
