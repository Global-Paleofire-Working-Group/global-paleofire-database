<?php
/*
 * fichier \Models\DistributionStatistique.php
 *
 */

require_once 'ObjectPaleofire.php';
include_once("Region.php");
include_once("Country.php");
include_once("CharcoalUnits.php");
require_once 'LandsDesc.php';
require_once 'BiomeType.php';
require_once 'SiteType.php';
require_once 'BasinSize.php';
require_once 'CatchSize.php';
require_once 'LocalVeg.php';
require_once 'RegionalVeg.php';
require_once 'FlowType.php';
require_once 'NoteCore.php';
require_once 'Core.php';

/**
 * Class Site
 *
 */
class DistributionStatistique extends ObjectPaleofire{

    public function read($values = null){

    }

    public function create(){

    }

    // fonction pour statistiques
    public static function getNbSitesByCountry(){
        $query = 'select count(id_site) as nbsites, country_iso_alpha2
                    from t_site
                    join tr_country on tr_country.id_country = t_site.id_country
                    group by country_iso_alpha2';

        $res_ = queryToExecute($query);
        $jsarray = null;
        while ($values = fetchAssoc($res_)) {
            $jsarray[] = "['".$values["country_iso_alpha2"]."',".$values["nbsites"]."]";
        }
        $jsarray = implode(',', $jsarray);
        return "[['Country', 'Number of sites'],".$jsarray."]";
    }

    public static function getNbSitesByBiomes(){
        $query = 'select count(t_site.id_biome_type) as nbsites, biome_type_name
                    from t_site
                    join tr_biome_type on tr_biome_type.id_biome_type = t_site.id_biome_type
                    group by t_site.id_biome_type';

        $res_ = queryToExecute($query);
        $jsarray = null;
        while ($values = fetchAssoc($res_)) {
            $jsarray[] = "['".$values["biome_type_name"]."',".$values["nbsites"]."]";
        }
        $jsarray = implode(',', $jsarray);
        return "[['Biomes', 'Number of sites'],".$jsarray."]";
    }

    public static function getRepartitionBiomes(){
        $label = "biome_type_name";

        $query = 'select count(id_site) as nb, t_site.id_biome_type, biome_type_name from t_site
                    left join tr_biome_type on tr_biome_type.id_biome_type = t_site.id_biome_type
                    group by biome_type_name';

        $res = queryToExecute($query);

        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);
        foreach($tabRes as $elt){
            $somme += $elt["nb"];
        }

        foreach($tabRes as $elt){
            //$pourcentage = round($elt["nb"]*100/$somme);
            //$jsarray[] = "['".$elt[$label]."',".$elt["nb"].",". $pourcentage."]";
            // le pie char réalise le pourcentage automatiquement
            $jsarray[] = "['".$elt[$label]."',".$elt["nb"]."]";
        }

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Biomes', 'Number of sites'],".$jsarray."]";
        return $qualite;

    }

    public static function getRepartitionSites($nom_table, $id_table, $label_table, $id_unknown){

        $query = 'select count(id_site) as nb, t_site.'.$id_table.', '.$label_table.'
                    from t_site
                    join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_site.'.$id_table.'
                    where t_site.' . $id_table .' != ' . $id_unknown . '
                    group by ' . $label_table . '
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
            $jsarray[] = "['".$elt[$label_table]."',".$elt["nb"]."]";
        }

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Biomes', 'Number of sites'],".$jsarray."]";
        return $qualite;

    }

    public static function getRepartitionForOneCore($nom_table, $id_table, $label_table, $id_unknown, $id_core){
        $query = "";
        if ($id_unknown != null){
            $query = 'select count(id_charcoal) as nb, t_charcoal.'.$id_table.', '.$label_table.'
                from t_sample
                left join t_charcoal on t_charcoal.id_sample = t_sample.id_sample and t_sample.id_core = ' . $id_core . '
                left join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_charcoal.'.$id_table.'
                where t_charcoal.' . $id_table .' != ' . $id_unknown . '
                group by ' . $label_table . '
                order by nb desc';
        } else {
            $query = 'select count(id_charcoal) as nb, t_charcoal.'.$id_table.', '.$label_table.'
                from t_sample
                left join t_charcoal on t_charcoal.id_sample = t_sample.id_sample and t_sample.id_core = ' . $id_core . '
                left join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_charcoal.'.$id_table.'
                where t_charcoal.'.$id_table. ' is not NULL
                group by ' . $label_table . '
                order by nb desc';
        }

        $res = queryToExecute($query);
        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);

        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            $jsarray[] = "['".htmlentities(preg_replace("#\n|\t|\r#"," ",$elt[$label_table]), ENT_QUOTES, "UTF-8")."',".$elt["nb"]."]";
        }

        $qualite = null;
        if ($jsarray != null) {
            $jsarray = implode(',', $jsarray);
            $qualite = new stdClass();
            $qualite->nbSites = $somme;
            $qualite->tabNbParElt = "[['".$nom_table."', 'Number of sites'],".$jsarray."]";
        }

        return $qualite;

    }

    public static function getProxyFireRepartitionForOneCore($nom_table, $id_table, $label_table, $id_unknown, $id_core){
        $query = "";
        if ($id_unknown != null){
            $query = 'select count(ID_PROXY_FIRE_DATA) as nb, t_proxy_fire_data.'.$id_table.', '.$label_table.'
                from t_sample
                left join t_proxy_fire_data on t_proxy_fire_data.id_sample = t_sample.id_sample and t_sample.id_core = ' . $id_core . '
                left join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_proxy_fire_data.'.$id_table.'
                where t_proxy_fire_data.' . $id_table .' != ' . $id_unknown . '
                group by ' . $label_table . '
                order by nb desc';
        } else {
            $query = 'select count(ID_PROXY_FIRE_DATA) as nb, t_proxy_fire_data.'.$id_table.', '.$label_table.'
                from t_sample
                left join t_proxy_fire_data on t_proxy_fire_data.id_sample = t_sample.id_sample and t_sample.id_core = ' . $id_core . '
                left join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_proxy_fire_data.'.$id_table.'
                where t_proxy_fire_data.'.$id_table. ' is not NULL
                group by ' . $label_table . '
                order by nb desc';
        }

        $res = queryToExecute($query);
        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);

        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            $jsarray[] = "['".htmlentities(preg_replace("#\n|\t|\r#"," ",$elt[$label_table]), ENT_QUOTES, "UTF-8")."',".$elt["nb"]."]";
        }

        $qualite = null;
        if ($jsarray != null) {
            $jsarray = implode(',', $jsarray);
            $qualite = new stdClass();
            $qualite->nbSites = $somme;
            $qualite->tabNbParElt = "[['".$nom_table."', 'Number of sites'],".$jsarray."]";
        }

        return $qualite;

    }

    public static function getDataQuality($nom_table, $id_table, $valeur_id_unknown){

        $query = 'select count(id_site) as nb, documented from
                    ( SELECT id_site, IF('.$id_table.' IS NULL OR '.$id_table.' = '.$valeur_id_unknown.', \'no\', \'yes\') as documented from t_site) tabDoc
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

        if ($jsarray != null) $jsarray = implode(',', $jsarray);
        else $jsarray = 'null';

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Documented/Undocummented', 'Number of sites'],".$jsarray."]";
        return $qualite;

    }

    public static function getDataQualityForOneCore($nom_table, $id_table, $valeur_id_unknown, $id_core){
        $query = "";

        if ($valeur_id_unknown != null) {
            $query = 'select count(id_charcoal) as nb, documented from
                        (
                            SELECT id_charcoal, IF('.$id_table.' IS NULL OR '.$id_table.' = '.$valeur_id_unknown.', \'no\', \'yes\') as documented
                            from t_sample
                            join t_charcoal on t_sample.id_sample = t_charcoal.id_sample and t_sample.id_core = ' . $id_core . '
                        ) tabDoc
                      group by documented order by documented desc';
        } else {
            if ($id_table=="ID_STATUS") { //on éliminie l'ambigüité sur le ID_STATUS
                $id_table="t_charcoal.ID_STATUS" ;
            }
            $query = 'select count(id_charcoal) as nb, documented from
                        (
                            SELECT id_charcoal, IF('.$id_table.' IS NULL, \'no\', \'yes\') as documented
                            from t_sample
                            join t_charcoal on t_sample.id_sample = t_charcoal.id_sample and t_sample.id_core = ' . $id_core . '
                        ) tabDoc
                      group by documented order by documented desc';
        }
        //var_dump($query);

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

    public static function getProxyFireDataQualityForOneCore($nom_table, $id_table, $valeur_id_unknown, $id_core){
        $query = "";

        if ($valeur_id_unknown != null) {
            $query = 'select count(ID_PROXY_FIRE_DATA) as nb, documented from
                        (
                            SELECT ID_PROXY_FIRE_DATA, IF('.$id_table.' IS NULL OR '.$id_table.' = '.$valeur_id_unknown.', \'no\', \'yes\') as documented
                            from t_sample
                            join t_proxy_fire_data on t_sample.id_sample = t_proxy_fire_data.id_sample and t_sample.id_core = ' . $id_core . '
                        ) tabDoc
                      group by documented order by documented desc';
        } else {
            if ($id_table=="ID_STATUS") { //on éliminie l'ambigüité sur le ID_STATUS
                $id_table="t_proxy_fire_data.ID_STATUS" ;
            }
            $query = 'select count(ID_PROXY_FIRE_DATA) as nb, documented from
                        (
                            SELECT ID_PROXY_FIRE_DATA, IF('.$id_table.' IS NULL, \'no\', \'yes\') as documented
                            from t_sample
                            join t_proxy_fire_data on t_sample.id_sample = t_proxy_fire_data.id_sample and t_sample.id_core = ' . $id_core . '
                        ) tabDoc
                      group by documented order by documented desc';
        }
        //var_dump($query);

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

    public static function getRepartitionDateInfoForOneCore($nom_table, $id_table, $label_table, $id_unknown, $id_core){
        $query = "";
        if ($id_unknown != null){
            $query = 'select count(t_sample.id_sample) as nb, t_date_info.'.$id_table.', '.$label_table.'
                from t_sample
                join t_date_info on t_sample.id_sample = t_date_info.id_sample and t_sample.id_core = ' . $id_core . '
                join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_date_info.'.$id_table.'
                where t_date_info.' . $id_table .' != ' . $id_unknown . '
                group by ' . $label_table . '
                order by nb desc';
        } else {
            $query = 'select count(t_sample.id_sample) as nb, t_date_info.'.$id_table.', '.$label_table.'
                from t_sample
                join t_date_info on t_sample.id_sample = t_date_info.id_sample and t_sample.id_core = ' . $id_core . '
                join ' . $nom_table. ' on '. $nom_table .'.'.$id_table.' = t_date_info.'.$id_table.'
                group by ' . $label_table . '
                order by nb desc';
        }

        $res = queryToExecute($query);
        $jsarray = null;
        $somme = 0;
        $tabRes = fetchAll($res);

        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            $jsarray[] = "['".$elt[$label_table]."',".$elt["nb"]."]";
        }

        $qualite = null;
        if (count($jsarray) > 0){
            $jsarray = implode(',', $jsarray);
            $qualite = new stdClass();
            $qualite->nbSites = $somme;
            $qualite->tabNbParElt = "[['".$nom_table."', 'Number of sites'],".$jsarray."]";
        }
        return $qualite;
    }

    public static function getDataQualityDateInfoForOneCore($nom_table, $id_table, $valeur_id_unknown, $id_core){
        $query = "";
        if ($valeur_id_unknown != null) {
            $query = 'select count(id_sample) as nb, documented from
                        (
                            SELECT t_sample.id_sample, IF('.$id_table.' IS NULL OR '.$id_table.' = '.$valeur_id_unknown.', \'no\', \'yes\') as documented
                            from t_sample
                            join t_date_info on t_sample.id_sample = t_date_info.id_sample and t_sample.id_core = ' . $id_core . '
                        ) tabDoc
                      group by documented order by documented desc';
        } else {
            $query = 'select count(id_sample) as nb, documented from
                        (
                            SELECT t_sample.id_sample, IF('.$id_table.' IS NULL, \'no\', \'yes\') as documented
                            from t_sample
                            join t_date_info on t_sample.id_sample = t_date_info.id_sample and t_sample.id_core = ' . $id_core . '
                        ) tabDoc
                      group by documented order by documented desc';
        }

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
        if (count($jsarray) > 0){
            $jsarray = implode(',', $jsarray);
            $qualite = new stdClass();
            $qualite->nbSites = $somme;
            $qualite->tabNbParElt = "[['Documented/Undocummented', 'Number of sites'],".$jsarray."]";
        }
        return $qualite;

    }


    public static function getRepartitionSitesChronologique($dateMax, $dateMin){
        if ($dateMax == null && $dateMin == null) return null;
        $filtre = "";
        if ($dateMax != null){
            if ($dateMin != null){
                $filtre = "est_age_cal_bp < " .$dateMax.
                    " and est_age_cal_bp >= " .$dateMin;
            } else {
                $filtre = "est_age_cal_bp < " .$dateMax;
            }
        } else {
            $filtre = "est_age_cal_bp >= " .$dateMin;
        }
        $query = "select count(distinct(t_site.id_site)) as nb, country_iso_alpha2 as pays
                    from (
                    select id_depth, est_age_cal_bp
                    from r_has_estimated_age
                    where ".$filtre.
                    ") tabAge
                    join t_depth on t_depth.id_depth = tabAge.id_depth
                    join t_sample on t_sample.id_sample = t_depth.id_related_sample
                    join t_core on t_core.id_core = t_sample.id_core
                    join t_site on t_site.id_site = t_core.id_site
                    join tr_country on tr_country.id_country = t_site.id_country
                    group by country_iso_alpha2";

        $res = queryToExecute($query);

        $jsarray = null;
        $somme = 0;
        $nbMax = 0;
        $tabRes = fetchAll($res);

        foreach($tabRes as $elt){
            $somme += $elt["nb"];
            if ($elt["nb"] > $nbMax) $nbMax = $elt["nb"];
            $jsarray[] = "['".$elt["pays"]."',".$elt["nb"]."]";
        }

        $jsarray = implode(',', $jsarray);

        $qualite = new stdClass();
        $qualite->nbSites = $somme;
        $qualite->tabNbParElt = "[['Country', 'Number of sites'],".$jsarray."]";
        $qualite->nbMaxSiteParElt = $nbMax;
        return $qualite;
    }
}
