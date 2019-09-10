<?php

/* 
 * fichier \Models\AgeModelMethod.php
 * 
 */

require_once 'ObjectPaleofire.php';

class AgeModelMethod extends ObjectPaleofire {

    const TABLE_NAME = 'tr_age_model_method';
    const ID = 'ID_AGE_MODEL_METHOD';
    const NAME = 'AGE_MODEL_METHOD_TYPE';
    const AGE_MODEL_METHOD_CODE="AGE_MODEL_METHOD_CODE";
    
    public  $_age_model_gcd_code;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_age_model_gcd_code=null;
    }

    
    public function setAgeModelGCDCode($_age_model_gcd_code) {
        $this->_age_model_gcd_code = $_age_model_gcd_code;
        $this->addOrUpdateFieldToGetId(self::AGE_MODEL_METHOD_CODE, $_age_model_gcd_code);
    }

        

    public function create() {
        return array();
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("AGE MODEL METHOD :: ERROR TO SELECT ALL INFORMATION ABOUT AGE MODEL METHOD ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setAgeModelGCDCode($values[self::AGE_MODEL_METHOD_CODE]);
            
        }
    }
    
    public static $_allAgeModelMethodByID = null;
    public static function getAllAgeModelMethodByID() {
        if (self::$_allAgeModelMethodByID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, AgeModelMethod::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $class_paleo = get_called_class();
                $tab_get_values = fetchAll($result_get_object);
                self::$_allAgeModelMethodByID = null;
                foreach($tab_get_values as $row){
                    $ageModelMethod = new AgeModelMethod();
                    $ageModelMethod->read($row);
                    self::$_allAgeModelMethodByID[$row[AgeModelMethod::ID]] = $ageModelMethod;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allAgeModelMethodByID = NULL;
            }
        }
        return self::$_allAgeModelMethodByID;
    }
    
    public static function getAgeModelMethodByID($id){
        $liste = self::getAllAgeModelMethodByID();
        if (key_exists($id, $liste)) return $liste[$id];
        else return null;
    }
}
