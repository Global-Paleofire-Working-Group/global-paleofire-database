<?php
/* 
 * fichier Pages/Admin/view_uploaded_files.php 
 * affiche la liste des fichiers qui ont été uploadé et sauvegardé 
 */
session_start();
require_once('../../config.php');
require_once '../../Models/user/WebAppRoleGCD.php';
if (isset($_SESSION['started']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {

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
    }
}   
   