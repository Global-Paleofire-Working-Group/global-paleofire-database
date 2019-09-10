<?php

/* 
 * fichier \Models\DatabaseTable.php
 * 
 */

abstract class DatabaseTable {
    /* Name of the table */

    private $_table_name;

    /* Name of the field for the id in the table */
    private $_id_field_label;
    private $_name_field_label;

    public function __construct($table_name, $id_field_name, $name_field_label) {
        $this->_table_name = $table_name;
        $this->_id_field_label = $id_field_name;
        $this->_name_field_label = $name_field_label;
    }

    /*     * ************************************************** GETTERS ************************************************** */

    public static function getAll($begin = NULL, $end = NULL, $where_clause = null, $order_by = null) {
        $callable_class = get_called_class();
        if ($callable_class::TABLE_NAME != NULL && $callable_class::TABLE_NAME != "") {
            if ($begin != NULL || $end != NULL) {
                if ($order_by == null) 
                { 
                    $order_by = $callable_class::NAME;
                }
                $result = getAllObjectsOrderedWithLimits(SQL_ALL, $callable_class::TABLE_NAME, $order_by, $begin, $end, $where_clause);
            } else {
                $result = getAllObjectsInTable($callable_class::TABLE_NAME, $where_clause, $order_by);
            }
            if (!$result) {
                return NULL;
            } else {
                $array_object = array();
                while ($tab_values = fetchAssoc($result)) {
                    $temp_object = new $callable_class();
                    $temp_object->read($tab_values);
                    $array_object[] = $temp_object;
                }
                freeResult($result);
                return $array_object;
            }
        } else {
            return NULL;
        }
    }
 
        public static function getAllIds($begin = NULL, $end = NULL, $where_clause = null) {
        $callable_class = get_called_class();
        if ($callable_class::TABLE_NAME != NULL && $callable_class::TABLE_NAME != "") {
            if ($begin != NULL || $end != NULL) {
                $result = getAllObjectsOrderedWithLimits($callable_class::ID, $callable_class::TABLE_NAME, $callable_class::NAME, $begin, $end, $where_clause);
            } else {
                $result = getFieldsFromTables($callable_class::ID, $callable_class::TABLE_NAME, $where_clause);
            }
            if (!$result) {
                return NULL;
            } else {
                $array_object = array();
                while ($tab_values = fetchAssoc($result)) {
                    $temp_object = new $callable_class();
                    $temp_object->setIdValue($tab_values[$callable_class::ID]);
                    $array_object[] = $temp_object;
                }
                freeResult($result);
                return $array_object;
            }
        } else {
            return NULL;
        }
    }
    

    public static function getAllOrderedByField($field_label) {
        $class_paleofire = get_called_class();
        $result = getAllOrderedObjectsInTable($class_paleofire::TABLE_NAME, $field_label);
        if (!$result) {
            return NULL;
        } else {
            $array_object = array();
            while ($tab_values = fetchAssoc($result)) {
                $temp_object = new $class_paleofire();
                $temp_object->read($tab_values);
                $array_object[] = $temp_object;
            }
            freeResult($result);
            return $array_object;
        }
    }

    protected function getDatabaseId($where_clauses) {
        $result = getFieldsFromTables($this->_id_field_label, $this->_table_name, $where_clauses);
        if (!$result) {
            return false;
        } else {
            $values = fetchAssoc($result);
            freeResult($result);
            return $values[$this->_id_field_label];
        }
    }

    protected function getAllFieldsValuesFromId($id_value) {
        return getFieldsFromTables(SQL_ALL, $this->_table_name, sql_equal($this->_id_field_label, $id_value));
    }

    protected function getFieldsValuesFromId($field_to_select, $table_name, $id_value) {
        return getFieldsFromTables($field_to_select, $table_name, sql_equal($this->_id_field_label, $id_value));
    }

    abstract protected function getSetValues();
}
