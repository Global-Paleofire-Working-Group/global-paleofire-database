<?php

/* 
 * fichier \Models\CoreType.php
 * 
 */

require_once 'ObjectPaleofire.php';

class CoreType extends ObjectPaleofire {

    const TABLE_NAME = 'tr_core_type';
    const ID = 'ID_CORE_TYPE';
    const NAME = 'CORE_TYPE_NAME';
    
    
    private $_date_type_code;
    private $_date_type_number;
    
    protected static $_allObjectsByID = null;
    

    public function __construct() {
        parent::__construct(self::TABLE_NAME, self::ID, self::NAME);
    }

    public function setNameValue($name_value) {
        parent::setNameValue($name_value);
        $this->addOrUpdateFieldToGetId(self::NAME, $name_value);
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
            throw new Exception("Core TYPE :: ERROR TO SELECT ALL INFORMATION ABOUT DATE TYPE ID : " . $this->getIdValue());
        } else {
            $this->setIdValue($values[self::ID]);
            $this->setNameValue($values[self::NAME]);
        }
    }
    
    public static function getRepartition(){
        $query = 'select count(id_core) as nb, t_core.'.self::ID.', '.self::NAME.' 
                    from t_core
                    left join ' . self::TABLE_NAME. ' on ' . self::TABLE_NAME . '.' . self::ID . ' = t_core.' . self::ID . '
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
                        SELECT id_core, IF('.self::ID.' IS NULL, \'no\', \'yes\') as documented 
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
