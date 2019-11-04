<?php
session_start();
require_once('../../config.php');
$path = $_SERVER["DOCUMENT_ROOT"] . GLOBAL_RACINE . 'Library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
$path = $_SERVER["DOCUMENT_ROOT"] . GLOBAL_RACINE . 'Models';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
$path = './Library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

require_once (REP_MODELS."/user/WebAppRoleGCD.php");
require_once (REP_MODELS."/user/WebAppPermGCD.php");
require_once (REP_MODELS."/user/WebAppUserGCD.php");
require_once (REP_LIB."data_securisation.php");

// require_once '../../Models/user/WebAppRoleGCD.php';
// require_once '../../Models/user/WebAppPermGCD.php';
// require_once '../../Models/user/WebAppUserGCD.php';
include_once 'Site.php';
include_once 'connect_database.php';
include_once 'database.php';
// require_once('../../Library/data_securisation.php');

if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)){
    $isAdmin = false;
    if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
            || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
        $isAdmin = TRUE;
    }
    if (isset($_GET['action'])){
        // appel asynchrone pour renvoyer des données d'affichage au formulaire
        $action = $_GET['action'];
        if ($action == 'site' && isset($_GET['country'])){
            // renvoi de la liste des sites en fct de l'identifiant du pays passé en paramètre
            $tab_id_country = explode(',',$_GET['country']);
            $sites = Site::getStaticList();
            $tab = null;
            foreach($sites as $elt){
                if (in_array($elt[Country::ID], $tab_id_country)) $tab[$elt[site::ID]] = $elt[Site::NAME];
            }
            $json = json_encode($tab);
            echo($json);
        }
        // appel asynchrone pour renvoyer des données d'affichage au formulaire
        else if ($action == 'country' && isset($_GET['region'])){
            // renvoi de la liste des pays en fct de l'identifiant de la
            // région passée en paramètre
            $tab_id_region = explode(',', $_GET['region']);
            $tab = [];
            $listeCountriesByRegion = Region::getListCountriesByRegion();
            if ($listeCountriesByRegion != NULL){
                foreach ($tab_id_region as $id_region){
                    $countries_tab = explode(",", $listeCountriesByRegion[$id_region]);
                    foreach($countries_tab as $id_country){
                        $tab[$id_country] = Country::getNameFromStaticList($id_country);
                    }
                }
            }
            asort($tab);
            $json = json_encode($tab);
            echo($json);
        }
        // /appel asynchrone pour renvoyer l'export des données
        else if ($action == 'export' && isset($_GET['region']) && isset($_GET['country']) && isset($_GET['site'])) {
            $id_region = $_GET['region'];
            $id_country = $_GET['country'];
            $id_site = $_GET['site'];

            if ($id_region != null) {
                $fields = null;
                $fieldsSample = null;
                $fieldsDateInfo = null;

                $interval_time_min = null;
                $interval_time_max = null;

                if (isset($_GET['f'])) $fields = $_GET['f'];
                if (isset($_GET['fs'])) $fieldsSample = $_GET['fs'];
                if (isset($_GET['fd'])) $fieldsDateInfo = $_GET['fd'];

                if (isset($_GET['tmin'])) $interval_time_min = $_GET['tmin'];
                if (isset($_GET['tmax'])) $interval_time_max = $_GET['tmax'];


                // récupération des données
                $objRes = Site::getDataForExportProxyFire($id_region, $id_country, $id_site, $fields, $fieldsSample, $fieldsDateInfo, $interval_time_min, $interval_time_max, $isAdmin);
                if ($objRes != null) creationArchiveZip($objRes);
            } else {
                // todo renvoie d'un msg d'erreur ?
            }
        }
        else if ($action == 'export' && isset($_GET['ids'])) {
            $tabIds = json_decode($_GET['ids']);
            $fields = null;
            $fieldsSample = null;
            $fieldsDateInfo = null;
            $interval_time_min = null;
            $interval_time_max = null;
            if (isset($_GET['f'])) $fields = $_GET['f'];
            if (isset($_GET['fs'])) $fieldsSample = $_GET['fs'];
            if (isset($_GET['fd'])) $fieldsDateInfo = $_GET['fd'];
            if (isset($_GET['tmin'])) $interval_time_min = $_GET['tmin'];
            if (isset($_GET['tmax'])) $interval_time_max = $_GET['tmax'];
            // récupération des données
            $objRes = Site::getDataProxyFireForExportbyCoreIDS($tabIds, $fields, $fieldsSample, $fieldsDateInfo, $interval_time_min, $interval_time_max, $isAdmin);
            if ($objRes != null) creationArchiveZip($objRes);
        }
    }
    else if (isset($_POST['action']))
    {
        $action = $_POST['action'];
        // /appel asynchrone pour renvoyer l'export des données
        if ($action == 'export' && isset($_POST['ids'])) {

            $tabIds = json_decode($_POST['ids']);
            $fields = null;
            $fieldsSample = null;
            $fieldsDateInfo = null;
            $interval_time_min = null;
            $interval_time_max = null;
            if (isset($_POST['f'])) $fields = $_POST['f'];
            if (isset($_POST['fs'])) $fieldsSample = $_POST['fs'];
            if (isset($_POST['fd'])) $fieldsDateInfo = $_POST['fd'];
            if (isset($_POST['tmin'])) $interval_time_min = $_POST['tmin'];
            if (isset($_POST['tmax'])) $interval_time_max = $_POST['tmax'];

            // récupération des données
            //$objRes = Site::getDataForExportbyCoreIDS($tabIds, $fields, $fieldsSample, $fieldsDateInfo, $isAdmin);
            $objRes = Site::getDataProxyFireForExportbyCoreIDS($tabIds, $fields, $fieldsSample, $fieldsDateInfo, $interval_time_min, $interval_time_max, $isAdmin);
            if ($objRes != null) creationArchiveZip($objRes);
        }
    }
}

function addFileToZipArchive($zip, $filename, $arrayData){
    $csv_caractere_delimiteur ='","';
    // on crée la première ligne d'entête avec le nom des champs
    $lines = "";
    $tabEntetes = array_keys($arrayData[0]);
    $lines .= '"'. implode($tabEntetes, $csv_caractere_delimiteur)."\"\n";
    foreach($arrayData as $elt){
        $lines .= '"'.implode($elt, $csv_caractere_delimiteur)."\"\n";
    }
    $ret = $zip->addFromString($filename, $lines);
}

function creationArchiveZip($obj){
    try{
        // création d'une archive zip
        $zip = new ZipArchive();
        $zipname = NULL;

        if (isset($_GET['fn']) && $_GET['fn'] != "" && $_GET['fn'] != NULL) {
            $chars = array(".", "/", "\\");
            $zipname = str_replace($chars, "", $_GET['fn']).'.zip';
        } else {
            $zipname = 'paleofireData'.date("Ymd-hms").'.zip';
        }
        $zipname = REP_TMP_ZIP.$zipname;

        $ret = $zip->open($zipname, ZipArchive::CREATE);
        if ($ret == false){
            logError("Error during creation of zip archive : " . $zipname);
            return "Error during data export";
        }

        // todo if != true exit("IMpossible d'ouvrir le fichier");
        if ($obj->tabSites != null && (count($obj->tabSites) > 0)) addFileToZipArchive($zip, "cores.csv", $obj->tabSites);
        if ($obj->tabCharcoals != null && (count($obj->tabCharcoals) > 0)) addFileToZipArchive($zip, "charcoals.csv", $obj->tabCharcoals);
        if ($obj->tabDatesInfos != null && (count($obj->tabDatesInfos) > 0)) addFileToZipArchive($zip, "datesinfos.csv", $obj->tabDatesInfos);

        $ret = $zip->close();
        if ($ret == false){
            logError("Error during closing of archive zip : " . $zipname .", check path and rights of the file");
            return "Error during data export";
        }
        if (file_exists($zipname) == FALSE){
            logError("Error during closing of archive zip : " . $zipname .", check path and rights of the file");
            return "Error during data export";
        }

        // on crée les entête pour le fichier à télécharger
        header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('Content-Type: application/force-download');
        header('Content-Disposition: attachment;filename='.$zipname);
        header("Content-Length: ".filesize($zipname));

        //lecture du fichier à télécharger
        readfile($zipname);
        unlink($zipname);
    } catch (Exception $e){
        logError($e->getMessage());
        return "Error during data export";
    }
}
