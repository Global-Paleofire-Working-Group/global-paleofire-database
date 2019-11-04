<?php
/*
 * fichier \Models\ProxyFireData.php
 *
 */

require_once 'ObjectPaleofire.php';
require_once 'Status.php';
require_once 'DataSource.php';
require_once 'Contact.php';
require_once 'Publi.php';
require_once (REP_MODELS."ProxyFire.php");
require_once (REP_MODELS."ProxyFireDataQuantity.php");
require_once (REP_MODELS."ProxyFireMeasurement.php");
require_once (REP_MODELS."ProxyFireMeasurementUnit.php");
require_once (REP_MODELS."ProxyFireMethodTreatment.php");
require_once (REP_MODELS."ProxyFireMethodEstimation.php");


include_once(REP_PAGES . "EDA/change_Log.php");

/**
 * Class ProxyFireData
 *
 */
class ProxyFireData extends ObjectPaleofire {

    const TABLE_NAME = 't_proxy_fire_data';
    const NAME = '';
    const ID = 'ID_PROXY_FIRE_DATA';
    const ID_PROXY_FIRE = 'ID_PROXY_FIRE';
    const ID_SAMPLE = 'ID_SAMPLE';
    const ID_PROXY_FIRE_MEASUREMENT_UNIT = 'ID_PROXY_FIRE_MEASUREMENT_UNIT';
    const ID_CONTACT = 'ID_CONTACT';
    const ID_LATEST_CONTACT = 'ID_LATEST_CONTACT';
    const ID_PROXY_FIRE_METHOD_TREATMENT = 'ID_PROXY_FIRE_METHOD_TREATMENT';
    const ID_PROXY_FIRE_METHOD_ESTIMATION = 'ID_PROXY_FIRE_METHOD_ESTIMATION';
    const ID_STATUS = 'ID_STATUS';
    const ID_DATA_SOURCE = "ID_DATA_SOURCE";
    const ID_DATABASE = "ID_DATABASE";
    const PROXY_FIRE_PARTICLE_SIZE_MIN = 'PROXY_FIRE_PARTICLE_SIZE_MIN';
    const PROXY_FIRE_PARTICLE_SIZE_MAX = 'PROXY_FIRE_PARTICLE_SIZE_MAX';
    const PROXY_FIRE_SIZE_VALUE = 'PROXY_FIRE_SIZE_VALUE';
    const CREATION_DATE = 'CREATION_DATE';
    const UPDATE_DATE = 'UPDATE_DATE';


    public $_sample_id;
    public $_proxy_fire_data_id;
    public $_proxy_fire_id;
    public $_proxy_fire_measurement_unit_id;
    public $_proxy_fire_method_treatment_id;
    public $_proxy_fire_method_estimation_id;
    public $_status_id;
    public $_proxy_fire_particle_size_max;
    public $_proxy_fire_particle_size_min;
    public $_proxy_fire_size_value;
    public $_list_proxy_fire_data_quantities;
    public $_list_authors;
    public $_list_publications;
    public $_database_id;
    public $_datasource_id;
    public $_contact_id;
    public $_latest_contact_id;
    public $_creation_date;
    public $_update_date;

    protected static $_allObjectsByID = null;


    /**
     * Constructeur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_proxy_fire_data_id = null;
        $this->_proxy_fire_id = null;
        $this->_sample_id = null;
        $this->_proxy_fire_measurement_unid_id = null;
        $this->_proxy_fire_method_treatment_id = null;
        $this->_proxy_fire_method_estimation_id = null;
        $this->_status_id = null;
        $this->_proxy_fire_particle_size_max = null;
        $this->_proxy_fire_particle_size_min = null;
        $this->_proxy_fire_size_value = null;
        $this->_list_proxy_fire_quantities = array();
        $this->_list_authors = array();
        $this->_list_publications = array();
        $this->_database_id = null;
        $this->_datasource_id = null;
        $this->_contact_id = null;
        $this->_latest_contact_id = null;
        $this->_creation_date = null;
        $this->_update_date = null;
    }

    public function getIdValue() {
        if ($this->_id_value == NULL) {
            if (!empty($this->_fields_to_get_id)) {
                $where_clauses = $this->construct_whereclause($this->_fields_to_get_id);
                $id = $this->getDatabaseId($where_clauses);
                $this->_id_value = $id;
            }
        }
        return $this->_id_value;
    }

    public function create(){
        $contact_exists = $this->exists();
        if ($contact_exists) {
            $this->getIdValue();
            $this->read();
        }
        else {
        }
      //TODO a completer
    }

    public function read($values = null){
      //TODO a completer
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function getTableName() {
        return self::TABLE_NAME;
    }

    public function addProxyFireDataQuantity($quantity_value, $unit_id) {
        $this->_list_proxy_fire_data_quantities[] = new ProxyFireDataQuantity($quantity_value, $unit_id);
    }


    public static function getProxyFireFromCore($id_core) {

        $request = "SELECT * from t_sample join t_proxy_fire_data on t_sample.ID_SAMPLE = t_proxy_fire_data.ID_SAMPLE WHERE t_sample.ID_CORE = " . $id_core;
        $result_get_ProxyFire = queryToExecute($request, "get fire proxy from core");
        $allValues = fetchAll($result_get_ProxyFire);
        $tabRes = [];
        if ($allValues != NULL) {
            foreach($allValues as $values){
                $proxy_fire_data = new ProxyFireData();
                $proxy_fire_data->read($values);
                $tabRes[] = $proxy_fire_data;
            }
        }
        return $tabRes;
    }

    public static function getProxyFireFromCoreOrderedByUnits($id_core) {

//        $request = "Select * "
            $request = "Select t_proxy_fire.type as PROXY_FIRE_TYPE , t_sample_depth_list.ID_SAMPLE, SAMPLE_NAME, t_sample_depth_list.ID_CORE, DEPTH_LIST, "
                . "t_proxy_fire_data.ID_PROXY_FIRE_DATA, t_proxy_fire_data.ID_OLD_ID_CHARCOAL, r_has_proxy_fire_data_quantity.ID_PROXY_FIRE_MEASUREMENT_UNIT, QUANTITY, t_proxy_fire_measurement_unit.unit as UNIT_VALUE, t_proxy_fire_measurement_unit.unit_info as UNIT_INFO , AUTHORS_LIST, ID_CONTACT, ID_LATEST_CONTACT,"
                . " ID_PROXY_FIRE_METHOD_TREATMENT, ID_PROXY_FIRE_METHOD_ESTIMATION, ID_STATUS, ID_DATA_SOURCE, ID_DATABASE, PROXY_FIRE_PARTICLE_SIZE_MIN, PROXY_FIRE_PARTICLE_SIZE_MAX, PROXY_FIRE_SIZE_VALUE,"
                . " CREATION_DATE, UPDATE_DATE "
                . "from ("
                    . "Select *, GROUP_CONCAT(depth SEPARATOR \",\") AS DEPTH_LIST "
                    . "FROM ("
                        . "SELECT t_sample.ID_SAMPLE, t_sample.SAMPLE_NAME, t_sample.ID_CORE,"
                        .' CONCAT( "[", t_depth.ID_DEPTH_TYPE, ",", t_depth.DEPTH_VALUE,",",'
                        .' ifnull(r_has_estimated_age.EST_AGE_CAL_BP,"null"),",",'
                        .' ifnull(r_has_estimated_age.EST_AGE_POSITIVE_ERROR,"null"),",",'
                        .' ifnull(r_has_estimated_age.EST_AGE_NEGATIVE_ERROR,"null"),"]"'
                        .") AS depth"
                        ." FROM t_sample"
                        ." JOIN t_depth ON t_sample.ID_SAMPLE = t_depth.ID_RELATED_SAMPLE"
                        ." join r_has_estimated_age ON r_has_estimated_age.ID_Depth = t_depth.ID_DEPTH"
                        ." WHERE t_sample.ID_Core = " . $id_core
                        ." ORDER BY t_depth.DEPTH_VALUE) t_sample_depth "
                        . " group BY ID_SAMPLE"
                        . " order by depth_list"
                        . " ) t_sample_depth_list "
                . " JOIN t_proxy_fire_data ON t_sample_depth_list.ID_SAMPLE = t_proxy_fire_data.ID_SAMPLE "
                . " JOIN t_proxy_fire ON t_proxy_fire.ID_PROXY_FIRE = t_proxy_fire_data.ID_PROXY_FIRE "
                . " JOIN r_has_proxy_fire_data_quantity ON t_proxy_fire_data.ID_PROXY_FIRE_DATA = r_has_proxy_fire_data_quantity.ID_PROXY_FIRE_DATA "
                . " JOIN t_proxy_fire_measurement_unit ON r_has_proxy_fire_data_quantity.ID_PROXY_FIRE_MEASUREMENT_UNIT = t_proxy_fire_measurement_unit.ID_PROXY_FIRE_MEASUREMENT_UNIT "
                    ." left JOIN ( "
                    ." SELECT r_has_proxy_fire_author.ID_PROXY_FIRE_DATA, GROUP_CONCAT(r_has_proxy_fire_author.ID_CONTACT, \",\") AS AUTHORS_LIST"
                    ." FROM r_has_proxy_fire_author"
                    ." GROUP BY r_has_proxy_fire_author.ID_PROXY_FIRE_DATA"
                    ." ) t_contact_list ON t_proxy_fire_data.ID_PROXY_FIRE_DATA = t_contact_list.ID_PROXY_FIRE_DATA"
                . " ORDER BY r_has_proxy_fire_data_quantity.ID_PROXY_FIRE_MEASUREMENT_UNIT" ;
            //var_dump($request);
        $result_get_ProxyFire = queryToExecute($request, "get fire proxy from core");
        $allValues = fetchAll($result_get_ProxyFire);

        return $allValues;
    }

    public static function delAllProxyFireForCore($id_core){
        $ret = true;
        $proxyFires = ProxyFireData::getProxyFireFromCore($id_core);

        if($proxyFires != NULL){
            foreach($proxyFires as $proxyFire){
                $ret = $ret && ProxyFireData::delObjectPaleofireFromId($proxyFire->getIdValue());
            }
        } else { $ret = false; }
        return $ret;
    }


    public function save() {
        $insert_errors = array();
        $column_values = array();

        if ($this->_proxy_fire_id != NULL) {
            $column_values[self::ID_PROXY_FIRE] = $this->_proxy_fire_id;
        }

        if ($this->_proxy_fire_measurement_unit_id != NULL) {
            $column_values[self::ID_PROXY_FIRE_MEASUREMENT_UNIT] = $this->_proxy_fire_measurement_unit_id;
        }

        if ($this->_proxy_fire_method_treatment_id != NULL) {
            $column_values[self::ID_PROXY_FIRE_METHOD_TREATMENT] = $this->_proxy_fire_method_treatment_id;
        }

        if ($this->_proxy_fire_method_estimation_id != NULL) {
            $column_values[self::ID_PROXY_FIRE_METHOD_ESTIMATION] = $this->_proxy_fire_method_estimation_id;
        }

        if ($this->_status_id != NULL) {
            $column_values[self::ID_STATUS] = $this->_status_id;
        }

        if ($this->_proxy_fire_particle_size_min != NULL) {
            $column_values[self::PROXY_FIRE_PARTICLE_SIZE_MIN] = $this->_proxy_fire_particle_size_min;
        }

        if ($this->_proxy_fire_particle_size_max != NULL) {
            $column_values[self::PROXY_FIRE_PARTICLE_SIZE_MAX] = $this->_proxy_fire_particle_size_max;
        }

        if ($this->_proxy_fire_size_value != NULL) {
            $column_values[self::PROXY_FIRE_SIZE_VALUE] = "'" . $this->_proxy_fire_size_value . "'";
        }

        if ($this->_sample_id != NULL) {
            $column_values[self::ID_SAMPLE] = $this->_sample_id;
        }

        if ($this->_datasource_id != NULL) {
            $column_values[self::ID_DATA_SOURCE] = $this->_datasource_id;
        }

        if ($this->_database_id != NULL) {
            $column_values[self::ID_DATABASE] = $this->_database_id;
        }

        // CONTACTS
        connectionBaseWebapp();
        $contact_id = WebAppUserGCD::getContactId($_SESSION['gcd_user_id']);
        connectionBaseInProgress();
        // charcoal does not exist yet, adding contributor (contact who create the charcoal)
        if ($this->getIdValue() == NULL) {
            $column_values[self::ID_CONTACT] = $contact_id;
        }
        $column_values[self::ID_LATEST_CONTACT] = $contact_id;

        // DATES
        // charcoal does not exist yet, adding creation date
        date_default_timezone_set('UTC');
        if ($this->getIdValue() == NULL) {
            $column_values[self::CREATION_DATE] = '"'.date('Y-m-d H:i:s').'"';
        }
        // adding modification date
        $column_values[self::UPDATE_DATE] = '"'.date('Y-m-d H:i:s').'"';

        if (empty($insert_errors)) {
            if ($this->getIdValue() == NULL){
                // le charcoal n'existe pas on le crée

                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);

                if (!$result_insert) {
                    $insert_errors[] = "Insert into table proxy fire" ;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            } else { // le charcoal existe, mise à jour

                $result = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, " id_charcoal = ".$this->getIdValue());
                if (!$result){
                    $insert_errors[] = "Update into table proxy fire failed. ID : ".$this->getIdValue();
                }
            }
        }

        if (empty($insert_errors)) {
            // on supprime déjà les lignes existante puis on créé les nouveaux enregistrements
            ProxyFireDataQuantity::delObjectPaleofireFromId($this->getIdValue());
            foreach ($this->_list_proxy_fire_data_quantities as $proxy_fire_data_quantity) {
                $proxy_fire_data_quantity->_proxy_fire_data_id = $this->getIdValue();
                $insert_errors = $proxy_fire_data_quantity->create();
            }
        }

        if (empty($insert_errors)) {
            // on supprime déjà les enregistrements en base préexistant
            $result_delete = deleteIntoTableFromId("r_has_proxy_fire_author", "ID_PROXY_FIRE_DATA", $this->getIdValue());
            if (!$result_delete) {
                $insert_errors[] = "Delete from table r_has_proxy_fire_author, fire proxy id : " + $this->getIdValue();
            }
            if ($this->_list_authors !=NULL) {
                foreach ($this->_list_authors as $author) {
                    // on enregistre les nouvelles données
                    $result_insert = insertIntoTable("r_has_proxy_fire_author", array("ID_CONTACT" => $author, self::ID => $this->getIdValue()));
                    if (!$result_insert) {
                        $insert_errors[] = "Insert into table ".Contact::TABLE_NAME;
                    }
                }
            }
        }
        //
        if (empty($insert_errors)) {
            // on supprime déjà les enregistrements en base préexistant
            $result_delete = deleteIntoTableFromId("r_has_proxy_fire_pub", "ID_PROXY_FIRE_DATA", $this->getIdValue());
            if (!$result_delete) {
                $insert_errors[] = "Delete from table r_has_proxy_fire_pub, fire proxy id : ".$this->getIdValue();
            }
            if ($this->_list_publications !=NULL) {
                foreach ($this->_list_publications as $publi) {
                    $result_insert = insertIntoTable("r_has_proxy_fire_pub", array("ID_PUB" => $publi, self::ID => $this->getIdValue()));
                    if (!$result_insert) {
                        $insert_errors[] = "Insert into table r_has_proxy_fire_pub";
                    }
                }
            }
        }
        return $insert_errors;
    }


}
