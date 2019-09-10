<?php

/* 
 * fichier \Models\CharcoalQuantity.php
 * 
 */

include_once("ObjectPaleofire.php");

class CharcoalQuantity extends ObjectPaleofire {

    const TABLE_NAME = 'r_has_charcoal_quantity';
    const ID = 'ID_CHARCOAL';
    const NAME = 'NA';
    const ID_CHARCOAL_UNITS = 'ID_CHARCOAL_UNITS';
    const QUANTITY = 'QUANTITY';

    public $_charcoal_quantity_value;
    public $_charcoal_unit_id;
    public $_charcoal_id;

    /**
     * Constucteur de la classe
     * */
    public function __construct($quantity_value = 0, $charcoal_unit_id = null) {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_charcoal_quantity_value = $quantity_value;
        $this->_charcoal_unit_id = $charcoal_unit_id;
        $this->_charcoal_id = null;
    }

    public function create() {
        $insert_errors = array();
        
        if ($this->_charcoal_quantity_value !== NULL) {
            $column_values[self::QUANTITY] = $this->_charcoal_quantity_value;
        }
        if ($this->_charcoal_unit_id != NULL) {
            $column_values[self::ID_CHARCOAL_UNITS] = $this->_charcoal_unit_id;
        }
                if ($this->_charcoal_id != NULL) {
            $column_values[self::ID] = $this->_charcoal_id;
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
        return $insert_errors;
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CHARCOAL QUANTITY :: ERROR TO SELECT ALL INFORMATION ABOUT CHARCOAL QUANTITY ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->_charcoal_quantity_value = $values[self::QUANTITY];
            $this->_charcoal_unit_id = $values[self::ID_CHARCOAL_UNITS];
        }
    }
   

}
