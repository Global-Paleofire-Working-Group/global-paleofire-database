<?php

/* 
 * fichier \Models\Region.php
 * 
 */


include_once("ObjectPaleofire.php");

/**
 * Class Region
 *
 */
class Region extends ObjectPaleofire {

    const TABLE_NAME = 'tr_region';
    const ID = 'ID_REGION';
    const NAME = 'REGION_NAME';
    const REGION_GCD_CODE='REGION_GCD_CODE';
    const IS_MARINE_REGION = "MARINE_REGION";

    private $_region_code_gcd;
    public $_marine_region;
    protected static $_allObjectsByID = null;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_region_code_gcd = null;
        $this->_marine_region = 0;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setRegionGCDCode($region_gcd_code_value){
        $this->_region_code_gcd = $region_gcd_code_value;
    }
    
    public function create() {
        $region_exists = $this->exists();
        //Si le patient existe, on l'instancie
        if ($region_exists) {
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
            throw new Exception("REGION :: ERROR TO SELECT ALL INFORMATION ABOUT REGION ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setRegionGCDCode($values[self::REGION_GCD_CODE]);
            $this->_marine_region = $values[self::IS_MARINE_REGION];
        }
    }
    
    public static function getRepartitionSites(){
        $query = 'select sum(nb) as nb, id, label from 
            ((select count(id_site) as nb, tr_country.ID_REGION as id, REGION_NAME as label
            from t_site
            join tr_country on tr_country.id_country = t_site.id_country
            join tr_region on tr_region.ID_REGION = tr_country.ID_REGION
            group by REGION_NAME)
            UNION
            (select count(id_site) as nb, tr_region.id_region as id, tr_region.region_name as label
            from t_site
            join tr_region on tr_region.id_region = t_site.id_marine_region
            group by region_name)) tab
            group by label';

        $res = queryToExecute($query);

        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);
        
        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            $jsarray[] = "['".$elt["label"]."',".$elt["nb"]."]";
        }

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Elt', 'Number of sites'],".$jsarray."]";
        return $qualite;
    }
    
    public static function getListCountriesByRegion(){
        $tabRes = NULL;
        $query = "select id_region, group_concat(id_country) as countries from r_situated_in_region GROUP by id_region";
        $res = queryToExecute($query);
        if($res != NULL){
            $tab = fetchAll($res);
            foreach($tab as $elt){
                $tabRes[$elt["id_region"]] = $elt["countries"];
            }
        }
        return $tabRes;
    }

    public static function getSitesBySearchOnRegion($recherche){
        $query = "select tr_region.REGION_NAME, COUNTRY_NAME, ID_SITE, SITE_NAME "
                . "from tr_region "
                . "join tr_country on tr_region.ID_REGION = tr_country.ID_REGION "
                . "join t_site on tr_country.id_country = t_site.ID_COUNTRY where tr_region.REGION_NAME like ".$recherche;
        $tab = null;
        $result = queryToExecute($query);
        if ($result != NULL){
            $tab = fetchAll($result);
        }
        return $tab;
    }
}
