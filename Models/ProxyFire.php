<?php
/*
 * fichier \Models\ProxyFire.php
 *
 */

require_once 'ObjectPaleofire.php';


include_once(REP_PAGES . "EDA/change_Log.php");

/**
 * Class Site
 *
 */
class ProxyFire extends ObjectPaleofire {

    const TABLE_NAME = 't_proxy_fire';
    const ID = 'ID_PROXY_FIRE';
    const NAME = 'PROXY_FIRE_NAME';
    const TYPE = 'TYPE';
    const INFO = 'INFO';

    private $_proxy_fire_id;
    private $_proxy_fire_type;
    private $_proxy_fire_info;

    /**
     * Constructeur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_proxy_fire_id = null;
        $this->_proxy_fire_type = null;
        $this->_proxy_fire_info = null;
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
    public static function getAllProxyFire(){
        $query = "select id_proxy_fire as id_proxy_fire, type, info ";
        $query .= "from t_proxy_fire ";
        $query .= "order by type";

        $res = queryToExecute($query);
        $tabRes = null;
        $id_proxy_fire = null;
        $type_proxy_fire = null;
        $info_proxy_fire = null;

        while ($row = fetchAssoc($res)) {
                $id_proxy_fire = $row['id_proxy_fire'];
                $type_proxy_fire = $row['type'];
                $info_proxy_fire = $row['info'];
                $tabRes[$id_proxy_fire] = array($type_proxy_fire, $id_proxy_fire, $info_proxy_fire);
        }
        return $tabRes;
    }

    public static function getProxyFireID($type){
        $names = array();
        $query = "select " . ProxyFire::ID . "," . ProxyFire::TYPE;
        $query .= " from " . ProxyFire::TABLE_NAME;
        $query .= " where " . ProxyFire::TYPE ." = '$type'";

        $bdd_gcd = new mysqli(BDD_IN_PROGRESS_GCD_HOSTNAME, BDD_IN_PROGRESS_GCD_LOGIN, BDD_IN_PROGRESS_GCD_PASSWORD, BDD_IN_PROGRESS_GCD_DATABASE);
        $result_get_object = $bdd_gcd->query(utf8_encode($query));

        if (getNumRows($result_get_object) > 0) {
          $tab = fetchAssoc($result_get_object);

          return $tab[ProxyFire::ID];
        } else {
          return NULL;
        }
    }

}
