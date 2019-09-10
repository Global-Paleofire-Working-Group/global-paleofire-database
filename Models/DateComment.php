<?php

/* 
 * fichier \Models\DateComment.php
 * 
 */

include_once("ObjectPaleofire.php");
include_once(REP_PAGES . "EDA/change_Log.php");


class DateComment extends ObjectPaleofire {

    const TABLE_NAME = 't_date_comments';
    const ID = 'ID_DATE_COMMENTS';
    const NAME = 'DATE_COMMENTS_NAME';
    const DATE_COMMENTS_CODE = 'DATE_COMMENTS_CODE';

    private $_date_comments_code;
    private static $_allDateCommentsByDATE_COMMENTS_CODE = null;

    /**
     * Constucteur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_date_comments_code = null;
    }

    public function create() {
        return array();
    }
    public function read($values = null){
        return array();
    }
    
    /**
     * @return null|\object_paleofire
     */
    public static function getAllDateCommentsByDATE_COMMENTS_CODE() {
        if (self::$_allDateCommentsByDATE_COMMENTS_CODE == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, self::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                $tab_get_values = fetchAll($result_get_object);
                self::$_allDateCommentsByDATE_COMMENTS_CODE = null;
                foreach($tab_get_values as $row){
                    self::$_allDateCommentsByDATE_COMMENTS_CODE[$row[self::DATE_COMMENTS_CODE]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
            } else {
                self::$_allDateCommentsByDATE_COMMENTS_CODE = NULL;
            }
        }
        return self::$_allDateCommentsByDATE_COMMENTS_CODE;
    }

}
