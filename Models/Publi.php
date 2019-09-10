<?php
/* 
 * fichier \Models\Publi.php
 * 
 */


require_once 'ObjectPaleofire.php';
include_once(REP_PAGES . "EDA/change_Log.php");
require_once 'Status.php';

class Publi extends ObjectPaleofire {

    const TABLE_NAME = 't_pub';
    const ID = 'ID_PUB';
    const NAME = 'PUB_CITATION';
    const PUB_LINK = "PUB_LINK";
    const DOI = "ID_DOI";
    const GCD_ACCESS_ID = "GCD_ACCESS_ID";
    const ID_STATUS = "ID_STATUS";

    public $_publi_link;
    public $_gcd_access_id;
    public $_doi;
    public $_status_id;
    
    public static $_allPubByCGD_ACCESS_ID;

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_publi_link = null;
        $this->_doi = null;
        $this->_status_id = 0;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    
 public function setStatusId($status_id) {
        $this->_status_id = $status_id;
    }


     public function getStatusId() {
        return $this->_status_id;
    }

    
    public function create() {
        $insert_errors = array();
        $object_exists = $this->exists();
        if (!$object_exists) {
            //BEGIN TRANSACTION
            beginTransaction();

            $column_values = array();

            if ($this->getName() != NULL) {
                $column_values[self::NAME] = sql_varchar(escapeString($this->getName()));
            } else {
                $insert_errors[] = "The publication citation  can't be empty !";
            }
            if ($this->_publi_link != NULL) {
                $column_values[self::PUB_LINK] = sql_varchar(escapeString($this->_publi_link));
            }
            if ($this->_gcd_access_id != NULL) {
                $column_values[self::GCD_ACCESS_ID] = $this->_gcd_access_id;
            }
            if ($this->_doi != NULL) {
                $column_values[self::DOI] = sql_varchar($this->_doi);
            }
            if ($this->_status_id != NULL) {
                 $column_values[self::ID_STATUS] = $this->getStatusId();           
            }

            if (empty($insert_errors)) {
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_pub", $this->getIdValue());
                }
            }

            //If no error, commit transaction !
            if (empty($insert_errors)) {
                commit();
            } else {
                rollBack();
            }
        }
        else{
            $insert_errors[] = "The publication already exists : ".$this->getName();
        }
        return $insert_errors;        
    }
   
    public function save() {
        $insert_errors = array();
        $obj_exists = ($this->getIdValue() == null)?false:true;
        //BEGIN TRANSACTION
        beginTransaction();

        $column_values = array();

        if ($this->getName() != NULL) {
            $column_values[self::NAME] = sql_varchar(escapeString($this->getName()));
        } else {
            $insert_errors[] = "The publication citation  can't be empty !";
        }
        if ($this->_publi_link != NULL) {
            $column_values[self::PUB_LINK] = sql_varchar(escapeString($this->_publi_link));
        } else $column_values[self::PUB_LINK] = 'NULL';
        
        if ($this->_gcd_access_id != NULL) {
            $column_values[self::GCD_ACCESS_ID] = $this->_gcd_access_id;
        }
        if ($this->_doi != NULL) {
            $column_values[self::DOI] = sql_varchar($this->_doi);
        } else $column_values[self::DOI] = 'NULL';
        
        if (is_numeric($this->getStatusId())) {
            $column_values[self::ID_STATUS] = $this->getStatusId();
        } else {
            $insert_errors[] = "Error : id status !";
        }

        if (empty($insert_errors)) {
            if ($obj_exists == true){
                // mise à jour
                $res = updateObjectWithWhereClause(self::TABLE_NAME, $column_values, self::ID . "=" . $this->getIdValue());
                if (!$res) {
                    $insert_errors[] = "Update table " . self::TABLE_NAME;
                    
                }
                else {
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("edit_pub", $this->getIdValue());
                    
                }
            } else {
                // création
                $result_insert = insertIntoTable(self::TABLE_NAME, $column_values);
                if (!$result_insert) {
                    $insert_errors[] = "Insert into table " . self::TABLE_NAME;
                } else {
                    $this->setIdValue(getLastCreatedId());
                    
                    //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                    writeChangeLog("add_pub", $this->getIdValue());

                }
            }
        }

        //If no error, commit transaction !
        if (empty($insert_errors)) {
            commit();
        } else {
            rollBack();
        }
        
        return $insert_errors;        
    }

    public function read($values = null) {
        //Get All Values of this object
        $request_values = ($values == null ? $this->getDatabaseObject() : $values);
        if ($request_values == NULL || empty($request_values)) {
            //If the request return nothing
            throw new Exception("PUBLICATION :: ERROR TO SELECT ALL INFORMATION ABOUT SITE PUBLICATION  ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->_publi_link = $values[self::PUB_LINK];
            $this->_doi = $values[self::DOI];
            $this->setStatusId($values[self::ID_STATUS]);
        }
    }

    public static function getAllPubByGCD_ACCESS_ID() {
        if (self::$_allPubByCGD_ACCESS_ID == null){
            $result_get_object = getFieldsFromTables(SQL_ALL, PUBLI::TABLE_NAME);
            if (getNumRows($result_get_object) > 0) {
                //$class_paleo = get_called_class();
                $tab_get_values = fetchAll($result_get_object);
                self::$_allPubByCGD_ACCESS_ID = null;
                foreach($tab_get_values as $row){
                    // pour l'import de données on récupère les site par le GCD_ACCESS_ID
                    //$object_paleo = new $class_paleo();
                    //$object_paleo->read($row);
                    self::$_allPubByCGD_ACCESS_ID[$row[PUBLI::GCD_ACCESS_ID]] = $row;//$object_paleo;
                }
                freeResult($result_get_object);
                unset($tab_get_values);
                unset($result_get_object);
            } else {
                self::$_allPubByCGD_ACCESS_ID = NULL;
            }
        }
        return self::$_allPubByCGD_ACCESS_ID;
    }
    
    public static function getRepartition($idCore){
        $query = 'select count(t_charcoal.id_charcoal) as nb, r_has_pub.id_pub, t_pub.pub_citation as name
            from t_sample 
            join t_charcoal on t_charcoal.id_sample = t_sample.id_sample and t_sample.id_core = ' . $idCore . ' 
            left join r_has_pub on t_charcoal.id_charcoal = r_has_pub.id_charcoal
            left join t_pub on r_has_pub.id_pub = t_pub.id_pub
            group by r_has_pub.id_pub
            order by nb desc';
        
        $res = queryToExecute($query);
        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);
        
        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            $jsarray[] = "['".htmlentities(preg_replace("#\n|\t|\r#"," ",$elt["name"]), ENT_QUOTES, "UTF-8")."',".$elt["nb"]."]";
        }

        $qualite = null;
        if ($jsarray != null) {
            $jsarray = implode(',', $jsarray);
            $qualite = new stdClass();
            $qualite->nbSites = $somme;
            $qualite->tabNbParElt = "[['Biomes', 'Number of sites'],".$jsarray."]";
        }
        return $qualite;
    }
    
    public static function getDataQuality($idCore){
        $query = 'select count(id_charcoal) as nb, documented from
            ( 
                SELECT t_charcoal.id_charcoal as id_charcoal, IF(ID_PUB IS NULL, \'no\', \'yes\') as documented 
                from t_sample 
                join t_charcoal on t_sample.id_sample = t_charcoal.id_sample and t_sample.id_core = ' . $idCore . '
                join r_has_pub on t_charcoal.id_charcoal = r_has_pub.id_charcoal
            ) tabDoc
            group by documented order by documented desc';
        
        $res = queryToExecute($query);

        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);
        
        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            if ($elt["documented"] == 'no'){
                $jsarray[] = "['undocumented',".$elt["nb"]."]";
            } else {
                $jsarray[] = "['documented',".$elt["nb"]."]";
            }
        }

        $qualite = null;
        if (count($jsarray) > 0) {
            $jsarray = implode(',', $jsarray);
            $qualite = new stdClass();
            $qualite->nbSites = $somme;
            $qualite->tabNbParElt = "[['Documented/Undocummented', 'Number of sites'],".$jsarray."]";
        }
        
        return $qualite;
    }
    
    public static function getDataForRExport(){      
        $tab = null;
        
        $query = 'select ID_PUB as id_pub, REPLACE(PUB_CITATION, \'"\', \'\') as citation from t_pub';

        try {
            $res = queryToExecute($query);
            $tab = fetchAll($res);
        } catch(Exception $e){
            $tab = null;
            logError($e->getMessage());
        }
        
        freeResult($res);
        return $tab;
    }
}
