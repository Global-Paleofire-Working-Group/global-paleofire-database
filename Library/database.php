<?php

define('SQL_ALL', "*");

/* * ********** LISTE DES FONCTIONS POUR l'EXECUTION OU LE TRAITEMENT DU RESULTAT D'UNE REQUETE ************** */

/**
 *
 * */
function queryToExecute($query, $msg = "") {
    //var_dump($query);
    global $bdd_gcd;
    $result = $bdd_gcd->query(utf8_encode($query));
    if (!$result) {
        logError("queryToExecute => " . $msg . "- $query - (" . $bdd_gcd->errno . ") " . $bdd_gcd->error);
    }/*else {
        logAction("queryToExecute => " . $msg . "- $query - ");
      } */
    //var_dump($result->fetch_fields());
    return $result;
}

/**
 *
 * */
function getNumRows($result) {
    $number = 0;
    if (isset($result) && $result) {
        $number = $result->num_rows;
    }
    return $number;
}

/**
 *
 * */
function freeResult($result) {
    if (isset($result) && $result) {
        $result->free_result();
    }
}

/**
 *
 * */
function fetchAssoc($queryResult) {

    if (isset($queryResult) && $queryResult) {
        return $queryResult->fetch_assoc();
    }
    return NULL;
}

/**
 *
 * */
function fetchObject($queryResult) {

    if (isset($queryResult) && $queryResult) {
        return $queryResult->fetch_object();
    }
    return NULL;
}

/**
 *
 * */
function fetchRow($queryResult) {

    if (isset($queryResult) && $queryResult) {
        return $queryResult->fetch_row();
    }
    return NULL;
}

/**
 *
 * */
function fetchArray($queryResult) {

    if (isset($queryResult) && $queryResult) {
        return $queryResult->fetch_array();
    }
    return NULL;
}

/**
 *
 * */
function fetchAll($queryResult) {
    if (isset($queryResult) && $queryResult) {
        //$res = $queryResult->fetch_all(MYSQLI_ASSOC);
        for ($res = array(); $tmp = fetchAssoc($queryResult);) $res[] = $tmp;
        return $res;
    }
    return NULL;
}

function beginTransaction() {
    global $bdd_gcd;
    $result = $bdd_gcd->autocommit(FALSE);
    if (!$result) {
        logError("beginTransaction");
    }
}

function rollBack() {
    global $bdd_gcd;
    $result = $bdd_gcd->rollback();
    if (!$result) {
        logError("rollBack");
    }
}

function commit() {
    global $bdd_gcd;
    $result = $bdd_gcd->commit();
    if (!$result) {
        logError("commit");
    } 
}

function getLastCreatedId() {
    global $bdd_gcd;
    return $bdd_gcd->insert_id;
}

/* * *********************************** LISTE DES REQUETES GENERIQUES ************************************* */

/*
  function sanitize_string($str) {
  if (get_magic_quotes_gpc()) {
  $sanitize = mysqli_real_escape_string(stripslashes($str));
  } else {
  $sanitize = mysqli_real_escape_string($str);
  }
  return $sanitize;
  }

 */

/**
 * Recupere l'ensemble des objets dans la table donnée en paramètre
 * @param $table_name nom de la table pour laquelle effectuer le SELECT ALL
 * @return bool|\mysqli_result
 * @result résultat de la requête SELECT ALL sur la table
 */
function getAllObjectsInTable($table_name, $where_clause = null, $order_by = null) {
    $query = "SELECT * FROM $table_name ";
    $query.= whereClause($where_clause);
    if ($order_by != null) $query .= " ORDER BY " . $order_by;
    $query.= ";";
    //var_dump($query);
    return queryToExecute($query, "SELECT ALL $table_name -");
}

/**
 * Recupere l'ensemble des objets dans la table donnée en paramètre ordonné par le champ en paramétre
 * @param $table_name nom de la table pour laquelle effectuer le SELECT ALL
 * @param $field_name nom du fichier pour le tri
 * @return bool|\mysqli_result
 * @result résultat de la requête SELECT ALL sur la table
 */
function getAllOrderedObjectsInTable($table_name, $field_name) {
    $query = "SELECT * FROM $table_name ORDER BY $field_name;";
    return queryToExecute($query, "SELECT ALL $table_name ORDER BY $field_name -");
}

function getAllObjectsOrderedWithLimits($field_values, $table_name, $order_by, $begin, $end, $where_clause = null) {
    $query = "SELECT ".$field_values." FROM $table_name ";
    $query.= whereClause($where_clause);
    $query.=" ORDER BY $order_by ASC LIMIT $begin, $end;";
    return queryToExecute($query, "SELECT * FROM $table_name ORDER BY $order_by WITH LIMITS -");
}

/**
 * Recupere l'ensemble des objets dans la table donnée en paramètre
 * @param $table : nom de la table pour laquelle effectuer le SELECT ALL
 * @param $field_name : nom du champ utilisé pour l'ordonnancement du résultat de la requête
 * @return bool|\mysqli_result
 * @result résultat de la requête SELECT ALL sur la table
 * */
function getAllObjectsInTableOrderByField($table_name, $field_name) {
    $query = "SELECT * FROM $table_name ORDER BY $field_name;";
    return queryToExecute($query, "SELECT ALL $table_name ORDER BY $field_name -");
}

/**
 * Recupere l'ensemble des objets dans la table donnée en paramètre
 * @param $table : nom de la table pour laquelle effectuer le SELECT ALL
 * @param $field_name : nom du champ utilisé pour l'ordonnancement du résultat de la requête
 * @return bool|\mysqli_result
 * @result résultat de la requête SELECT ALL sur la table
 * */
function getAllDistinctObjectsInTableOrderByField($table_name, $field_name) {
    $query = "SELECT DISTINCT $field_name FROM $table_name ORDER BY $field_name;";
    return queryToExecute($query, "SELECT ALL $table_name ORDER BY $field_name -");
}

/**
 *
 * */
function getObjectIdFromName($table, $id, $field_name, $name) {
    $query = "SELECT $id FROM $table WHERE $field_name = '$name';";
    return queryToExecute($query, "SELECT ID FROM NAME -");
}

/**
 *
 * Get all object which have the code given in parameter
 *
 * @param    $table_name name of the table
 * @param    $field_code label of the field code
 * @param    $object_code code of the object
 * @return   Result of the Select ALL
 *
 */
function getAllObjectFromId($table_name, $field_code, $object_code) {
    $query = "SELECT * FROM $table_name WHERE ";
    if ($object_code != NULL && $object_code != "NULL") {
        if (isset($object_code) && is_numeric($object_code)) {
            $query.="$field_code = $object_code;";
        } else {
            $query.="$field_code = '$object_code';";
        }
    } else {
        $query.="$field_code IS NULL;";
    }
    return queryToExecute($query, "SELECT ALL $table_name FROM $field_code -");
}

/**
 *
 * */
function getResultFunctionOnField($function, $field, $as, $table_name, $where_clause = null) {
    $query = "SELECT $function($field) as $as FROM $table_name ";
    $query.= whereClause($where_clause);
    $query.= ";";
    return queryToExecute($query, "SELECT $function($field) as $as FROM $table_name; -");
}

/**
 *
 * Insert into values into a table
 *
 * @param    $table_name : name of the table
 * @param    $values : array of values to insert
 * @return   Result of the SQL Query INSERT INTO
 *
 */
function insertIntoTableFieldsValues($table_name, $fields, $values) {
    $clause_values = "";
    $clause_fields = "";

    foreach ($values as $value) {
        if (empty($value)) {
            $clause_values.="\"\"";
        } else {
            if (is_numeric($value)) {
                $clause_values.=$value;
            } else {
                $clause_values.="'" . $value . "'";
            }
        }
        $clause_values.=",";
    }
    foreach ($fields as $field_name) {
        $clause_fields.=$field_name;
        $clause_fields.=",";
    }
    $length = strlen($clause_values);
    $clause_values = substr($clause_values, 0, $length - 1);
    unset($length);
    $length = strlen($clause_fields);
    $clause_fields = substr($clause_fields, 0, $length - 1);
    unset($length);

    $query = "INSERT INTO $table_name (" . $clause_fields . ") VALUES (" . $clause_values . ")";
    return queryToExecute($query, "Insertion en BdD dans la table " . $table_name . " des valeurs ( " . $clause_values . ") pour les champs (" . $clause_fields . ")");
}

/**
 *
 * Insert into values into a table
 *
 * @param    $table_name : name of the table
 * @param    $values : array of values of all fields to insert
 * @return   Result of the SQL Query INSERT INTO
 *
 */
function insertIntoTableAllValues($table_name, $values) {
    $clause_values = "";
    foreach ($values as $value) {
        if (empty($value)) {
            $clause_values.="\"\"";
        } else {
            $clause_values.=$value;
        }
        $clause_values.=",";
    }
    $length = strlen($clause_values);
    $clause_values = substr($clause_values, 0, $length - 1);
    unset($length);
    $query = "INSERT INTO $table_name VALUES ($clause_values)";
    return queryToExecute($query, "INSERT INTO $table_name VALUES ($clause_values) -");
}

/**
 * Recupere l'ensemble des objets selon les paramètres 
 * @param $table_name nom de la table pour laquelle effectuer le SELECT ALL
 * @return bool|\mysqli_result
 * @result résultat de la requête SELECT ALL sur la table
 */
function getFieldsFromTables($fields, $table_names, $where_clauses = NULL, $array_order_by = NULL) {

    $query = "SELECT ";

    $query.= listOfFields($fields);

    $query.= fromClause($table_names);

    $query.= whereClause($where_clauses);

    $query.= orderByClause($array_order_by);

    $query.= ";";
    
    return queryToExecute($query, "$query -");
}

function deleteIntoTableFromId($table_name, $id_column, $id_value){
    $query = "DELETE FROM " . $table_name . " WHERE " . $id_column . " = " . $id_value;
    $result = queryToExecute($query, "DELETE FROM " . $table_name);
    return ($result);
}

function insertIntoTable($table_name, $column_values = array()) {
    //INSERT INTO table_name (column1, column2, column3,...) VALUES (value1, value2, value3,...)
    if (empty($column_values)) {
        $query = "INSERT INTO $table_name";
    } else {
        $field_labels = array();
        $clause_values = array();
        foreach ($column_values as $column_name => $value) {
            $field_labels[] = $column_name;
            $clause_values[] = $value;
        }
        $query = "INSERT INTO $table_name (" . implode(',', $field_labels) . ") VALUES (" . implode(',', $clause_values) . ");";
        
        unset($field_labels);
        unset($clause_values);
    }
    $result = queryToExecute($query, "INSERT INTO $table_name -");
    //logAction($query);
    return $result;
}

function escapeString($str){
    global $bdd_gcd;
    return $bdd_gcd->real_escape_string($str);
}
/**
 *
 * Update values of each field contains in $sets for the object referred by $where_field_label=$where_field_value
 *
 * @param    $table_name name of the table
 * @param    $sets set of values to update array (field_label => newValue, ...)
 * @return   Result of the UPDATE
 *
 */
function updateObjectWithWhereClause($table_name, $sets, $where_clause) {
    $updates = "";
    foreach ($sets as $field => $value) {
        $updates.=$field;
        $updates.=" = ";
        
        if (is_null($value))
            $updates.= "NULL";
        else
            $updates.=$value;
        $updates.=", ";
    }
    $length = strlen($updates);
    $updates = substr($updates, 0, $length - 2);
    unset($field);
    unset($value);

    $query = "UPDATE $table_name SET $updates ";
    $query.= whereClause($where_clause);
    $query.= ";";
    //echo'query='.$query;
    return queryToExecute($query, "UPDATE $table_name -");
}

/* * ***************************** METHODE LISTE OBJETS SQL **************************************** */

//Cette fonction retourne la liste des champs du tableau sous forme SQL
function listOfFields($fields_to_select) {
    $select = " ";
    if ($fields_to_select != NULL) {
        if (is_array($fields_to_select)) {
            foreach ($fields_to_select as $field) {
                $select.= $field;
                $select.= ",";
            }
            unset($field);
            $length = strlen($select);
            $select = substr($select, 0, $length - 1);
        } else {
            $select.= $fields_to_select;
        }
        $select.=" ";
    } else {
        $select = " * ";
    }
    return $select;
}

//function qui retourne la liste des tables du tableau sous forme SQL
function fromClause($from_tables) {
    $from = "";
    if (is_array($from_tables)) {
        foreach ($from_tables as $table) {
            $from.= $table;
            $from.= ",";
        }
        unset($table);
        $length = strlen($from);
        $from = substr($from, 0, $length - 1);
    } else { // seule un champ est passé en paramètre
        $from = $from_tables;
    }
    $from = " FROM $from ";
    return $from;
}

/**
 * @param $where_clauses
 * @return string
 */
function whereClause($where_clauses) {
    $where = "";
    if ($where_clauses != NULL) {
        if (is_array($where_clauses)) {
            foreach ($where_clauses as $clause) {
                $where.= $clause;
                $where.= " AND ";
            }
            unset($clause);
            $length = strlen($where);
            $where = substr($where, 0, $length - 5);
        } else { // seule un champ est passé en paramètre
            $where = $where_clauses;
        }
        if (strpos($where, "WHERE") == FALSE) {
            $where = "WHERE $where ";
        }
    }
    return $where;
}

function orderByClause($array_order_by) {
    $order_by = "";
    if ($array_order_by != NULL) {
        if (is_array($array_order_by)) {
            foreach ($array_order_by as $order_by_value) {
                $order_by.= $order_by_value;
                $order_by.= ",";
            }
            $length = strlen($order_by);
            $order_by = substr($order_by, 0, $length - 1);
        } else { // seul un champ est passé en paramètre
            $order_by = $array_order_by;
        }
        $order_by = "ORDER BY $order_by ";
    }
    return $order_by;
}

/* * ************************ METHODES AMELIORANT LA LISIBILITE DU CODE *********************************** */

/*
 * This function crypt the password before save it in database
 */

function database_encrypt_password($password) {
    return "SHA1('" . $password . "')";
}

/**
 * @param $table_name
 * @param $field_name
 * @return string
 */
function sql_field($table_name, $field_name) {
    return $table_name . "." . $field_name;
}

/**
 * @param $value_left
 * @param $value_right
 * @return string
 */
function sql_equal($value_left, $value_right, $func = null) {
    if (isset($value_right) && is_numeric($value_right)) {
        $value_right = $value_right;
    } else {
        $value_right = "\"$value_right\"";
    }
    if ($func == null) {
        return $value_left . " = " . $value_right;
    } else {
        return $func . "(" . $value_left . ") = " . $func . "(" . $value_right . ")";
    }
}

/**
 * @param $value_left
 * @param $value_right
 * @return string
 */
function sql_diff($value_left, $value_right) {
    return $value_left . " != " . $value_right;
}

function getFirstValue($field_name, $result) {
    $res = fetchAssoc($result);
    if (isset($res) && $res) {
        return $res[$field_name];
    } else {
        return "";
    }
}

function sql_varchar($chaine) {
    return "\"$chaine\"";
}
