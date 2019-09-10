<?php
/* 
 * fichier Pages/Admin/rexport.php 
 * affiche la liste des fichiers qui ont été uploadé et sauvegardé 
 */
session_start();
require_once('../../config.php');
require_once '../../Models/user/WebAppRoleGCD.php';
require_once '../../Models/Site.php';
require_once '../../Models/Publi.php';
require_once '../../Models/Charcoal.php';
require_once '../../Library/connect_database.php';
require_once '../../Library/database.php';
if (isset($_SESSION['started']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    // on récupère les sites
    $tabSites = Site::getDataForRExport();
    // on récupère les publications
    $tabPublications = Publi::getDataForRExport();
    // on récupère les "datas" (charbons, profondeur, age par site)
    $tabCharcoal = Charcoal::getDataForRExport();
    $objRes = NULL;
    if(!empty($tabSites)) $objRes["sites_table.csv"] = $tabSites;
    if(!empty($tabPublications)) $objRes["publis_table.csv"] = $tabPublications;
    if(!empty($tabCharcoal)) $objRes["data_table.csv"] = $tabCharcoal;
    if ($objRes != null) creationArchiveZip($objRes);      
    /*
    if(!empty($_GET["f"])){
        // on récupère que le nom du fichier // en cas d'attaque
        $file_name = basename($_GET["f"]);
        // on vérifie que le nom de fichier n'a pas de caractères spéciaux // en cas d'attaque
        if (preg_match('#[a-zA-Z0-9].xlsx$#i', $file_name)){
            $file_path = REP_CHARCOALS_IMPORT.$file_name;

            // Redirect output to a client’s web browser
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$file_name.'"');
            header('Cache-Control: max-age=0');

            readfile($file_path);            
            exit;
        }
    }*/
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

        $zipname = 'rexport'.date("Ymd-hms").'.zip';
        $dlZipName = $zipname;
        $zipname = REP_TMP_ZIP.$zipname;
        
        $ret = $zip->open($zipname, ZipArchive::CREATE);
        if ($ret == false){
            logError("Error during creation of zip archive : " . $zipname);
            return "Error during data export";
        }

        // todo if != true exit("IMpossible d'ouvrir le fichier");
        foreach($obj as $key => $elt){
            if ($elt != null && (count($elt) > 0)) addFileToZipArchive($zip, $key, $elt);
        }
        
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
        header('Content-Disposition: attachment;filename='.$dlZipName);
        header("Content-Length: ".filesize($zipname));            
        
        //lecture du fichier à télécharger
        readfile($zipname);
        unlink($zipname);
    } catch (Exception $e){
        logError($e->getMessage());
        return "Error during data export";
    }  
}
   