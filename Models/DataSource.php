<?php

/*
 * fichier \Models\DataSource.php
 *
 */

require_once 'ObjectPaleofire.php';

class DataSource extends ObjectPaleofire {

    const TABLE_NAME = 'tr_data_source';
    const ID = 'ID_DATA_SOURCE';
    const NAME = 'DATA_SOURCE_DESC';
    const DATA_SOURCE_CODE="DATA_SOURCE_CODE";
    const UNKNOWN_ID = '9';

    private $_data_source_code;

    protected static $_allObjectsByID = null;


    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_data_source_code=null;
    }


    private function setDataSourceCode($cs_code){
        $this->_data_source_code = $cs_code;
        $this->addOrUpdateFieldToGetId(self::DATA_SOURCE_CODE, $cs_code);
    }

    public function create() {

    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("DATA SOURCE :: ERROR TO SELECT ALL INFORMATION ABOUT DATA SOURCE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setDataSourceCode($values[self::DATA_SOURCE_CODE]);
        }
    }

    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID, $idCore);
    }

    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityForOneCore(self::TABLE_NAME, self::ID, self::UNKNOWN_ID, $idCore);
    }

    public static function getAllDataSourcesNameById($id){
        $names = array();
        $query = "select " . DATASOURCE::ID . "," . DATASOURCE::NAME . " from " . DATASOURCE::TABLE_NAME ;
        $query .= " where " . DATASOURCE::ID . " = $id ";

        $bdd_gcd = new mysqli(BDD_IN_PROGRESS_GCD_HOSTNAME, BDD_IN_PROGRESS_GCD_LOGIN, BDD_IN_PROGRESS_GCD_PASSWORD, BDD_IN_PROGRESS_GCD_DATABASE);
        $result_get_object = $bdd_gcd->query(utf8_encode($query));

        if (getNumRows($result_get_object) > 0) {
          while ($tab = fetchAssoc($result_get_object)) {
              $names[$tab[DATASOURCE::ID]] = $tab[DATASOURCE::NAME];
          }
          freeResult($result_get_object);
          return $names;
        } else {
          return NULL;
        }

    }
}
