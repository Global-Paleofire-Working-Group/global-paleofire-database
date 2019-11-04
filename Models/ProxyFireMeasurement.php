<?php
/*
 * fichier \Models\ProxyFireMeasurement.php
 *
 */

require_once 'ObjectPaleofire.php';


include_once(REP_PAGES . "EDA/change_Log.php");

/**
 * Class Site
 *
 */
class ProxyFireMeasurement extends ObjectPaleofire {

    const TABLE_NAME = 't_proxy_fire_measurement';
    const ID = 'ID_PROXY_FIRE_MEASUREMENT';
    const NAME = 'PROXY_FIRE_MEASUREMENT_NAME';
    const TYPE = 'TYPE';
    const INFO = 'INFO';

    private $_proxy_fire_measurement_id;
    private $_proxy_fire_measurement_type;
    private $_proxy_fire_measurement_info;

    /**
     * Constructeur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_proxy_fire_measurement_id = null;
        $this->_proxy_fire_measurement_type = null;
        $this->_proxy_fire_measurement_info = null;
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
    public static function getAllProxyFireMeasurement(){
        $query = "select id_proxy_fire_measurement as id_proxy_fire_measurement, type, proxy_fire_measurement_name, info ";
        $query .= "from t_proxy_fire_measurement ";
        $query .= "order by type";

        $res = queryToExecute($query);
        $tabRes = null;
        $id_proxy_fire_measurement = null;
        $type_proxy_fire_measurement = null;
        $info_proxy_fire_measurement = null;
        $name_proxy_fire_measurement = null;

        while ($row = fetchAssoc($res)) {
                $id_proxy_fire_measurement = $row['id_proxy_fire_measurement'];
                $type_proxy_fire_measurement = $row['type'];
                $info_proxy_fire_measurement = $row['info'];
                $name_proxy_fire_measurement = $row['proxy_fire_measurement_name'];
                $tabRes[$id_proxy_fire_measurement] = array($type_proxy_fire_measurement, $name_proxy_fire_measurement,$info_proxy_fire_measurement, $id_proxy_fire_measurement);
        }
        return $tabRes;
    }




}
