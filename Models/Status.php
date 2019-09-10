<?php
/* 
 * fichier \Models\Status.php
 * 
 */

require_once 'ObjectPaleofire.php';

class Status extends ObjectPaleofire {

    const TABLE_NAME = 'tr_status';
    const ID = 'ID_STATUS';
    const NAME = 'STATUS_DESC';
    const GCD_CODE = 'STATUS_CODE';
    
    private $_status_code;
    protected static $_allObjectsByID = null;
    
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_status_code=null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    
    public function setStatusCode($gcd_code_value){
        $this->_status_code=$gcd_code_value;
    }

    public function create() {
        $land_exists = $this->exists();
        if ($land_exists) {
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
            throw new Exception("STATUS :: ERROR TO SELECT ALL INFORMATION ABOUT STATUS ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setStatusCode($values[self::GCD_CODE]);
        }
    }
    
    protected function getSetValues(){
        $array_values=array();
        $array_values[]=$this->_status_code;
        $array_values = array_merge($array_values, parent::getSetValues());
        return $array_values;
    }

    public static function getRepartition($idCore){
        return DistributionStatistique::getRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, null, $idCore);
    }
    
    public static function getDataQuality($idCore){
        return DistributionStatistique::getDataQualityForOneCore(self::TABLE_NAME, self::ID, null, $idCore);
    }
    
    public static function isWaiting($idStatus){
        return $idStatus == 0;
    }
    
    public static function isValid($idStatus){
        return $idStatus == 1;
    }
    
    public static function isDenied($idStatus){
        return in_array($idStatus, [2,3,4,5,6,7]);
    }
}
