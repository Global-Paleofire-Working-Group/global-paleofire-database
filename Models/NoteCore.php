<?php

/* 
 * fichier \Models\NoteCore.php
 * 
 */

include_once("ObjectPaleofire.php");
include_once(REP_PAGES . "EDA/change_Log.php");

class NoteCore extends ObjectPaleofire {

    const TABLE_NAME = 't_notes';
    const ID = 'ID_NOTES';
    const NAME = 'WHAT';
    const WHO = "WHO";
    const NOTES_DATE = "NOTES_DATE";
    const ID_CORE = "ID_CORE";
    const ID_CONTACT = 'ID_CONTACT';

    private $_core_note_who;
    private $_core_note_date;
    private $_core_id;
    public $_contact_id;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_core_note_who = null;
        $this->_core_note_date = null;
        $this->_core_id = null;
        $this->_contact_id = null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setCoreNoteWho($who_value) {
        $this->_core_note_who = $who_value;
    }
    
    public function getCoreNoteWho(){
        return $this->_core_note_who;
    }
    
    public function setCoreNoteWhat($what_value) {
        $this->setNameValue($what_value);
    }
    
    public function getCoreNoteWhat(){
        return $this->getName();
    }

    public function setCoreNoteDate($date_value) {
        $this->_core_note_date = $date_value;
    }
    
    public function getCoreNoteDate(){
        return $this->_core_note_date;
    }
    
    public function setCoreId($core_id){
        $this->_core_id = $core_id;
    }
    
    public function getCoreId(){
        return $this->_core_id;
    }

    public function create() {
        $insert_errors = array();
        $object_exists = $this->exists();
        if (!$object_exists) {
            //BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->_core_id != NULL) {
                $column_values[self::ID_CORE] = $this->_core_id;
            } else {
                $insert_errors[] = "The core associated to the note  can't be empty !";
            }

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = sql_varchar($this->getName());
            } else {
                $insert_errors[] = "The note can't be empty !";
            }
            if ($this->_core_note_who != NULL) {
                $column_values[self::WHO] = sql_varchar($this->_core_note_who);
            }
            if ($this->_core_note_date != NULL) {
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
                    writeChangeLog("add_note_core", $this->getIdValue());
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

        if ($this->_core_id != NULL) {
            $column_values[self::ID_CORE] = $this->_core_id;
        } else {
            $insert_errors[] = "The core associated to the note  can't be empty !";
        }

        if ($this->getName() != NULL) {
            $column_values[self::NAME] = sql_varchar($this->getName());
        } else {
            $insert_errors[] = "The note can't be empty !";
        }
        if ($this->_core_note_who != NULL) {
            $column_values[self::WHO] = sql_varchar($this->_core_note_who);
        }
        /*if ($this->_core_note_date != NULL) {
            $column_values[self::NOTES_DATE] =  sql_varchar($this->_core_note_date->format('Y-m-d H:i:s'));
        }
        else{*/
        // on force toujours la date du jours pour la modif ou la création    
        $column_values[self::NOTES_DATE] =  sql_varchar(date('Y-m-d H:i:s'));
        //}

        if (empty($insert_errors)) {
            if ($obj_exists){
                // mise à jour
                $result_insert = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID." = ".$this->getIdValue());
                if (!$result_insert) {
                    $insert_errors[] = "Update into table " . self::TABLE_NAME;
                }
            } else {
                // création
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                     //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_note_core", $this->getIdValue());
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
            throw new Exception("SITE NOTE :: ERROR TO SELECT ALL INFORMATION ABOUT SITE NOTE  ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setCoreNoteWho($values[self::WHO]);
            $this->setCoreNoteWhat($values[self::NAME]);
            $this->setCoreNoteDate($values[self::NOTES_DATE]);
            $this->setCoreId($values[self::ID_CORE]);
        }
    }

}
