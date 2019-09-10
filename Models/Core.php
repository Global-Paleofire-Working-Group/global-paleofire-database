<?php
/* 
 * fichier \Models\Core.php
 * 
 */

include_once("ObjectPaleofire.php");
require_once 'DepoContext.php';
require_once 'AgeModel.php';
require_once 'CoreType.php';
require_once 'Sample.php';
require_once 'Status.php';
require_once 'DataBaseVersion.php';
include_once(REP_PAGES . "EDA/change_Log.php");


/**
 * Class Core
 *
 */
class Core extends ObjectPaleofire {

    const TABLE_NAME = 't_core';
    const ID = 'ID_CORE';
    const NAME = 'CORE_NAME';
    const LATITUDE = "LATITUDE";
    const LONGITUDE = "LONGITUDE";
    const ELEVATION = "ELEVATION";
    const WATER_DEPTH = "WATER_DEPTH";
    const CORING_DATE = "CORING_DATE";
    const ID_SITE = "ID_SITE";
    const ID_DEPO_CONTEXT = "ID_DEPO_CONTEXT";
    const ID_AFFILIATION = "ID_AFFILIATION";
    const ID_CORE_TYPE = "ID_CORE_TYPE";
    const ID_STATUS = "ID_STATUS";
    
    const ID_IMPORT = "ID_SITE";

    public $_site;
    public $_depo_context_id;
    public $_latitude;
    public $_longitude;
    public $_elevation;
    public $_water_depth_value;
    private $_list_age_model;
    private $_list_samples;
    public $_list_core_notes;
    private $_site_id;
    public $_coring_date;
    public $_core_type_id;
    public $_affiliation_id;
    public $_core_id_status;

    
    protected static $_allObjectsByID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct($site = null) {
        $field_names = array(self::ID, self::NAME);
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME, $field_names);

        ($site != NULL) ? $this->_site = $site : $this->_site = NULL;
        $this->setNameValue($this->generate_name());

        $this->_depo_context_id = null;
        $this->_latitude = null;
        $this->_longitude = null;
        $this->_elevation = null;
        $this->_water_depth_value = null;
        $this->_list_age_model = array();
        $this->_list_samples = array();
        $this->_list_core_notes = array();
        $this->_site_id = null;
        $this->_coring_date = null;
        $this->_core_type_id = null;
        $this->_affiliation_id = null;
    }

    /** ---------------------------SETTERS------------------------- * */
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function addAgeModel(AgeModel $age_model) {
        $this->_list_age_model[] = $age_model;
    }

    public function setSite(Site $site) {
        $this->_site = $site;
    }
            
    public function setStatusId($status_id) {
        $this->_core_id_status = $status_id;
    }
    
    public function addSample(Sample $sample) {
        $this->_list_samples[] = $sample;
    }

    /** ---------------------------GETTERS------------------------- * */
    
    public static function getCoreDataFromID($id){
        $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME, sql_equal(self::ID, $id));
        if (getNumRows($result_get_object) > 0) {
            $values = fetchAssoc($result_get_object);
            $core = new Core();
            
            if ($values == NULL || empty($values)) {
                //If the request return nothing
                throw new Exception("CORE :: ERROR TO SELECT ALL INFORMATION ABOUT CORE ID : " . $id);
            } else {
                $core->setIdValue($values[self::ID]);
                $core->setNameValue($values[self::NAME]);
                $core->_site_id = $values[self::ID_SITE];
                $core->_latitude = $values[self::LATITUDE];
                $core->_longitude = $values[self::LONGITUDE];
                $core->_elevation = $values[self::ELEVATION];
                $core->_water_depth_value = $values[self::WATER_DEPTH];
                $core->_depo_context_id = $values[self::ID_DEPO_CONTEXT];
                $core->_core_type_id = $values[self::ID_CORE_TYPE];
                $core->_affiliation_id = $values[self::ID_AFFILIATION];
            }
            
            freeResult($result_get_object);
            return $core;
        } else {
            return NULL;
        }
        
        
        
        
    }
    
    public function getSite() {
        if ($this->_site != null) {
            return $this->_site;
        } else {
            $this->_site = Site::getObjectPaleofireFromId($this->_site_id);
            return $this->_site;
        }
    }

    public function getSiteId() {
        if ($this->_site == NULL) {
            return $this->_site_id;
        } else {
            return $this->getSite()->getIdValue();
        }
    }
    
    public function getStatusId() {
        return $this->_core_id_status;
    }

    public function getLongitude() {
        if ($this->_longitude == NULL) {
            $this->_longitude = Core::getFieldValueFromWhere(Core::LONGITUDE, sql_equal(Core::ID, $this->getIdValue()));
        }
        return $this->_longitude;
    }

    public function getLatitude() {
        if ($this->_latitude == NULL) {
            $this->_latitude = Core::getFieldValueFromWhere(Core::LATITUDE, sql_equal(Core::ID, $this->getIdValue()));
        }
        return $this->_latitude;
    }

    public function getElevation() {
        if ($this->_elevation == NULL) {
            $this->_elevation = Core::getFieldValueFromWhere(Core::ELEVATION, sql_equal(Core::ID, $this->getIdValue()));
        }
        return $this->_elevation;
    }

    public function getCoringDate() {
        if ($this->_coring_date == NULL) {
            $this->_coring_date = Core::getFieldValueFromWhere(Core::CORING_DATE, sql_equal(Core::ID, $this->getIdValue()));
        }
        return $this->_coring_date;
    }

    public function getWaterDepth() {
        if ($this->_water_depth_value == NULL) {
            $this->_water_depth_value = Core::getFieldValueFromWhere(Core::WATER_DEPTH, sql_equal(Core::ID, $this->getIdValue()));
        }
        return $this->_water_depth_value;
    }

    public function getAllAgeModel() {
        return $this->_list_age_model;
    }
    
    public function getAgeModels(){
        $query = "select t_age_model.id_age_model, t_age_model.age_model_version, r_has_age.age_value, r_has_age.age_positive_error, r_has_age.age_negative_error,
                    r_has_age.id_age_units,
                    r_has_age.id_calibration_method, 
                    r_has_age.id_calibration_version
                    from t_core
                    join t_age_model on t_age_model.id_core = t_core.id_core
                    join r_has_age on r_has_age.id_age_model = t_age_model.id_age_model
                    where t_core.id_core = 1;";
        
        
    }

    public function getAllSamples() {
        return $this->_list_samples;
    }
    
    public function getAllCharcoals() {
        $query = "SELECT t_sample.*, t_charcoal.*, t_depth.*, r_has_estimated_age.* FROM `t_core` 
            join t_sample on t_sample.id_core = t_core.id_core
            join t_charcoal on t_charcoal.id_sample = t_sample.id_sample
            join t_depth on t_sample.id_sample = t_depth.id_sample_if_default
            left join r_has_estimated_age on t_depth.id_depth = r_has_estimated_age.id_depth
            WHERE t_core.id_core = ".$this->getIdValue();
        
        $res = queryToExecute($query);
        $tab = fetchAll($res);
        $liste = null;
        foreach($tab as $values){
            $depth = new Depth();
            $depth->read($values);
            
            // TODO si plusieurs ages comment faire
            $age = new EstimatedAge();
            $age->read($values);
            
            $sample = new Sample();
            $sample->read($values);
            $sample->_default_depth = $depth;
            
            $charcoal = new Charcoal();
            $charcoal->read($values);
            $charcoal->_sample = $sample;
            
            $liste[] = $charcoal;
        }
        return $liste;
    }
    
    public function getAllDateInfo(){
        
    }

    public function countSamples() {
        return count(Sample::getAllIds(NULL, NULL, sql_equal(Sample::ID_CORE, $this->getIdValue())));
    }

    /** --------------------------- CRUD ------------------------- * */
    public function create() {

        $insert_errors = array();
        $obj_exists = $this->exists();
        
        if (!$obj_exists) {
            
            //BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->getSiteId() != NULL) {
                $column_values[self::ID_SITE] = $this->getSiteId();
            } else {
                $insert_errors[] = "The site associated to the core  can't be empty !";
            }

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = utf8_decode(sql_varchar($this->getName()));
            } else {
                $insert_errors[] = "The core name can't be empty !";
            }


            if ($this->getLatitude() != NULL) {
                $column_values[self::LATITUDE] = $this->getLatitude();
            } else {
                $insert_errors[] = "The core latitude  can't be empty !";
            }
            if ($this->getLongitude() != NULL) {
                $column_values[self::LONGITUDE] = $this->getLongitude();
            } else {
                $insert_errors[] = "The core longitude  can't be empty !";
            }
            if ($this->getElevation() != NULL) {
                $column_values[self::ELEVATION] = $this->getElevation();
            }
            if ($this->getWaterDepth() != NULL) {
                $column_values[self::WATER_DEPTH] = $this->getWaterDepth();
            }
            if ($this->_core_type_id != NULL) {
                $column_values[self::ID_CORE_TYPE] = $this->_core_type_id;
            }
            if ($this->_depo_context_id != NULL) {
                $column_values[self::ID_DEPO_CONTEXT] = $this->_depo_context_id;
            }

            if ($this->_coring_date != NULL) {
                $column_values[self::CORING_DATE] = sql_varchar(date_format($this->_coring_date, 'Y-m-d h:i:s'));
            }
            
            if (is_numeric($this->getStatusId())) {
                $column_values[self::ID_STATUS] = $this->getStatusId();
            } else {
                $insert_errors[] = "Error : id status !";
            }
            
            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_core", $this->getIdValue());
                }
            }

            if (empty($insert_errors)) {
                foreach ($this->_list_core_notes as $note) {
                    $note->setCoreId($this->getIdValue());
                    $insert_errors = array_merge($insert_errors, $note->create());
                }

                //Create all age model
                foreach ($this->_list_age_model as $age_model) {
                    $age_model->_core_id = $this->getIdValue();
                    $insert_errors = array_merge($insert_errors, $age_model->create());
                }

                //Create all samples
                foreach ($this->_list_samples as $sample) {
                    $sample->_sample_core_id = $this->getIdValue();
                    $insert_errors = array_merge($insert_errors, $sample->create());
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
    
    /*
     * Création d'un enregistrement d'une carotte en base de données
     */
    public function save($Operation) {
        $insert_errors = array();
        $obj_exists = ($this->getIdValue() == null)?false:true;
        $column_values = array();
        if ($this->_site != NULL && $this->_site->getIdValue() != null) {
            $column_values[self::ID_SITE] = $this->_site->getIdValue();
        } else {
            $insert_errors[] = "The site associated to the core can't be empty !";
        }

        if ($this->getName() != NULL) {
            $column_values[self::NAME] = sql_varchar($this->getName());
        } else {
            $insert_errors[] = "The core name can't be empty !";
        }

        if ($this->getLatitude() != NULL) {
            $column_values[self::LATITUDE] = $this->getLatitude();
        } else {
            $insert_errors[] = "The core latitude can't be empty !";
        }
        
        if ($this->getLongitude() != NULL) {
            $column_values[self::LONGITUDE] = $this->getLongitude();
        } else {
            $insert_errors[] = "The core longitude can't be empty !";
        } 

        if ($this->_elevation != NULL) {
            $column_values[self::ELEVATION] = $this->_elevation;
        } else {
            $column_values[self::ELEVATION] = 'NULL';
        }
        
        if ($this->_water_depth_value != NULL) {
            $column_values[self::WATER_DEPTH] = $this->_water_depth_value;
        } else {
            $column_values[self::WATER_DEPTH] = 'NULL';
        }

        if ($this->_core_type_id != NULL) {
            $column_values[self::ID_CORE_TYPE] = $this->_core_type_id;
        } else {
            $column_values[self::ID_CORE_TYPE] = 'NULL';
        }
         
        if ($this->_depo_context_id != NULL) {
            $column_values[self::ID_DEPO_CONTEXT] = $this->_depo_context_id;
        } else {
            $column_values[self::ID_DEPO_CONTEXT] = 'NULL';
        }
        
        if (!empty($this->_coring_date) && $this->_coring_date != "0000-00-00") {
            $column_values[self::CORING_DATE] = sql_varchar(date_format($this->_coring_date, 'Y-d-m h:i:s'));
        } else {
            $column_values[self::CORING_DATE] = 'NULL';
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
                    writeChangeLog("edit_core", $this->getIdValue());
                }
            } else {
                // création
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_core", $this->getIdValue());
                }
            }
        }
        
        return $insert_errors;
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CORE :: ERROR TO SELECT ALL INFORMATION ABOUT CORE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_site_id = $values[self::ID_SITE];
            $this->_latitude = $values[self::LATITUDE];
            $this->_longitude = $values[self::LONGITUDE];
            $this->_elevation = $values[self::ELEVATION];
            $this->_water_depth_value = $values[self::WATER_DEPTH];
            $this->_depo_context_id = $values[self::ID_DEPO_CONTEXT];
            $this->_list_age_model = AgeModel::getObjectsPaleofireFromWhere(sql_equal(AgeModel::ID_CORE, $values[self::ID]));
            $this->_list_samples = Sample::getObjectsPaleofireFromWhere(sql_equal(Sample::ID_CORE, $values[self::ID]));
            $this->_core_type_id = $values[self::ID_CORE_TYPE];
            $this->_affiliation_id = $values[self::ID_AFFILIATION];
            $this->_coring_date = $values[self::CORING_DATE];
            $this->setStatusId($values[self::ID_STATUS]);
        }
    }

    public function updateDepotContextId($depo_context_id) {
        queryToExecute("UPDATE " . self::TABLE_NAME . " SET " . self::ID_DEPO_CONTEXT . "=" . $depo_context_id . " WHERE " . self::ID . "=" . $this->getIdValue() . ";");
    }

    /** --------------------------- FUNCTIONS ------------------------- * */
    private function generate_name() {
        if ($this->_site != null) {
            $nbcore = $this->_site->countCore() + 1;
            return $this->_site->getName() . "_core" . $nbcore;
        } else {
            return "unknown_core_name";
        }
    }

    public static function getAverageCoreBySite() {
        $query = "SELECT AVG( cnt.nb_core ) as avg_core
FROM (
SELECT count( `ID_CORE` ) AS nb_core
FROM t_core
JOIN t_site ON t_site.`ID_SITE` = t_core.`ID_SITE`
GROUP BY t_site.`ID_SITE`) cnt ;";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["avg_core"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function getFuncCoreBySite($func) {
        $nb_site = array();
        ($func == "MAX") ? $order_by = "DESC" : $order_by = "ASC";
        $query = "SELECT " . $func . "( cnt.nb_core ) AS result_func, ID_SITE FROM (
            SELECT count( `ID_CORE` ) AS nb_core, t_site.`ID_SITE`
            FROM t_core
            JOIN t_site ON t_site.`ID_SITE` = t_core.`ID_SITE`
            GROUP BY t_site.`ID_SITE`
            ORDER BY nb_core " . $order_by . "
            ) cnt;";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site[1] = $values["result_func"];
            $nb_site[2] = $values["ID_SITE"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }
    
    public static function getAllCoreForMap(){
        $query = "select t_core.id_core as id, site_name as name, t_core.latitude as lat, t_core.longitude as lon, ";
        $query .= " t_core.elevation as el, t_site.id_site as id_site, tr_country.country_name as country, ";
        $query .= " tr_site_type.site_type_desc as type ";
        $query .= " from t_site ";
        $query .= "join t_core on t_site.id_site = t_core.id_site ";
        $query .= "left join tr_country on t_site.id_country = tr_country.id_country ";
        $query .= "left join tr_site_type on t_site.id_site_type = tr_site_type.id_site_type ";
        
        $res = queryToExecute($query);
        $tabRes = null;
        while ($row = fetchAssoc($res)) {
            $tabRes[$row['id']] = array($row['name'], $row['lat'], $row['lon'], $row['el'], $row['id_site'], $row['country'], $row['type']);
        }
        return $tabRes;
    }
    
    public static function getListIDPublishedCore(){
        $idDatabaseVersionInProgress = DataBaseVersion::getVersionInProgress();
        $query = "SELECT DISTINCT t_core.id_core FROM t_core "
                . "join t_sample on t_core.ID_CORE = t_sample.ID_core "
                . "join t_charcoal on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE "
                . "join tr_database on t_charcoal.ID_DATABASE = tr_database.ID_DATABASE "
                . "WHERE t_charcoal.ID_DATABASE <> ".$idDatabaseVersionInProgress." AND t_charcoal.ID_DATABASE is not NULL" ;
        
        $res = queryToExecute($query);
        $tabRes = null;
        while($row = fetchAssoc($res)){
            $tabRes[] = $row["id_core"];
        }
        return $tabRes;
    }
    
    public static function getListIDInProgressCore(){
        $idDatabaseVersionInProgress = DataBaseVersion::getVersionInProgress();
        $query = "SELECT DISTINCT t_core.id_core FROM t_core "
                . "join t_sample on t_core.ID_CORE = t_sample.ID_core "
                . "join t_charcoal on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE "
                . "join tr_database on t_charcoal.ID_DATABASE = tr_database.ID_DATABASE "
                . "WHERE t_charcoal.ID_DATABASE = ".$idDatabaseVersionInProgress;
        
        $res = queryToExecute($query);
        $tabRes = null;
        while($row = fetchAssoc($res)){
            $tabRes[] = $row["id_core"];
        }
        return $tabRes;
    }
    
    
    public static function getCoreForPage($begin = NULL, $end = NULL){
        $tab = null;
        
        $query = "select t_core.id_core as id, core_name, coring_date, elevation, water_depth, nb_samples ";
        $query .= "from t_core ";
        $query .= "left join (select id_core, count(id_sample) as nb_samples ";
        $query .= "from t_sample ";
        $query .= "group by id_core) tabTemp on t_core.id_core = tabTemp.id_core ";
        $query .= "order by t_core.id_core ";

        if (is_numeric($begin) && is_numeric($end)){
            $query .= "limit " . $begin ."," . $end .";";
        } else if (is_numeric($end)){
            $query .= "limit " . $end .";";
        }

        $res = queryToExecute($query);
        if ($res) {
            while ($row = fetchAssoc($res)) {
                $tab[] = $row;
            }
        }
       
        return $tab;
    }

    public static function getCoresWithCharcoalsReferencedByPublication($id_pub){
        $cores_list = [];
        if (is_numeric($id_pub)){
            $requete = "SELECT DISTINCT t_core.* FROM `r_has_pub` 
                join t_charcoal on r_has_pub.ID_CHARCOAL = t_charcoal.ID_CHARCOAL 
                join t_sample on t_charcoal.ID_sample = t_sample.ID_SAMPLE 
                join t_core on t_sample.ID_CORE = t_core.ID_CORE where id_pub = ".$id_pub;
            $res = queryToExecute($requete);
            $rows = fetchAll($res);
            foreach($rows as $values){
                $core = new Core();
                $core->read($values);
                $cores_list[] = $core;
                $core = NULL;
            }
        }
        return $cores_list;
    }
    
    public static function getCoreWithCharcoalsWaitingForValidation(){
        $cores_list = [];
        $requete = "select t_core.id_core as ID_CORE, t_core.CORE_NAME as CORE_NAME, count(ID_CHARCOAL) as nb_charcoals, GROUP_CONCAT(DISTINCT t_charcoal.ID_STATUS, ',') as list_status "
                . "from t_charcoal "
                . "join t_sample on t_charcoal.ID_SAMPLE = t_sample.ID_SAMPLE "
                . "join t_core on t_sample.id_core = t_core.ID_CORE "
                . "where t_charcoal.ID_STATUS <> 1 "
                . "group by t_core.id_core "
                . "order by t_core.id_core DESC";
        $res = queryToExecute($requete);
        $rows = fetchAll($res);
        return $rows;
    }
    
    public static function getCoresWithCharcoalsFromContributor($id_contact){
        // beaucoup de données à gérer, on augmente temporairement la mémoire alouée
        // (ini_set n'est valable que pendant le script) 
        ini_set('memory_limit', '512M');
        $cores_list = [];
        if (is_numeric($id_contact)){
            $requete = "SELECT DISTINCT t_core.* FROM `t_charcoal` "
                    . "join t_sample on t_charcoal.ID_SAMPLE = t_sample.ID_SAMPLE "
                    . "join t_core on t_sample.ID_CORE = t_core.ID_CORE where id_contact = ".$id_contact;
            $res = queryToExecute($requete);
            if ($res != NULL){
                $rows = fetchAll($res);
                foreach($rows as $values){
                    $core = new Core();
                    $core->read($values);
                    $cores_list[] = $core;
                    $core = NULL;
                }
            }
        }
        return $cores_list;
    }
    
    public static function getCoresWithCharcoalsFromAuthor($id_contact){
        $cores_list = [];
        if (is_numeric($id_contact)){
            $requete = "SELECT DISTINCT t_core.* "
                    . "FROM r_has_author "
                    . "join `t_charcoal` on r_has_author.ID_CHARCOAL = t_charcoal.ID_CHARCOAL "
                    . "join t_sample on t_charcoal.ID_SAMPLE = t_sample.ID_SAMPLE "
                    . "join t_core on t_sample.ID_CORE = t_core.ID_CORE where r_has_author.id_contact = ".$id_contact;
            $res = queryToExecute($requete);
            if ($res != NULL){
                $rows = fetchAll($res);
                foreach($rows as $values){
                    $core = new Core();
                    $core->read($values);
                    $cores_list[] = $core;
                    $core = NULL;
                }
            }
        }
        return $cores_list;
    }
    
    
    
    public static function getAllIdNameBySiteField($where_clause) {
        $names = array();
        $query = "SELECT ID_CORE,CORE_NAME FROM t_core NATURAL JOIN t_site WHERE t_site.";
        $query.=$where_clause . ";";
        $res = queryToExecute($query);
        while ($tab = fetchAssoc($res)) {
            $names[$tab[Core::ID]] = $tab[Core::NAME];
        }
        unset($res);
        return $names;
    }

    public static function countDatedSamples($id_core) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_sample.ID_SAMPLE) as nb_samples, t_core.ID_CORE
            FROM t_sample
            INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . "
    AND
t_sample.ID_SAMPLE IN
(
SELECT DISTINCT(t_date_info.ID_SAMPLE)
FROM t_date_info
)
";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["nb_samples"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function countCharcoalSamples($id_core) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_sample.ID_SAMPLE) as nb_samples, t_core.ID_CORE
            FROM t_sample
            INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . "
    AND
t_sample.ID_SAMPLE IN
(
SELECT DISTINCT(t_charcoal.ID_SAMPLE)
FROM t_charcoal
)
";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["nb_samples"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function countCharcoalsWhere($id_core, $where_clause = NULL) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_charcoal.ID_CHARCOAL) as nb_charcoals, t_core.ID_CORE
            FROM t_charcoal
INNER JOIN t_sample on t_charcoal.ID_SAMPLE = t_sample.ID_SAMPLE
            INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . "
";
        if ($where_clause != null) {
            $query.=" AND " . $where_clause;
        }
        $query.=";";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["nb_charcoals"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public function getAllContactIds() {
        $id_core = $this->getIdValue();
        $query = "(SELECT r_has_author.ID_CONTACT from r_has_author
JOIN t_charcoal on t_charcoal.ID_CHARCOAL = r_has_author.ID_CHARCOAL
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . ")
UNION
(SELECT t_charcoal.ID_CONTACT from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . ")";

        $array_contacts = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_contacts[] = $values["ID_CONTACT"];
        }
        freeResult($res_);
        return $array_contacts;
    }

    public function getAllPubliIds() {
        $id_core = $this->getIdValue();
        $query = "SELECT DISTINCT(r_has_pub.ID_PUB) from r_has_pub
JOIN t_charcoal on t_charcoal.ID_CHARCOAL = r_has_pub.ID_CHARCOAL
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . ";";

        $array_publis = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_publis[] = $values["ID_PUB"];
        }
        freeResult($res_);
        return $array_publis;
    }

    public function getAllCharcoalMethods() {
        $id_core = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_CHARCOAL_METHOD`) as IDCHARCOALMETH from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDCHARCOALMETH"];
        }
        freeResult($res_);
        return $array_objects;
    }

    public function getAllCharcoalSizes() {
        $id_core = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_CHARCOAL_SIZE`) as IDCHARCOALSIZE from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDCHARCOALSIZE"];
        }
        freeResult($res_);
        return $array_objects;
    }

    public function getAllCharcoalUnits() {
        $id_core = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_CHARCOAL_UNITS`) as IDCHARCOALUNIT from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE= " . $id_core . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDCHARCOALUNIT"];
        }
        freeResult($res_);
        return $array_objects;
    }

    public function getAllDataSource() {
        $id_core = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_DATA_SOURCE`) as IDDATASOURCE from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
WHERE t_core.ID_CORE = " . $id_core . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDDATASOURCE"];
        }
        freeResult($res_);
        return $array_objects;
    }

    private static $_allIdCoreForOneSite = null;
    public static function getAllIdCoreByIdSite() {
        if (self::$_allIdCoreForOneSite == null){
            $result_get_object = getFieldsFromTables("ID_SITE, ID_CORE, CORE_NAME", CORE::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allIdCoreForOneSite = null;
                foreach($tab_get_values as $row){
                    self::$_allIdCoreForOneSite[$row[CORE::ID_SITE]][] = $row;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allIdCoreForOneSite = NULL;
            }
        }
        return self::$_allIdCoreForOneSite;
    }
    
    public static function getAllCoreBySite(){
        $query = "select t_site.id_site as id_site, site_name, id_core, core_name ";
        $query .= "from t_site ";
        $query .= "join t_core on t_site.id_site = t_core.id_site ";
        $query .= "order by site_name, core_name";
        
        $res = queryToExecute($query);
        $tabRes = null;
        $id_site = null;
        $site_name = null;
        $tabCores = null;
        while ($row = fetchAssoc($res)) {
            if ($id_site == null) { 
                $id_site = $row['id_site'];
                $site_name = $row['site_name'];
            }
            if ($id_site != $row['id_site']){
                $tabRes[$id_site] = array($site_name, $tabCores);
                $tabCores = null;
                $id_site = $row['id_site'];
                $site_name = $row['site_name'];
            }
            $tabCores[$row['id_core']] = $row['core_name'];
        }
        return $tabRes;
    }
    
    public static function coreEtSiteExist($id_core, $id_site){
        $query = "select t_core.* from t_core join t_site on t_core.id_site = t_site.id_site where id_core = ? and t_core.id_site = ? ";
        global $bdd_gcd;
        $stmt = $bdd_gcd->prepare($query);
        $stmt->bind_param("ii", $id_core, $id_site);
        $stmt->execute();
        $result = $stmt->get_result();
        //$result = queryToExecute($query);//xli 20/6 
        if ($result != null) return true;
        else return false;        
    }
}
