<?php
/*
 * fichier \Models\ProxyFireMeasurementUnit.php
 *
 */

require_once 'ObjectPaleofire.php';
require_once (REP_MODELS."ProxyFireMeasurement.php");
require_once (REP_MODELS."ProxyFire.php");
require_once (REP_MODELS."ProxyFireDataQuantity.php");
require_once (REP_MODELS."ProxyFireMeasurement.php");
require_once (REP_MODELS."ProxyFireMethodTreatment.php");
require_once (REP_MODELS."ProxyFireMethodEstimation.php");


include_once(REP_PAGES . "EDA/change_Log.php");

/**
 * Class Site
 *
 */
class ProxyFireMeasurementUnit extends ObjectPaleofire {

    const TABLE_NAME = 't_proxy_fire_measurement_unit';
    const ID = 'ID_PROXY_FIRE_MEASUREMENT_UNIT';
    const ID_MEASUREMENT = 'ID_PROXY_FIRE_MEASUREMENT';
    const NAME = 'PROXY_FIRE_MEASUREMENT_UNIT_NAME';
    const UNIT = 'UNIT';
    const INFO = 'INFO';
    const UNKNOWN_ID = '0';

    private $_proxy_fire_measurement_unit_id;
    private $_proxy_fire_measurement_id;
    private $_proxy_fire_measurement_unit_unit;
    private $_proxy_fire_measurement_unit_info;
    protected static $_allObjectsByID = null;

    /**
     * Constructeur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_proxy_fire_measurement_unit_id = null;
        $this->_proxy_fire_measurement_unit_unit = null;
        $this->_proxy_fire_measurement_unit_info = null;
    }

    public function create(){
        $contact_exists = $this->exists();
        if ($contact_exists) {
            $this->getIdValue();
            $this->read();
        }
        else {
        }
      //TODO a completer
    }

    public function read($values = null){
      //TODO a completer
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }

    public function getTableName() {
        return self::TABLE_NAME;
    }
    public static function getAllProxyFireMeasurementUnit($type){

        $query = "select id_proxy_fire_measurement_unit as id_proxy_fire_measurement_unit, unit, type," . ProxyFireMeasurementUnit::NAME . " as name from t_proxy_fire_measurement_unit";
        $query .=" join t_proxy_fire_measurement ON t_proxy_fire_measurement_unit.id_proxy_fire_measurement = t_proxy_fire_measurement.id_proxy_fire_measurement";
        $query .=" where t_proxy_fire_measurement.type = '$type'";

        $res = queryToExecute($query);
        $tabRes = null;
        $id_proxy_fire_measurement_unit = null;
        $unit_proxy_fire_measurement_unit = null;
        $type_proxy_fire_measurement_unit = null;

        while ($row = fetchAssoc($res)) {
                $id_proxy_fire_measurement_unit = $row['id_proxy_fire_measurement_unit'];
                $unit_proxy_fire_measurement = $row['unit'];
                $type_proxy_fire_measurement = $row['type'];
                $name_proxy_fire_measurement = $row['name'];
                $tabRes[$id_proxy_fire_measurement_unit] = array($unit_proxy_fire_measurement, $type_proxy_fire_measurement,$name_proxy_fire_measurement);
        }
        return $tabRes;
    }

    public static function getProxyFireMeasurementUnitID($unit){
        $names = array();
        $query = "select " . ProxyFireMeasurementUnit::ID . "," . ProxyFireMeasurementUnit::UNIT;
        $query .= " from " . ProxyFireMeasurementUnit::TABLE_NAME;
        $query .= " where " . ProxyFireMeasurementUnit::UNIT ." = '$unit'";

        $bdd_gcd = new mysqli(BDD_IN_PROGRESS_GCD_HOSTNAME, BDD_IN_PROGRESS_GCD_LOGIN, BDD_IN_PROGRESS_GCD_PASSWORD, BDD_IN_PROGRESS_GCD_DATABASE);
        $result_get_object = $bdd_gcd->query(utf8_encode($query));

        if (getNumRows($result_get_object) > 0) {
          $tab = fetchAssoc($result_get_object);

          return $tab[ProxyFireMeasurementUnit::ID];
        } else {
          return NULL;
        }
    }

    public static function getRepartition($idCore){
        return DistributionStatistique::getProxyFireRepartitionForOneCore(self::TABLE_NAME, self::ID, self::NAME, self::UNKNOWN_ID, $idCore);
    }

    public static function getDataQuality($idCore){
        return DistributionStatistique::getProxyFireDataQualityForOneCore(self::TABLE_NAME, self::ID, self::UNKNOWN_ID, $idCore);
    }




}
