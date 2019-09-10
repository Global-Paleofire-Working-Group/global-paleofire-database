<?php

require '../../config.php';
include_once(REP_LIB."connect_database.php");
include_once(REP_LIB."database.php");
include_once(REP_LIB."PaleofireHtmlTools.php");
require_once (REP_MODELS."AgeModel.php");
require_once (REP_MODELS."Sample.php");
require_once (REP_LIB."/data_securisation.php");

//if (isset($_SESSION['started'])) {
    if (isset($_GET['action'])){       
        // appel asynchrone pour renvoyer des données d'affichage au formulaire
        $action = $_GET['action'];
        if ($action == 'agemodel'){
            // renvoie de la liste des age_models en fct de l'id core passé en paramètre
            $id_core = $_GET['core'];
            $agemodels = AgeModel::getAll(null, null, sql_equal(Sample::ID_CORE, $id_core));
            $tab = null;
            foreach($agemodels as $agemodel){
                $tab[$agemodel->getIdValue()] = $agemodel->getName();
            }
            $json = json_encode($tab);
            echo($json);
        }
    }
//}