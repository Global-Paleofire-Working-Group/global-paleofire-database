<?php

/* 
 * fichier \Models\BiomeType.php
 * 
 */

require_once 'ObjectPaleofire.php';

require_once 'RefBiome.php';

class BiomeType extends ObjectPaleofire {

    const TABLE_NAME = 'tr_biome_type';
    const ID = 'ID_BIOME_TYPE';
    const NAME = 'BIOME_TYPE_NAME';
    const BIOME_TYPE_CODE = "BIOME_TYPE_CODE";
    const ID_REF_BIOME = "ID_REF_BIOME";
    const UNKNOWN_VALUE = 'not known';
    const UNKNOWN_ID = 28;

    private $_ref_biome;
    protected static $_allObjectsByID = null;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_ref_biome = new RefBiome();
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function setRefBiome($ref_biome) {
        if ($ref_biome instanceof RefBiome) {
            $this->_ref_biome = $ref_biome;
        } else {
            if (is_numeric($ref_biome)) {
                $this->_ref_biome = RefBiome::getObjectPaleofireFromId($ref_biome);
            } else {
                $this->_ref_biome = $ref_biome;
            }
        }
    }

    public function create() {
        $object_exists = $this->exists();
        if ($object_exists) {
            $this->getIdValue();
            $this->read();
        } else {
            //
        }
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("BIOME TYPE :: ERROR TO SELECT ALL INFORMATION ABOUT BIOME TYPE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setRefBiome(RefBiome::getObjectPaleofireFromId($values[self::ID_REF_BIOME]));
        }
    }
    
    public static function getRepartitionSites(){
            return DistributionStatistique::getRepartitionSites(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID);
    }
    
    public static function getDataQuality(){
        return DistributionStatistique::getDataQuality(self::TABLE_NAME, self::ID, self::UNKNOWN_ID);
    }

}
