<?php

/* 
 * fichier \Models\user\WebAppUserGCD.php
 * 
 */

// RM ce fichier etait date du Dec 1 17:24

include_once(REP_LIB . "WebAppUser/WebAppUser.php");

class WebAppUserGCD extends WebAppUser {

    const TABLE_NAME = BDD_USER_USERTABLE; //xli 01/06
 
    //const TABLE_NAME = 'gcd_paleofire_webapp.paleofire_user';
    //const TABLE_NAME = 'paleofire_user';
    const LOGINFIELD = "WEBAPP_LOGIN";
    const IDENTIFIER = "ID_USER";
    const PASSWORDFIELD = "WEBAPP_PSWD";
    const ID_ROLE = "ID_ROLE";
    const ID_CONTACT = "ID_CONTACT";

    /**
     * Role of the user : class WebAppRole
     * */
    public $_webAppUser_role;
    public $_webAppUser_id;
    public $_webAppContact_id;


    public function __construct($login, $password, $id_role = NULL, $id_contact = NULL) {
        parent::__construct($login, $password);
        $this->_webAppUser_role = $id_role;
        $this->_webAppContact_id = $id_contact;
    }

    /**
     * @param $role_name password of the webAppUser
     */
    public function setWebAppUserRole($id_role) {
        $this->_webAppUser_role = $id_role;
    }

    public function getWebAppUserRole() {
        if ($this->_webAppUser_role == NULL) {
            $result_get_role = getFieldsFromTables(WebAppRoleGCD::NAMEFIELD, WebAppRoleGCD::TABLE_NAME, sql_equal(WebAppRoleGCD::IDENTIFIER, $this->getRoleUserId()));
            if (getNumRows($result_get_role) > 0) {
                $tab_get_role = fetchAssoc($result_get_role);
                $this->setWebAppUserRole($tab_get_role[WebAppRoleGCD::NAMEFIELD]);
                unset($tab_get_role);
                freeResult($result_get_role);
            }
        }
        return $this->_webAppUser_role;
    }
    
    public function getWebAppUserID() {
        return $this->_webAppUser_id;
    }
    
    public function setWebAppUserPassword($password_value) {
        $this->_wepAppUser_password = $password_value;
    }
    
    protected function loginExistsInDataBase($login){
        $result_exist_object = getFieldOfObjectFromFieldValue(self::LOGINFIELD, self::TABLE_NAME, self::LOGINFIELD, $login);
        if (getNumRows($result_exist_object) > 0) {
            return true;
        }
        return false;
    }

    public static function loginExists($login){
        $result_exist_object = getFieldsFromTables(self::LOGINFIELD, self::TABLE_NAME, sql_equal(self::LOGINFIELD,$login));
        if (getNumRows($result_exist_object) > 0) {
            return true;
        }
        return false;
    }
    
    public function checkPassword() {
        if (strtolower($this->getWebAppUserLogin()) == "visitor") {
            $clauses_where = array(
                self::LOGINFIELD . " = \"" . $this->getWebAppUserLogin() . "\"",
            );
        } else {
            $clauses_where = array(
                self::PASSWORDFIELD . " = " . database_encrypt_password(utf8_decode($this->getWebAppUserPassword())),
                self::LOGINFIELD . " = \"" . utf8_decode($this->getWebAppUserLogin()) . "\"",
            );
        }
        //$result_get_password = getFieldsFromTables(self::PASSWORDFIELD, self::TABLE_NAME, $clauses_where);
        // 11/08/15 CBU // autant récupérer toutes les infos de l'utilisateur au même moment
        // et pas besoin de ramener le mot de passe on le connait et il est déjà testé
        $result_get_password = getFieldsFromTables(self::ID_ROLE. ", " . self::IDENTIFIER, self::TABLE_NAME, $clauses_where);
        if (getNumRows($result_get_password) > 0) {
            $res = fetchAssoc($result_get_password);
            $this->_webAppUser_role = $res[self::ID_ROLE];
            $this->_webAppUser_id = $res[self::IDENTIFIER];
            return true;
        }
        return false;
    }

    protected function createInDatabase($login_value, $encrypted_password, $id_role) {
        $result = insertIntoTableAllValues(self::TABLE_NAME, array("", $login_value, database_encrypt_password($encrypted_password), "", $id_role));
    }

    
    public static function delete($id_user){
        $result = deleteIntoTableFromId(self::TABLE_NAME, self::IDENTIFIER, $id_user);
        return $result;
    }

    public function getDatabaseId() {
        $result_get_id = getFieldsFromTables(self::IDENTIFIER, self::TABLE_NAME, sql_equal(self::LOGINFIELD, $this->getWebAppUserLogin()));
        if (getNumRows($result_get_id) > 0) {
            $tab_get_id = fetchAssoc($result_get_id);
            freeResult($result_get_id);
            return $tab_get_id[self::IDENTIFIER];
        }
        return NULL;
    }
    
    public static function getContactId($user_id) {
        $result_get_id = getFieldsFromTables(self::ID_CONTACT, self::TABLE_NAME, sql_equal(self::IDENTIFIER, $user_id));
        if (getNumRows($result_get_id) > 0) {
            $tab_get_id = fetchAssoc($result_get_id);
            freeResult($result_get_id);
            return $tab_get_id[self::ID_CONTACT];
        }
        return NULL;
    }

    public static function getDatabaseIdFromLogin($login) {
        $result_get_id = getFieldsFromTables(self::IDENTIFIER, self::TABLE_NAME, sql_equal(self::LOGINFIELD, $login));
        if (getNumRows($result_get_id) > 0) {
            $tab_get_id = fetchAssoc($result_get_id);
            freeResult($result_get_id);
            return $tab_get_id[self::IDENTIFIER];
        }
        return NULL;
    }


    public function getRoleNumber() {
        if (isset($this->_webAppUser_role)){
            return $this->_webAppUser_role;
        } else {
            $user_role = $this->getWebAppUserRole();
            if ($user_role == NULL) {
                return WebAppRoleGCD::VISITOR;
            } else {
                return $user_role->getWebAppRoleNumber();
            }
        }
    }

    protected function getRoleUserId() {
        if (isset($this->_webAppUser_id)){
            return $this->_webAppUser_id;
        } else {
            $result_get_id = getFieldsFromTables(self::ID_ROLE, self::TABLE_NAME, sql_equal(self::LOGINFIELD, $this->getWebAppUserLogin()));
            if (getNumRows($result_get_id) > 0) {
                $tab_get_id = fetchAssoc($result_get_id);
                freeResult($result_get_id);
                return $tab_get_id[self::ID_ROLE];
            }
            return NULL;
        }
    }

    public static function instantiate($id_user) {
        $user = NULL;
        $result_get_user = getFieldsFromTables(SQL_ALL, self::TABLE_NAME, sql_equal(self::IDENTIFIER, $id_user));
        $request_values = fetchAssoc($result_get_user);
        //freeResult($request_values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("WEBAPPUSEREA :: ERROR TO SELECT ALL INFORMATION ABOUT WEBAPPUSEREA ID : " . $id_user);
        } else {
            $user = new WebAppUserGCD($request_values[self::LOGINFIELD], null/*$request_values[self::PASSWORDFIELD]*/, $request_values[self::ID_ROLE], $request_values[self::ID_CONTACT]); // MOD RM
            $user->_webAppUser_id = $id_user;
        }


        return $user;
    }

    public function canRead() {
        return true;
    }

    public function canModify() {
        return true;
    }

    public function canDelete() {
        return true;
    }

    public static function getAll() {
        $result = getAllObjectsInTable(self::TABLE_NAME, null, null);
        if (!$result) {
            return NULL;
        } else {
            $array_object = array();
            while ($tab_values = fetchAssoc($result)) {
                $obj = new WebAppUserGCD($tab_values[self::LOGINFIELD], $tab_values[self::PASSWORDFIELD], $tab_values[self::ID_ROLE], $tab_values[self::ID_CONTACT]);
                $obj->_webAppUser_id = $tab_values[self::IDENTIFIER];
                $array_object[] = $obj;
            }
            return $array_object;
        }
    }
   
    /*retourne la liste des users correspondant au numéro de contact fourni en paramètre*/
    public static function getListUserForAContact($contact_id){
        $result = getAllObjectsInTable(self::TABLE_NAME, self::ID_CONTACT." = ". $contact_id, null);
        if (!$result) {
            return NULL;
        } else {
            $array_object = array();
            while ($tab_values = fetchAssoc($result)) {
            $obj = new WebAppUserGCD($tab_values[self::LOGINFIELD], $tab_values[self::PASSWORDFIELD], $tab_values[self::ID_ROLE], $tab_values[self::ID_CONTACT]);
                $array_object[] = $obj;
            }
            return $array_object;
        }
    }
    
    
     /*retourne la liste des users correspondant au rôle fourni en paramètre */   
    public static function getListUserForARole($role){
        $result = getAllObjectsInTable(self::TABLE_NAME, self::ID_ROLE ." = ". $role, null);
        if (!$result) {
            return NULL;
        } else {
            $array_object = array();
            while ($tab_values = fetchAssoc($result)) {
            $obj = new WebAppUserGCD($tab_values[self::LOGINFIELD], $tab_values[self::PASSWORDFIELD], $tab_values[self::ID_ROLE], $tab_values[self::ID_CONTACT]);
                $array_object[] = $obj;
            }
            return $array_object;
        }
    }
    
    /*retourne les users correspondant à la chaine de recherche fournie en paramètre */   
    public static function searchUser($search){
        $result = getAllObjectsInTable(self::TABLE_NAME, self::LOGINFIELD ." LIKE ". $search, null);
        if (!$result) {
            return NULL;
        } else {
            $array_object = array();
            while ($tab_values = fetchAssoc($result)) {
                $array_object[$tab_values[self::IDENTIFIER]] = [$tab_values[self::LOGINFIELD],$tab_values[self::ID_CONTACT]];
            }
            return $array_object;
        }
    }
    
    public function save() {
        $insert_errors = array();
        $obj_exists = ($this->_webAppUser_id == null)?false:true;
        //BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();

        if ($this->_webAppUser_id != NULL) {
            $column_values[self::IDENTIFIER] = sql_varchar($this->_webAppUser_id);
        }

        if ($this->getWebAppUserLogin() != NULL && $this->getWebAppUserLogin() != "") {
            $column_values[self::LOGINFIELD] = utf8_decode(sql_varchar($this->getWebAppUserLogin()));
        } else {
            $insert_errors[$this->_webAppUser_id] = "The login of the user can't be empty !";
        }
        if (($this->getWebAppUserPassword() != NULL && $this->getWebAppUserPassword() != "")) {
            $column_values[self::PASSWORDFIELD] = database_encrypt_password(utf8_decode($this->getWebAppUserPassword()));
        } else if ($obj_exists && $this->getWebAppUserPassword() == NULL){
            // on fait une mise à jour de l'utilisateur mais pas du password donc on ne touche pas au password
        } else {
            $insert_errors[$this->_webAppUser_id] = "The password of the user can't be empty !";
        }
        if ($this->getWebAppUserRole() != NULL && $this->getWebAppUserRole() != "") {
            $column_values[self::ID_ROLE] = sql_varchar($this->getWebAppUserRole());
        } else {
            $insert_errors[$this->_webAppUser_id] = "The role of the user can't be empty !";
        }
        if ($this->_webAppContact_id != NULL && $this->_webAppContact_id != "") {
            $column_values[self::ID_CONTACT] = sql_varchar($this->_webAppContact_id);
        }

// RM
//var_dump($column_values);
//$insert_errors[] = 'This feature is shutdown for maintenance contact us to make modifications.';
//return $insert_errors;
// RM

        if (empty($insert_errors)) {
           if ($obj_exists == true){
               // mise a jour
               $res = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::IDENTIFIER . "=" . $this->_webAppUser_id);
               if (!$res) {
                   $insert_errors[] = "Update table " . self::TABLE_NAME;
               }
           } else {
               // creation
               $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
               if (!$result_insert) {
                   $insert_errors[] = "Insert into table " . self::TABLE_NAME;
               } else {
                   $this->_webAppUser_id = getLastCreatedId();
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
    
}
