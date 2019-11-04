<?php
/*
 * fichier \Models\AgeModel.php
 *
 */

include_once("ObjectPaleofire.php");
require_once 'AgeModelMethod.php';
require_once 'NoteAgeModel.php';
include_once(REP_PAGES . "EDA/change_Log.php");
require_once 'Status.php';

/**
 * Class AgeModel
 *
 */
class AgeModel extends ObjectPaleofire {

    const TABLE_NAME = 't_age_model';
    const ID = 'ID_AGE_MODEL';
    const NAME = 'AGE_MODEL_VERSION';
    const ID_AGE_MODEL_METHOD = "ID_AGE_MODEL_METHOD";
    const ID_CONTACT = "ID_CONTACT";
    const ID_CORE = "ID_CORE";
    const ID_STATUS = "ID_STATUS";

    public $_age_model_method;
    public $_core_id;
    public $_age_model_notes;
    public $_contact_id;

    protected static $_allObjectsByID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_age_model_method = null;
        $this->_core_id = null;
        $this->_age_model_notes = array();
        $this->_contact_id = null;
        $this->_age_model_id_status = 0;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    /**
     *
     * @param LandsDesc $land ($id or AgeModelMethod Object)
     */
    public function setAgeModelMethod($age_model) {
        if ($age_model instanceof AgeModelMethod) {
            $this->_age_model_method = $age_model;
        } else {
            if (is_numeric($age_model)) {
                $this->_age_model_method = AgeModelMethod::getObjectPaleofireFromId($age_model);
            } else {
                $this->_age_model_method = $age_model;
            }
        }
    }

    public function setContactId($contact_id) {
        $this->_contact_id = $contact_id;
    }

    public function setStatusId($status_id) {
        $this->_age_model_id_status = $status_id;
    }



    public function addAgeModelNote(NoteAgeModel $note) {
        $this->_age_model_notes[] = $note;
    }

    public function getContactId() {
        return $this->_contact_id;
    }

    public function getAgeModelNotes() {
        return $this->_age_model_notes;
    }

    public function getStatusId() {
        return $this->_age_model_id_status;
    }

    public function create($avecTransaction = TRUE) {
        $insert_errors = array();
        $object_exists = $this->exists();
        if (!$object_exists) {
            //BEGIN TRANSACTION
            if ($avecTransaction == true) beginTransaction();

            $column_values = array();

            if ($this->_age_model_method != NULL) {
                $column_values[self::ID_AGE_MODEL_METHOD] = $this->_age_model_method->getIdValue();
            } else {
                $insert_errors[] = "The age model method associated to the age model  can't be empty !";
            }

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = sql_varchar($this->getName());
            } else {
                $insert_errors[] = "The age model version  can't be empty !";
            }

            if ($this->_core_id != NULL) {
                $column_values[self::ID_CORE] = $this->_core_id;
            } else {
                $insert_errors[] = "The core associated to the age model  can't be empty !";
            }


            if ($this->getContactId() != NULL) {
                $column_values[self::ID_CONTACT] = $this->getContactId();
            }

            if ($this->_age_model_id_status != NULL) {
                 $column_values[self::ID_STATUS] = $this->getStatusId();
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            }

            //Create all age model notes
            foreach ($this->getAgeModelNotes() as $note_age_model) {
                $note_age_model->setAgeModelId($this->getIdValue());
                $insert_errors = array_merge($insert_errors, $note_age_model->create());
            }

//If no error, commit transaction !
            if ($avecTransaction == true) {
                if (empty($insert_errors)) {
                    commit();
                } else {
                    rollBack();
                }
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

        if ($this->_age_model_method != NULL) {
            $column_values[self::ID_AGE_MODEL_METHOD] = $this->_age_model_method->getIdValue();
        } else {
            $insert_errors[] = "The age model method associated to the age model  can't be empty !";
        }

        if ($this->getName() != NULL) {
            $column_values[self::NAME] = utf8_decode(sql_varchar($this->getName()));
        } else {
            $insert_errors[] = "The age model version  can't be empty !";
        }

        if ($this->_core_id != NULL) {
            $column_values[self::ID_CORE] = $this->_core_id;
        } else {
            $insert_errors[] = "The core associated to the age model  can't be empty !";
        }


        if ($this->getContactId() != NULL) {
            $column_values[self::ID_CONTACT] = $this->getContactId();
        }

        if (is_numeric($this->getStatusId())) {
                $column_values[self::ID_STATUS] = $this->getStatusId();
        } else {
            $insert_errors[] = "Error : id status !";
        }

        if (empty($insert_errors)) {
            if ($obj_exists == true){
                // mise à jour
                $res = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID . "=" . $this->getIdValue());
                if (!$res) {
                    $insert_errors[] = "Update table " . self::TABLE_NAME;
                }
                else {
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("edit_age_model", $this->getIdValue());
                }
            } else {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_age_model", $this->getIdValue());
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
            throw new Exception("AGE MODEL  :: ERROR TO SELECT ALL INFORMATION ABOUT AGE MODEL ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setAgeModelMethod(AgeModelMethod::getAgeModelMethodByID($values[self::ID_AGE_MODEL_METHOD]));
            //$this->setAgeModelMethod(AgeModelMethod::getObjectPaleofireFromId($values[self::ID_AGE_MODEL_METHOD]));
            $this->_core_id = $values[self::ID_CORE];
            $this->setContactId($values[self::ID_CONTACT]);
            $this->_age_model_notes = NoteAgeModel::getNoteAgeModelByID($values[self::ID]);
            $this->setStatusId($values[self::ID_STATUS]);
            //$this->_age_model_notes = NoteAgeModel::getObjectsPaleofireFromWhere(sql_equal(NoteAgeModel::ID_AGE_MODEL, $values[self::ID]));
        }
    }

    public function getDatesInfo(){
        $age_model_id = $this->getIdValue();
        // todo requête à reprendre trop compliquée
        /*$query ="(SELECT *
            FROM
            (
            SELECT AGE_VALUE as cal_age, AGE_POSITIVE_ERROR as cal_pos_err, AGE_NEGATIVE_ERROR as cal_neg_err,r_has_age.ID_AGE_UNITS as cal_age_units,ID_CALIBRATION_METHOD,
            ID_CALIBRATION_VERSION, r_has_age.ID_DATE_INFO as cal_id_date_info, t_sample.ID_SAMPLE, t_depth.DEPTH_VALUE as cal_depth, date_lab_number, id_date_comment
            FROM r_has_age
            join tr_age_units on r_has_age.id_age_units = tr_age_units.id_age_units and tr_age_units.age_units_calornot = 1
            JOIN t_date_info on r_has_age.ID_DATE_INFO = t_date_info.ID_DATE_INFO
            JOIN t_sample on t_sample.ID_SAMPLE = t_date_info.ID_SAMPLE
            JOIN t_depth on t_sample.ID_SAMPLE = t_depth.ID_SAMPLE_IF_DEFAULT
            WHERE r_has_age.ID_AGE_MODEL = ".$age_model_id."
                )tabCalibratedAge
            left join
            (
            SELECT AGE_VALUE as not_cal_age, AGE_POSITIVE_ERROR as not_cal_pos_err, AGE_NEGATIVE_ERROR as not_cal_neg_err,r_has_age.ID_AGE_UNITS, r_has_age.ID_DATE_INFO, t_sample.ID_SAMPLE, t_depth.DEPTH_VALUE
            FROM r_has_age
            join tr_age_units on r_has_age.id_age_units = tr_age_units.id_age_units and tr_age_units.age_units_calornot = 0
            JOIN t_date_info on r_has_age.ID_DATE_INFO = t_date_info.ID_DATE_INFO
            JOIN t_sample on t_sample.ID_SAMPLE = t_date_info.ID_SAMPLE
            JOIN t_depth on t_sample.ID_SAMPLE = t_depth.ID_SAMPLE_IF_DEFAULT
            WHERE r_has_age.ID_AGE_MODEL = ".$age_model_id."
                )tabNotCalibratedAge
                    ON tabCalibratedAge.id_sample = tabNotCalibratedAge.id_sample
            ORDER BY tabCalibratedAge.cal_depth)
            UNION
            (SELECT *
            FROM
            (
            SELECT AGE_VALUE as cal_age, AGE_POSITIVE_ERROR as cal_pos_err, AGE_NEGATIVE_ERROR as cal_neg_err,r_has_age.ID_AGE_UNITS as cal_age_units,ID_CALIBRATION_METHOD,
            ID_CALIBRATION_VERSION, r_has_age.ID_DATE_INFO as cal_id_date_info, t_sample.ID_SAMPLE, t_depth.DEPTH_VALUE as cal_depth, date_lab_number, id_date_comment
            FROM r_has_age
            join tr_age_units on r_has_age.id_age_units = tr_age_units.id_age_units and tr_age_units.age_units_calornot = 1
            JOIN t_date_info on r_has_age.ID_DATE_INFO = t_date_info.ID_DATE_INFO
            JOIN t_sample on t_sample.ID_SAMPLE = t_date_info.ID_SAMPLE
            JOIN t_depth on t_sample.ID_SAMPLE = t_depth.ID_SAMPLE_IF_DEFAULT
            WHERE r_has_age.ID_AGE_MODEL = ".$age_model_id."
                )tabCalibratedAge
            right join
            (
            SELECT AGE_VALUE as not_cal_age, AGE_POSITIVE_ERROR as not_cal_pos_err, AGE_NEGATIVE_ERROR as not_cal_neg_err,r_has_age.ID_AGE_UNITS, r_has_age.ID_DATE_INFO, t_sample.ID_SAMPLE, t_depth.DEPTH_VALUE
            FROM r_has_age
            join tr_age_units on r_has_age.id_age_units = tr_age_units.id_age_units and tr_age_units.age_units_calornot = 0
            JOIN t_date_info on r_has_age.ID_DATE_INFO = t_date_info.ID_DATE_INFO
            JOIN t_sample on t_sample.ID_SAMPLE = t_date_info.ID_SAMPLE
            JOIN t_depth on t_sample.ID_SAMPLE = t_depth.ID_SAMPLE_IF_DEFAULT
            WHERE r_has_age.ID_AGE_MODEL = ".$age_model_id."
                )tabNotCalibratedAge
                    ON tabCalibratedAge.id_sample = tabNotCalibratedAge.id_sample
            ORDER BY tabCalibratedAge.cal_depth)

        ";*/

        $query = "SELECT * from
                (select ID_DATE_INFO, DATE_LAB_NUMBER, ID_DATE_TYPE, ID_SAMPLE, ID_DATE_COMMENT, ID_STATUS, CONCAT('[', GROUP_CONCAT(AGE SEPARATOR ','), ']') as AGES
                from
                    (select t_date_info.ID_DATE_INFO, t_date_info.DATE_LAB_NUMBER, t_date_info.ID_DATE_TYPE, t_date_info.ID_SAMPLE,
                        t_date_info.ID_DATE_COMMENT, t_date_info.ID_STATUS, CONCAT('[', COALESCE(ID_AGE_UNITS, 'null'),',',
                        AGE_VALUE,',', COALESCE(AGE_POSITIVE_ERROR, 'null'),',', COALESCE(AGE_NEGATIVE_ERROR,'null') ,',',
                        COALESCE(ID_CALIBRATION_METHOD,'null'),',', COALESCE(ID_CALIBRATION_VERSION, 'null'), ']') as AGE
                    from t_date_info
                    join r_has_age on t_date_info.ID_DATE_INFO = r_has_age.ID_DATE_INFO
                    where r_has_age.id_age_model = ".$age_model_id."
                    ) tDateInfoWithAge
                group by ID_DATE_INFO, DATE_LAB_NUMBER, ID_DATE_TYPE, ID_SAMPLE, ID_DATE_COMMENT, ID_STATUS
                ) tDateInfoWithTabAges
            join t_sample on tDateInfoWithTabAges.id_sample = t_sample.ID_SAMPLE
            join t_depth on t_sample.ID_SAMPLE = t_depth.ID_SAMPLE_IF_DEFAULT";

        return queryToExecute($query);
    }

    public function getEstimatedAges(){
        $age_model_id = $this->getIdValue();

        $query ="select est_age_cal_bp as age, est_age_positive_error as pos_err,
            est_age_negative_error as neg_err, depth_value as depth, quantity, charcoal_units_name as units
            from r_has_estimated_age
            join t_depth on t_depth.id_depth = r_has_estimated_age.id_depth
            join t_sample on t_sample.id_sample = t_depth.id_related_sample
            join t_charcoal on t_charcoal.id_sample = t_sample.id_sample
            join r_has_charcoal_quantity on r_has_charcoal_quantity.id_charcoal = t_charcoal.id_charcoal
            join tr_charcoal_units on tr_charcoal_units.id_charcoal_units = r_has_charcoal_quantity.id_charcoal_units
            where r_has_estimated_age.id_age_model = ".$age_model_id."
            and t_depth.id_depth_type = 3
            order by units, depth
        ";
        return queryToExecute($query);
    }

    public function getProxyFireEstimatedAges(){
        $age_model_id = $this->getIdValue();

        $query ="select est_age_cal_bp as age, est_age_positive_error as pos_err,
            est_age_negative_error as neg_err, depth_value as depth, quantity, PROXY_FIRE_MEASUREMENT_UNIT_NAME as units
            from r_has_estimated_age
            join t_depth on t_depth.id_depth = r_has_estimated_age.id_depth
            join t_sample on t_sample.id_sample = t_depth.id_related_sample
            join t_proxy_fire_data on t_proxy_fire_data.id_sample = t_sample.id_sample
            join r_has_proxy_fire_data_quantity on r_has_proxy_fire_data_quantity.ID_PROXY_FIRE_DATA = t_proxy_fire_data.ID_PROXY_FIRE_DATA
            join t_proxy_fire_measurement_unit on t_proxy_fire_measurement_unit.id_proxy_fire_measurement_unit = r_has_proxy_fire_data_quantity.id_proxy_fire_measurement_unit
            where r_has_estimated_age.id_age_model = ".$age_model_id."
            and t_depth.id_depth_type = 3
            order by units, depth
        ";
        return queryToExecute($query);
    }

    public function getQueryAges() {
        $age_model_id = $this->getIdValue();

        /*$query = "SELECT `AGE_VALUE`, AGE_POSITIVE_ERROR, AGE_NEGATIVE_ERROR,ID_AGE_UNITS, `ID_CALIBRATION_METHOD`,`ID_CALIBRATION_VERSION`, r_has_age.`ID_AGE_MODEL`, r_has_age.`ID_DATE_INFO`, t_core.ID_CORE, t_sample.ID_SAMPLE, t_depth.DEPTH_VALUE

        FROM r_has_age, t_age_model, t_core, t_date_info, t_sample

        INNER JOIN t_depth on t_depth.ID_SAMPLE_IF_DEFAULT = t_sample.ID_SAMPLE

        WHERE r_has_age.ID_AGE_MODEL =".$age_model_id."
        and t_age_model.ID_AGE_MODEL = r_has_age.ID_AGE_MODEL
        and t_age_model.ID_CORE = t_core.ID_CORE
        and t_date_info.ID_DATE_INFO = r_has_age.ID_DATE_INFO
        and  t_sample.ID_CORE = t_core.ID_CORE and t_date_info.ID_SAMPLE = t_sample.ID_SAMPLE
        ORDER BY t_depth.DEPTH_VALUE";*/
        return queryToExecute($query);
    }


    public static $_allAgeModelByID = null;
    public static function getAllAgeModelByID() {
        if (self::$_allAgeModelByID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, AgeModel::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $class_paleo = get_called_class();
                $tab_get_values = fetchAll($result_get_object);
                self::$_allAgeModelByID = null;
                foreach($tab_get_values as $row){
                    $object = new AgeModel();
                    $object->read($row);
                    self::$_allAgeModelByID[$row[AgeModel::ID]] = $object;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allAgeModelByID = NULL;
            }
        }
        return self::$_allAgeModelByID;
    }

    public static function getAgeModelByID($id){
        $liste = self::getallAgeModelByID();
        if (array_key_exists($id, $liste)){
            return $liste[$id];
        } else {
            return null;
        }
    }


    private static $_allIdAgeModelByIdCore = null;
    public static function getAllIdAgeModelByIdCore() {
        if (self::$_allIdAgeModelByIdCore == null){
            $result_get_object = getFieldsFromTables("ID_CORE, ID_AGE_MODEL", AgeModel::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allIdAgeModelByIdCore = null;
                foreach($tab_get_values as $row){
                    self::$_allIdAgeModelByIdCore[$row[AgeModel::ID_CORE]][] = $row[AgeModel::ID];
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allIdAgeModelByIdCore = NULL;
            }
        }
        return self::$_allIdAgeModelByIdCore;
    }

    public static function getAgeModelFromModeller($id_contact){
        $list = [];
        if (is_numeric($id_contact)){
            $requete = "SELECT * "
                    . "FROM t_age_model "
                    . "where id_contact = ".$id_contact;
            $res = queryToExecute($requete);
            if ($res != NULL){
                $rows = fetchAll($res);
                foreach($rows as $values){
                    $age_model = new AgeModel();
                    $age_model->read($values);
                    $list[] = $age_model;
                    $age_model = NULL;
                }
            }
        }
        return $list;
    }

}
