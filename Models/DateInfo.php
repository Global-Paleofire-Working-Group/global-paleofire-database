<?php

/* 
 * fichier \Models\DateInfo.php
 * 
 */

require_once 'ObjectPaleofire.php';
include_once(REP_PAGES . "EDA/change_Log.php");


class DateInfo extends ObjectPaleofire {

    const TABLE_NAME = 't_date_info';
    const ID = 'ID_DATE_INFO';
    const NAME = 'DATE_LAB_NUMBER';
    const ID_DATE_TYPE = "ID_DATE_TYPE";
    const ID_SAMPLE = 'ID_SAMPLE';
    const ID_MAT_DATED = 'ID_MAT_DATED';
    const ID_DATE_COMMENT = 'ID_DATE_COMMENT';

    public $_date_type_id;
    public $_sample_id;
    public $_mat_dated_id;
    public $_date_lab_number;
    public $_date_comment_id;
    private $_list_has_ages;
    //private $_array_date_comments;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_date_type_id = null;
        $this->_mat_dated_id = null;
        $this->_sample_id = null;
        $this->_date_lab_number = null;
        $this->_date_comment_id = null;
        $this->_list_has_ages = array();
        //$this->_array_date_comments = array();
    }

    public function addHasAge(Age $has_age) {
        $this->_list_has_ages[] = $has_age;
    }
    
    public function getHasAge(){
        return $this->_list_has_ages;
    }
    /*
    public function addDateComment($comment) {
        $this->_array_date_comments[] = $comment;
    }*/
    
    public function getDateComment(){
        return $this->_date_comment_id; 
    }

    public function create($avecTransaction = true) {
        $insert_errors = array();
        //BEGIN TRANSACTION
        if ($avecTransaction == true) beginTransaction();

        $column_values = array();

        if ($this->_date_type_id != NULL) {
            $column_values[self::ID_DATE_TYPE] = $this->_date_type_id;
        } else {
            $insert_errors[] = "The date type associated to the date info  can't be empty !";
        }

        if ($this->_sample_id != NULL) {
            $column_values[self::ID_SAMPLE] = $this->_sample_id;
        } else {
            $insert_errors[] = "The sample associated to the date info  can't be empty !";
        }

        if ($this->_mat_dated_id != NULL) {
            $column_values[self::ID_MAT_DATED] = $this->_mat_dated_id;
        }

        if ($this->_date_lab_number != NULL) {
            $column_values[self::NAME] = sql_varchar($this->_date_lab_number);
        }
        
        if ($this->_date_comment_id != NULL) {
            $column_values[self::ID_DATE_COMMENT] = sql_varchar($this->_date_comment_id);
        }

        $id = null;
        if (empty($insert_errors)) {
            $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
            if (!$result_insert) {
                $insert_errors[] = "Insert into table " . self::TABLE_NAME;
            } else {
                $id = getLastCreatedId();
                $this->setIdValue($id);
                //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                writeChangeLog("add_date_info", $this->getIdValue());
            }
        }
        
       /* annulé pour l'instant il n'y a qu'un date comment par date_info
        * if (empty($insert_errors)){
            if ($this->_array_date_comments != NULL && count($this->_array_date_comments)> 0) {
            foreach($this->_array_date_comments as $comment) {
                    $values_comments['ID_DATE_INFO'] = $id;
                    $values_comments['ID_DATE_COMMENTS'] = $comment;
                    $res = insertIntoTable("r_has_date_comments", $values_comments);
                    if (!$res) {
                        $insert_errors[] = "Insert into table r_has_table_comments";
                    }
                }
            }
        }*/
        
        if (empty($insert_errors)) {
            foreach ($this->_list_has_ages as $has_age) {
                $has_age->setIdValue($this->getIdValue());
                $insert_errors = array_merge($insert_errors, $has_age->create());
            }
        }
        //Create all age model notes
        /* foreach ($this->getAgeModelNotes() as $note_age_model) {
          $note_age_model->setAgeModelId($this->getIdValue());
          $insert_errors = array_merge($insert_errors, $note_age_model->create());
          } */


//If no error, commit transaction !
        if ($avecTransaction == true){
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
        }
        return $insert_errors;
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("DATE INFO :: ERROR TO SELECT ALL INFORMATION ABOUT DATE INFO ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_date_lab_number = $values[self::NAME];
            $this->_date_type_id = $values[self::ID_DATE_TYPE];
            $this->_mat_dated_id = $values[self::ID_MAT_DATED];
            $this->_sample_id = $values[self::ID_SAMPLE];
            //var_dump($values);
            $this->_date_comment_id = $values[self::ID_DATE_COMMENT];
            
        }
    }

    public static function getListeFromCore($core_id){
        
    }
}
