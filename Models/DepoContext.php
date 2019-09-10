<?php

/* 
 * fichier \Models\DepoContexte.php
 * 
 */

require_once 'ObjectPaleofire.php';

class DepoContext extends ObjectPaleofire {

    const TABLE_NAME = 'tr_depo_context';
    const ID = 'ID_DEPO_CONTEXT';
    const NAME = 'DEPO_CONTEXT_NAME';
    const DEPO_CONTEXT_CODE="DEPO_CONTEXT_CODE";
    const DEPO_CONTEXT_NUMBER="DEPO_CONTEXT_NUMBER";
    const UNKNOWN_ID = 13;
    
    
    private $_depo_context_code;
    private $_depo_context_number;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
        $this->_depo_context_code=null;
        $this->_depo_context_number=null;
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
    }
    
    public function setDepoContextCode($code_value){
        $this->_depo_context_code=$code_value;
    }
    
    public function setDepoContextNumber($number_value){
        $this->_depo_context_number=$number_value;
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
            throw new Exception("DEPO CONTEXT :: ERROR TO SELECT ALL INFORMATION ABOUT DEPO CONTEXT ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
            $this->setDepoContextCode($values[self::DEPO_CONTEXT_CODE]);
            $this->setDepoContextNumber($values[self::DEPO_CONTEXT_NUMBER]);
        }
    }
    
    public static function getRepartition(){         
        $query = 'select count(id_core) as nb, t_core.'.self::ID.', '.self::NAME.' 
                    from t_core
                    left join ' . self::TABLE_NAME. ' on ' . self::TABLE_NAME . '.' . self::ID . ' = t_core.' . self::ID . '
                    and t_core.' . self::ID .' != ' . self::UNKNOWN_ID . '
                    group by ' . self::NAME . '
                    order by nb desc';
        
        $res = queryToExecute($query);
        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);
        
        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            //$pourcentage = round($elt["nb"]*100/$somme);
            //$jsarray[] = "['".$elt[$label]."',".$elt["nb"].",". $pourcentage."]";
            // le pie char réalise le pourcentage automatiquement
            $jsarray[] = "['".$elt[self::NAME]."',".$elt["nb"]."]";
        }

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Biomes', 'Number of sites'],".$jsarray."]";
        return $qualite;
    }
    
    public static function getDataQuality(){
        $query = 'select count(id_core) as nb, documented from
                    ( 
                        SELECT id_core, IF('.self::ID.' IS NULL OR '.self::ID.' = '.self::UNKNOWN_ID.', \'no\', \'yes\') as documented 
                        from t_core  
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

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Documented/Undocummented', 'Number of sites'],".$jsarray."]";
        return $qualite;
    }
}
