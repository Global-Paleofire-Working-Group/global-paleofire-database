<?php

/* 
 * fichier \Models\Depth.php
 * 
 */


require_once 'DepthType.php';

class Depth extends ObjectPaleofire {

    const TABLE_NAME = 't_depth';
    const ID = 'ID_DEPTH';
    const NAME = 'DEPTH_VALUE';
    const ID_DEPTH_TYPE = "ID_DEPTH_TYPE";
    const ID_RELATED_SAMPLE = "ID_RELATED_SAMPLE";
    const ID_SAMPLE_WHEN_DEFAULT = "ID_SAMPLE_IF_DEFAULT";

    private $_depth_type;
    
    /*This id of sample represents the related sample */
    public $_related_sample_id;
    /* This id of sample represents the sample for which this depth is the default  of the sample */
    public $_sample_id_default_depth;
    
    public $_depth_value;

    public function __construct($value = -1, $depth_type = null) {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->setDepthType($depth_type);
        $this->_sample_id = null;
        $this->_related_sample_id = null;
        $this->_depth_value = $value;
        $this->_sample_id_default_depth = null;
    }


    public function setDepthType($type) {
        $this->_depth_type = $type;
        $this->addOrUpdateFieldToGetId(self::ID_DEPTH_TYPE, $type);
    }

    // Ne plus utiliser cette fonction utiliser SAVE //
    public function create() {
        $this->addOrUpdateFieldToGetId(self::ID_RELATED_SAMPLE, $this->_related_sample_id);
        $insert_errors = array();
        $object_exists = $this->exists();
        if (!$object_exists) {
            beginTransaction();

            $column_values = array();
            if ($this->_depth_value != -1) {
                $column_values[self::NAME] = $this->_depth_value;
            } else {
                $insert_errors[] = "DEPTH CREATION::The depth value  can't be empty !";
            }
            if ($this->_related_sample_id != NULL) {
                $column_values[self::ID_RELATED_SAMPLE] = $this->_related_sample_id;
            }
            else{
                $insert_errors[] = "DEPTH CREATION::The related sample  can't be null !";
            }

            if ($this->_depth_type != NULL) {
                $column_values[self::ID_DEPTH_TYPE] = DepthType::getObjectPaleofireFromWhere(sql_equal(DepthType::NAME, $this->_depth_type))->getIdValue();
            } else {
                $insert_errors[] = "DEPTH CREATION::The depth type  can't be empty !";
            }
            if ($this->_sample_id_default_depth != NULL) {
                $column_values[self::ID_SAMPLE_WHEN_DEFAULT] = $this->_sample_id_default_depth;
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            }
            unset($column_values);

            //If no error, commit transaction !
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
        }
        return $insert_errors;
    }
    
    public function save() {
        $this->addOrUpdateFieldToGetId(self::ID_RELATED_SAMPLE, $this->_related_sample_id);
        $insert_errors = array();

        beginTransaction();

        $column_values = array();
        if ($this->_depth_value != -1) {
            $column_values[self::NAME] = $this->_depth_value;
        } else {
            $insert_errors[] = "DEPTH CREATION::The depth value  can't be empty !";
        }
        if ($this->_related_sample_id != NULL) {
            $column_values[self::ID_RELATED_SAMPLE] = $this->_related_sample_id;
        }
        else{
            $insert_errors[] = "DEPTH CREATION::The related sample  can't be null !";
        }
        if ($this->_depth_type != NULL) {
            if(is_string($this->_depth_type)){
                $column_values[self::ID_DEPTH_TYPE] = DepthType::getObjectPaleofireFromWhere(sql_equal(DepthType::NAME, $this->_depth_type))->getIdValue();
            } else {
                $column_values[self::ID_DEPTH_TYPE] = DepthType::getObjectPaleofireFromWhere(sql_equal(DepthType::NAME, $this->_depth_type->getName()))->getIdValue();
            }
        } else {
            $insert_errors[] = "DEPTH CREATION::The depth type  can't be empty !";
        }
        if ($this->_sample_id_default_depth != NULL) {
            $column_values[self::ID_SAMPLE_WHEN_DEFAULT] = $this->_sample_id_default_depth;
        }

        if (empty($insert_errors)) {
            if ($this->getIdValue() == NULL) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            } else {
                $result_insert = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, Depth::ID." = ".$this->getIdValue());
                if (!$result_insert) {
                    $insert_errors[] = "Update table " . self::TABLE_NAME ." ID " . $this->getIdValue();
                }
            }
            unset($column_values);

            //If no error, commit transaction !
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
        }
        return $insert_errors;
    }

    public function getDepthType(){
        return  $this->_depth_type;
    }
    
    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("DEPTH :: ERROR TO SELECT ALL INFORMATION ABOUT DEPTH ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_depth_value = $values[self::NAME];
            $this->_depth_type = DepthType::getDepthTypeById($values[self::ID_DEPTH_TYPE]);
            //$this->_depth_type = DepthType::getObjectPaleofireFromId($values[self::ID_DEPTH_TYPE]);
        }
    }

}
