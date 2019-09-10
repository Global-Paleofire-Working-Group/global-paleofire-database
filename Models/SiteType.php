<?php

/* 
 * fichier \Models\SiteType.php
 * 
 */


require_once 'ObjectPaleofire.php';

class SiteType extends ObjectPaleofire {

    const TABLE_NAME = 'tr_site_type';
    const ID = 'ID_SITE_TYPE';
    const NAME = 'SITE_TYPE_DESC';
    const SITE_TYPE_LEVEL="SITE_TYPE_LEVEL";
    const SITE_TYPE_HIGH_LEVEL="SITE_TYPE_HIGH_LEVEL";
    const UNKNOWN_ID = 30;
    
    private $_site_type_level;
    private $_site_type_high_level;
    
    protected static $_allObjectsByID = null;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    
    public function setSiteTypeLevel($_site_type_level) {
        $this->_site_type_level = $_site_type_level;
    }

    public function setSiteTypeHighLevel($_site_type_high_level) {
        $this->_site_type_high_level = $_site_type_high_level;
    }

    

    public function create() {
        $obj_exists = $this->exists();
        if ($obj_exists) {
            $this->getIdValue();
            $this->read();
        } else {
//BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->getIdValue() != NULL) {
                $column_values[self::ID_SITE] = $this->getIdValue();
            } 

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = sql_varchar($this->getName());
            }

            $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
            if (!$result_insert) {
                $insert_errors[] = "Insert into table " . self::TABLE_NAME;
            } else {
                $this->setIdValue(getLastCreatedId());
            }

            //If no error, commit transaction !
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
// END TRANSACTION
        }
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("SITE TYPE :: ERROR TO SELECT ALL INFORMATION ABOUT SITE TYPE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setSiteTypeLevel($values[self::SITE_TYPE_LEVEL]);
            $this->setSiteTypeHighLevel($values[self::SITE_TYPE_HIGH_LEVEL]);
        }
    }
    
    public static function getRepartitionSites(){
        $query = 'select count(id_site) as nb, t.site_type_desc as label
                from 
                (select id_site, t_site.id_site_type, site_type_high_level, site_type_desc, site_type_level 
                from t_site 
                join tr_site_type on tr_site_type.id_site_type = t_site.id_site_type
                and tr_site_type.id_site_type != '. self::UNKNOWN_ID .'
                ) tabSiteTypeHigh 
                join tr_site_type t on tabSiteTypeHigh.site_type_high_level = t.site_type_level 
                group by t.site_type_desc 
                order by nb desc';

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
        $qualite->tabNbParElt = "[['Biomes', 'Number of sites'],".$jsarray."]";
        return $qualite;
    }
    
    public static function getDataQuality(){
        return DistributionStatistique::getDataQuality(self::TABLE_NAME, self::ID, self::UNKNOWN_ID);
    }
}
