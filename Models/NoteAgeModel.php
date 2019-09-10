<?php

/* 
 * fichier \Models\NoteAgeModel.php
 * 
 */

require_once 'ObjectPaleofire.php';
include_once(REP_PAGES . "EDA/change_Log.php");


class NoteAgeModel extends ObjectPaleofire {

    const TABLE_NAME = 't_note_age_model';
    const ID = 'ID_NOTE_AGE_MODEL';
    const NAME = 'WHAT';
    const WHO = "WHO";
    const NOTES_DATE = "NOTE_DATE";
    const ID_AGE_MODEL = "ID_AGE_MODEL";

    public $_age_model_note_who;
    public $_age_model_note_date;
    private $_age_model_id;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_age_model_note_who = null;
        $this->_age_model_note_date = null;
        $this->_age_model_id = null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setAgeModelNoteWho($who_value) {
        $this->_age_model_note_who = $who_value;
    }

    public function setAgeModelNoteDate($date_value) {
        $this->_age_model_note_date = $date_value;
    }

    public function setAgeModelId($age_model_id) {
        $this->_age_model_id = $age_model_id;
    }

    public function getAgeModelId() {
        return $this->_age_model_id;
    }

    public function getNoteWho() {
        return $this->_age_model_note_who;
    }

    public function getNoteDate() {
        return $this->_age_model_note_date;
    }

    public function create() {
        $insert_errors = array();
        $object_exists = $this->exists();
        if (!$object_exists) {
            //BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->getAgeModelId() != NULL) {
                $column_values[self::ID_AGE_MODEL] = $this->getAgeModelId();
            } else {
                $insert_errors[] = "The age model associated to the note  can't be empty !";
            }

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = sql_varchar($this->getName());
            } else {
                $insert_errors[] = "The age model name  can't be empty !";
            }
            if ($this->getNoteWho() != NULL) {
                $column_values[self::WHO] = sql_varchar($this->getNoteWho());
            }
            if ($this->getNoteDate() != NULL) {
                $column_values[self::NOTES_DATE] =  sql_varchar($this->_core_note_date->format('Y-m-d H:i:s'));
            }
            else{
                $column_values[self::NOTES_DATE] =  sql_varchar(date('Y-m-d H:i:s'));
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_note_age_model", $this->getIdValue());
                }
            }

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
        $insert_errors = array();
        $obj_exists = ($this->getIdValue() == null)?false:true;
        
        //BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();

        if ($this->getAgeModelId() != NULL) {
            $column_values[self::ID_AGE_MODEL] = $this->getAgeModelId();
        } else {
            $insert_errors[] = "The age model associated to the note  can't be empty !";
        }

        if ($this->getName() != NULL) {
            $column_values[self::NAME] = sql_varchar($this->getName());
        } else {
            $insert_errors[] = "The age model name  can't be empty !";
        }
        if ($this->getNoteWho() != NULL) {
            $column_values[self::WHO] = sql_varchar($this->getNoteWho());
        }
        /*if ($this->getNoteDate() != NULL) {
            $column_values[self::NOTES_DATE] =  sql_varchar($this->_core_note_date->format('Y-m-d H:i:s'));
        }
        else{
            $column_values[self::NOTES_DATE] =  sql_varchar(date('Y-m-d H:i:s'));
        }*/

        // on force toujours la date du jours pour la modif ou la création    
        $column_values[self::NOTES_DATE] =  sql_varchar(date('Y-m-d H:i:s'));

        if (empty($insert_errors)) {
            if ($obj_exists){
                // mise à jour
                $result_insert = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID." = ".$this->getIdValue());
                if (!$result_insert) {
                    $insert_errors[] = "Update into table " . self::TABLE_NAME;
                }
                else {
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("edit_note_age_model", $this->getIdValue());
                }
            } else {
                // création
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_note_age_model", $this->getIdValue());
                }
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
            throw new Exception("AGE MODEL NOTE :: ERROR TO SELECT ALL INFORMATION ABOUT AGE MODEL NOTE  ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setAgeModelNoteWho($values[self::WHO]);
            $this->setAgeModelNoteDate($values[self::NOTES_DATE]);
            $this->setAgeModelId($values[self::ID_AGE_MODEL]);
        }
    }
    
    
    public static $_allNoteAgeModelByID = null;
    public static function getAllNoteAgeModelByID() {
        if (self::$_allNoteAgeModelByID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, NoteAgeModel::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $class_paleo = get_called_class();
                $tab_get_values = fetchAll($result_get_object);
                self::$_allNoteAgeModelByID = null;
                foreach($tab_get_values as $row){
                    $object = new NoteAgeModel();
                    $object->read($row);
                    self::$_allNoteAgeModelByID[$row[NoteAgeModel::ID]] = $object;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allNoteAgeModelByID = NULL;
            }
        }
        return self::$_allNoteAgeModelByID;
    }
    
    public static function getNoteAgeModelByID($id){
        $liste = self::getAllNoteAgeModelByID();
        if (array_key_exists($id, $liste)){
            return $liste[$id];
        } else {
            return null;
        }
    }

}
