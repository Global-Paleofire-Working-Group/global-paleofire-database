<?php

/*
 * fichier \Models\ObjectPaleofire.php
 *
 */

include_once 'DatabaseTable.php';

/**
 *
 */
abstract class ObjectPaleofire extends DatabaseTable {

    //Value of the id of the object
    protected $_id_value;
    //Liste des champs de la table qui permettent de recuperer l'id dans la table
    private $_fields_to_get_id;
    private $_name_value;

    const TABLE_NAME = null;
    const ID = null;
    const NAME = null;

    public function __construct($table_name, $id_field_name, $_name_field_label) {
        parent::__construct($table_name, $id_field_name, $_name_field_label);
        $this->_id_value = NULL;
        $this->_fields_to_get_id = array();
        $this->_name_value = NULL;
    }

    /*     * **************************************************** SETTERS *************************************** */

    public function setIdValue($id_value) {
        $this->_id_value = $id_value;
    }

    public function setNameValue($name_value) {
        $this->_name_value = $name_value;
    }

    protected function addOrUpdateFieldToGetId($field_name, $field_value) {
        $this->_fields_to_get_id[$field_name] = $field_value;
    }

    /*     * ************************************************** GETTERS ************************************************** */

    public function getIdValue() {
        if ($this->_id_value == NULL) {
            if (empty($this->_fields_to_get_id)) {
                throw new Exception("It is impossible to get an id in database without criterias to get it !");
            }
            $where_clauses = $this->construct_whereclause($this->_fields_to_get_id);
            $id = $this->getDatabaseId($where_clauses);
            $this->_id_value = $id;
        }

        return $this->_id_value;
    }

    public function getName() {
        return $this->_name_value;
    }

    protected function getDatabaseObject() {
        //echo 'debut getDataBaseObject';
        $res = $this->getAllFieldsValuesFromId($this->_id_value);
        if (getNumRows($res) > 0) {
            $tab_values = fetchAssoc($res);
            freeResult($res);
            //echo 'fin getDataBaseObject 1';
            return $tab_values;
        } else {
            freeResult($res);
            //echo 'fin getDataBaseObject 2';
            return NULL;
        }
    }

    protected function getLinkedFields($table_name, $field_to_select) {
        $res = $this->getFieldsValuesFromId($field_to_select, $table_name, $this->_id_value);
        if (getNumRows($res) > 0) {
            $array_of_results = array();
            while ($tab_values = fetchAssoc($res)) {
                $array_of_results[] = $tab_values;
            }
            return $array_of_results;
        } else {
            return NULL;
        }
    }

    /**
     * Get the unknwon value in the table which corresponds to the class
     * @return Value of constant UNKNOWN_VALUE in the called class
     */
    public static function getUnknownValue() {
        $callable_class = get_called_class();
        return $callable_class::UNKNOWN_VALUE;
    }

    /*     * ********************* FUNCTIONS STATIC ******************************** */

    /**
     *
     * @param value $id
     * @return null|\object_paleofire
     */
    public static function getObjectPaleofireFromId($id_value) {
        $class_paleo = get_called_class();
        return self::getObjectPaleofireFromWhere(sql_equal($class_paleo::ID, $id_value));
    }

     /**
     *
     * @param value $id
     * @return null|\object_paleofire
     */
    public static function delObjectPaleofireFromId($id_value) {
        $class_paleo = get_called_class();
        return deleteIntoTableFromId($class_paleo::TABLE_NAME, $class_paleo::ID, $id_value);
    }
    /**
     *
     * @param sql where $where_clause
     * @return null|\object_paleofire
     */
    /*public static $_liste_objectPaleofire = null;
    public static function getObjectPaleofireFromFilter($filter) {
        if (self::$_liste_objectPaleofire == null){
            $class_paleo = get_called_class();
            $result_get_object = getFieldsFromTables(SQL_ALL, $class_paleo::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                foreach($tab_get_values as $row){
                    // pour l'import de données on récupère les site par le GCD_ACCESS_ID
                    $object_paleo = new $class_paleo();
                    $object_paleo->read($row);
                    self::$_allSitesByCGD_ACCESS_ID[$row[$class_paleo::ID_IMPORT]] = $object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                return NULL;
            }
        }

        return self::$_liste_objectPaleofire[$filter];
    }*/

    public static function getObjectPaleofireFromWhere($where_clause) {
            $class_paleo = get_called_class();
            $result_get_object = getFieldsFromTables(SQL_ALL, $class_paleo::TABLE_NAME, $where_clause);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAssoc($result_get_object);
                $object_paleo = new $class_paleo();
                $object_paleo->read($tab_get_values);
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
                return $object_paleo;
            } else {
                return NULL;
            }
    }

    public static function getFieldValueFromWhere($field_name, $where_clause) {
        $class_paleo = get_called_class();
        $result_get_object = getFieldsFromTables($field_name, $class_paleo::TABLE_NAME, $where_clause);
        if (getNumRows($result_get_object) > 0) {
            $tab_get_values = fetchAssoc($result_get_object);

            $result_value = $tab_get_values[$field_name];

            freeResult($result_get_object);
            unset($tab_get_values);
            unset($result_get_object);
            return $result_value;
        } else {
            return NULL;
        }
    }

    public static function getArrayFieldsValueFromWhere($fields, $where_clause = NULL) {
        $class_paleo = get_called_class();
        $result_get_object = getFieldsFromTables($fields, $class_paleo::TABLE_NAME, $where_clause);
        $result = array();
        while ($tab_get_values = fetchAssoc($result_get_object)) {
            $result[] = $tab_get_values;
        }
        freeResult($result_get_object);
        return $result;
    }

    /**
     *
     * @param sql where $where_clause
     * @return null|\array
     */
    public static function getObjectsPaleofireFromWhere($where_clause) {
        $all_objects = array();
        $class_paleo = get_called_class();
        $result_get_object = getFieldsFromTables(SQL_ALL, $class_paleo::TABLE_NAME, $where_clause);
        if (getNumRows($result_get_object) > 0) {
            while ($tab_get_values = fetchAssoc($result_get_object)) {
                $object_paleo = new $class_paleo();
                $object_paleo->read($tab_get_values);
                $all_objects[] = $object_paleo;
            }
            freeResult($result_get_object);
            unset($tab_get_values);
            unset($result_get_object);
        }
        return $all_objects;
    }

    /**
     *
     * @param sql where $where_clause
     * @return null|\array
     */
    public static function getIdsPaleofireFromWhere($field_id_name, $where_clause) {
        $all_ids = array();
        $class_paleo = get_called_class();
        $result_get_object = getFieldsFromTables($field_id_name, $class_paleo::TABLE_NAME, $where_clause);
        if (getNumRows($result_get_object) > 0) {
            while ($tab_get_values = fetchAssoc($result_get_object)) {
                $all_ids[] = $tab_get_values[$field_id_name];
            }
            freeResult($result_get_object);
            unset($tab_get_values);
            unset($result_get_object);
        }
        return $all_ids;
    }

    public static function getObjectsFromNameValue($name_value) {
        $class_paleofire = get_called_class();
        $array_objects = array();
        $res = getFieldsFromTables(SQL_ALL, $class_paleofire::TABLE_NAME, sql_equal($class_paleofire::NAME, $name_value));
        if (getNumRows($res) > 0) {
            while ($tab_values = fetchAssoc($res)) {
                $array_objects[] = $tab_values;
            }
        }
        unset($res);
        return $array_objects;
    }

    protected static function getAllIdNameFromField($field_label, $field_value) {
        $callable_class = get_called_class();
        $names = array();
        $res = getFieldsFromTables(array($callable_class::ID, $callable_class::NAME), $callable_class::TABLE_NAME, sql_equal($field_label, $field_value), $callable_class::NAME);
        while ($tab = fetchAssoc($res)) {
            $names[$tab[$callable_class::ID]] = $tab[$callable_class::NAME];
        }
        unset($res);
        return $names;
    }

    /**
     * Return the number of called class objects objects
     * @return int
     */
    public static function countPaleofireObjects($where_clause = null) {
        $class_paleofire = get_called_class();
        $result_count = getResultFunctionOnField("count", $class_paleofire::ID, "countRows", $class_paleofire::TABLE_NAME, $where_clause);

        $r = fetchArray($result_count);
        $nbResultats = $r['countRows'];
        freeResult($result_count);
        unset($r);
        unset($class_paleofire);
        return $nbResultats;
    }

    public static function getAllIdName($where_clause = null) {
        $callable_class = get_called_class();
        $names = array();
        $res = getFieldsFromTables(array($callable_class::ID, $callable_class::NAME), $callable_class::TABLE_NAME, $where_clause, $callable_class::NAME);
        while ($tab = fetchAssoc($res)) {
            $names[$tab[$callable_class::ID]] = $tab[$callable_class::NAME];
        }
        unset($res);
        return $names;
    }

    /*     * ****************** ABSTRACT FUNCTIONS ********************************* */

    abstract public function create();

    abstract public function read($values = null);

    //abstract public function modify();
    //abstract public function delete();


    /*     * ****************** DATABASE FUNCTIONS ********************************* */

    public function createOrCompleteObject($complete = true) {
        $errors = array();
        $this->instantiateFromDatabase();
        if ($this->getObjectId() == -1) {
            $errors = $this->create();
            if (empty($errors)) {
                $this->instantiateFromDatabase();
            } else {
                return $errors;
            }
        } else {
            if (!$complete) {
                throw new Exception('This object already exists in database !');
            }
        }
        return $errors;
    }

    private function instantiateFromDatabase() {
        $where_condition = " WHERE ";
        $i = 0;
        foreach ($this->getFields() as $field_name) {
            $value = $this->getObjectFieldValue($field_name);
            if ($value != NULL) {
                if ($i > 0)
                    $where_condition.=" AND ";
                if (is_numeric($value)) {
                    $where_condition.=$field_name . "=" . $value . "";
                } else {
                    $where_condition.=$field_name . "='" . $value . "'";
                }
                $i++;
            }
        }
        $sql = "SELECT * FROM " . $this->getTableName();
        if ($where_condition != " WHERE ") {
            $sql.=$where_condition;
        }
        $result_get_values = queryToExecute($sql, "get all values of current object");
        $tab_values = array();
        if (fetchRow($result_get_values)) {
            foreach ($this->getFields() as $field_name) {
                $tab_values[$field_name] = getValueInResult($result_get_values, $field_name);
            }
        }
        if ($tab_values != NULL && !empty($tab_values)) {
            $this->setArrayValues($tab_values);
        }
    }

    protected static function objectExistsInDatabase($table_name, $field_name, $id_value) {
        $result_exists = getAllObjectFromId($table_name, $field_name, $id_value);
        $res = fetchRow($result_exists);
        $current_id = getValueInResult($result_exists, $field_name);
        if (isset($current_id) && $current_id != NULL) {
            return true;
        }
        return false;
    }

    public static function idExistsInDatabase($id) {
        $retour = false;
        if (is_numeric($id)){ // si le code n'est pas numéric c'est peut être une tentative d'injection
            $class = get_called_class();
            $query = "SELECT " . $class::ID;
            $query .= " FROM " . $class::TABLE_NAME;
            $query .= " WHERE " . $class::ID . " = " . $id;

            $result = queryToExecute($query, "$query -");
            // on ne devrait pas avoir plus d'une ligne
            if ($result->num_rows == 1) $retour = true;
        }
        return $retour;
    }

    /*     * ****************** OTHERS FUNCTIONS ********************************* */

    public function toString() {
        return $this->getName();
    }

    public function getDetails() {
        echo "<table>";
        echo "<tr><td colspan='2'>Details:</tr></td>";
        echo "<tr><td>FIELD NAME</td>";
        echo "<td>Value</tr></td>";
        foreach ($this->getFields() as $field) {
            echo "<tr><td>" . $field . "</td>";
            echo "<td>" . $this->getObjectFieldValue($field) . "</tr></td>";
        }
        echo "</table>";
    }

    public function isDefined() {
        if (empty($this->_field_values)) {
            return false;
        }
        return true;
    }

    /*     * ************************************** PALEOFIRE OBJECT FUNCTIONS ********************************************** */

    protected function exists() {
        $where_clauses = $this->construct_whereclause($this->_fields_to_get_id);
        $id = $this->getDatabaseId($where_clauses);
        if (!$id || $id == NULL) {
            return false;
        }
        return true;
    }

    /**
     * Cette fonction transforme un tableau de champ/valeur en tableau de champ=valeur pour la clause where
     */
    protected function construct_whereclause($array_where) {
        $where_clauses = array();
        foreach ($array_where as $field_name => $field_value) {
            $callable_class = get_called_class();
            if ($callable_class::NAME == $field_name) {
                $where_clauses[] = $field_name . " = '" . addslashes($field_value)."'";
            } else {
                $where_clauses[] = sql_equal($field_name, $field_value);
            }

            unset($field_name);
            unset($field_value);
            unset($where);
        }
        return $where_clauses;
    }

    protected function getSetValues() {
        $array_values = array();
        $array_values[] = "";
        $array_values[] = $this->_name_value;
        return array_values;
    }


    protected static $_allObjectsByID = null;
    public static function getStaticList() {

        $called_class = get_called_class();
        if ($called_class::$_allObjectsByID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, $called_class::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                $called_class::$_allObjectsByID = null;
                foreach($tab_get_values as $row){
                    $called_class::$_allObjectsByID[$row[$called_class::ID]] = $row;
                }
            } else {
                $called_class::$_allObjectsByID = NULL;
            }
        }
        return $called_class::$_allObjectsByID;
    }

    public static function getObjectFromStaticList($key){
        $called_class = get_called_class();
        $list = $called_class::getStaticList();
        //$list = self::getStaticList();
        if ($key != null && key_exists($key, $list)) {
            $called_class = get_called_class();
            $obj = new $called_class();
            $obj->read($list[$key]);
            return $obj;
        }
        else return null;
    }

    public static function getNameFromStaticList($key){
        $called_class = get_called_class();
        $list = $called_class::getStaticList();
        //$list = self::getStaticList();
        if ($key != null && key_exists($key, $list)) return $list[$key][$called_class::NAME];
        else return null;
    }

    public static function getUnitFromStaticList($key){
        $called_class = get_called_class();
        $list = $called_class::getStaticList();
        //$list = self::getStaticList();
        if ($key != null && key_exists($key, $list)) return $list[$key][$called_class::UNIT];
        else return null;
    }


    public static function updateStaticList(){
        self::$_allObjectsByID = null;
    }
}
