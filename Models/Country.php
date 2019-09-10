<?php

/* 
 * fichier \Models\Country.php
 * 
 */

include_once("ObjectPaleofire.php");
require_once 'Region.php';

/**
 * Class Country
 *
 */
class Country extends ObjectPaleofire {

    const TABLE_NAME = 'tr_country';
    const ID = 'ID_COUNTRY';
    const NAME = 'COUNTRY_NAME';
    const COUNTRY_GCD_CODE = 'COUNTRY_GCD_CODE';
    const ID_REGION = 'ID_REGION';
    const ID_REGION_BIS = 'ID_REGION_BIS';
    
    const COUNTRY_ISO_ALPHA3="COUNTRY_ISO_ALPHA3";
    const COUNTRY_ISO_ALPHA2="COUNTRY_ISO_ALPHA2";

    private $_country_code_gcd;
    private $_region;
    public $_region_id_bis;
    public $_country_iso_alpha2;
    
    protected static $_allObjectsByID = null;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_country_code_gcd = null;
        $this->_region = new Region();
        $this->_country_iso_alpha2 = null;
        $this->_region_id_bis = null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setCountryGCDCode($gcd_code_value) {
        $this->_country_code_gcd = $gcd_code_value;
    }

    public function setRegion($region) {
        if ($region instanceof Region) {
            $this->_region = $region;
        } else {
            if (is_numeric($region)) {
                $this->_region = Region::getObjectPaleofireFromId($region);
            } else {
                $this->_region = $region;
            }
        }
    }
    
    public function getRegionName() {
        if (isset($this->_region)){
            return $this->_region->getName();
        } else return null;
    }

    public function create() {
        $country_exists = $this->exists();
        //Si le patient existe, on l'instancie
        if ($country_exists) {
            $this->getIdValue();
            $this->read();
        } else {
            insertIntoTableFieldsValues(self::TABLE_NAME, $values);
        }
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("COUNTRY :: ERROR TO SELECT ALL INFORMATION ABOUT COUNTRY ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setCountryGCDCode($values[self::COUNTRY_GCD_CODE]);
            $this->setRegion(Region::getObjectPaleofireFromId($values[self::ID_REGION]));
            $this->_country_iso_alpha2 = $values[self::COUNTRY_ISO_ALPHA2];
             $this->_region_id_bis = $values[self::ID_REGION_BIS];
        }
    }
    
    public static function getAllIdNameFromRegion($id_region=null){
        if($id_region==null){
            return self::getAllIdName();
        }
        else{
            return self::getAllIdNameFromField(self::ID_REGION,$id_region);
        }
            
    }
    
    public static function getArrayISORegion(){
        $query = "SELECT  COUNTRY_ISO_ALPHA2, tr_country.ID_REGION, REGION_NAME";
        $query .= " FROM tr_country";
        $query .= " join tr_region on tr_region.ID_REGION = tr_country.ID_REGION;";
        
        $tab = null;
        $result = queryToExecute($query, "$query -");
        while ($values = fetchAssoc($result)) {
            $tab[$values['COUNTRY_ISO_ALPHA2']] = $values['REGION_NAME'];
        }
        return $tab;
    }

}
