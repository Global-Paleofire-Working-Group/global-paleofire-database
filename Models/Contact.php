<?php
/* 
 * fichier \Models\Contact.php
 * 
 */

include_once("ObjectPaleofire.php");
require_once 'Affiliation.php';
include_once(REP_PAGES . "EDA/change_Log.php");
require_once 'Status.php';

/**
 * Class Contact
 *
 */
class Contact extends ObjectPaleofire {

    const TABLE_NAME = 't_contact';
    const ID = 'ID_CONTACT';
    const NAME = 'LASTNAME';
    const LASTNAME = 'LASTNAME';
    const FIRSTNAME = 'FIRSTNAME';
    const EMAIL = 'EMAIL';
    const ID_AFFILIATION = 'ID_AFFILIATION';
    /* TODO après confirmation suppression complète CBO 23/10/2017 Suppression du tél car inutilisé
    const PHONE = "PHONE";
    */
    const ID_STATUS = "ID_STATUS";

    public $_lastname;
    public $_firstname;
    public $_email;
    //public $_phone_number;
    public $_affiliation;
    public $_contact_id_status;
    
    protected static $_allObjectsByID = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_lastname = "";
        $this->_firstname = "";
        $this->_email = null;
        //$this->_phone_number = null;
        $this->_affiliation = null;
        $this->_contact_id_status = 0;
    }

    /** ---------------------------SETTERS------------------------- * */
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->_lastname = $name_value;
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setFirstname($_firstname) {
        $this->_firstname = $_firstname;
        $this->addOrUpdateFieldToGetId(self::FIRSTNAME, $_firstname);
    }

    public function setEmail($_email) {
        $this->_email = $_email;
        $this->addOrUpdateFieldToGetId(self::EMAIL, $_email);
    }

    /*public function setPhoneNumber($_phone_number) {
        $this->_phone_number = $_phone_number;
    }*/

    public function setAffiliation($affiliation_id) {
        $this->_affiliation = $affiliation_id;
    }
    public function setStatusId($status_id) {
        $this->_contact_id_status = $status_id;
    }
    
    /** ---------------------------GETTERS------------------------- * */
    public function getAffiliation() {
        return $this->_affiliation;
    }

    public function getEmail() {
        return $this->_email;
    }

    public function getLastName() {
        return $this->_lastname;
    }

    public function getFirstName() {
        return $this->_firstname;
    }

    /*public function getPhone() {
        return $this->_phone_number;
    }*/

    private static $_allContacts = null;
    
    public static function getAllContacts() {
        if (self::$_allContacts == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allContacts = null;
                foreach($tab_get_values as $row){
                    self::$_allContacts[$row[self::ID]] = $row;
                }
            } else {
                self::$_allContacts = NULL;
            }
        }
        return self::$_allContacts;
    }
    
        public function getStatusId() {
        return $this->_contact_id_status;
    }
    
     public function getContactparMail($email){
        return Contact::getObjectsPaleofireFromWhere(Contact::EMAIL .' = \'' . $email."'");
    }
    
    
    /** --------------------------- CRUD ------------------------- * */
    public function create() {
        $insert_errors = array();
        $contact_exists = $this->exists();
        //Si le patient existe, on l'instancie
        if (!$contact_exists) {
            //BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->getIdValue() != NULL) {
                $column_values[self::ID] = sql_varchar($this->getIdValue());
            }

            if ($this->getLastName() != NULL && $this->getLastName() != "") {
                $column_values[self::LASTNAME] = sql_varchar($this->getLastName());
            } else {
                $insert_errors[$this->getIdValue()] = "The last name of the contact can't be empty !";
            }
            if ($this->getFirstName() != NULL && $this->getFirstName() != "") {
                $column_values[self::FIRSTNAME] = sql_varchar($this->getFirstName());
            } else {
                $insert_errors[$this->getIdValue()] = "The first name of the contact can't be empty !";
            }
            if ($this->getEmail() != NULL && $this->getEmail() != "") {
                $column_values[self::EMAIL] = sql_varchar($this->getEmail());
            } else {
                $insert_errors[$this->getIdValue()] = "The email of the contact can't be empty !";
            }
            /*if ($this->getPhone() != NULL) {
                $column_values[self::PHONE] = sql_varchar($this->getPhone());
            }*/

            if ($this->getAffiliation() != NULL) {
                $insert_errors = array_merge($insert_errors, $this->getAffiliation()->create());
                if (empty($insert_errors)) {
                    $column_values[self::ID_AFFILIATION] = $this->getAffiliation()->getIdValue();
                }
            }
            else {
                $insert_errors[$this->getIdValue()] = "The affiliation of the contact can't be empty !";
            }

            if (is_numeric($this->getStatusId())) {
                $column_values[self::ID_STATUS] = $this->getStatusId();
            } else {
                $insert_errors[] = "Error : id status !";
            }
            
            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[$this->getIdValue()] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_contact", $this->getIdValue());
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
    
    public function save($Operation) {
        $insert_errors = array();
        try {
            $obj_exists = ($this->getIdValue() == null)?false:true;
        }
        catch (Exception $e) {
        }    
        //Si le patient existe, on l'instancie
        
        //BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();

        if ($this->getIdValue() != NULL) {
            $column_values[self::ID] = sql_varchar($this->getIdValue());
        }

        if ($this->getLastName() != NULL && $this->getLastName() != "") {
            $column_values[self::LASTNAME] = utf8_decode(sql_varchar($this->getLastName()));
        } else {
            $insert_errors[$this->getIdValue()] = "The last name of the contact can't be empty !";
        }
        if ($this->getFirstName() != NULL && $this->getFirstName() != "") {
            $column_values[self::FIRSTNAME] = utf8_decode(sql_varchar($this->getFirstName()));
        } else {
            $insert_errors[$this->getIdValue()] = "The first name of the contact can't be empty !";
        }
        if ($this->getEmail() != NULL && $this->getEmail() != "") {
            $column_values[self::EMAIL] = utf8_decode(sql_varchar($this->getEmail()));
        } else {
            $insert_errors[$this->getIdValue()] = "The email of the contact can't be empty !";
        }
        /*if ($this->getPhone() != NULL) {
            $column_values[self::PHONE] = sql_varchar($this->getPhone());
        }*/

        if ($this->getAffiliation() != NULL) {
            $insert_errors = array_merge($insert_errors, $this->getAffiliation()->create());
            if (empty($insert_errors)) {
                $column_values[self::ID_AFFILIATION] = $this->getAffiliation()->getIdValue();
            }
        }
         else {
            $insert_errors[$this->getIdValue()] = "The affiliation of the contact can't be empty !";
        }
        

        if (is_numeric($this->getStatusId())) {
                $column_values[self::ID_STATUS] = $this->getStatusId();
        } else {
            $insert_errors[] = "Error : id status !";
        }  
   

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
                         writeChangeLog("edit_contact", $this->getIdValue());
                     }
                } else {
                    $insert_errors[] = "This contact already exists";
                }     
           } else {
                //if ($Operation=="add") {//060407
                    // création
                    $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                    if (!$result_insert) {
                          $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                    } else {
                        $this->setIdValue(getLastCreatedId());
                        //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                        writeChangeLog("add_contact", $this->getIdValue());
                    }
               // } else {//060407
               //     $insert_errors[] = "Unknown contact";//060407
               // }      //060407
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
            throw new Exception("CONTACT :: ERROR TO SELECT ALL INFORMATION ABOUT CONTACT ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::LASTNAME]);
            $this->setFirstname($values[self::FIRSTNAME]);
            $this->setEmail($values[self::EMAIL]);
            /*$this->setPhoneNumber($values[self::PHONE]);*/
            $this->setAffiliation($values[self::ID_AFFILIATION]);
            $this->setStatusId($values[self::ID_STATUS]);
        }
    }

    public static function getAllIdName($where_clause = null) {
        $callable_class = get_called_class();
        $names = array();
        $res = getFieldsFromTables(array(self::ID, self::NAME, self::FIRSTNAME), $callable_class::TABLE_NAME, $where_clause, $callable_class::NAME);
        while ($tab = fetchAssoc($res)) {
            $names[$tab[$callable_class::ID]] = $tab[self::NAME].' '.$tab[self::FIRSTNAME];
        }
        unset($res);
        return $names;
    }
    
    public static function getMailingList(){
        $query = "SELECT LASTNAME, FIRSTNAME, EMAIL FROM `t_contact` order by LASTNAME, FIRSTNAME";
        $res = queryToExecute($query);
        if ($res != null){
            $tab = fetchAll($res);
            $tabRetour = [];
            foreach($tab as $elt){
                $tabRetour[] = Array($elt['LASTNAME'], $elt['FIRSTNAME'], $elt['EMAIL']);
            }
            return $tabRetour;
        }
        
        return null;
    }
    
    public static function getUserMailingList(){
        $query = "SELECT LASTNAME, FIRSTNAME, EMAIL FROM `t_contact` "
                . "join ". BDD_USER_USERTABLE ." on t_contact.ID_CONTACT = ". BDD_USER_USERTABLE.".ID_CONTACT "
                . "order by LASTNAME, FIRSTNAME";
        $res = queryToExecute($query);
        if ($res != null){
            $tab = fetchAll($res);
            $tabRetour = [];
            foreach($tab as $elt){
                $tabRetour[] = Array($elt['LASTNAME'], $elt['FIRSTNAME'], $elt['EMAIL']);
            }
            return $tabRetour;
        }
        
        return null;
    }
    
    public static function getListForSelect(){
        $query = "SELECT LASTNAME, FIRSTNAME, ID_CONTACT FROM `t_contact` "
                . "order by LASTNAME, FIRSTNAME";
        $res = queryToExecute($query);
        if ($res != null){
            $tab = fetchAll($res);
            /*$tabRetour = [];
            foreach($tab as $elt){
                $tabRetour[] = Array($elt['LASTNAME'], $elt['FIRSTNAME'], $elt['EMAIL']);
            }*/
            return $tab;
        }
        
        return null;
    }
}
