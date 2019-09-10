<?php
/* 
 * fichier \Models\EstimatedAge.php
 * 
 */

class EstimatedAge extends ObjectPaleofire {

    const TABLE_NAME = 'r_has_estimated_age';
    const ID = 'ID_Depth';
    const NAME = 'EST_AGE_CAL_BP';
    const ID_AGE_MODEL = "ID_AGE_MODEL";
    const ID_SAMPLE = "ID_SAMPLE";
    //const EST_AGE_POSITIVE_ERROR = "EST_AGE_POSITIVE_ERROR";
    //const EST_AGE_NEGATIVE_ERROR = "EST_AGE_NEGATIVE_ERROR";

    public $_sample_id;
    public $_age_model;
    public $_est_age;
    public $_depth;
    //public $_est_age_positive_error;
    //public $_est_age_negative_error;

    /**
     * Constucteur de la classe
     * */
    public function __construct($est_age = -1) {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_est_age = $est_age;
        $this->_age_model = null;
        $this->_depth = null;
        $this->_sample_id = null;
       //$this->_est_age_positive_error;
       //$this->_est_age_negative_error;
    }

    /** ---------------------------GETTERS------------------------- * */

    /** ---------------------------CRUD------------------------- * */
    public function create() {
        $insert_errors = array();
        //BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();
        if ($this->_est_age !== NULL) {
            $column_values[self::NAME] = $this->_est_age;
        } else {
            $insert_errors[] = "The est age  can't be empty !";
        }
        if ($this->_sample_id != NULL) {
            $column_values[self::ID_SAMPLE] = $this->_sample_id;
        } else {
            $insert_errors[] = "The sample id of estimated age can't be empty !";
        }
        if ($this->_age_model != NULL) {
            $column_values[self::ID_AGE_MODEL] = $this->_age_model->getIdValue();
        } else {
            $insert_errors[] = "The age model id of estimated age can't be empty !!";
        }
        if ($this->_depth != NULL) {
            $column_values[self::ID] = $this->_depth->getIdValue();
        } else {
            $insert_errors[] = "The depth id of estimated age can't be empty !";
        }
        //if ($this->_est_age_negative_error != NULL) {
        //    $column_values[self::EST_AGE_NEGATIVE_ERROR] = $this->_est_age_negative_error;
        //}
        //if ($this->_est_age_positive_error != NULL) {
        //    $column_values[self::EST_AGE_POSITIVE_ERROR] = $this->_est_age_positive_error;
        //}
        
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

    public function save() {
        $insert_errors = array();
        //BEGIN TRANSACTION
        beginTransaction();
        $column_values = array();
        if ($this->_est_age !== NULL) {
            $column_values[self::NAME] = $this->_est_age;
        } else {
            $insert_errors[] = "The est age  can't be empty !";
        }
        if ($this->_sample_id != NULL) {
            $column_values[self::ID_SAMPLE] = $this->_sample_id;
        } else {
            $insert_errors[] = "The sample id of estimated age can't be empty !";
        }
        if ($this->_age_model != NULL) {
            $column_values[self::ID_AGE_MODEL] = $this->_age_model->getIdValue();
        } else {
            $insert_errors[] = "The age model id of estimated age can't be empty !";
        }
        if ($this->_depth != NULL) {
            $column_values[self::ID] = $this->_depth->getIdValue();
        } else {
            $insert_errors[] = "The depth id of estimated age can't be empty !";
        }
        //if ($this->_est_age_negative_error != NULL) {
        //    $column_values[self::EST_AGE_NEGATIVE_ERROR] = $this->_est_age_negative_error;
        //}
        //if ($this->_est_age_positive_error != NULL) {
        //    $column_values[self::EST_AGE_POSITIVE_ERROR] = $this->_est_age_positive_error;
        //}
        
        if (empty($insert_errors)) {
            // vérifier si la ligne existe en base de données
            $exist = FALSE;
            $query = "select * from r_has_estimated_age WHERE ".EstimatedAge::ID." = ".$this->_depth->getIdValue()
                        ." AND ".EstimatedAge::ID_AGE_MODEL." = ".$this->_age_model->getIdValue()
                        ." AND ".EstimatedAge::ID_SAMPLE." = ".$this->_sample_id;
            
            $res = queryToExecute($query);
            if ($res != NULL){
                $rows = fetchAll($res);
                if(count($rows) > 0){
                    $exist = TRUE;
                }
            }

            if($exist == FALSE){            
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "!Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            } else {
                $query = " ".EstimatedAge::ID." = ".$this->getIdValue()
                        ." AND ".EstimatedAge::ID_AGE_MODEL." = ".$this->_age_model->getIdValue()
                        ." AND ".EstimatedAge::ID_SAMPLE." = ".$this->_sample_id;
                $result_insert = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, $query);
                if (!$result_insert){
                    echo $query;
                }
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
            throw new Exception("Estimated age :: ERROR TO SELECT ALL INFORMATION ABOUT Estimated age ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_est_age = $values[self::NAME];
            $this->_depth = Depth::getObjectPaleofireFromWhere(sql_equal(Depth::ID, $values[self::ID]));
        }
    }

}
