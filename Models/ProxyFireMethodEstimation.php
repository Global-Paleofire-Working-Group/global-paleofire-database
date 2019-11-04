<?php
/*
 * fichier \Models\ProxyFireMethodEstimation.php
 *
 */

require_once 'ObjectPaleofire.php';


include_once(REP_PAGES . "EDA/change_Log.php");

/**
 * Class Site
 *
 */
class ProxyFireMethodEstimation extends ObjectPaleofire {

    const TABLE_NAME = 't_proxy_fire_method_estimation';
    const ID = 'ID_PROXY_FIRE_METHOD_ESTIMATION';
    const ID_PROXY_FIRE = 'ID_PROXY_FIRE';
    const NAME = 'PROXY_FIRE_METHOD_ESTIMATION_NAME';
    const METHOD = 'METHOD';
    const INFO = 'INFO';
    const UNKNOWN_ID = '4';

    private $_proxy_fire_method_estimation_id;
    private $_proxy_fire_id;
    private $_proxy_fire_method_estimation_method;
    private $_proxy_fire_method_estimation_info;
    protected static $_allObjectsByID = null;

    /**
     * Constructeur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_proxy_fire_method_estimation_id = null;
        $this->_proxy_fire_method_estimation_method = null;
        $this->_proxy_fire_method_estimation_info = null;
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
    public static function getAllProxyFireMethodEstimation($type){

        $query = "select id_proxy_fire_method_estimation as id_proxy_fire_method_estimation, method, proxy_fire_method_estimation_name as name from t_proxy_fire_method_estimation";
        $query .=" join t_proxy_fire ON t_proxy_fire_method_estimation.id_proxy_fire = t_proxy_fire.id_proxy_fire";
        $query .=" where t_proxy_fire.type = '$type'";

        $res = queryToExecute($query);
        $tabRes = null;
        $id_proxy_fire_method_estimation = null;
        $method_proxy_fire_method_estimation = null;
        $info_proxy_fire_method_estimation = null;
        $name_proxy_fire_method_estimation = null;

        while ($row = fetchAssoc($res)) {
                $id_proxy_fire_method_estimation = $row['id_proxy_fire_method_estimation'];
                $method_proxy_fire_method_estimation = $row['method'];
                $info_proxy_fire_method_estimation = $row['info'];
                $name_proxy_fire_method_estimation = $row['name'];
                $tabRes[$id_proxy_fire_method_estimation] = array($method_proxy_fire_method_estimation, $name_proxy_fire_method_estimation, $info_proxy_fire_method_estimation);
        }
        return $tabRes;
    }

    public static function getProxyFireMethodEstimationID($method){
        $names = array();
        $query = "select " . ProxyFireMethodEstimation::ID . "," . ProxyFireMethodEstimation::METHOD;
        $query .= " from " . ProxyFireMethodEstimation::TABLE_NAME;
        $query .= " where " . ProxyFireMethodEstimation::METHOD ." = '$method'";

        $bdd_gcd = new mysqli(BDD_IN_PROGRESS_GCD_HOSTNAME, BDD_IN_PROGRESS_GCD_LOGIN, BDD_IN_PROGRESS_GCD_PASSWORD, BDD_IN_PROGRESS_GCD_DATABASE);
        $result_get_object = $bdd_gcd->query(utf8_encode($query));

        if (getNumRows($result_get_object) > 0) {
          $tab = fetchAssoc($result_get_object);

          return $tab[ProxyFireMethodEstimation::ID];
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
