<?php
/* 
 * fichier \Models\Sample.php
 * 
 */

require_once 'ObjectPaleofire.php';
require_once 'Depth.php';
require_once 'EstimatedAge.php';
require_once 'Charcoal.php';
require_once 'DateInfo.php';

/**
 * Class Sample
 *
 */
class Sample extends ObjectPaleofire {

    const TABLE_NAME = 't_sample';
    const ID = 'ID_SAMPLE';
    const NAME = 'SAMPLE_NAME';
    const ID_CORE = "ID_CORE";
    const ID_AGE_MODEL = "ID_AGE_MODEL";
    const GCD_ACCESS_ID = "GCD_ACCESS_ID";

    public $_sample_core_id;
    public $_sample_age_model;
    public $_default_depth;
    private $_list_depths;
    private $_list_estimated_age;
    private $_list_date_infos;
    private $_list_charcoals;
    private $_temp_contrib_imported_id;
    public $_gcd_access_id;
    public $_temp_author_imported_id;
    
    public static $_allSamplesByCGD_ACCESS_ID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        $field_names = array(self::ID, self::NAME);
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME, $field_names);
        $this->_temp_contrib_imported_id = NULL;
        $this->_sample_core_id = NULL;
        $this->_sample_age_model = NULL;
        $this->_default_depth = NULL;
        $this->_list_depths = array();
        $this->_list_estimated_age = array();
        $this->_list_date_infos = array();
        $this->_list_charcoals = array();
        $this->_gcd_access_id = null;
        $this->_temp_author_imported_id = null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, (string) $name_value);
    }

    public function setTempContributor($temp_author) {
        $this->_temp_contrib_imported_id = $temp_author;
    }

    public function addDepth(Depth $depth) {
        $this->_list_depths[] = $depth;
    }

    public function addImportEstimatedAge(EstimatedAge $estimated_age) {
        $estimated_age->_age_model = $this->_sample_age_model;
        $estimated_age->_depth = $this->_default_depth;
        $this->_list_estimated_age[] = $estimated_age;
    }

    public function addEstimatedAge(EstimatedAge $estimated_age) {
        $estimated_age->_age_model = $this->_sample_age_model;
        $this->_list_estimated_age[] = $estimated_age;
    }
    
    public function addDateInfo(DateInfo $date_info) {
        $this->_list_date_infos[] = $date_info;
    }

    public function getListEstimatedAge() {
        return $this->_list_estimated_age;
    }

    public function getListDatesInfo() {
        if ($this->_list_date_infos == null){
            // on tente de récupérer les dates infos
            $this->_list_date_infos = DateInfo::getObjectsPaleofireFromWhere(sql_equal(DateInfo::ID_SAMPLE, $this->getIdValue()));
        }
        return $this->_list_date_infos;
    }

    public function getListCharcoals() {
        if ($this->_list_charcoals == null){
            $this->_list_charcoals = Charcoal::getObjectsPaleofireFromWhere(sql_equal(Charcoal::ID_SAMPLE, $this->getIdValue()));
        }
        return $this->_list_charcoals;
    }

    public static function getAllSamples() {
        return getAllObjectsInTable(self::TABLE_NAME);
    }

    public function getTableName() {
        return self::TABLE_NAME;
    }

    public function getListDepths() {
        return $this->_list_depths;
    }

    public static function getMetaDataSamplesOfSite($id_site) {
        $metadata_samples = array(self::NAME, "DEPTH", "EST_AGE");
        $request = "SELECT ";
        foreach ($metadata_samples as $metadata) {
            $request.= $metadata . ",";
        }
        $request.= self::ID;
        $request.= " FROM " . self::TABLE_NAME . " WHERE ID_SITE = " . $id_site . "";
        return queryToExecute($request, "select metadatas samples from a specific site");
    }

    public static function getSample($id_Sample) {
        $current_Sample = new Sample();
        $result_get_Sample = getAllObjectFromId(self::TABLE_NAME, self::ID, $id_Sample);
        $tab_values = array();
        if (fetchRow($result_get_Sample)) {
            foreach ($current_Sample->getFields() as $field_name) {
                $tab_values[$field_name] = getValueInResult($result_get_Sample, $field_name);
            }
        }
        if ($tab_values != NULL && !empty($tab_values)) {
            $current_Sample->setArrayValues($tab_values);
            return $current_Sample;
        } else {
            return NULL;
        }
    }

    public static function countSamples($field_name = NULL, $value = NULL) {
        $nb_samples = 0;

        if ($field_name != NULL && $value != NULL) {
            if (is_numeric($value)) {
                $res_get_nb_samples = queryToExecute("SELECT COUNT(" . self::ID . ") as NBSAMPLES FROM " . self::TABLE_NAME . " WHERE " . $field_name . "=" . $value . "", "get samples filtered");
            } else {
                $res_get_nb_samples = queryToExecute("SELECT COUNT(" . self::ID . ") as NBSAMPLES FROM " . self::TABLE_NAME . " WHERE " . $field_name . "='" . $value . "'", "get samples filtered");
            }
        } else {
            $res_get_nb_samples = getResultFunctionOnField("COUNT", self::ID, "NBSAMPLES", self::TABLE_NAME);
        }
        $row = fetchRow($res_get_nb_samples);
        if ($row != null) $nb_samples = $row[0];
        return $nb_samples;
    }

    public function create($avecTransaction = true) {
        $insert_errors = array();
        $object_exists = $this->exists();
        if (!$object_exists) {
//BEGIN TRANSACTION
            if ($avecTransaction == true) beginTransaction();

            $column_values = array();

            if ($this->getName() != NULL && $this->getName() != "") {
                $column_values[self::NAME] = utf8_decode(sql_varchar($this->getName()));
            } else {
                $insert_errors[] = "The name of the sample can't be empty !";
            }
            if ($this->_sample_core_id != NULL) {
                $column_values[self::ID_CORE] = $this->_sample_core_id;
            } else {
                $insert_errors[] = "The id of reference core can't be empty !";
            }
            if ($this->_sample_age_model != NULL) {
                if (gettype($this->_sample_age_model) == "object")
                    $column_values[self::ID_AGE_MODEL] = $this->_sample_age_model->getIdValue();
                else 
                    $column_values[self::ID_AGE_MODEL] = $this->_sample_age_model;
            } else {
                $insert_errors[] = "The id of age model can't be empty !";
            }
            if ($this->_gcd_access_id != NULL) {
                $column_values[self::GCD_ACCESS_ID] = $this->_gcd_access_id;
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    //writeChangeLog("add_sample", $this->getIdValue());
                }
            }
            if (empty($insert_errors)) {

                if ($this->_default_depth != NULL) {
                    $this->_default_depth->_sample_id_default_depth = $this->getIdValue();
                    $this->_default_depth->_related_sample_id = $this->getIdValue();
                    $insert_errors = array_merge($insert_errors, $this->_default_depth->create());
                } else {
                    $insert_errors[] = "Sample::".$this->getName().", the default depth can't be null !";
                }

                foreach ($this->_list_depths as $depth) {
                    if (isset($this->_default_depth) && $depth->_depth_value != $this->_default_depth->_depth_value) {
                        $depth->_related_sample_id = $this->getIdValue();
                        $insert_errors = array_merge($insert_errors, $depth->create());
                    }
                }

                foreach ($this->_list_estimated_age as $estimated_age) {
                    //echo 'sample create';
                    //var_dump($estimated_age);
                    $estimated_age->_sample_id = $this->getIdValue();
                    $errors = $estimated_age->create();
                    if (isset($column_values[self::GCD_ACCESS_ID])) {
                        $errors = addValueToEachValuesInArray($column_values[self::GCD_ACCESS_ID], $errors);
                    }
                    $insert_errors = array_merge($insert_errors, $errors);
                }

                foreach ($this->_list_date_infos as $date_info) {
                    $date_info->_sample_id = $this->getIdValue();
                    $insert_errors = array_merge($insert_errors, $date_info->create(false));
                }
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
    
    
    public function save($avecTransaction = true) {
        $insert_errors = array();

        //BEGIN TRANSACTION
        if ($avecTransaction == true) beginTransaction();

        $column_values = array();

        // if creation record of the name, if update the name must not be changed
        if($this->getIdValue() == NULL){
            if ($this->getName() != NULL && $this->getName() != "") {
                $column_values[self::NAME] = sql_varchar($this->getName());
            } else {
                $insert_errors[] = "The name of the sample can't be empty !";
            }
        }
        if ($this->_sample_core_id != NULL) {
            $column_values[self::ID_CORE] = $this->_sample_core_id;
        } else {
            $insert_errors[] = "The id of reference core can't be empty !";
        }
        if ($this->_sample_age_model != NULL) {
            if (gettype($this->_sample_age_model) == "object")
                $column_values[self::ID_AGE_MODEL] = $this->_sample_age_model->getIdValue();
            else 
                $column_values[self::ID_AGE_MODEL] = $this->_sample_age_model;
        } else {
            $insert_errors[] = "The id of age model can't be empty !";
        }
        if ($this->_gcd_access_id != NULL) {
            $column_values[self::GCD_ACCESS_ID] = $this->_gcd_access_id;
        }

        if (empty($insert_errors)) {
            if ($this->getIdValue() == NULL) {
                // création
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_sample", $this->getIdValue());
                }
            } else {
                //mise à jour
                $result = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID." = ".$this->getIdValue());
                if (!$result){
                    $insert_errors[] = "Update into table ".self::TABLE_NAME." failed. ID : ".$this->getIdValue();
                    writeChangeLog("update_sample", $this->getIdValue());
                }

            }
        }
        if (empty($insert_errors)) {

            if ($this->_default_depth != NULL) {
                $this->_default_depth->_sample_id_default_depth = $this->getIdValue();
                $this->_default_depth->_related_sample_id = $this->getIdValue();
                //$insert_errors = array_merge($insert_errors, $this->_default_depth->save());

            } else {
                $insert_errors[] = "Sample::".$this->getName().", the default depth can't be null !";
            }

            /*foreach ($this->_list_depths as $depth) {
                if (isset($this->_default_depth) && $depth->_depth_value != $this->_default_depth->_depth_value) {
                    $depth->_related_sample_id = $this->getIdValue();
                    $insert_errors = array_merge($insert_errors, $depth->save());
                }
            }*/

            
            foreach ($this->_list_estimated_age as $estimated_age) {
                $estimated_age->_sample_id = $this->getIdValue();
                if ($estimated_age->_depth != NULL){
                    $estimated_age->_depth->_related_sample_id = $this->getIdValue();
                    $errors = $estimated_age->_depth->save();
                }
                $insert_errors = array_merge($insert_errors, $errors);
                
                $errors = $estimated_age->save();
                if (isset($column_values[self::GCD_ACCESS_ID])) {
                    $errors = addValueToEachValuesInArray($column_values[self::GCD_ACCESS_ID], $errors);
                }
                $insert_errors = array_merge($insert_errors, $errors);
            }

            foreach ($this->_list_date_infos as $date_info) {
                $date_info->_sample_id = $this->getIdValue();
                $insert_errors = array_merge($insert_errors, $date_info->create(false));
            }
        }

        //If no error, commit transaction !
        if ($avecTransaction == true) {
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
        }

        return $insert_errors;

    }
    
    
    
    

    public function createTemporaryData() {
        $column_values = array();

        try {
            $column_values["ID_SAMPLE_TEMP"] = $this->getIdValue();

            if ($this->_temp_author_imported_id != NULL) {
                $column_values["AUTHOR_TEMP_ID"] = $this->_temp_author_imported_id;
            }
            if ($this->_temp_contrib_imported_id != NULL) {
                $column_values["CONTRIBUTOR_TEMP_ID"] = $this->_temp_contrib_imported_id;
            }

            insertIntoTable("tt_sample_import", $column_values);
        } catch (Exception $e) {
//Do nothing
        }
    }

    public function prepareAndSetFieldValue($field, $value) {
        parent::setFieldValue($field, $value);
    }

    public function read($values = null) {
//Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
//If the request return nothing
            throw new Exception("SAMPLE :: ERROR TO SELECT ALL INFORMATION ABOUT SAMPLE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_gcd_access_id = $values[self::GCD_ACCESS_ID];
            $this->_sample_core_id = $values[self::ID_CORE];
            // inutile pour l'import des données
            //$this->_default_depth = Depth::getObjectPaleofireFromWhere(sql_equal(Depth::ID_SAMPLE_WHEN_DEFAULT, $values[self::ID]));
            
            //$this->_sample_age_model = AgeModel::getObjectPaleofireFromId($values[self::ID_AGE_MODEL]);
            $this->_sample_age_model = AgeModel::getAgeModelByID($values[self::ID_AGE_MODEL]);
            
            $this->_list_depths = Depth::getObjectsPaleofireFromWhere(sql_equal(Depth::ID_RELATED_SAMPLE, $values[self::ID]));
            $this->_list_estimated_age = EstimatedAge::getObjectsPaleofireFromWhere(sql_equal(EstimatedAge::ID_SAMPLE, $values[self::ID]));
            $this->_list_date_infos = DateInfo::getObjectsPaleofireFromWhere(sql_equal(DateInfo::ID_SAMPLE, $values[self::ID]));
            // inutile pour l'import des données la table n'existe pas encore
            //$this->_list_charcoals = Charcoal::getObjectsPaleofireFromWhere(sql_equal(Charcoal::ID_SAMPLE, $values[self::ID]));
        }
    }

    public function isInSite($site_id) {
        if ($this->_sample_core_id == null) {
            $this->read();
        }
        $temp_core = Core::getObjectPaleofireFromId($this->_sample_core_id);
//If the core id of the sample doesn't have a core in the database, return false
        if ($temp_core == null) {
            return false;
        } else {
            if ($temp_core->getSite() == null) {
                $temp_core->read();
            }
            if ($temp_core->getSite()->getIdValue() == $site_id) {
                return true;
            }
        }
        return false;
    }

    public function getQueryAges() {
        $sample_id = $this->getIdValue();
        $age_model_id = $this->_sample_age_model->getIdValue();

        $query = "SELECT AGE_VALUE, AGE_POSITIVE_ERROR, AGE_NEGATIVE_ERROR, ID_AGE_UNITS, ID_CALIBRATION_METHOD, r_has_age.ID_DATE_INFO
FROM r_has_age
LEFT JOIN t_date_info ON r_has_age.ID_DATE_INFO = t_date_info.ID_DATE_INFO
WHERE
t_date_info.ID_SAMPLE =" . $sample_id . "
AND ID_AGE_MODEL =" . $age_model_id . "";
        return queryToExecute($query);
    }

    public static function getSampleFromDepthAvg($depth_value) {
        $query = "SELECT t_site.ID_SITE,t_sample.ID_SAMPLE,t_depth.DEPTH_VALUE FROM t_site 
JOIN t_core ON t_core.ID_SITE = t_site.ID_SITE
JOIN t_sample ON t_core.ID_CORE = t_sample.ID_CORE
JOIN t_depth ON t_sample.ID_SAMPLE=t_depth.ID_SAMPLE
WHERE t_depth.DEPTH_VALUE = " . $depth_value . ";";
    }

    public static function getFuncSamplesBySite($func) {
        ($func == "MAX") ? $order_by = "DESC" : $order_by = "ASC";
        $query = "SELECT " . $func . "( countsample.nb_samples ) AS result_func, ID_SITE FROM (            
SELECT COUNT(t_sample.ID_SAMPLE) as nb_samples, t_site.ID_SITE
            FROM t_sample
            INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site ON t_core.ID_SITE = t_site.ID_SITE
GROUP BY t_site.ID_SITE ORDER BY nb_samples " . $order_by . ") countsample";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site[1] = $values["result_func"];
            $nb_site[2] = $values["ID_SITE"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function getFuncOnEstAge($func, $id_site) {
        $result_func_value = 0;
        $query = "SELECT " . $func . "(EST_AGE_CAL_BP) AS result_value
FROM r_has_estimated_age
INNER JOIN  t_sample ON t_sample.ID_SAMPLE = r_has_estimated_age.ID_SAMPLE
INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site ON t_core.ID_SITE = t_site.ID_SITE
WHERE t_site.ID_SITE =" . $id_site . ";";

        $res = queryToExecute($query);
        while ($values = fetchAssoc($res)) {
            $result_func_value = $values["result_value"];
        }
        freeResult($res);
        return $result_func_value;
    }
    
    /**
     * @return null|\object_paleofire
     */
    public static function getAllSamplesByGCD_ACCESS_ID() {
        if (self::$_allSamplesByCGD_ACCESS_ID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allSamplesByCGD_ACCESS_ID = null;
                foreach($tab_get_values as $row){
                    // pour l'import de données on récupère les site par le GCD_ACCESS_ID
                    //$object_paleo = new $class_paleo();
                    //$object_paleo->read($row);
                    self::$_allSamplesByCGD_ACCESS_ID[$row[self::GCD_ACCESS_ID]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allSamplesByCGD_ACCESS_ID = NULL;
            }
        }
        return self::$_allSamplesByCGD_ACCESS_ID;
    }

}
