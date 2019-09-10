<?php
/* 
 * fichier \Models\Affiliation.php
 * 
 */

include_once("ObjectPaleofire.php");
include_once(REP_PAGES . "EDA/change_Log.php");
require_once 'Status.php';


class Affiliation extends ObjectPaleofire {

    const TABLE_NAME = 't_affiliation';
    const ID = 'ID_AFFILIATION';
    const NAME = 'AFFILIATION_NAME';
    const ADDRESS1 = "ADDRESS1";
    const ADDRESS2 = "ADDRESS2";
    const CITY = "CITY";
    const STATE_PROV = "STATE_PROV";
    const STATE_PROV_CODE = "STATE_PROV_CODE";
    const ID_COUNTRY = "ID_COUNTRY";
    const ID_STATUS = "ID_STATUS";

    public $_affiliation_address1;
    public $_affiliation_address2;
    public $_affiliation_city;
    public $_affiliation_state_prov;
    public $_affiliation_state_prov_code;
    public $_affiliation_id_country;
    public $_affiliation_id_status;

    protected static $_allObjectsByID = null;
    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_affiliation_address1 = "";
        $this->_affiliation_address2 = "";
        $this->_affiliation_city = "";
        $this->_affiliation_state_prov = "";
        $this->_affiliation_state_prov_code = "";
        $this->_affiliation_id_country = NULL;
        $this->_affiliation_id_status = 0;
    }

    /** ---------------------------SETTERS------------------------- * */
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setAddress1($address1_value) {
        $this->_affiliation_address1 = $address1_value;
    }

    public function setAddress2($address2_value) {
        $this->_affiliation_address2 = $address2_value;
    }

    public function setCity($city_value) {
        $this->_affiliation_city = $city_value;
    }

    public function setStateProv($state_prov_value) {
        $this->_affiliation_state_prov = $state_prov_value;
    }

    public function setStateProvCode($state_prov_code_value) {
        $this->_affiliation_state_prov_code = $state_prov_code_value;
    }

    public function setCountryId($country_id) {
        $this->_affiliation_id_country = $country_id;
    }

    public function setStatusId($status_id) {
        $this->_affiliation_id_status = $status_id;
    }
    
    /** ---------------------------GETTERS------------------------- * */
    public function getAddress1() {
        return $this->_affiliation_address1;
    }

    public function getAddress2() {
        return $this->_affiliation_address2;
    }

    public function getCity() {
        return $this->_affiliation_city;
    }

    public function getStateProv() {
        return $this->_affiliation_state_prov;
    }

    public function getStateProvCode() {
        return $this->_affiliation_state_prov_code;
    }

    public function getCountryId() {
        return $this->_affiliation_id_country;
    }  
    
     public function getStatusId() {
        return $this->_affiliation_id_status;
    }
    
    public function getContacts(){
        return Contact::getObjectsPaleofireFromWhere(Contact::ID_AFFILIATION .' = ' . $this->getIDValue());
    }
    /** ---------------------------CRUD------------------------- * */
    public function create() {
        $insert_errors = array();
        $object_exists = $this->exists();
        //Si le patient existe, on l'instancie
        if (!$object_exists) {
            //BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();


            if ($this->getName() != NULL && $this->getName() != "") {
                $column_values[self::NAME] = sql_varchar($this->getName());
            } else {
                $insert_errors[] = "The name of the affiliation can't be empty !";
            }
            if ($this->getCity() != NULL && $this->getCity() != "") {
                $column_values[self::CITY] = sql_varchar($this->getCity());
            } else {
                $insert_errors[] = "The city of the affiliation can't be empty !";
            }

            if ($this->getAddress1() != NULL) {
                $column_values[self::ADDRESS1] = sql_varchar(addslashes($this->getAddress1()));
            }
            if ($this->getAddress2() != NULL) {
                $column_values[self::ADDRESS2] = sql_varchar(addslashes($this->getAddress2()));
            }
            if ($this->getStateProv() != NULL) {
                $column_values[self::STATE_PROV] = sql_varchar($this->getStateProv());
            }
            if ($this->getStateProvCode() != NULL) {
                $column_values[self::STATE_PROV_CODE] = sql_varchar($this->getStateProvCode());
            }

            if ($this->getCountryId() != NULL) {
                $column_values[self::ID_COUNTRY] = $this->getCountryId();
            }
            
            if ($this->affiliation_status_id != NULL) {
                 $column_values[self::ID_STATUS] = $this->getStatusId();                     
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_affiliation", $this->getIdValue());
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

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("AFFILIATION :: ERROR TO SELECT ALL INFORMATION ABOUT AFFILIATION ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setAddress1($values[self::ADDRESS1]);
            $this->setAddress2($values[self::ADDRESS2]);
            $this->setCity($values[self::CITY]);
            $this->setStateProv($values[self::STATE_PROV]);
            $this->setStateProvCode($values[self::STATE_PROV_CODE]);
            $this->setCountryId($values[self::ID_COUNTRY]);
            $this->setStatusId($values[self::ID_STATUS]);
        }
    }

    public static function getUnknownAffiliation() {
        return self::getObjectPaleofireFromWhere(sql_equal(Affiliation::NAME, "UNKNOWN AFFILIATION"));
    }

    /*
     * Création d'un enregistrement d'une carotte en base de données
     */
    public function save($Operation) {
        $insert_errors = array();
         try {
        $obj_exists = ($this->getIdValue() == null)?false:true;
         }
        catch (Exception $e) {
        }
        $column_values = array();

        if ($this->getName() != NULL) {
            $column_values[self::NAME] = sql_varchar($this->getName());
        } else {
            $insert_errors[] = "The name can't be empty !";
        }

        if ($this->getCity() != NULL) {
            $column_values[self::CITY] = sql_varchar($this->getCity());
        } else {
            $insert_errors[] = "The city can't be empty !";
        }

        if ($this->getCountryId() != NULL) {
            if (is_numeric($this->getCountryId())) {
                $column_values[self::ID_COUNTRY] = $this->getCountryId();
            } else {
                $insert_errors[] = "The id country must be numeric !";
            }
        } else {
            $insert_errors[] = "The country can't be empty !";
        }
        
        if (is_numeric($this->getStatusId())) {
            $column_values[self::ID_STATUS] = $this->getStatusId();
        } else {
            $insert_errors[] = "Error : id status !";
        }
        
   
        $column_values[self::ADDRESS1] = sql_varchar($this->getAddress1());
        $column_values[self::ADDRESS2] = sql_varchar($this->getAddress2());
        $column_values[self::STATE_PROV] = sql_varchar($this->getStateProv());
        $column_values[self::STATE_PROV_CODE] = sql_varchar($this->getStateProvCode());
                   

        if (empty($insert_errors)) {
            if ($obj_exists == true){
                if (($Operation=="edit")||($Operation=="edit_pending")) {
                    // mise à jour
                    $res = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID . "=" . $this->getIdValue());
                    if (!$res) {
                        $insert_errors[] = "Update table " . self::TABLE_NAME;
                    }
                    else {
                        //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                        writeChangeLog("edit_affiliation", $this->getIdValue());
                    }
                } else {
                    $insert_errors[] = "This affiliation already exists";
                }
              
            } else {
                if ($Operation=="add") {
                    // création
                    $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                    if (!$result_insert) {
                        $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                    } else {
                        $this->setIdValue(getLastCreatedId());
                        //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                        writeChangeLog("add_affiliation", $this->getIdValue());
                    }
                } else {
                    $insert_errors[] = "Unknow affiliation";
                }    
            }
        }
        return $insert_errors;
    }
}
