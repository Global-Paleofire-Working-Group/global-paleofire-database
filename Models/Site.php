<?php
/*
 * fichier \Models\Site.php
 *
 */

include_once("ObjectPaleofire.php");
include_once("Region.php");
include_once("Country.php");
include_once("CharcoalUnits.php");
require_once 'LandsDesc.php';
require_once 'BiomeType.php';
require_once 'SiteType.php';
require_once 'BasinSize.php';
require_once 'CatchSize.php';
require_once 'LocalVeg.php';
require_once 'RegionalVeg.php';
require_once 'FlowType.php';
require_once 'NoteCore.php';
require_once 'Core.php';
require_once 'Status.php';
include_once(REP_PAGES . "EDA/change_Log.php");

//require_once 'data_securisation.php';

/**
 * Class Site
 *
 */
class Site extends ObjectPaleofire {

    const TABLE_NAME = 't_site';
    const TABLE_TEMP_NAME = 'tt_site_import';
    const ID_SITE_TEMP = 'ID_SITE_TEMP';
    const ID = 'ID_SITE';
    const NAME = 'SITE_NAME';
    const GCD_ACCESS_ID = "GCD_ACCESS_ID";
    const ID_REGION = "ID_MARINE_REGION";
    const ID_COUNTRY = "ID_COUNTRY";
    const ID_LANDS_DESC = "ID_LANDS_DESC";
    const ID_BIOME_TYPE = "ID_BIOME_TYPE";
    const ID_SITE_TYPE = "ID_SITE_TYPE";
    const ID_BASIN_SIZE = "ID_BASIN_SIZE";
    const ID_FLOW_TYPE = "ID_FLOW_TYPE";
    const ID_CATCH_SIZE = "ID_CATCH_SIZE";
    const ID_REGIONAL_VEG = "ID_REGIONAL_VEG";
    const ID_LOCAL_VEG = "ID_LOCAL_VEG";
    const BASIN_SIZE_VALUE = "BASIN_SIZE_VALUE";
    const CATCH_SIZE_VALUE = "CATCH_SIZE_VALUE";
    const ID_STATUS = "ID_STATUS";

    const ID_IMPORT = "GCD_ACCESS_ID";

    private $_old_gcd_access_id;
    public $_site_country;
    public $_site_region_id;
    public $_site_land_id;
    public $_biome_type_id;
    public $_site_type_id;
    public $_basin_size_id;
    public $_basin_size_value;
    public $_catch_size_id;
    public $_catch_size_value;
    public $_site_id_status;

    public $_flow_type_id;
    private $_list_site_notes;
    private $_list_core;
    public $_liste_publi_id;

    public $_local_veg_id;
    public $_regional_veg_id;


    /** FIELDS USED ONLY FOR IMPORT PROCESS * */
    private $_temp_status_import_id;
    public $_temp_latitude_value;
    public $_temp_longitude_value;
    private $_temp_elevation_value;
    private $_temp_charcoal_pref_unit_id;
    private $_temp_water_depth_value;
    private $_temp_age_model_method_id;
    private $_temp_data_source_id;
    private $_temp_database_version_id;
    public $_temps_age_units_id;
    public static $_allSitesByCGD_ACCESS_ID = null;
    public static $_allTempSitesByID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_old_gcd_access_id = null;
        $this->_site_country = null;
        $this->_site_region_id = null;
        $this->_site_land_id = null;
        $this->_biome_type_id = null;
        $this->_site_type_id = null;
        $this->_basin_size_id = null;
        $this->_basin_size_value = null;
        $this->_catch_size_id = null;
        $this->_catch_size_value = null;
        $this->_local_veg_id = null;
        $this->_regional_veg_id = null;
        $this->_flow_type_id = null;
        $this->_site_id_status = 0 ;

        $this->_list_site_notes = array();
        $this->_liste_publi_id = array(); //xli initialisation des publications.

        $this->_temp_status_import_id = null;
        $this->_temp_latitude_value = null;
        $this->_temp_longitude_value = null;
        $this->_temp_elevation_value = null;
        $this->_temp_charcoal_pref_unit_id = null;
        $this->_temp_water_depth_value = null;
        $this->_temp_age_model_method_id = null;
        $this->_temp_data_source_id = null;
        $this->_temp_database_version_id = null;
        $this->_temps_age_units_id = null;
        $this->_list_core = array();
    }

    /**     * ********************** SETTERS************************************* * */
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setGCDAccessId($old_gcd_access_id) {
        $this->_old_gcd_access_id = $old_gcd_access_id;
        $this->addOrUpdateFieldToGetId(self::GCD_ACCESS_ID, $old_gcd_access_id);
    }

    public function setSiteCountry($country) {
        if ($country instanceof Country) {
            $this->_site_country = $country;
        } else {
            if (is_numeric($country)) {
                $this->_site_country = Country::getObjectPaleofireFromId($country);
            } else {
                $this->_site_country = $country;
            }
        }
    }

    public function setSiteRegionalVeg(RegionalVeg $reg_veg) {
        $this->_regional_veg_id = $reg_veg;
    }

    public function setStatusId($status_id) {
        $this->_site_id_status = $status_id;
    }

    public function addNote(NoteCore $note) {
        $this->_list_site_notes[] = $note;
    }

///TEMP

    public function setTempImportStatus($status_id) {
        $this->_temp_status_import_id = $status_id;
    }

    public function setTempLatitudeValue($lat_value) {
        $this->_temp_latitude_value = $lat_value;
    }

    public function setTempLongitudeValue($long_value) {
        $this->_temp_longitude_value = $long_value;
    }

    public function setTempElevationValue($elev_value) {
        $this->_temp_elevation_value = $elev_value;
    }

    public function setTempCharcoalPrefUnit($charcoal_pref_unit_id) {
        $this->_temp_charcoal_pref_unit_id = $charcoal_pref_unit_id;
    }

    public function setTempWaterDepthValue($water_depth_value) {
        $this->_temp_water_depth_value = $water_depth_value;
    }

    public function setTempAgeModelMethodId($age_model_method_type_id) {
        $this->_temp_age_model_method_id = $age_model_method_type_id;
    }

    public function setTempDataSourceId($data_source_id) {
        $this->_temp_data_source_id = $data_source_id;
    }

    public function setTempDataBaseVersionId($database_version_id) {
        $this->_temp_database_version_id = $database_version_id;
    }

    public function addCore(Core $core) {
        $this->_list_core[] = $core;
    }

    /**     * ********************** GETTERS ************************************* * */
    public function getGCDAccessId() {
        return $this->_old_gcd_access_id;
    }

    public function getSiteCountry() {
        if ($this->_site_country == NULL) {
            $this->_site_country = Country::getObjectPaleofireFromWhere(sql_equal(Country::ID, Site::getFieldValueFromWhere(Site::ID_COUNTRY, sql_equal(Site::ID, $this->getIdValue()))));
        }
        return $this->_site_country;
    }

    public function getRegionalVeg() {
        return $this->_regional_veg_id;
    }

    public function getAllCores() {
        return $this->_list_core;

    }


     public function getStatusId() {
        return $this->_site_id_status;
    }

    /*
     * get Site
     *
     * SELECT * FROM t_site
left join tr_site_type on tr_site_type.id_site_type = t_site.id_site_type
left join tr_biome_type on tr_biome_type.id_biome_type = t_site.id_biome_type
left join tr_country on tr_country.id_country = t_site.id_country
left join tr_basin_size on tr_basin_size.id_basin_size = t_site.id_basin_size
left join tr_flow_type on tr_flow_type.id_flow_type = t_site.id_flow_type
left join tr_catch_size on tr_catch_size.id_catch_size = t_site.id_catch_size
left join tr_lands_desc on tr_lands_desc.id_lands_desc = t_site.id_lands_desc
left join tr_regional_veg on tr_regional_veg.id_regional_veg = t_site.id_regional_veg
left join tr_local_veg on tr_local_veg.id_local_veg = t_site.id_local_veg
WHERE id_site = 1
     *
     */
    /**     * **************** GETTERS TEMP (IMPORT) ********************* * */
    /**
     * @return null|\object_paleofire
     */
    public static function getAllSitesByGCD_ACCESS_ID() {
        if (self::$_allSitesByCGD_ACCESS_ID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, SITE::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                //$class_paleo = get_called_class();
                $tab_get_values = fetchAll($result_get_object);
                self::$_allSitesByCGD_ACCESS_ID = null;
                foreach($tab_get_values as $row){
                    // pour l'import de données on récupère les site par le GCD_ACCESS_ID
                    //$object_paleo = new $class_paleo();
                    //$object_paleo->read($row);
                    self::$_allSitesByCGD_ACCESS_ID[$row[Site::GCD_ACCESS_ID]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allSitesByCGD_ACCESS_ID = NULL;
            }
        }
        return self::$_allSitesByCGD_ACCESS_ID;
    }

    public static function getAllSitesWithCountry() {
        $tab = [];
        $query = "select t_site.ID_SITE, tr_country.COUNTRY_ISO_ALPHA2 "
                . "from t_site "
                . "join tr_country on t_site.id_country = tr_country.id_country";
        $result = queryToExecute($query);
        if (getNumRows($result) > 0) {
            $tab_get_values = fetchAll($result);
            foreach($tab_get_values as $row){
                $tab[$row[Site::ID]] = $row[Country::COUNTRY_ISO_ALPHA2];
            }
        }
        return $tab;
    }

    public static function getAllTempSitesByID() {
        if (self::$_allTempSitesByID == null){
            $result_get_object = getFieldsFromTables(" ID_SITE_TEMP, STATUS_TEMP_ID, CHARCOAL_PREF_UNIT_TEMP_ID, DATA_SOURCE_ID_TEMP, DATABASE_VERSION_ID ",
                    SITE::TABLE_TEMP_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allTempSitesByID = null;
                foreach($tab_get_values as $row){
                    self::$_allTempSitesByID[$row[Site::ID_SITE_TEMP]] = $row;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allTempSitesByID = NULL;
            }
        }
        return self::$_allTempSitesByID;
    }

    public static function gcdIdExists($id) {
        $query = "select id_site from t_site where " . SITE::GCD_ACCESS_ID . " = ".$id;
        $result = queryToExecute($query);

        if ($result != NULL && mysql_num_rows($result) == 1) {
            return true;
        }
        else {
            return false;
        }
    }

    public function getTempLatitude() {

        if ($this->_temp_latitude_value != NULL) {
            return $this->_temp_latitude_value;
        } else {
            $result = getFieldsFromTables("LATITUDE_TEMP", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["LATITUDE_TEMP"];
            }
        }
    }

    public function getTempLongitude() {
        if ($this->_temp_longitude_value != NULL) {
            return $this->_temp_longitude_value;
        } else {
            $result = getFieldsFromTables("LONGITUDE_TEMP", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["LONGITUDE_TEMP"];
            }
        }
    }

    public function getTempElevation() {
        if ($this->_temp_elevation_value != NULL) {
            return $this->_temp_elevation_value;
        } else {
            $result = getFieldsFromTables("ELEVATION_TEMP", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["ELEVATION_TEMP"];
            }
        }
    }

    public function getTempWaterDepthValue() {
        if ($this->_temp_water_depth_value != NULL) {
            return $this->_temp_water_depth_value;
        } else {
            $result = getFieldsFromTables("WATER_DEPTH_TEMP", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["WATER_DEPTH_TEMP"];
            }
        }
    }

    public function getTempStatusId() {
        if ($this->_temp_status_import_id != NULL) {
            return $this->_temp_status_import_id;
        } else {
            $result = getFieldsFromTables("STATUS_TEMP_ID", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["STATUS_TEMP_ID"];
            }
        }
    }

    public function getTempCharcoalPrefUnitId() {
        if ($this->_temp_charcoal_pref_unit_id != NULL) {
            return $this->_temp_charcoal_pref_unit_id;
        } else {
            $result = getFieldsFromTables("CHARCOAL_PREF_UNIT_TEMP_ID", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["CHARCOAL_PREF_UNIT_TEMP_ID"];
            }
        }
    }

    public function getTempDataSourceId() {
        if ($this->_temp_data_source_id != NULL) {
            return $this->_temp_data_source_id;
        } else {
            $result = getFieldsFromTables("DATA_SOURCE_ID_TEMP", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["DATA_SOURCE_ID_TEMP"];
            }
        }
    }

    public function getTempDataBaseVersionId() {
        if ($this->_temp_database_version_id != NULL) {
            return $this->_temp_database_version_id;
        } else {
            $result = getFieldsFromTables("DATABASE_VERSION_ID", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["DATABASE_VERSION_ID"];
            }
        }
    }

    public function getTempAgeModelMethodId() {
        if ($this->_temp_age_model_method_id != NULL) {
            return $this->_temp_age_model_method_id;
        } else {
            $result = getFieldsFromTables("AGE_MODEL_METH_ID_TEMP", "tt_site_import", sql_equal("ID_SITE_TEMP", $this->getIdValue()));
            if (!$result) {
                return NULL;
            } else {
                $values = fetchAssoc($result);
                freeResult($result);
                return $values["AGE_MODEL_METH_ID_TEMP"];
            }
        }
    }

    /**
     */
    public function getPublications($id_site)
            //xli valable uniquement pour la base de données Access
            {
        $links_with_pub = array();
        $sql = "SELECT ID_PUB FROM SITE_PUB WHERE ID_SITE = " . ($id_site);
        $rs = queryToExecute($sql, "get all links with a publication");
        while (fetchRow($rs)) {
            $id_pub_temp = getValueInResult($rs, "ID_PUB");
            if (isset($id_pub_temp) && $id_pub_temp != "" && $id_pub_temp != NULL) {
                $links_with_pub[] = $id_pub_temp;
            }
        }

        $publications = array();
        foreach ($links_with_pub as $id_publi) {
            $sql = "SELECT PubAbbrev FROM PUB WHERE ID_PUB = " . ($id_publi);
            $rs = queryToExecute($sql, "get all publications");
            while (fetchRow($rs)) {
                $pub_abbrev = getValueInResult($rs, "PubAbbrev");
                if (isset($pub_abbrev) && $pub_abbrev != "" && $pub_abbrev != NULL) {
                    $publications[] = $pub_abbrev;
                }
            }
        }

        return $publications;
    }

    public function getListeCoreEtCumulsProxyFire() {
        $sql = "select * from
            (select * from t_core where id_site = ".$this->getIdValue().")tabCore
            left join
            (
                select t_core.id_core, count(ID_PROXY_FIRE_DATA) as nb_charcoals from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_proxy_fire_data on t_sample.id_sample = t_proxy_fire_data.id_sample
                group by t_core.id_core
            )tabCharcoals on tabCore.id_core = tabCharcoals.id_core
            left join
            (
                select t_core.id_core, count(id_date_info) as nb_date_info from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_date_info on t_sample.id_sample = t_date_info.id_sample
                group by t_core.id_core
            )tabDateInfo on tabCore.id_core = tabDateInfo.id_core
            left JOIN
            (
				        select t_core.id_core, GROUP_CONCAT(DISTINCT t_proxy_fire_data.ID_PROXY_FIRE_METHOD_TREATMENT SEPARATOR ',') as list_methods_treatment from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_proxy_fire_data on t_sample.id_sample = t_proxy_fire_data.id_sample
                join t_proxy_fire_method_treatment on t_proxy_fire_data.ID_PROXY_FIRE_METHOD_TREATMENT = t_proxy_fire_method_treatment.ID_PROXY_FIRE_METHOD_TREATMENT
                group by t_core.id_core
			      )tabMethodTreatment on tabCore.id_core = tabMethodTreatment.id_core
            left JOIN
            (
				        select t_core.id_core, GROUP_CONCAT(DISTINCT t_proxy_fire_data.ID_PROXY_FIRE_METHOD_ESTIMATION SEPARATOR ',') as list_methods_estimation from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_proxy_fire_data on t_sample.id_sample = t_proxy_fire_data.id_sample
                join t_proxy_fire_method_estimation on t_proxy_fire_data.ID_PROXY_FIRE_METHOD_ESTIMATION = t_proxy_fire_method_estimation.ID_PROXY_FIRE_METHOD_ESTIMATION
                group by t_core.id_core
			      )tabMethodEstimation on tabCore.id_core = tabMethodEstimation.id_core
            left JOIN
            (
                select t_core.id_core, GROUP_CONCAT(DISTINCT t_proxy_fire_data.ID_PROXY_FIRE_MEASUREMENT_UNIT SEPARATOR ',') as list_units from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_proxy_fire_data on t_sample.id_sample = t_proxy_fire_data.id_sample
                join t_proxy_fire_measurement_unit on t_proxy_fire_data.ID_PROXY_FIRE_MEASUREMENT_UNIT = t_proxy_fire_measurement_unit.ID_PROXY_FIRE_MEASUREMENT_UNIT
                group by t_core.id_core
            )tabUnits on tabCore.id_core = tabUnits.id_core";

        $result = queryToExecute($sql);
        $array = [];
        foreach(fetchAll($result) as $row){
            $array[$row[Core::ID]] = $row;
        }
        return $array;

    }

    public function getListeCoreEtCumuls() {
        $sql = "select * from
            (select * from t_core where id_site = ".$this->getIdValue().")tabCore
            left join
            (
                select t_core.id_core, count(id_charcoal) as nb_charcoals from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_charcoal on t_sample.id_sample = t_charcoal.id_sample
                group by t_core.id_core
            )tabCharcoals on tabCore.id_core = tabCharcoals.id_core
            left join
            (
                select t_core.id_core, count(id_date_info) as nb_date_info from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_date_info on t_sample.id_sample = t_date_info.id_sample
                group by t_core.id_core
            )tabDateInfo on tabCore.id_core = tabDateInfo.id_core
            left JOIN
            (
				select t_core.id_core, GROUP_CONCAT(DISTINCT t_charcoal.id_charcoal_method SEPARATOR ',') as list_methods from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_charcoal on t_sample.id_sample = t_charcoal.id_sample
                join tr_charcoal_method on t_charcoal.ID_CHARCOAL_METHOD = tr_charcoal_method.ID_CHARCOAL_METHOD
                group by t_core.id_core
			)tabMethod on tabCore.id_core = tabMethod.id_core
            left JOIN
            (
                select t_core.id_core, GROUP_CONCAT(DISTINCT t_charcoal.ID_CHARCOAL_UNITS SEPARATOR ',') as list_units from t_core
                join t_sample on t_core.id_core = t_sample.id_core
                join t_charcoal on t_sample.id_sample = t_charcoal.id_sample
                join tr_charcoal_units on t_charcoal.ID_CHARCOAL_UNITS = tr_charcoal_units.ID_CHARCOAL_UNITS
                group by t_core.id_core
            )tabUnits on tabCore.id_core = tabUnits.id_core";

        $result = queryToExecute($sql);
        $array = [];
        foreach(fetchAll($result) as $row){
            $array[$row[Core::ID]] = $row;
        }
        return $array;

    }

    public function countCore() {
        if (empty($this->_list_core)) {
            return Core::countPaleofireObjects(sql_equal(Core::ID_SITE, $this->getIdValue()));
        }
        return count($this->_list_core);
    }

    /**     * ********************** CRUD ************************************* * */
    public function create() {
        $insert_errors = array();
        $site_exists = $this->exists();
        if (!$site_exists) {
//BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();


            if ($this->getGCDAccessId() != NULL) {
                $column_values[self::GCD_ACCESS_ID] = $this->getGCDAccessId();
            }
            if ($this->getName() != NULL && $this->getName() != "") {
                //$all_same_site = Site::getAllIds(null, null, sql_equal(Site::NAME, $this->getName()));
                //if (empty($all_same_site)) {
                    $column_values[self::NAME] = utf8_decode(sql_varchar($this->getName()));
                //} else {
                //    $column_values[self::NAME] = sql_varchar($this->getName() . "_B");
                //}
            } else {
                $insert_errors[] = "The name of the site can't be empty !";
            }

            if ($this->_site_type_id != NULL) {
                $column_values[self::ID_SITE_TYPE] = $this->_site_type_id;
            }
            if ($this->_biome_type_id != NULL) {
                $column_values[self::ID_BIOME_TYPE] = $this->_biome_type_id;
            }
            if ($this->_site_region_id != NULL) {
                $column_values[self::ID_REGION] = $this->_site_region_id;
            }
            if ($this->getSiteCountry() != NULL) {
                $column_values[self::ID_COUNTRY] = $this->getSiteCountry()->getIdValue();
            }
            if ($this->_basin_size_id != NULL) {
                $column_values[self::ID_BASIN_SIZE] = $this->_basin_size_id;
            }
            if ($this->_basin_size_value != NULL) {
                $column_values[self::BASIN_SIZE_VALUE] = intval($this->_basin_size_value);
            }
            if ($this->_flow_type_id != NULL) {
                $column_values[self::ID_FLOW_TYPE] = $this->_flow_type_id;
            }
            if ($this->_catch_size_id != NULL) {
                $column_values[self::ID_CATCH_SIZE] = $this->_catch_size_id;
            }
            if ($this->_catch_size_value != NULL) {
                $column_values[self::CATCH_SIZE_VALUE] = intval($this->_catch_size_value);
            }
            if ($this->_site_land_id != NULL) {
                $column_values[self::ID_LANDS_DESC] = $this->_site_land_id;
            }
            if ($this->getRegionalVeg() != NULL) {
                $column_values[self::ID_REGIONAL_VEG] = $this->getRegionalVeg()->getIdValue();
            }
            if ($this->_local_veg_id != NULL) {
                $column_values[self::ID_LOCAL_VEG] = $this->_local_veg_id;
            }
            if (is_numeric($this->getStatusId())) {
                 $column_values[self::ID_STATUS] = $this->getStatusId();
            }

            if (empty($insert_errors)) {

                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    print_r("id=". $this->getIdValue());
                    writeChangeLog("add_site", $this->getIdValue());
                }
            }

            if (empty($insert_errors)) {
//Create all age model
                foreach ($this->getAllCores() as $core) {
                    $core->setSite($this);
                    $core->_list_core_notes = $this->_list_site_notes;
                    $insert_errors = array_merge($insert_errors, $core->create());
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

    /**     * ********************** CRUD ************************************* * */
    public function save($Operation) {
        $insert_errors = array();
        $site_exists = ($this->getIdValue() == null)?false:true;

//BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();

        if ($this->getGCDAccessId() != NULL) {
            $column_values[self::GCD_ACCESS_ID] = $this->getGCDAccessId();
        }
        if ($this->getName() != NULL && $this->getName() != "") {
            //$all_same_site = Site::getAllIds(null, null, sql_equal(Site::NAME, $this->getName()));
            //if (empty($all_same_site)) {
                $column_values[self::NAME] = utf8_decode(sql_varchar($this->getName()));
            //} else {
            //    $column_values[self::NAME] = sql_varchar($this->getName() . "_B");
            //}
        } else {
            $insert_errors[] = "The name of the site can't be empty !";
        }



        if ($this->_site_type_id != NULL) {
            $column_values[self::ID_SITE_TYPE] = $this->_site_type_id;
        } else {
            $column_values[self::ID_SITE_TYPE] = 'NULL';
        }

        if ($this->_biome_type_id != NULL) {
            $column_values[self::ID_BIOME_TYPE] = $this->_biome_type_id;
        }
        /*else if ($Operation!="Add")
            {$column_values[self::ID_BIOME_TYPE] = NULL;}*/

        if ($this->_site_region_id != NULL) {
            $column_values[self::ID_REGION] = $this->_site_region_id;
        }
        /*else if ($Operation!="Add")
            {$column_values[self::ID_REGION] = NULL;}*/


        if ($this->getSiteCountry() != NULL) {
            $column_values[self::ID_COUNTRY] = $this->getSiteCountry()->getIdValue();
        }
        /*else if ($Operation!="Add")
            {$column_values[self::ID_COUNTRY] = NULL;}*/

        if ($this->_basin_size_id != NULL) {
            $column_values[self::ID_BASIN_SIZE] = $this->_basin_size_id;
        }
        /*else if ($Operation!="Add")
            {$column_values[self::ID_BASIN_SIZE] = NULL;}*/


        if ($this->_basin_size_value != NULL) {
            $column_values[self::BASIN_SIZE_VALUE] = intval($this->_basin_size_value);
        } else {
            $column_values[self::BASIN_SIZE_VALUE] = 'NULL';
        }


        if ($this->_flow_type_id != NULL) {
            $column_values[self::ID_FLOW_TYPE] = $this->_flow_type_id;
        }
        /*else if ($Operation!="Add")
            {$column_values[self::ID_FLOW_TYPE] = NULL;}*/


        if ($this->_catch_size_id != NULL) {
            $column_values[self::ID_CATCH_SIZE] = $this->_catch_size_id;
        }
        /*else if ($Operation!="Add")
            {$column_values[self::ID_CATCH_SIZE] = NULL;}*/


        if ($this->_catch_size_value != NULL) {
            $column_values[self::CATCH_SIZE_VALUE] = intval($this->_catch_size_value);
        } else {
            $column_values[self::CATCH_SIZE_VALUE] = 'NULL';
        }

        if ($this->_site_land_id != NULL) {
            $column_values[self::ID_LANDS_DESC] = $this->_site_land_id;
        } else {
            $column_values[self::ID_LANDS_DESC] = 'NULL';
        }

        if ($this->_regional_veg_id != NULL) {
            $column_values[self::ID_REGIONAL_VEG] = $this->_regional_veg_id;
        } else {
            $column_values[self::ID_REGIONAL_VEG] = 'NULL';
        }

        if ($this->_local_veg_id != NULL) {
            $column_values[self::ID_LOCAL_VEG] = $this->_local_veg_id;
        } else {
            $column_values[self::ID_LOCAL_VEG] = 'NULL';
        }

        if (is_numeric($this->getStatusId())) {
                $column_values[self::ID_STATUS] = $this->getStatusId();
        } else {
            $insert_errors[] = "Error : id status !";
        }

        if (empty($insert_errors)) {
            if ($site_exists) {
                //mise à jour

                $result_insert = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID ."=".$this->getIdValue());
                if (!$result_insert) {
                    $insert_errors[] = "Update table " . self::TABLE_NAME;
                }
                else {
                    writeChangeLog("edit_site", $this->getIdValue());
                }
            } else {
                // création d'un enregistrement
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    //print_r(" add_site=", $this->getIdValue());
                    writeChangeLog("add_site", $this->getIdValue());
                }
            }

        }

                if (empty($insert_errors)) {
            // on supprime les enregistrement de la table r_site_is_referenced
            foreach ($this->_liste_publi_id as $publi_id) {
                $result_delete = deleteIntoTableFromId("r_site_is_referenced", "ID_PUB", $publi_id);
                if (!$result_delete) $insert_errors[] = "Delete into table r_site_is_referenced";
            }
        }

        if(empty($insert_errors)){
            // puis on insére les nouveaux enregistrements de la table r_site_is_referenced

            foreach ($this->_liste_publi_id as $publi_id) {
                   $result_insert = insertIntoTable("r_site_is_referenced", array("ID_PUB" => $publi_id, "ID_SITE" => $this->getIdValue()));
                if (!$result_insert) $insert_errors[] = "Insert into table r_site_is_referenced";
            }
        }
		// CBO // TODO
		//var_dump($column_values);
		//var_dump($insert_errors);

        //If no error, commit transaction !
        if (empty($insert_errors)) {
            commit();
        } else {
            rollBack();
        }

        return $insert_errors;
    }

    public function createTemporaryData() {
        $column_values = array();


        $column_values["ID_SITE_TEMP"] = $this->getIdValue();

        if ($this->getTempStatusId() != NULL) {
            $column_values["STATUS_TEMP_ID"] = $this->getTempStatusId();
        }
        if ($this->getTempCharcoalPrefUnitId() != NULL) {
            $column_values["CHARCOAL_PREF_UNIT_TEMP_ID"] = $this->getTempCharcoalPrefUnitId();
        }
        if ($this->getTempDataBaseVersionId() != NULL) {
            $column_values["DATABASE_VERSION_ID"] = $this->getTempDataBaseVersionId();
        }
        if ($this->getTempDataSourceId() != NULL) {
            $column_values["DATA_SOURCE_ID_TEMP"] = $this->getTempDataSourceId();
        }
        if ($this->_temps_age_units_id != NULL) {
            $column_values["DATING_TYPE_ID_TEMP"] = $this->_temps_age_units_id;
        }


        insertIntoTable("tt_site_import", $column_values);
    }

    public function read($values = null) {
//Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
//If the request return nothing
            throw new Exception("SITE :: ERROR TO SELECT ALL INFORMATION ABOUT SITE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setGCDAccessId($values[self::GCD_ACCESS_ID]);
            $this->_site_type_id = $values[self::ID_SITE_TYPE];
            $this->_site_region_id = $values[self::ID_REGION];
            $this->setSiteCountry(Country::getObjectPaleofireFromId($values[self::ID_COUNTRY]));
            $this->_site_land_id = $values[self::ID_LANDS_DESC];
            $this->_list_core = Core::getObjectsPaleofireFromWhere(sql_equal(Core::ID_SITE, $values[self::ID]));

            $this->_biome_type_id = $values[self::ID_BIOME_TYPE];
            $this->_basin_size_id = $values[self::ID_BASIN_SIZE];
            $this->_basin_size_value = $values[self::BASIN_SIZE_VALUE];
            $this->_catch_size_id = $values[self::ID_CATCH_SIZE];
            $this->_catch_size_value = $values[self::CATCH_SIZE_VALUE];
            $this->_flow_type_id = $values[self::ID_FLOW_TYPE];
            $this->_local_veg_id = $values[self::ID_LOCAL_VEG];
            $this->_regional_veg_id = $values[self::ID_REGIONAL_VEG];
            $this->setStatusId($values[self::ID_STATUS]);
        }
    }

    /**     * ********************** STATICS ************************************* * */
    public static function getMandatoryFields() {
        $array_mandatory_fields = array(self::NAME, "LATITUDE", "LONGITUDE", "ELEV", "PREF_UNITS", "HAVE_PUB", /* "CHRON_SOURCE", */ "AGE_MODEL");
        return $array_mandatory_fields;
    }

    public static function countSites($field_name = NULL, $value = NULL) {
        $nb_site = 0;

        if ($field_name != NULL && $value != NULL) {
            $res_get_nb_site = queryToExecute("SELECT COUNT(" . self::ID . ") as NBSITES FROM " . self::TABLE_NAME . " WHERE " . $field_name . "='" . $value . "'", "get site by filter(s)");
        } else {
            $res_get_nb_site = getResultFunctionOnField("COUNT", self::ID, "NBSITES", self::TABLE_NAME);
        }
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["NBSITES"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function countSitesArray($array_where = array()) {
        $nb_site = 0;

        if (!empty($array_where)) {
            $where = " WHERE ";
            $i = 0;
            foreach ($array_where as $field_name => $value) {
                if ($i > 0)
                    $where.=" AND ";
                if (is_numeric($value)) {
                    $where.=$field_name . "=" . $value . "";
                } else {
                    $where.=$field_name . "='" . $value . "'";
                }
                $i++;
            }
            $sql = "SELECT COUNT(" . self::ID . ") as NBSITES FROM " . self::TABLE_NAME . $where;
            $res_get_nb_site = queryToExecute($sql, "get site filter by region");
        } else {
            $res_get_nb_site = getResultFunctionOnField("COUNT", self::ID, "NBSITES", self::TABLE_NAME);
        }
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["NBSITES"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function getIncompleteSites($field_name = NULL, $value = NULL) {
        $nb_incomplete = 0;
        $sql = "SELECT COUNT(" . self::ID . ") AS NBSITESINCOMPLETE FROM " . self::TABLE_NAME . " WHERE (";

        $i = 0;
        foreach (self::getMandatoryFields() as $field_mandatory_name) {
            $sql.=$field_mandatory_name . " IS NULL ";
            $i++;
            if ($i < count(self::getMandatoryFields())) {
                $sql.= " OR ";
            }
        }
        $sql.= " ) ";
        if ($field_name != NULL && $value != NULL) {
            $sql .= " AND " . $field_name . "='" . $value . "'";
        }
        $rs = queryToExecute($sql, "count incomplete site");
        while (fetchRow($rs)) {
            $nb_incomplete = getValueInResult($rs, "NBSITESINCOMPLETE");
        }
        return $nb_incomplete;
    }

    public static function getAllCharcoalsIdFromGCDAccessId($gcd_access_site_id) {
        $charcoals_id = array();
        $query = "SELECT t_charcoal.ID_CHARCOAL FROM t_charcoal
INNER JOIN t_sample on t_charcoal.ID_SAMPLE = t_sample.ID_SAMPLE
INNER JOIN t_core on t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site on t_core.ID_SITE = t_site.ID_SITE
WHERE t_site.GCD_ACCESS_ID=$gcd_access_site_id;";
        $result = queryToExecute($query);
        if ($result) {
            while ($values = fetchAssoc($result)) {
                $charcoals_id[] = $values["ID_CHARCOAL"];
            }
            freeResult($result);
        }
        return $charcoals_id;
    }

    public static function countSamples($id_site) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_sample.ID_SAMPLE) as nb_samples, t_site.ID_SITE
FROM t_sample
INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site ON t_core.ID_SITE = t_site.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . "
";

        $res_get_nb_site = queryToExecute($query);
        while ($values = fetchAssoc($res_get_nb_site)) {
            $nb_site = $values["nb_samples"];
        }
        freeResult($res_get_nb_site);
        return $nb_site;
    }

    public static function countCharcoalsWhere($id_site, $where_clause = NULL) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_charcoal.ID_CHARCOAL) as nb_charcoals, t_site.ID_SITE
FROM t_charcoal
INNER JOIN t_sample on t_charcoal.ID_SAMPLE = t_sample.ID_SAMPLE
INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site ON t_core.ID_SITE = t_site.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . "
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

    public static function countDatedSamples($id_site) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_sample.ID_SAMPLE) as nb_samples, t_site.ID_SITE
FROM t_sample
INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site ON t_core.ID_SITE = t_site.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . "
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

    public static function countCharcoalSamples($id_site) {
        $nb_site = 0;
        $query = "SELECT COUNT(t_sample.ID_SAMPLE) as nb_samples, t_site.ID_SITE
FROM t_sample
INNER JOIN t_core ON t_sample.ID_CORE = t_core.ID_CORE
INNER JOIN t_site ON t_core.ID_SITE = t_site.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . "
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

    public function getAllContactIds() {
        $id_site = $this->getIdValue();
        $query = "(SELECT r_has_author.ID_CONTACT from r_has_author
JOIN t_charcoal on t_charcoal.ID_CHARCOAL = r_has_author.ID_CHARCOAL
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ")
UNION
(SELECT t_charcoal.ID_CONTACT from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ")";

        $array_contacts = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_contacts[] = $values["ID_CONTACT"];
        }
        freeResult($res_);
        return $array_contacts;
    }

    /*public function getAllPubliIds() {

        $id_site = $this->getIdValue();
        var_dump($id_site);
        $query = "SELECT DISTINCT(r_has_pub.ID_PUB) from r_has_pub
JOIN t_charcoal on t_charcoal.ID_CHARCOAL = r_has_pub.ID_CHARCOAL
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ";";

        $array_publis = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_publis[] = $values["ID_PUB"];
        }
        freeResult($res_);
        return $array_publis;
    }*/

    public function getAllPubli() {

        $id_site = $this->getIdValue();
        $query = "SELECT DISTINCT(t_pub.ID_PUB), t_pub.pub_citation, t_pub.pub_link, t_pub.pub_abbrev, t_pub.id_doi from t_pub
            join r_has_pub on r_has_pub.id_pub = t_pub.id_pub
            JOIN t_charcoal on t_charcoal.ID_CHARCOAL = r_has_pub.ID_CHARCOAL
            JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
            JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
            JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
            WHERE t_site.ID_SITE =" . $id_site . ";";

        $array_publis = array();
        $res_ = queryToExecute($query);
        $values = fetchAll($res_);
        freeResult($res_);
        return $values;
    }

    public static function getSitesReferencedByPublication($id_pub){
        $sites_list = [];
        if (is_numeric($id_pub)){
            $requete = "SELECT t_site.* FROM `r_site_is_referenced`
                join t_site on r_site_is_referenced.ID_SITE = t_site.ID_SITE
                where id_pub = ".$id_pub;
            $res = queryToExecute($requete);
            if ($res != NULL){
                $rows = fetchAll($res);
                foreach($rows as $values){
                    $site = new Site();
                    $site->read($values);
                    $sites_list[] = $site;
                    $site = NULL;
                }
            }
        }
        return $sites_list;
    }

    public function removeReferencePubli($id_publi) {
        $erreurs = array();
        $site_exists = ($this->getIdValue() == null)?false:true;

        if ($site_exists){
            $result_delete = deleteIntoTableFromId("r_site_is_referenced", "ID_PUB", $id_publi);
            if (!$result_delete) $erreurs[] = "Error (Deletion of id in r_site_is_referenced)";
        }

        return $erreurs;
    }

    public function getPubliReferencedBySite() {
        $id_site = $this->getIdValue();

        $query = "SELECT t_pub.ID_PUB, t_pub.pub_citation, t_pub.pub_link, t_pub.id_doi
            FROM r_site_is_referenced, t_pub WHERE t_pub.ID_PUB=r_site_is_referenced.ID_PUB AND r_site_is_referenced.ID_SITE =" . $id_site . ";";
        $array_publis = array();
        $res_ = queryToExecute($query);
        $values = fetchAll($res_);
        freeResult($res_);
        return $values;
    }

    public function getAllCharcoalMethods() {
        $id_site = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_CHARCOAL_METHOD`) as IDCHARCOALMETH from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDCHARCOALMETH"];
        }
        freeResult($res_);
        return $array_objects;
    }

    public function getAllCharcoalSizes() {
        $id_site = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_CHARCOAL_SIZE`) as IDCHARCOALSIZE from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDCHARCOALSIZE"];
        }
        freeResult($res_);
        return $array_objects;
    }

    public function getAllCharcoalUnits() {
        $id_site = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_CHARCOAL_UNITS`) as IDCHARCOALUNIT from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDCHARCOALUNIT"];
        }
        freeResult($res_);
        return $array_objects;
    }

    public function getAllDataSource() {
        $id_site = $this->getIdValue();
        $query = "    SELECT DISTINCT(`ID_DATA_SOURCE`) as IDDATASOURCE from t_charcoal
JOIN t_sample on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
JOIN t_core on t_core.ID_CORE = t_sample.ID_CORE
JOIN t_site on t_site.ID_SITE = t_core.ID_SITE
WHERE t_site.ID_SITE = " . $id_site . ";";

        $array_objects = array();
        $res_ = queryToExecute($query);
        while ($values = fetchAssoc($res_)) {
            $array_objects[] = $values["IDDATASOURCE"];
        }
        freeResult($res_);
        return $array_objects;
    }

    // fonction pour statistiques
    public static function getNbSitesByCountry(){
        $query = 'select count(id_site) as nbsites, country_iso_alpha2, country_name
                    from t_site
                    join tr_country on tr_country.id_country = t_site.id_country
                    group by country_iso_alpha2';

        $res_ = queryToExecute($query);
        $jsarray = null;
        $cumul = 0;
        while ($values = fetchAssoc($res_)) {
            $jsarray[] = "['".$values["country_name"]."',".$values["nbsites"]."]";
            $cumul += $values["nbsites"];
        }
        $jsarray = implode(',', $jsarray);

        $obj = new stdClass();
        $obj->nbSites = $cumul;
        $obj->tab = "[['Country', 'Number of sites'],".$jsarray."]";
        return $obj;
    }

    public static function getNbSitesByBiomes(){
        $query = 'select count(t_site.id_biome_type) as nbsites, biome_type_name
                    from t_site
                    join tr_biome_type on tr_biome_type.id_biome_type = t_site.id_biome_type
                    group by t_site.id_biome_type';

        $res_ = queryToExecute($query);
        $jsarray = null;
        while ($values = fetchAssoc($res_)) {
            $jsarray[] = "['".$values["biome_type_name"]."',".$values["nbsites"]."]";
        }
        $jsarray = implode(',', $jsarray);
        return "[['Biomes', 'Number of sites'],".$jsarray."]";
    }

    public static function getRepartitionBiomes(){
        $label = "biome_type_name";

        $query = 'select count(id_site) as nb, t_site.id_biome_type, biome_type_name from t_site
                    left join tr_biome_type on tr_biome_type.id_biome_type = t_site.id_biome_type
                    group by biome_type_name';

        $res = queryToExecute($query);

        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);
        foreach($tabRes as $elt){
            $somme += $elt["nb"];
        }

        foreach($tabRes as $elt){
            //$pourcentage = round($elt["nb"]*100/$somme);
            //$jsarray[] = "['".$elt[$label]."',".$elt["nb"].",". $pourcentage."]";
            // le pie char réalise le pourcentage automatiquement
            $jsarray[] = "['".$elt[$label]."',".$elt["nb"]."]";
        }

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Biomes', 'Number of sites'],".$jsarray."]";
        return $qualite;
    }

    public static function getIDSSiteForARegion($id_region){
        $id_region = data_securisation::toBdd($id_region);
        $query = 'select t_site.id_site from tr_country
                    join t_site on t_site.id_country = tr_country.id_country
                    where id_region = ' . $id_region . '
                         or id_region_bis = ' . $id_region . '
                    UNION
                    select t_site.id_site from t_site
                    where id_marine_region = ' . $id_region;
        $res = queryToExecute($query);
        $tabRes = fetchAll($res);
        $tab_ids = null;
        foreach($tabRes as $elt){
            $tab_ids[] = $elt["id_site"];
        }
        $ids = null;
        if ($tab_ids != null) $ids = '('.implode(',', $tab_ids).')';
        return $ids;
    }

    private static $tabJointuresSites = Array("cb_rv" => ["left join tr_regional_veg rv on rv.id_regional_veg = s.id_regional_veg ", "rv.regional_veg_desc"],
        "cb_lv" => ["left join tr_local_veg lv on lv.id_local_veg = s.id_local_veg ", "lv.local_veg_desc"],
        "cb_ld" => ["left join tr_lands_desc ld on ld.id_lands_desc = s.id_lands_desc ", "ld.lands_desc_name"],
        "cb_st" => ["left join tr_site_type st on st.id_site_type = s.id_site_type ", "st.site_type_desc"],
        "cb_bs" => ["left join tr_basin_size bs on bs.id_basin_size = s.id_basin_size ", "bs.basin_size_desc, s.basin_size_value"],
        "cb_cs" => ["left join tr_catch_size cs on cs.id_catch_size = s.id_catch_size ", "cs.catch_size_name, s.catch_size_value"],
        "cb_ft" => ["left join tr_flow_type ft on ft.id_flow_type = s.id_flow_type ", "ft.flow_type_name"],
        "cb_bt" => ["left join tr_biome_type bt on bt.id_biome_type = s.id_biome_type ", "bt.biome_type_name"],
        "cb_la" => ["", "c.latitude"],
        "cb_lo" => ["", "c.longitude"],
        "cb_el" => ["", "c.elevation"],
        "cb_wd" => ["", "c.water_depth"],
        "cb_cd" => ["", "c.coring_date"]);

    private static $tabSamples = Array("cb_cs"=>["left join tr_charcoal_size cs on cs.id_charcoal_size = c.id_charcoal_size ", "c.charcoal_size_value, cs.charcoal_size_desc"],
        "cb_ds" => ["left join tr_data_source ds on ds.id_data_source = c.id_data_source ", "ds.data_source_desc"],
        "cb_cm" => ["left join tr_charcoal_method cm on cm.id_charcoal_method = c.id_charcoal_method ", "cm.charcoal_method_name"],
        "cb_pcu" => ["left join tr_charcoal_units pcu on pcu.id_charcoal_units = c.id_charcoal_units ", "pcu.charcoal_units_name"],
        "cb_db" => ["left join tr_database db on db.id_database = c.id_database ", "db.gcd_access_version"],
        "cb_am" => ["left join r_has_estimated_age r_ag on r_ag.id_sample = s.id_sample left join t_age_model as ag on ag.id_age_model = r_ag.id_age_model ", "r_ag.est_age_cal_bp, ag.age_model_version"]);

    private static $tabSamplesProxyFire = Array("cb_ds" => ["left join tr_data_source ds on ds.id_data_source = c.id_data_source ", "ds.data_source_desc"],
        "cb_cm" => ["left join t_proxy_fire_method_treatment cm on cm.ID_PROXY_FIRE_METHOD_TREATMENT = c.ID_PROXY_FIRE_METHOD_TREATMENT ", "cm.proxy_fire_method_treatment_name"],
        "cb_pcu" => ["left join t_proxy_fire_measurement_unit pcu on pcu.ID_PROXY_FIRE_MEASUREMENT_UNIT = c.ID_PROXY_FIRE_MEASUREMENT_UNIT ", "pcu.unit"],
        "cb_db" => ["left join tr_database db on db.id_database = c.id_database ", "db.gcd_access_version"],
        "cb_am" => ["left join r_has_estimated_age r_ag on r_ag.id_sample = s.id_sample left join t_age_model as ag on ag.id_age_model = r_ag.id_age_model ", "r_ag.est_age_cal_bp, ag.age_model_version"]);


    private static $tabJointuresDateInfo = Array("cb_dt" => ["left join tr_date_type dty on di.id_date_type = dty.id_date_type ", "dty.date_type_name"],
        "cb_md" => ["left join tr_mat_dated md on di.id_mat_dated = md.id_mat_dated ", "md.mat_dated_type"],
        "cb_cm" => ["left join tr_calibration_method cm on r_ha.id_calibration_method = cm.id_calibration_method ", "cm.calibration_method_type"],
        "cb_cv" => ["left join tr_calibration_version cv on r_ha.id_calibration_version = cv.id_calibration_version ", "cv.calibration_curve_version"],
        "cb_au" => ["left join tr_age_units au on r_ha.id_age_units = au.id_age_units ", "au.age_units_calornot, au.age_units_type"],
        "cb_am" => ["left join t_age_model am on r_ha.id_age_model = am.id_age_model ", "am.age_model_version"]);

    public static function getDataForExport($id_region, $id_country, $id_site, $selected_fields, $selected_fieldsSample, $selected_fieldsDateInfo, $interval_time_min, $interval_time_max, $oldGcdCodeIncluded = false){
        $result = null;
        // beaucoup de données à gérer, on augmente temporairement la mémoire alouée
        // (ini_set n'est valable que pendant le script)
        ini_set('memory_limit', '512M');

        if($interval_time_min == "" || $interval_time_min == 'NULL' || $interval_time_min < -60 )
        {
            $interval_time_min = -60;
        }

        if($interval_time_max == "" || $interval_time_max == 'NULL' || $interval_time_max > 1500000 )
        {
            $interval_time_max = 1500000;
        }

        if ($id_region != "" && $id_region != 'NULL' && $id_region != 'null' && $id_region != null){
            $result = new stdClass();
            if ($id_country == "" || $id_country == 'NULL' || $id_country == 'null' || $id_country == null){
                // on récupère la liste des sites liés au id_region
                $id_sites = Site::getIDSSiteForARegion($id_region);
            } else if ($id_site == "" || $id_site == 'NULL' || $id_site == 'null' || $id_site == null) {
                // on récupère la liste des sites liés au id_country
                $sites = Site::getStaticList();
                $tab = null;
                foreach($sites as $elt){
                    if ($elt[Country::ID] == $id_country) $tab[] = $elt[site::ID];
                }
                $id_sites = '('.implode(',', $tab).')';
            } else $id_sites = '('.$id_site.')';

            $tabSelectedFields = json_decode($selected_fields);
            $jointure = "";
            $tabFieldsToSelectDB = [];
            foreach($tabSelectedFields as $field){
                if (self::$tabJointuresSites[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresSites[$field][1];
                $jointure .= self::$tabJointuresSites[$field][0];
            }

            $query = "SELECT s.id_site, s.site_name, c.id_core, c.core_name, co.country_name, r.region_name, r1.region_name";
            if ($oldGcdCodeIncluded == true) $query .= ",s.gcd_access_id ";
            if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
            $query .= " FROM t_site s ";
            $query .= "left join t_core c on s.id_site = c.id_site ";
            $query .= "left join tr_country co on co.id_country = s.id_country ";
            $query .= "left join tr_region r1 on r1.id_region = co.id_region ";
            $query .= "left join tr_region r on r.id_region = s.id_marine_region ";
            $query .= $jointure;
            $query .= "WHERE s.id_site in " . data_securisation::toBdd($id_sites);
            try {
                $res = queryToExecute($query);
                $result->tabSites = fetchAll($res);
            } catch(Exception $e){
                $result->tabSites = null;
                logError($e->getMessage());
            }

            // récupération des infos sur les samples
            $tabSelectedFields = json_decode($selected_fieldsSample);
            $jointure = "";
            $tabFieldsToSelectDB = [];
            foreach($tabSelectedFields as $field){
                if (self::$tabSamples[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabSamples[$field][1];
                $jointure .= self::$tabSamples[$field][0];
            }

            $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_cq.quantity, cu.charcoal_units_name, tabContacts.contacts ";
            if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
            $query .= " FROM t_core ";
            $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_site in ".  data_securisation::toBdd($id_sites);
            $query .= " join t_charcoal c on s.id_sample = c.id_sample ";
            $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
            $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
            $query .= "left join r_has_charcoal_quantity r_cq on r_cq.id_charcoal = c.id_charcoal ";
            $query .= "left join tr_charcoal_units cu on cu.id_charcoal_units = r_cq.id_charcoal_units ";
            $query .= $jointure;
            $query .= "left join (
                     select r_has_author.id_charcoal, GROUP_CONCAT( concat( t_contact.lastname, ' ', t_contact.firstname ) ) as contacts from r_has_author join t_contact on r_has_author.ID_CONTACT = t_contact.ID_CONTACT group by r_has_author.id_charcoal ) tabContacts on tabContacts.id_charcoal = c.id_charcoal ";
            $query .= "WHERE r_ag.est_age_cal_bp >= '$interval_time_min' and r_ag.est_age_cal_bp <= '$interval_time_max'";

            try {
                $res = queryToExecute($query);
                $result->tabCharcoals = fetchAll($res);
            } catch(Exception $e){
                $result->tabCharcoals = null;
                logError($e->getMessage());
            }

            // récupération des infos sur les dates infos
            $tabSelectedFields = json_decode($selected_fieldsDateInfo);
            $jointure = "";
            $tabFieldsToSelectDB = [];
            foreach($tabSelectedFields as $field){
                if (self::$tabJointuresDateInfo[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresDateInfo[$field][1];
                $jointure .= self::$tabJointuresDateInfo[$field][0];
            }

            $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_ha.age_value";
            if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
            $query .= " FROM t_core ";
            $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_site in ". data_securisation::toBdd($id_sites);
            $query .= " join t_date_info di on s.id_sample = di.id_sample ";
            $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
            $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
            $query .= "left join r_has_age r_ha on di.id_date_info = r_ha.id_date_info ";
            $query .= $jointure;
            try {
                $res = queryToExecute($query);
                $result->tabDatesInfos = fetchAll($res);
            } catch(Exception $e){
                $result->tabDatesInfos = null;
                logError($e->getMessage());
            }
        }

        return $result;
    }

    public static function getDataForExportProxyFire($id_region, $id_country, $id_site, $selected_fields, $selected_fieldsSample, $selected_fieldsDateInfo, $interval_time_min, $interval_time_max, $oldGcdCodeIncluded = false){
        $result = null;
        // beaucoup de données à gérer, on augmente temporairement la mémoire alouée
        // (ini_set n'est valable que pendant le script)
        ini_set('memory_limit', '512M');

        if($interval_time_min == "" || $interval_time_min == 'NULL' || $interval_time_min < -60 )
        {
            $interval_time_min = -60;
        }

        if($interval_time_max == "" || $interval_time_max == 'NULL' || $interval_time_max > 1500000 )
        {
            $interval_time_max = 1500000;
        }

        if ($id_region != "" && $id_region != 'NULL' && $id_region != 'null' && $id_region != null){
            $result = new stdClass();
            if ($id_country == "" || $id_country == 'NULL' || $id_country == 'null' || $id_country == null){
                // on récupère la liste des sites liés au id_region
                $id_sites = Site::getIDSSiteForARegion($id_region);
            } else if ($id_site == "" || $id_site == 'NULL' || $id_site == 'null' || $id_site == null) {
                // on récupère la liste des sites liés au id_country
                $sites = Site::getStaticList();
                $tab = null;
                foreach($sites as $elt){
                    if ($elt[Country::ID] == $id_country) $tab[] = $elt[site::ID];
                }
                $id_sites = '('.implode(',', $tab).')';
            } else $id_sites = '('.$id_site.')';

            $tabSelectedFields = json_decode($selected_fields);
            $jointure = "";
            $tabFieldsToSelectDB = [];
            foreach($tabSelectedFields as $field){
                if (self::$tabJointuresSites[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresSites[$field][1];
                $jointure .= self::$tabJointuresSites[$field][0];
            }

            $query = "SELECT s.id_site, s.site_name, c.id_core, c.core_name, co.country_name, r.region_name, r1.region_name";
            if ($oldGcdCodeIncluded == true) $query .= ",s.gcd_access_id ";
            if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
            $query .= " FROM t_site s ";
            $query .= "left join t_core c on s.id_site = c.id_site ";
            $query .= "left join tr_country co on co.id_country = s.id_country ";
            $query .= "left join tr_region r1 on r1.id_region = co.id_region ";
            $query .= "left join tr_region r on r.id_region = s.id_marine_region ";
            $query .= $jointure;
            $query .= "WHERE s.id_site in " . data_securisation::toBdd($id_sites);

            try {
                $res = queryToExecute($query);
                $result->tabSites = fetchAll($res);
            } catch(Exception $e){
                $result->tabSites = null;
                logError($e->getMessage());
            }

            // récupération des infos sur les samples
            $tabSelectedFields = json_decode($selected_fieldsSample);
            $jointure = "";
            $tabFieldsToSelectDB = [];
            foreach($tabSelectedFields as $field){
                if (self::$tabSamplesProxyFire[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabSamplesProxyFire[$field][1];
                $jointure .= self::$tabSamplesProxyFire[$field][0];
            }

            $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_cq.quantity, cu.unit, cu.unit_info, pfm.type, tabContacts.contacts, c.proxy_fire_size_value, r_ag.est_age_cal_bp ";
            if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
            $query .= " FROM t_core ";
            $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_site in ".  data_securisation::toBdd($id_sites);
            $query .= " join t_proxy_fire_data c on s.id_sample = c.id_sample ";
            $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
            $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
            $query .= "left join r_has_proxy_fire_data_quantity r_cq on r_cq.ID_PROXY_FIRE_DATA = c.ID_PROXY_FIRE_DATA ";
            $query .= "left join t_proxy_fire_measurement_unit cu on cu.ID_PROXY_FIRE_MEASUREMENT_UNIT = r_cq.ID_PROXY_FIRE_MEASUREMENT_UNIT ";
            $query .= "left join t_proxy_fire_measurement pfm on pfm.ID_PROXY_FIRE_MEASUREMENT = cu.ID_PROXY_FIRE_MEASUREMENT ";
            $query .= "left join r_has_estimated_age r_ag on r_ag.id_sample = s.id_sample ";
            $query .= $jointure;
            $query .= "left join (
                     select r_has_proxy_fire_author.ID_PROXY_FIRE_DATA, GROUP_CONCAT( concat( t_contact.lastname, ' ', t_contact.firstname ) ) as contacts from r_has_proxy_fire_author join t_contact on r_has_proxy_fire_author.ID_CONTACT = t_contact.ID_CONTACT group by r_has_proxy_fire_author.ID_PROXY_FIRE_DATA ) tabContacts on tabContacts.ID_PROXY_FIRE_DATA = c.ID_PROXY_FIRE_DATA ";
            $query .= "WHERE r_ag.est_age_cal_bp >= '$interval_time_min' and r_ag.est_age_cal_bp <= '$interval_time_max'";

            try {
                $res = queryToExecute($query);
                $result->tabCharcoals = fetchAll($res);
            } catch(Exception $e){
                $result->tabCharcoals = null;
                logError($e->getMessage());
            }

            // récupération des infos sur les dates infos
            $tabSelectedFields = json_decode($selected_fieldsDateInfo);
            $jointure = "";
            $tabFieldsToSelectDB = [];
            foreach($tabSelectedFields as $field){
                if (self::$tabJointuresDateInfo[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresDateInfo[$field][1];
                $jointure .= self::$tabJointuresDateInfo[$field][0];
            }

            $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_ha.age_value";
            if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
            $query .= " FROM t_core ";
            $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_site in ". data_securisation::toBdd($id_sites);
            $query .= " join t_date_info di on s.id_sample = di.id_sample ";
            $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
            $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
            $query .= "left join r_has_age r_ha on di.id_date_info = r_ha.id_date_info ";
            $query .= $jointure;

            try {
                $res = queryToExecute($query);
                $result->tabDatesInfos = fetchAll($res);
            } catch(Exception $e){
                $result->tabDatesInfos = null;
                logError($e->getMessage());
            }
        }

        return $result;
    }

    public static function getDataForRExport(){
        $tabSites = null;

        /*$query = 'select t_site.ID_SITE as id_site, t_site.old_gcd_site,
            CONCAT(t_site.SITE_NAME, " ", t_core.CORE_NAME) as site_name,
            t_core.LATITUDE as lat,
            t_core.LONGITUDE as "long",
            t_core.ELEVATION as elev,
            t_site.ID_COUNTRY as id_country,
            t_site.ID_SITE_TYPE as site_type,
            tr_site_type.SITE_TYPE_LEVEL as id_site_type,
            t_core.WATER_DEPTH as water_depth,
            t_site.ID_BASIN_SIZE as basin_size,
            tr_basin_size.BASIN_SIZE_CODE as id_basin_size,
            t_site.ID_CATCH_SIZE as catch_size,
            tr_catch_size.CATCH_SIZE_CODE as id_catch_size,
            t_site.ID_LANDS_DESC as id_land_desc
            from t_site
            join t_core on t_site.id_site = t_core.id_site
            join tr_site_type on t_site.ID_SITE_TYPE = tr_site_type.ID_SITE_TYPE
            join tr_basin_size on t_site.ID_BASIN_SIZE = tr_basin_size.ID_BASIN_SIZE
            join tr_catch_size on t_site.ID_CATCH_SIZE = tr_catch_size.ID_CATCH_SIZE
            order by t_site.id_site';*/

        $query = 'select t_site.ID_SITE as id_site, t_site.gcd_access_id as gcd_access_id,
            t_site.SITE_NAME as site_name,
            t_core.LATITUDE as lat,
            t_core.LONGITUDE as "long",
            t_core.ELEVATION as elev,
            t_site.ID_COUNTRY as id_country,
            tr_site_type.SITE_TYPE_LEVEL as id_site_type,
            t_core.WATER_DEPTH as water_depth,
            tr_basin_size.BASIN_SIZE_CODE as id_basin_size,
            tr_catch_size.CATCH_SIZE_CODE as id_catch_size,
            t_site.ID_LANDS_DESC as id_land_desc
            from t_site
            join t_core on t_site.id_site = t_core.id_site
            join tr_site_type on t_site.ID_SITE_TYPE = tr_site_type.ID_SITE_TYPE
            join tr_basin_size on t_site.ID_BASIN_SIZE = tr_basin_size.ID_BASIN_SIZE
            join tr_catch_size on t_site.ID_CATCH_SIZE = tr_catch_size.ID_CATCH_SIZE
            order by t_site.id_site';

        try {
            $res = queryToExecute($query);
            $tabSites = fetchAll($res);
        } catch(Exception $e){
            $tabSites = null;
            logError($e->getMessage());
        }

        freeResult($res);
        return $tabSites;
    }

    public static function getDataForExportbyCoreIDS($tabIds, $selected_fields, $selected_fieldsSample, $selected_fieldsDateInfo, $interval_time_min, $interval_time_max, $oldGcdCodeIncluded = false){
        // beaucoup de données à gérer, on augmente temporairement la mémoire alouée
        // (ini_set n'est valable que pendant le script)
        ini_set('memory_limit', '512M');

        if($interval_time_min == "" || $interval_time_min == 'NULL' || $interval_time_min < -60 )
        {
            $interval_time_min = -60;
        }

        if($interval_time_max == "" || $interval_time_max == 'NULL' || $interval_time_max > 1500000 )
        {
            $interval_time_max = 1500000;
        }

        $id_cores = '('.implode(',', $tabIds).')';
        $result = new stdClass();

        $tabSelectedFields = json_decode($selected_fields);
        $jointure = "";
        $tabFieldsToSelectDB = [];
        foreach($tabSelectedFields as $field){
            if (self::$tabJointuresSites[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresSites[$field][1];
            $jointure .= self::$tabJointuresSites[$field][0];
        }

        $query = "SELECT s.id_site, s.site_name, c.id_core, c.core_name, co.country_name, r.region_name, r1.region_name";
        if ($oldGcdCodeIncluded == true) $query .= ",s.gcd_access_id ";
        if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
        $query .= " FROM t_core c ";
        $query .= "left join t_site s on c.id_site = s.id_site ";
        $query .= "left join tr_country co on co.id_country = s.id_country ";
        $query .= "left join tr_region r1 on r1.id_region = co.id_region ";
        $query .= "left join tr_region r on r.id_region = s.id_marine_region ";
        $query .= $jointure;
        $query .= "WHERE c.id_core in " . data_securisation::toBdd($id_cores);

        try {
            $res = queryToExecute($query);
            $result->tabSites = fetchAll($res);
        } catch(Exception $e){
            $result->tabSites = null;
            logError($e->getMessage());
        }

        freeResult($res);

        $tabSelectedFields = json_decode($selected_fieldsSample);
        $jointure = "";
        $tabFieldsToSelectDB = [];
        foreach($tabSelectedFields as $field){
            if (self::$tabSamples[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabSamples[$field][1];
            $jointure .= self::$tabSamples[$field][0];
        }

        $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_cq.quantity, cu.charcoal_units_name, tabContacts.contacts ";
        if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
        $query .= " FROM t_core ";
        $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_core in ".data_securisation::toBdd($id_cores);
        $query .= " join t_charcoal c on s.id_sample = c.id_sample ";
        $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
        $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
        $query .= "left join r_has_charcoal_quantity r_cq on r_cq.id_charcoal = c.id_charcoal ";
        $query .= "left join tr_charcoal_units cu on cu.id_charcoal_units = r_cq.id_charcoal_units ";
        $query .= $jointure;
        $query .= "left join (
                     select r_has_author.id_charcoal, GROUP_CONCAT( concat( t_contact.lastname, ' ', t_contact.firstname ) ) as contacts from r_has_author join t_contact on r_has_author.ID_CONTACT = t_contact.ID_CONTACT group by r_has_author.id_charcoal ) tabContacts on tabContacts.id_charcoal = c.id_charcoal ";
        $query .= "WHERE r_ag.est_age_cal_bp >= '$interval_time_min' and r_ag.est_age_cal_bp <= '$interval_time_max'";

        try {
            $res = queryToExecute($query);
            $result->tabCharcoals = fetchAll($res);
        } catch(Exception $e){
            $result->tabCharcoals = null;
            logError($e->getMessage());
        }
        freeResult($res);
        /*
        echo 'passe';

        try {

            $offset = $limit;
            var_dump($query . " LIMIT " .$limit);

            $res = queryToExecute($query . " LIMIT " .$limit);

            var_dump($res);
            while ($res != FALSE){
                $tab = fetchAll($res);
                $result->tabCharcoals[] = $tab;
                freeResult($res);
                $offset += $limit;
                var_dump($query . " LIMIT " .$limit ." OFFSET " . $offset);
                $res = queryToExecute($query . " LIMIT " .$limit ." OFFSET " . $offset);
            }
            freeResult($res);
            //var_dump($result->tabCharcoals);
            //var_dump($result);
        } catch(Exception $e){
            var_dump('passe2');
            var_dump($e);
            $result->tabCharcoals = null;
            logError($e->getMessage());
                            var_dump('passe2');
        }
        var_dump('passe3');
        exit();
*/

        // récupération des infos sur les dates infos
        $tabSelectedFields = json_decode($selected_fieldsDateInfo);
        $jointure = "";
        $tabFieldsToSelectDB = [];
        foreach($tabSelectedFields as $field){
            if (self::$tabJointuresDateInfo[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresDateInfo[$field][1];
            $jointure .= self::$tabJointuresDateInfo[$field][0];
        }

        $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_ha.age_value";
        if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
        $query .= " FROM t_core ";
        $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_core in ".data_securisation::toBdd($id_cores);
        $query .= " join t_date_info di on s.id_sample = di.id_sample ";
        $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
        $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
        $query .= "left join r_has_age r_ha on di.id_date_info = r_ha.id_date_info ";
        $query .= $jointure;

        try {
            $res = queryToExecute($query);
            $result->tabDatesInfos = fetchAll($res);
        } catch(Exception $e){
            $result->tabDatesInfos = null;
            logError($e->getMessage());
        }
        freeResult($res);

        return $result;
    }

    public static function getDataProxyFireForExportbyCoreIDS($tabIds, $selected_fields, $selected_fieldsSample, $selected_fieldsDateInfo, $interval_time_min, $interval_time_max, $oldGcdCodeIncluded = false){
        // beaucoup de données à gérer, on augmente temporairement la mémoire alouée
        // (ini_set n'est valable que pendant le script)
        ini_set('memory_limit', '512M');

        if($interval_time_min == "" || $interval_time_min == 'NULL' || $interval_time_min < -60 )
        {
            $interval_time_min = -60;
        }

        if($interval_time_max == "" || $interval_time_max == 'NULL' || $interval_time_max > 1500000 )
        {
            $interval_time_max = 1500000;
        }

        $id_cores = '('.implode(',', $tabIds).')';
        $result = new stdClass();

        $tabSelectedFields = json_decode($selected_fields);
        $jointure = "";
        $tabFieldsToSelectDB = [];
        foreach($tabSelectedFields as $field){
            if (self::$tabJointuresSites[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresSites[$field][1];
            $jointure .= self::$tabJointuresSites[$field][0];
        }

        $query = "SELECT s.id_site, s.site_name, c.id_core, c.core_name, co.country_name, r.region_name, r1.region_name";
        if ($oldGcdCodeIncluded == true) $query .= ",s.gcd_access_id ";
        if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
        $query .= " FROM t_core c ";
        $query .= "left join t_site s on c.id_site = s.id_site ";
        $query .= "left join tr_country co on co.id_country = s.id_country ";
        $query .= "left join tr_region r1 on r1.id_region = co.id_region ";
        $query .= "left join tr_region r on r.id_region = s.id_marine_region ";
        $query .= $jointure;
        $query .= "WHERE c.id_core in " . data_securisation::toBdd($id_cores);

        try {
            $res = queryToExecute($query);
            $result->tabSites = fetchAll($res);
        } catch(Exception $e){
            $result->tabSites = null;
            logError($e->getMessage());
        }

        freeResult($res);

        $tabSelectedFields = json_decode($selected_fieldsSample);
        $jointure = "";
        $tabFieldsToSelectDB = [];
        foreach($tabSelectedFields as $field){
            if (self::$tabSamplesProxyFire[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabSamplesProxyFire[$field][1];
            $jointure .= self::$tabSamplesProxyFire[$field][0];
        }

        $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_cq.quantity, cu.unit, cu.unit_info, pfm.type, tabContacts.contacts, c.proxy_fire_size_value, r_ag.est_age_cal_bp ";
        if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
        $query .= " FROM t_core ";
        $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_core in ".data_securisation::toBdd($id_cores);
        // $query .= " join t_charcoal c on s.id_sample = c.id_sample ";
        $query .= " join t_proxy_fire_data c on s.id_sample = c.id_sample ";
        $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
        $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
        // $query .= "left join r_has_charcoal_quantity r_cq on r_cq.id_charcoal = c.id_charcoal ";
        $query .= "left join r_has_proxy_fire_data_quantity r_cq on r_cq.ID_PROXY_FIRE_DATA = c.ID_PROXY_FIRE_DATA ";
        // $query .= "left join tr_charcoal_units cu on cu.id_charcoal_units = r_cq.id_charcoal_units ";
        $query .= "left join t_proxy_fire_measurement_unit cu on cu.ID_PROXY_FIRE_MEASUREMENT_UNIT = r_cq.ID_PROXY_FIRE_MEASUREMENT_UNIT ";
        $query .= "left join t_proxy_fire_measurement pfm on pfm.ID_PROXY_FIRE_MEASUREMENT = cu.ID_PROXY_FIRE_MEASUREMENT ";
        $query .= "left join r_has_estimated_age r_ag on r_ag.id_sample = s.id_sample ";
        $query .= $jointure;
        $query .= "left join (
                     select r_has_proxy_fire_author.ID_PROXY_FIRE_DATA, GROUP_CONCAT( concat( t_contact.lastname, ' ', t_contact.firstname ) ) as contacts from r_has_proxy_fire_author join t_contact on r_has_proxy_fire_author.ID_CONTACT = t_contact.ID_CONTACT group by r_has_proxy_fire_author.ID_PROXY_FIRE_DATA ) tabContacts on tabContacts.ID_PROXY_FIRE_DATA = c.ID_PROXY_FIRE_DATA ";
        $query .= "WHERE r_ag.est_age_cal_bp >= '$interval_time_min' and r_ag.est_age_cal_bp <= '$interval_time_max'";

        try {
            $res = queryToExecute($query);
            $result->tabCharcoals = fetchAll($res);
        } catch(Exception $e){
            $result->tabCharcoals = null;
            logError($e->getMessage());
        }
        freeResult($res);
        /*
        echo 'passe';

        try {

            $offset = $limit;
            var_dump($query . " LIMIT " .$limit);

            $res = queryToExecute($query . " LIMIT " .$limit);

            var_dump($res);
            while ($res != FALSE){
                $tab = fetchAll($res);
                $result->tabCharcoals[] = $tab;
                freeResult($res);
                $offset += $limit;
                var_dump($query . " LIMIT " .$limit ." OFFSET " . $offset);
                $res = queryToExecute($query . " LIMIT " .$limit ." OFFSET " . $offset);
            }
            freeResult($res);
            //var_dump($result->tabCharcoals);
            //var_dump($result);
        } catch(Exception $e){
            var_dump('passe2');
            var_dump($e);
            $result->tabCharcoals = null;
            logError($e->getMessage());
                            var_dump('passe2');
        }
        var_dump('passe3');
        exit();
*/

        // récupération des infos sur les dates infos
        $tabSelectedFields = json_decode($selected_fieldsDateInfo);
        $jointure = "";
        $tabFieldsToSelectDB = [];
        foreach($tabSelectedFields as $field){
            if (self::$tabJointuresDateInfo[$field][1] != null) $tabFieldsToSelectDB[] = self::$tabJointuresDateInfo[$field][1];
            $jointure .= self::$tabJointuresDateInfo[$field][0];
        }

        $query = "SELECT t_core.id_site, t_core.id_core, s.id_sample, s.sample_name, d.depth_value, dt.depth_type_name, r_ha.age_value";
        if ($tabFieldsToSelectDB != null) $query .= "," .implode($tabFieldsToSelectDB, ','). " ";
        $query .= " FROM t_core ";
        $query .= "join t_sample s on t_core.id_core = s.id_core and t_core.id_core in ".data_securisation::toBdd($id_cores);
        $query .= " join t_date_info di on s.id_sample = di.id_sample ";
        $query .= "left join t_depth d on s.id_sample = d.id_sample_if_default ";
        $query .= "left join tr_depth_type dt on d.id_depth_type = dt.id_depth_type ";
        $query .= "left join r_has_age r_ha on di.id_date_info = r_ha.id_date_info ";
        $query .= $jointure;

        try {
            $res = queryToExecute($query);
            $result->tabDatesInfos = fetchAll($res);
        } catch(Exception $e){
            $result->tabDatesInfos = null;
            logError($e->getMessage());
        }
        freeResult($res);

        return $result;
    }



    public static function getSitesBySearchOnCountry($recherche){
        $query = "select tr_country.ID_COUNTRY, COUNTRY_NAME, ID_SITE, SITE_NAME "
                . "from tr_country join t_site on tr_country.id_country = t_site.ID_COUNTRY "
                . "where tr_country.COUNTRY_NAME like ".$recherche;
        $tab = null;
        $result = queryToExecute($query);
        if ($result != NULL){
            $tab = fetchAll($result);
        }
        return $tab;
    }

}
