<?php

/*
 * fichier \Models\ProxyFireDataQuantity.php
 *
 */

include_once("ObjectPaleofire.php");

class ProxyFireDataQuantity extends ObjectPaleofire {

    const TABLE_NAME = 'r_has_proxy_fire_data_quantity';
    const ID = 'ID_PROXY_FIRE_DATA';
    const NAME = '';
    const ID_PROXY_FIRE_MEASUREMENT_UNIT = 'ID_PROXY_FIRE_MEASUREMENT_UNIT';
    const QUANTITY = 'QUANTITY';

    public $_proxy_fire_data_quantity_value;
    public $_unit_id;
    public $_proxy_fire_data_id;

    /**
     * Constructeur de la classe
     * */
    public function __construct($quantity_value = 0, $unit_id = null) {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_proxy_fire_data_quantity_value = $quantity_value;
        $this->_unit_id = $unit_id;
        $this->_proxy_fire_data_id = null;
    }

    public function create() {
        $insert_errors = array();
        if ($this->_proxy_fire_data_quantity_value !== NULL) {
            $column_values[self::QUANTITY] = $this->_proxy_fire_data_quantity_value;
        }
        if ($this->_unit_id != NULL) {
            $column_values[self::ID_PROXY_FIRE_MEASUREMENT_UNIT] = $this->_unit_id;
        }
        if ($this->_proxy_fire_data_id != NULL) {
            $column_values[self::ID] = $this->_proxy_fire_data_id;
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
            throw new Exception("PROXY FIRE DATA QUANTITY :: ERROR TO SELECT ALL INFORMATION ABOUT PROXY FIRE DATA QUANTITY ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->_proxy_fire_data_quantity_value = $values[self::QUANTITY];
            $this->_unit_id = $values[self::ID_PROXY_FIRE_MEASUREMENT_UNIT];
        }
    }


}
