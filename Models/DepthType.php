<?php

/* 
 * fichier \Models\DepthType.php
 * 
 */

class DepthType extends ObjectPaleofire {

    const TABLE_NAME = 'tr_depth_type';
    const ID = 'ID_DEPTH_TYPE';
    const NAME = 'DEPTH_TYPE_NAME';
    const DEPTH_TOP = "TOP";
    const DEPTH_MIDDLE = "MIDDLE";
    const DEPTH_BOTTOM = "BOTTOM";
    
    const ID_DEPTH_TOP = "1";
    const ID_DEPTH_MIDDLE = "3";
    const ID_DEPTH_BOTTOM = "2";

    protected static $_allObjectsByID = null;
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function create() {
        return array();
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("DEPTH TYPE :: ERROR TO SELECT ALL INFORMATION ABOUT DEPTH TYPE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
        }
    }
    
    public static $_allDepthTypeByID = null;
    public static function getAllDepthTypeByID() {
        if (self::$_allDepthTypeByID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, DepthType::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $class_paleo = get_called_class();
                $tab_get_values = fetchAll($result_get_object);
                self::$_allDepthTypeByID = null;
                foreach($tab_get_values as $row){
                    $object = new DepthType();
                    $object->read($row);
                    self::$_allDepthTypeByID[$row[DepthType::ID]] = $object;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allDepthTypeByID = NULL;
            }
        }
        return self::$_allDepthTypeByID;
    }
    
    public static function getDepthTypeByID($id){
        $liste = self::getAllDepthTypeByID();
        if (array_key_exists($id, $liste)){
            return $liste[$id];
        } else {
            return null;
        }
    }

}
