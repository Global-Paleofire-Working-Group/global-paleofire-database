<?php

/* 
 * fichier \Models\FlowType.php
 * 
 */

require_once 'ObjectPaleofire.php';

class FlowType extends ObjectPaleofire {

    const TABLE_NAME = 'tr_flow_type';
    const ID = 'ID_FLOW_TYPE';
    const NAME = 'FLOW_TYPE_NAME';
    const FLOW_TYPE_CODE="FLOW_TYPE_CODE";
    const UNKNOWN_VALUE = 'not known';
    const UNKNOWN_ID = 6;
    
    private $_flow_type_gcd_code;
    protected static $_allObjectsByID = null;
    
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_flow_type_gcd_code = null;
    }

    private function setFlowTypeGCDCode($code_value){
        $this->_flow_type_gcd_code = $code_value;
        $this->addOrUpdateFieldToGetId(self::FLOW_TYPE_CODE, $code_value);
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
            throw new Exception("FLOW TYPE :: ERROR TO SELECT ALL INFORMATION ABOUT FLOW TYPE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setFlowTypeGCDCode($values[self::FLOW_TYPE_CODE]);
        }
    }
    
    public static function getRepartitionSites(){
            return DistributionStatistique::getRepartitionSites(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID);
    }
    
    public static function getDataQuality(){
        return DistributionStatistique::getDataQuality(self::TABLE_NAME, self::ID, self::UNKNOWN_ID);
    }
}
