<?php
/* 
 * fichier Pages/CDA/affiliation_view_ajax.php 
 *  
 */
session_start();
require_once('../../config.php');
$path = $_SERVER["DOCUMENT_ROOT"] . GLOBAL_RACINE . 'Library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
$path = './Library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once '../../Models/user/WebAppRoleGCD.php';
require_once '../../Models/user/WebAppPermGCD.php';
require_once '../../Models/user/WebAppUserGCD.php';
/** Include PHPExcel */
require_once '../../Library/PHPExcel/Classes/PHPExcel.php';
require_once '../../Models/Contact.php';
require_once '../../Library/connect_database.php';
require_once '../../Library/database.php';

if ((isset($_SESSION['gcd_user_role'])) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
    // export de toute la liste des mails
    $filename = 'MailingList.csv';
    // on créer la première ligne d'entête avec le nom des champs
    $lines = "";
    //echo "fn=".$filename;
    $arrayData = Contact::getMailingList();
    $tabEntetes = Array('LASTNAME', 'FIRSTNAME', 'EMAIL');
    $lines .= implode($tabEntetes, ';')."\n";
    foreach($arrayData as $elt){
        $lines .= implode($elt, ';')."\n";
    }
    
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Type: text/csv");
    header("Content-disposition:".$filename);
    
    echo $lines;
}