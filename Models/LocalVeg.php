<?php

/* 
 * fichier \Models\LocalVeg.php
 * 
 */

require_once 'ObjectPaleofire.php';

class LocalVeg extends ObjectPaleofire {

    const TABLE_NAME = 'tr_local_veg';
    const ID = 'ID_LOCAL_VEG';
    const NAME = 'LOCAL_VEG_DESC';
   
    protected static $_allObjectsByID = null;

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
            throw new Exception("LOCAL VEG :: ERROR TO SELECT ALL INFORMATION ABOUT LOCAL VEG ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
        }
    }
}
