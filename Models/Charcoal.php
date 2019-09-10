<?php
/* 
 * fichier \Models\Charcoal.php
 * 
 */

require_once 'ObjectPaleofire.php';
require_once 'CharcoalQuantity.php';
require_once 'CharcoalMethod.php';
require_once 'CharcoalSize.php';
require_once 'Status.php';
require_once 'DataSource.php';
require_once 'Contact.php';
require_once 'Publi.php';
include_once(REP_PAGES . "EDA/change_Log.php");

//require_once 'data_securisation.php';

class Charcoal extends ObjectPaleofire {

    const TABLE_NAME = 't_charcoal';
    const ID = 'ID_CHARCOAL';
    const NAME = '';
    const ID_SAMPLE = "ID_SAMPLE";
    const ID_CHARCOAL_METHOD = "ID_CHARCOAL_METHOD";
    const ID_CHARCOAL_SIZE = 'ID_CHARCOAL_SIZE';
    const CHARCOAL_SIZE_VALUE = 'CHARCOAL_SIZE_VALUE';
    const ID_STATUS = 'ID_STATUS';
    const ID_PREF_CHARCOAL_UNITS = 'ID_CHARCOAL_UNITS';
    const ID_DATABASE = "ID_DATABASE";
    const ID_DATA_SOURCE = "ID_DATA_SOURCE";
    const ID_CONTACT = 'ID_CONTACT';
    const ID_LATEST_CONTACT = 'ID_LATEST_CONTACT';
    const CREATION_DATE = 'CREATION_DATE';
    const UPDATE_DATE = 'UPDATE_DATE';

    public $_site_id;
    public $_sample_id;
    public $_charcoal_method_id;
    public $_charcoal_size_id;
    public $_charcoal_size_value;
    public $_list_charcoal_quantities;
    public $_list_authors;
    public $_list_publications;
    public $_charcoal_status_id;
    public $_charcoal_database_id;
    public $_charcoal_datasource_id;
    public $_charcoal_charcoal_units_id;
    public $_charcoal_contact_id;
    public $_charcoal_latest_contact_id;
    public $_creation_date;
    public $_update_date;
    public $_sample;

    
    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_site_id = null;
        $this->_sample_id = null;
        $this->_charcoal_method_id = null;
        $this->_charcoal_size_id = null;
        $this->_charcoal_size_value = null;
        $this->_list_charcoal_quantities = array();
        $this->_list_publications = array();
        $this->_list_contributors = array();
        $this->_charcoal_status_id = null;
        $this->_charcoal_database_id = null;
        $this->_charcoal_datasource_id = null;
        $this->_charcoal_charcoal_units_id = null;
        $this->_charcoal_contact_id = null;
        $this->_charcoal_latest_contact_id = null;
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
    
    public function addCharcoalQuantity($quantity_value, $charcoal_unit_id) {
        $this->_list_charcoal_quantities[] = new CharcoalQuantity($quantity_value, $charcoal_unit_id);
    }

    public static function getAllCharcoal() {
        return getAllObjectsInTable(self::TABLE_NAME);
    }

    public function getTableName() {
        return self::TABLE_NAME;
    }
    
public function  getIdSite() { //retourne l'identifiant du site du charbon
    $query = "SELECT t_core.ID_SITE"
             ."from t_core, t_sample, t_charcoal"
             ."where t_core.ID_CORE = t_sample.ID_CORE and t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE and t_charcoal.ID_CHARCOAL = " . $this->getIdValue();
    $res = queryToExecute($query, "Charcoal get site id");
    return ($res);             
}

public function getListCharcoalQuantities() {
        return $this->_list_charcoal_quantities;
    }

    public function getListPublications() {
        if ($this->_list_publications == null){
            $query = "Select * from r_has_pub "
                    . "join t_pub on r_has_pub.id_pub = t_pub.id_pub "
                    . "where id_charcoal = " . $this->getIdValue();
            //echo $query;
            $res = queryToExecute($query, "Charcoal getListPublications");
            $tab = fetchAll($res);
            $this->_list_publications = [];
            if ($tab != NULL) {
                foreach($tab as $values){
                    $publi = new Publi();
                    $publi->read($values);
                    $this->_list_publications[] = $publi;  
                }
            }
        }
        return $this->_list_publications;
    }
    
    public function getListAuthors() {
       // if ($this->_list_authors == null){
            $query = "SELECT * from r_has_author "
                    . "join t_contact on r_has_author.id_contact = t_contact.id_contact "
                    . "where id_charcoal = ".$this->getIdValue();
            $res = queryToExecute($query, "Charcoal getListAuthors");         
            $tab = fetchAll($res);           
            if ($tab!=NULL) {
                $this->_list_authors = [];
                foreach($tab as $values){
                    $author = new Contact();
                    $author->read($values);
                    $this->_list_authors[] = $author;                  
                }
            }
      //  }

        return $this->_list_authors;
    }

    public function getListContributors() {
        return $this->_list_contributors;
    }

    public static function getCharcoal($id_Charcoal) {
        $current_Charcoal = new Charcoal();
        $result_get_Charcoal = getAllObjectFromId(self::TABLE_NAME, self::ID, $id_Charcoal);

        $tab_values = array();
        if (fetchRow($result_get_Charcoal)) {
            foreach ($current_Charcoal->getFields() as $field_name) {
                $tab_values[$field_name] = getValueInResult($result_get_Charcoal, $field_name);
            }
        }
        if ($tab_values != NULL && !empty($tab_values)) {
            $current_Charcoal->setArrayValues($tab_values);
            return $current_Charcoal;
        } else {
            return NULL;
        }
    }

    public static function getCharcoalsFromCore($id_core) {

        $request = "SELECT * from t_sample join t_charcoal on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE WHERE t_sample.ID_CORE = " . $id_core;
        $result_get_Charcoal = queryToExecute($request, "get charcoal from core");
        $allValues = fetchAll($result_get_Charcoal);
        $tabRes = [];
        if ($allValues != NULL) {
            foreach($allValues as $values){
                $charcoal = new Charcoal();
                $charcoal->read($values);
                $tabRes[] = $charcoal;
            }
        }
        return $tabRes;
    }
    
    public static function getCharcoalsFromCoreOrderedByUnits($id_core) {
 
//        $request = "Select * "
            $request = "Select t_sample_depth_list.ID_SAMPLE, SAMPLE_NAME, t_sample_depth_list.ID_CORE, DEPTH_LIST, "
                . "t_charcoal.ID_CHARCOAL, r_has_charcoal_quantity.ID_CHARCOAL_UNITS, QUANTITY, AUTHORS_LIST, ID_CONTACT, ID_LATEST_CONTACT,"
                . " ID_CHARCOAL_METHOD, ID_STATUS, ID_DATA_SOURCE, ID_DATABASE, ID_CHARCOAL_SIZE, CHARCOAL_SIZE_VALUE,"
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
                . " JOIN t_charcoal ON t_sample_depth_list.ID_SAMPLE = t_charcoal.ID_SAMPLE "
                . " JOIN r_has_charcoal_quantity ON t_charcoal.ID_CHARCOAL = r_has_charcoal_quantity.ID_CHARCOAL "
                . " JOIN tr_charcoal_units ON r_has_charcoal_quantity.ID_CHARCOAL_UNITS = tr_charcoal_units.ID_CHARCOAL_UNITS "
                    ." left JOIN ( "
                    ." SELECT r_has_author.ID_CHARCOAL, GROUP_CONCAT(r_has_author.ID_CONTACT, \",\") AS AUTHORS_LIST"
                    ." FROM r_has_author"
                    ." GROUP BY r_has_author.ID_CHARCOAL"
                    ." ) t_contact_list ON t_charcoal.ID_CHARCOAL = t_contact_list.ID_CHARCOAL"
                . " ORDER BY r_has_charcoal_quantity.id_charcoal_units" ;
            //var_dump($request);
        $result_get_Charcoal = queryToExecute($request, "get charcoal from core");
        $allValues = fetchAll($result_get_Charcoal);
        
        return $allValues;
    }
    
    public static function getCharcoalFromSiteAndSample($id_site, $id_sample) {

        $charcoals = array();
        $request = "SELECT * FROM CHARCOAL WHERE ID_SITE=" . $id_site . " AND ID_SAMPLE=" . $id_sample . "";
        $result_get_Charcoal = queryToExecute($request, "get charcoal from site and sample");


        while (fetchRow($result_get_Charcoal)) {
            $tab_values = array();
            $current_Charcoal = new Charcoal();
            foreach ($current_Charcoal->getFields() as $field_name) {
                $tab_values[$field_name] = getValueInResult($result_get_Charcoal, $field_name);
            }
            if ($tab_values != NULL && !empty($tab_values)) {

                $current_Charcoal->setArrayValues($tab_values);
                $charcoals[] = $current_Charcoal;
            }
        }
        return $charcoals;
    }

    public static function getDataForRExport(){
        ini_set('memory_limit', '512M');
        $tab = null;

        /*$query = 'select t_site.id_site as id_site, 
            t_site.GCD_ACCESS_ID as gcd_access_id, 
            t_depth.DEPTH_VALUE as depth, 
            r_has_estimated_age.EST_AGE_CAL_BP as est_age,
            r_has_charcoal_quantity.QUANTITY as quantity,
            r_has_charcoal_quantity.ID_CHARCOAL_UNITS as id_unit,
            tr_charcoal_units.charcoal_units_first_level as type,
            t_charcoal.ID_CHARCOAL_METHOD as id_method,
            tr_charcoal_method.CHARCOAL_METHOD_CODE as method,
            t_charcoal.ID_CHARCOAL_SIZE as id_type,
            tr_charcoal_size.CHARCOAL_SIZE_CODE as type
            from t_site
            join t_core on t_site.ID_SITE = t_core.ID_SITE
            join t_sample on t_core.ID_CORE = t_sample.ID_CORE
            join t_depth on t_sample.ID_SAMPLE = t_depth.ID_RELATED_SAMPLE
            join r_has_estimated_age on t_depth.ID_DEPTH = r_has_estimated_age.ID_Depth
            join t_charcoal on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
            join r_has_charcoal_quantity on t_charcoal.ID_CHARCOAL = r_has_charcoal_quantity.ID_CHARCOAL
            join tr_charcoal_method on t_charcoal.id_charcoal_method = tr_charcoal_method.id_charcoal_method
            join tr_charcoal_size on t_charcoal.id_charcoal_size = tr_charcoal_size.id_charcoal_size
            join tr_charcoal_units on r_has_charcoal_quantity.ID_CHARCOAL_UNITS = tr_charcoal_units.id_charcoal_units';
*/
        $query = 'select t_site.id_site as id_site, 
            t_site.GCD_ACCESS_ID as gcd_access_id, 
            t_depth.DEPTH_VALUE as depth, 
            r_has_estimated_age.EST_AGE_CAL_BP as est_age,
            r_has_charcoal_quantity.QUANTITY as quantity,
            tr_charcoal_units.charcoal_units_first_level as type,
            tr_charcoal_units.charcoal_units_name as units,
            tr_charcoal_units.CHARCOAL_UNITS_HIGH_LEVEL as id_units,
            tr_charcoal_method.CHARCOAL_METHOD_CODE as method
            from t_site
            join t_core on t_site.ID_SITE = t_core.ID_SITE
            join t_sample on t_core.ID_CORE = t_sample.ID_CORE
            join t_depth on t_sample.ID_SAMPLE = t_depth.ID_RELATED_SAMPLE
            join r_has_estimated_age on t_depth.ID_DEPTH = r_has_estimated_age.ID_Depth
            join t_charcoal on t_sample.ID_SAMPLE = t_charcoal.ID_SAMPLE
            join r_has_charcoal_quantity on t_charcoal.ID_CHARCOAL = r_has_charcoal_quantity.ID_CHARCOAL
            join tr_charcoal_method on t_charcoal.id_charcoal_method = tr_charcoal_method.id_charcoal_method
            join tr_charcoal_size on t_charcoal.id_charcoal_size = tr_charcoal_size.id_charcoal_size
            join tr_charcoal_units on r_has_charcoal_quantity.ID_CHARCOAL_UNITS = tr_charcoal_units.id_charcoal_units';
                
        try {
            $res = queryToExecute($query);
            $tab = fetchAll($res);
        } catch(Exception $e){
            $tab = null;
            logError($e->getMessage());
        }

        freeResult($res);
        return $tab;
    }
    
    public function create() {
        beginTransaction();
        $insert_errors = array();

        if ($this->_charcoal_method_id != NULL) {
            $column_values[self::ID_CHARCOAL_METHOD] = $this->_charcoal_method_id;
        }
        if ($this->_charcoal_size_id != NULL) {
            $column_values[self::ID_CHARCOAL_SIZE] = $this->_charcoal_size_id;
        }
        if ($this->_sample_id != NULL) {
            $column_values[self::ID_SAMPLE] = $this->_sample_id;
        }

        //$result = getFieldsFromTables("STATUS_TEMP_ID, CHARCOAL_PREF_UNIT_TEMP_ID, DATA_SOURCE_ID_TEMP, DATABASE_VERSION_ID", 
        //        "tt_site_import", 
        //        sql_equal("ID_SITE_TEMP", $this->_site_id));
        //if ($result) {
            //$values = fetchAssoc($result);
            //freeResult($result);
       $listeSiteTemp = Site::getAllTempSitesByID();
       if ($listeSiteTemp != null && key_exists($this->_site_id, $listeSiteTemp)){
            $values = $listeSiteTemp[$this->_site_id];
            $column_values[self::ID_STATUS] = $values["STATUS_TEMP_ID"];
            $column_values[self::ID_PREF_CHARCOAL_UNITS] = $values["CHARCOAL_PREF_UNIT_TEMP_ID"];
            $id_data_source = $values["DATA_SOURCE_ID_TEMP"];
            if ($id_data_source != null && $id_data_source != "") {
                $column_values[self::ID_DATA_SOURCE] = $id_data_source;
            }
            $column_values[self::ID_DATABASE] = $values["DATABASE_VERSION_ID"];
       }
        //}
        
        $result = getFieldsFromTables("CONTRIBUTOR_TEMP_ID, AUTHOR_TEMP_ID", "tt_sample_import", sql_equal("ID_SAMPLE_TEMP", $this->_sample_id));
        if ($result) {
            $values = fetchAssoc($result);
            //var_dump($values);
            freeResult($result);
            $id_contact = $values["CONTRIBUTOR_TEMP_ID"];
            if ($id_contact != null && $id_contact != "") {
                if (Contact::getObjectPaleofireFromId($id_contact)) {
                    $column_values[self::ID_CONTACT] = $id_contact;
                } else {
                    $insert_errors[] = "The contact with id " . $id_contact . " does not exist in the database";
                }
            }
        }

        if (empty($insert_errors)) {
            $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);         
            if (!$result_insert) {
                $insert_errors[] = "Insert into table " . self::TABLE_NAME;
            } else {
                $this->setIdValue(getLastCreatedId());
                //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                //writeChangeLog("add_charcoal", $this->getIdValue());
            }
        }

        if (empty($insert_errors)) {
            foreach ($this->_list_charcoal_quantities as $charcoal_quantity) {
                $charcoal_quantity->_charcoal_id = $this->getIdValue();
                $insert_errors = array_merge($insert_errors, $charcoal_quantity->create());
            }


            //$result = getFieldsFromTables("AUTHOR_TEMP_ID", "tt_sample_import", sql_equal("ID_SAMPLE_TEMP", $this->_sample_id));
            //if ($result) {
                //$values = fetchAssoc($result);
                //freeResult($result);
                if (isset($values["AUTHOR_TEMP_ID"]) && $values["AUTHOR_TEMP_ID"] != NULL) {
                    insertIntoTable("r_has_author", array("ID_CONTACT" => $values["AUTHOR_TEMP_ID"], self::ID => $this->getIdValue()));
                }
            //}
        }

        //If no error, commit transaction !
        if (empty($insert_errors)) {
            commit();
        } else {
            rollBack();
        }

        return $insert_errors;
    }
    
    public function save() {
        $insert_errors = array();
        $column_values = array();

        
        if ($this->_charcoal_method_id != NULL) {
            $column_values[self::ID_CHARCOAL_METHOD] = $this->_charcoal_method_id;
        }
        if ($this->_charcoal_size_id != NULL) {
            $column_values[self::ID_CHARCOAL_SIZE] = $this->_charcoal_size_id;
        }
        if ($this->_charcoal_size_value != NULL && is_numeric($this->_charcoal_size_value)) {
            $column_values[self::CHARCOAL_SIZE_VALUE] = $this->_charcoal_size_value;
        }
        if ($this->_sample_id != NULL) {
            $column_values[self::ID_SAMPLE] = $this->_sample_id;
        }
        if ($this->_charcoal_status_id != NULL) {
            $column_values[self::ID_STATUS] = $this->_charcoal_status_id;
        }
        if ($this->_charcoal_charcoal_units_id != NULL) {
            $column_values[self::ID_PREF_CHARCOAL_UNITS] = $this->_charcoal_charcoal_units_id;
        }
        if ($this->_charcoal_datasource_id != NULL) {
            $column_values[self::ID_DATA_SOURCE] = $this->_charcoal_datasource_id;
        }
        if ($this->_charcoal_database_id != NULL) {
            $column_values[self::ID_DATABASE] = $this->_charcoal_database_id;
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
                    $insert_errors[] = "Insert into table charcoal" ;
                } else {
                    $this->setIdValue(getLastCreatedId());
                }
            } else { // le charcoal existe, mise à jour
                $result = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, " id_charcoal = ".$this->getIdValue());  
                if (!$result){
                    $insert_errors[] = "Update into table charcoal failed. ID : ".$this->getIdValue();
                }
            }
        }

        if (empty($insert_errors)) {
            // on supprime déjà les lignes existante puis on créé les nouveaux enregistrements
            CharcoalQuantity::delObjectPaleofireFromId($this->getIdValue());
            foreach ($this->_list_charcoal_quantities as $charcoal_quantity) {
                $charcoal_quantity->_charcoal_id = $this->getIdValue();
                $insert_errors = $charcoal_quantity->create();
            }
        }
        
        if (empty($insert_errors)) {
            // on supprime déjà les enregistrement en base préexistant
            $result_delete = deleteIntoTableFromId("r_has_author", "ID_CHARCOAL", $this->getIdValue());
            if (!$result_delete) {
                $insert_errors[] = "Delete from table r_has_author charcoal_id : " + $this->getIdValue();
            }
            if ($this->_list_authors !=NULL) {
                foreach ($this->_list_authors as $author) {
                    // on enregistre les nouvelles données
                    $result_insert = insertIntoTable("r_has_author", array("ID_CONTACT" => $author, self::ID => $this->getIdValue()));
                    if (!$result_insert) {
                        $insert_errors[] = "Insert into table ".Contact::TABLE_NAME;
                    }
                }
            }
        }
        
        if (empty($insert_errors)) {
            // on supprime déjà les enregistrement en base préexistant
            $result_delete = deleteIntoTableFromId("r_has_pub", "ID_CHARCOAL", $this->getIdValue());
            if (!$result_delete) {
                $insert_errors[] = "Delete from table R_HAS_PUB charcoal_id : ".$this->getIdValue();
            }
            if ($this->_list_publications !=NULL) {
                foreach ($this->_list_publications as $publi) {
                    $result_insert = insertIntoTable("r_has_pub", array("ID_PUB" => $publi, self::ID => $this->getIdValue()));
                    if (!$result_insert) {
                        $insert_errors[] = "Insert into table r_has_pub";
                    }
                }
            }
        }                     
        return $insert_errors;
    }
    
    public function updateStatusID($newStatusID){
        // on vérfifie que le status existe dans la liste
        $status = Status::getNameFromStaticList($newStatusID);
        if ($status != null) {
            $ret = updateObjectWithWhereClause(self::TABLE_NAME, Array(self::ID_STATUS => $newStatusID), self::ID."=".$this->getIdValue());
            
            //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
            //writeChangeLog("edit_charcoal", $this->getIdValue());
            
            return $ret;
        }
        return false;
    }
    
    public function prepareAndSetFieldValue($field, $value) {
        parent::setFieldValue($field, $value);
    }

    public static function getDatabaseObjectID($name) {
        $result_get_Charcoal = getObjectIdFromName(self::TABLE_NAME, self::ID, self::NAME, $name);
        fetchRow($result_get_Charcoal);
        $current_id_Charcoal = getValueInResult($result_get_Charcoal, self::ID);
        return $current_id_Charcoal;
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("CHARCOAL :: ERROR TO SELECT ALL INFORMATION ABOUT CHARCOAL ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->_sample_id = $values[self::ID_SAMPLE];
            $this->_charcoal_method_id = $values[self::ID_CHARCOAL_METHOD];
            $this->_charcoal_size_id = $values[self::ID_CHARCOAL_SIZE];
            $this->_charcoal_size_value = $values[self::CHARCOAL_SIZE_VALUE];
            $this->_charcoal_status_id = $values[self::ID_STATUS];
            $this->_charcoal_database_id = $values[self::ID_DATABASE];
            $this->_charcoal_datasource_id = $values[self::ID_DATA_SOURCE];
            $this->_charcoal_charcoal_units_id = $values[self::ID_PREF_CHARCOAL_UNITS];
            $this->_charcoal_contact_id = $values[self::ID_CONTACT];

            $this->_list_charcoal_quantities = CharcoalQuantity::getObjectsPaleofireFromWhere(sql_equal(CharcoalQuantity::ID, $values[self::ID]));

            // on récupère les publications
            $query = "Select * from r_has_pub "
                    . "join t_pub on r_has_pub.id_pub = t_pub.id_pub "
                    . "where id_charcoal = " . $this->getIdValue();
            $res = queryToExecute($query, "Charcoal getListPublications");
            $tab = fetchAll($res);
            $this->_list_publications = [];
            foreach($tab as $values){
                $publi = new Publi();
                $publi->read($values);
                $this->_list_publications[] = $publi;  
            }
        
            /*$result_get_object = getFieldsFromTables("ID_PUB", "r_has_pub", sql_equal("ID_CHARCOAL", $values[self::ID]));
            if (getNumRows($result_get_object) > 0) {
                while ($tab_get_values = fetchAssoc($result_get_object)) {
                    $this->_list_publications = Publi::getObjectsPaleofireFromWhere(sql_equal(Publi::ID, $tab_get_values["ID_PUB"]));
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            }*/

            //on récupère les authors 
            $query = "SELECT * from r_has_author "
                    . "join t_contact on r_has_author.id_contact = t_contact.id_contact "
                    . "where id_charcoal = ".$this->getIdValue();
            $res = queryToExecute($query, "Charcoal getListAuthors");
            $tab = fetchAll($res);
            $this->_list_authors = [];
            if ($tab != null)
            {
                foreach($tab as $values){
                    $author = new Contact();
                    $author->read($values);
                    $this->_list_authors[] = $author;  
                }
            }
            
            /*$result_get_object = getFieldsFromTables("ID_CONTACT", "r_has_author", sql_equal("ID_CHARCOAL", $values[self::ID]));
            if (getNumRows($result_get_object) > 0) {
                while ($tab_get_values = fetchAssoc($result_get_object)) {
                    $this->_list_contributors = Contact::getObjectsPaleofireFromWhere(sql_equal(Contact::ID, $tab_get_values["ID_CONTACT"]));
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            }*/
        }
    }
    
    public static function delObjectPaleofireFromId($id){
        $id = data_securisation::toBdd($id);
        // on récupère l'objet charcoal pour connaitre les id des samples, depths....
        $charcoal = Charcoal::getObjectPaleofireFromId($id);
        if ($charcoal != null){
            beginTransaction();
            // on tente de supprimer les quantités associées
            $res = deleteIntoTableFromId('r_has_charcoal_quantity', 'ID_CHARCOAL', $id);
            // on tente de supprimer les associations aux autheurs mais pas les auteurs
            if ($res) $res = deleteIntoTableFromId('r_has_author', 'ID_CHARCOAL', $id);
            // on tente de supprimer les associations aux publications mais pas les publications
            if ($res) $res = deleteIntoTableFromId('r_has_pub', 'ID_CHARCOAL', $id);
            
            // on tente de supprimer le charcoal
            if ($res) $res = deleteIntoTableFromId(Charcoal::TABLE_NAME, Charcoal::ID, $id);
            if ($res) {
                // on rècupère le sample il ne devrait pas y avoir d'autre charcoal ou d'autre date info mais on verifie quand même
                $sample = Sample::getObjectPaleofireFromId($charcoal->_sample_id);
                if ($sample->getListCharcoals() == null && $sample->getListDatesInfo() == null){
                    // on tente de supprimer les depths et les ages estimés
                    if ($res) $res = deleteIntoTableFromId('r_has_estimated_age', 'ID_SAMPLE', $charcoal->_sample_id);
                    if ($res) $res = deleteIntoTableFromId(Depth::TABLE_NAME, Depth::ID_RELATED_SAMPLE, $charcoal->_sample_id);
                    // on tente de supprimer le sample 
                    if ($res) $res = Sample::delObjectPaleofireFromId($charcoal->_sample_id);
                }
            }

            //If no error, commit transaction !
            if ($res) {
                commit();
            } else {
                rollBack();
                logError("Deletion aborted : charcoal_id ".$id);
            }
        }
        return $res;
    }
    
    public static function delAllCharcoalsForCore($id_core){
        $ret = true;
        $charcoals = Charcoal::getCharcoalsFromCore($id_core);
        
        if($charcoals != NULL){
            foreach($charcoals as $charcoal){
                $ret = $ret && Charcoal::delObjectPaleofireFromId($charcoal->getIdValue());
            }
        } else { $ret = false; }
        return $ret;
    }
    
    public static function updateStatusAllCharcoalsForCore($id_core, $id_status){
        $ret = true;
        $charcoals = Charcoal::getCharcoalsFromCore($id_core);
        
        if($charcoals != NULL){
            foreach($charcoals as $charcoal){
                $ret = $ret && updateObjectWithWhereClause(Charcoal::TABLE_NAME, array( Charcoal::ID_STATUS => $id_status), Charcoal::ID." = ".$charcoal->getIdValue());
            }
        } else { $ret = false; }
        return $ret;
    }

}
