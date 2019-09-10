<?php

/* 
 * fichier \Models\Age.php
 * 
 */

class Age extends ObjectPaleofire {

    const TABLE_NAME = 'r_has_age';
    const ID = 'ID_DATE_INFO';
    const NAME = 'AGE_VALUE';
    const AGE_POSITIVE_ERROR = 'AGE_POSITIVE_ERROR';
    const AGE_NEGATIVE_ERROR = 'AGE_NEGATIVE_ERROR';
    const ID_AGE_UNITS = "ID_AGE_UNITS";
    const ID_AGE_MODEL = "ID_AGE_MODEL";
    const ID_CALIBRATION_METHOD = "ID_CALIBRATION_METHOD";
    const ID_CALIBRATION_VERSION = "ID_CALIBRATION_VERSION";

    public $_age_model_id;
    public $_age_value;
    public $_age_positive_error;
    public $_age_negative_error;
    public $_age_units_id;
    public $_age_calibration_method_id;
    public $_age_calibration_version_id;

    /**
     * Constucteur de la classe
     * */
    public function __construct($age = -1) {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_age_model_id = null;
        $this->_age_value = $age;
        $this->_age_negative_error = 0;
        $this->_age_positive_error = 0;
        $this->_age_units_id = null;
        $this->_age_calibration_method_id = null;
        $this->_age_calibration_version_id = null;
    }

    /** ---------------------------GETTERS------------------------- * */

    /** ---------------------------CRUD------------------------- * */
    public function create() {
        $insert_errors = array();
        //BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();

        $column_values[self::ID] = $this->getIdValue();
        
        // pourquoi la valeur de l'age doit elle être différente de -1 ?! supprimé suite à confirmation BV
        if (/*$this->_age_value != -1 && */$this->_age_value !== NULL) {
            $column_values[self::NAME] = $this->_age_value;
        } else {
            $insert_errors[] = "The age value  can't be empty !";
        }

        $column_values[self::AGE_NEGATIVE_ERROR] = $this->_age_negative_error;
        $column_values[self::AGE_POSITIVE_ERROR] = $this->_age_positive_error;

        if (isset($this->_age_units_id)) {
            $column_values[self::ID_AGE_UNITS] = $this->_age_units_id;
        }
        if (isset($this->_age_model_id)) {
            $column_values[self::ID_AGE_MODEL] = $this->_age_model_id;
        }
        if (isset($this->_age_calibration_method_id)) {
            $column_values[self::ID_CALIBRATION_METHOD] = $this->_age_calibration_method_id;
        }
        if (isset($this->_age_calibration_version_id)) {
            $column_values[self::ID_CALIBRATION_VERSION] = $this->_age_calibration_version_id;
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
        unset($column_values);
        return $insert_errors;
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("Has Age :: ERROR TO SELECT ALL INFORMATION ABOUT Has Age ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_age_value = $values[self::NAME];
            $this->_age_calibration_method_id = $values[self::ID_CALIBRATION_METHOD];
            $this->_age_calibration_version_id = $values[self::ID_CALIBRATION_VERSION];
            $this->_age_negative_error = $values[self::AGE_NEGATIVE_ERROR];
            $this->_age_positive_error = $values[self::AGE_POSITIVE_ERROR];
            $this->_age_units_id = $values[self::ID_AGE_UNITS];
            $this->_age_model_id = $values[self::ID_AGE_MODEL];
        }
    }
    
    public static function delByDateInfo($id_date_info){
        $query = "DELETE FROM R_HAS_AGE WHERE ID_DATE_INFO = " . $id_date_info;
        $result = queryToExecute($query, "DELETE FROM R_HAS_AGE");
        return ($result);
    }
    
    public static function del($id_date_info, $id_age_model){
        $query = "DELETE FROM R_HAS_AGE WHERE ID_DATE_INFO = " . $id_date_info . " AND ID_AGE_MODEL = " . $id_age_model;
        $result = queryToExecute($query, "DELETE FROM R_HAS_AGE");
        return ($result);
    }

}
