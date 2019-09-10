<?php

/* 
 * fichier \Models\DatabaseVersion.php
 * 
 */

require_once 'ObjectPaleofire.php';


class DataBaseVersion extends ObjectPaleofire {

    const TABLE_NAME = 'tr_database';
    const ID = 'ID_DATABASE';
    const NAME = 'GCD_ACCESS_VERSION';
    const DATABASE_PUB_DATE="DATABASE_PUB_DATE";
    
    private $_database_pub_date;
    protected static $_allObjectsByID = null;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_database_pub_date=null;
    }
    
    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    
    private function setDataBaseDatePub($date_value){
        $this->_database_pub_date = $date_value;
        
    }

    public function create() {
                $contact_exists = $this->exists();
        //Si le patient existe, on l'instancie
        if ($contact_exists) {
            $this->getIdValue();
            $this->read();
        } else {
        }
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("DATABASE VERSION :: ERROR TO SELECT ALL INFORMATION ABOUT DATABASE VERSION ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setDataBaseDatePub($values[self::DATABASE_PUB_DATE]);
            print_r("id =".self::ID.", NAME=".self::NAME.", DATABASE_PUB_DATE=".self::DATABASE_PUB_DATE);//xli test
        }
    }
    
    public static function getVersionInProgress(){
        $query = "select ID_DATABASE from tr_database where database_pub_date is NULL";
        
        $result = queryToExecute($query);
        if ($result != null && getNumRows($result) == 1){
            return getFirstValue('ID_DATABASE', $result);
        }
        return null;
    }
    
    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, null, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityForOneCore(self::TABLE_NAME, self::ID, null, $idCore);
    }
}
