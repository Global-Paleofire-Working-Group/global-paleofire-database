<?php
/*
 * fichier \Models\ProxyFireMethodTreatment.php
 *
 */

require_once 'ObjectPaleofire.php';


include_once(REP_PAGES . "EDA/change_Log.php");

/**
 * Class Site
 *
 */
class ProxyFireMethodTreatment extends ObjectPaleofire {

    const TABLE_NAME = 't_proxy_fire_method_treatment';
    const ID = 'ID_PROXY_FIRE_METHOD_TREATMENT';
    const ID_PROXY_FIRE = 'ID_PROXY_FIRE';
    const NAME = 'PROXY_FIRE_METHOD_TREATMENT_NAME';
    const METHOD = 'METHOD';
    const INFO = 'INFO';
    const UNKNOWN_ID = '7';

    private $_proxy_fire_method_treatment_id;
    private $_proxy_fire_id;
    private $_proxy_fire_method_treatment_method;
    private $_proxy_fire_method_treatment_info;
    protected static $_allObjectsByID = null;


    /**
     * Constructeur de la classe
     * */
    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);

        $this->_proxy_fire_method_treatment_id = null;
        $this->_proxy_fire_method_treatment_method = null;
        $this->_proxy_fire_method_treatment_info = null;
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
    public static function getAllProxyFireMethodTreatment($type){

        $query = "select id_proxy_fire_method_treatment as id_proxy_fire_method_treatment, method, proxy_fire_method_treatment_name as name from t_proxy_fire_method_treatment";
        $query .=" join t_proxy_fire ON t_proxy_fire_method_treatment.id_proxy_fire = t_proxy_fire.id_proxy_fire";
        $query .=" where t_proxy_fire.type = '$type'";

        $res = queryToExecute($query);
        $tabRes = null;
        $id_proxy_fire_method_treatment = null;
        $method_proxy_fire_method_treatment = null;
        $info_proxy_fire_method_treatment = null;
        $name_proxy_fire_method_treatment = null;

        while ($row = fetchAssoc($res)) {
                $id_proxy_fire_method_treatment = $row['id_proxy_fire_method_treatment'];
                $method_proxy_fire_method_treatment = $row['method'];
                $info_proxy_fire_method_treatment = $row['info'];
                $name_proxy_fire_method_treatment = $row['name'];
                $tabRes[$id_proxy_fire_method_treatment] = array($method_proxy_fire_method_treatment, $name_proxy_fire_method_treatment, $info_proxy_fire_method_treatment);
        }
        return $tabRes;
    }


    public static function getProxyFireMethodTreatmentID($method){
        $names = array();
        $query = "select " . ProxyFireMethodTreatment::ID . "," . ProxyFireMethodTreatment::METHOD;
        $query .= " from " . ProxyFireMethodTreatment::TABLE_NAME;
        $query .= " where " . ProxyFireMethodTreatment::METHOD ." = '$method'";

        $bdd_gcd = new mysqli(BDD_IN_PROGRESS_GCD_HOSTNAME, BDD_IN_PROGRESS_GCD_LOGIN, BDD_IN_PROGRESS_GCD_PASSWORD, BDD_IN_PROGRESS_GCD_DATABASE);
        $result_get_object = $bdd_gcd->query(utf8_encode($query));

        if (getNumRows($result_get_object) > 0) {
          $tab = fetchAssoc($result_get_object);

          return $tab[ProxyFireMethodTreatment::ID];
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
