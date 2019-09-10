<?php

/* 
 * fichier Pages/EDA/change_Log.php 
 * Auteur : XLI 17/03/16
 */

//écrit une chaîne de caractères $String dans un fichier $File
function writeFile($File, $String)
{
//on crée le fichier s'il n'existe pas
    if (file_exists($File)) 
        {    $fp = fopen($File,'a'); }
    else                        
        {    $fp = fopen($File,'w'); }
    fwrite($fp,$String); 
    fclose($fp); 
} 

//écriture dans le fichier journal en fonction de la valeur de $Modif_DB
// $Modif_DB : "add, edit ou del" + "_" + "nom de la table modifiée"
// $id_obj : identifiant incrémental de la base de données 

function writeChangeLog($Modif_DB,$id_obj)
    {
    //print_r("modif=".$Modif_DB. ", id_obj=".$id_obj);
    $date = date('c'); // on ajoutera dans le fichier la date de la modification de la Base de Données
    $sChange_Log = $date."-- ";
    if (!empty($_SESSION['gcd_login'])) {        
        $sChange_Log .= $_SESSION['gcd_login']; // on ajoute le login de la personne ayant effectué la modification de la BD
    }
   
    switch ($Modif_DB){
        case 'add_affiliation':
            $sChange_Log .= " -- add affiliation: ".$id_obj." => name : ".utf8_decode($_POST[Affiliation::NAME])."\n"; 
            break;
        case 'add_age_model':
            $sChange_Log .= " -- add age model: ".$id_obj." => name : ".utf8_decode($_POST[AgeModel::NAME])."\n"; 
            break;
        case 'add_sample':
            $sChange_Log .= " -- add new sample => ". $id_obj; 
            break;
        case 'update_sample':
            $sChange_Log .= " -- update sample => ". $id_obj; 
            break;
        case 'add_charcoal':
            $sChange_Log .= " -- add new data charcoal => site: "; 
            break;
        case 'add_contact':
            $sChange_Log .= " -- add contact: ".$id_obj." => first name : ".utf8_decode($_POST[Contact::FIRSTNAME]).", last name : ".utf8_decode($_POST[Contact::LASTNAME])."\n"; 
            break;
        case 'add_core':
            $sChange_Log .= " -- add core: ".$id_obj." => name : ".utf8_decode($_POST[Core::NAME]).", site : ".utf8_decode($_POST[Site::ID])."\n"; 
            break;
        case 'add_date_comment':
            $sChange_Log .= " -- add date comment : ".$id_obj." => name : ".utf8_decode($_POST[DateComment::NAME])."\n"; 
            break;
        case 'add_date_info':
            $sChange_Log .= " -- add date info : ".$id_obj." => name : ".utf8_decode($_POST[Dateinfo::NAME])."\n"; 
            break;
        case 'add_note_age_model':
            $sChange_Log .= " -- add note age model : ".$id_obj." => name : ".utf8_decode($_POST[NoteAgeModel::NAME])."\n"; 
            break;      
        case 'add_note_core':
            $sChange_Log .= " -- add note core : ".$id_obj." => name : ".utf8_decode($_POST[NoteCore::NAME])."\n"; 
            break;      
        case 'add_pub':
            $sChange_Log .= " -- add pub : ".$id_obj." => name : ".utf8_decode($_POST[Publi::NAME])."\n"; 
            break; 
        case 'add_site':
            //print_r(", id2=".utf8_decode($_POST[Site::NAME]));
            $sChange_Log .= " -- add site : ".$id_obj." => name : ".utf8_decode($_POST[Site::NAME])."\n"; 
            break; 
        case "edit_affiliation":
            $sChange_Log .= " -- edit affiliation: ".$id_obj." => name : ".utf8_decode($_POST[Affiliation::NAME])."\n"; 
            break;
        case 'edit_age_model':
            $sChange_Log .= " -- edit age model: ".$id_obj." => name : ".utf8_decode($_POST[AgeModel::NAME])."\n"; 
            break;
        case 'edit_charcoals':
            $sChange_Log .= " -- edit charcoal: ".$id_obj." => name : ".utf8_decode($_POST[Charcoal::NAME])."\n"; 
            break;
        case "edit_contact":
            $sChange_Log .= " -- edit contact: ".$id_obj." => name : ".utf8_decode($_POST[Contact::NAME])."\n"; 
            break;
        case 'edit_core':
            $sChange_Log .= " -- edit core: ".$id_obj." => name : ".utf8_decode($_POST[Core::NAME]).", site : ".utf8_decode($_POST[Site::ID])."\n"; 
            break;
        case 'edit_date_comment':
            $sChange_Log .= " -- edit date comment : ".$id_obj." => name : ".utf8_decode($_POST[DateComment::NAME])."\n"; 
            break;
        case 'edit_date_info':
            $sChange_Log .= " -- edit date info : ".$id_obj." => name : ".utf8_decode($_POST[Dateinfo::NAME])."\n"; 
            break;
        case 'edit_note_age_model':
            $sChange_Log .= " -- edit note age model : ".$id_obj." => name : ".utf8_decode($_POST[NoteAgeModel::NAME])."\n"; 
            break;      
        case 'edit_note_core':
            $sChange_Log .= " -- edit note core : ".$id_obj." => name : ".utf8_decode($_POST[NoteCore::NAME])."\n"; 
            break;      
        case 'edit_pub':
            $sChange_Log .= " -- edit pub : ".$id_obj."\n"; 
            break; 
        case 'edit_site':
            $sChange_Log .= " -- edit site : ".$id_obj." => name : ".utf8_decode($_POST[Site::NAME])."\n"; 
            break; 
        case 'del_affiliation':
            $sChange_Log .= " -- del affiliation: ".$id_obj."\n"; 
            break;
        case 'del_age_model':
            $sChange_Log .= " -- del age model: ".$id_obj."\n"; 
            break;
        case 'del_charcoal':
            $sChange_Log .= " -- del charcoal: ".$id_obj."\n"; 
            break;
        case 'del_contact':
            $sChange_Log .= " -- del contact: ".$id_obj."\n"; 
            break;
        case 'del_core':
            $sChange_Log .= " -- del core: ".$id_obj." ".$Modif_DB." \n"; 
            break;
        case 'del_date_comment':
            $sChange_Log .= " -- del date comment : ".$id_obj."\n"; 
            break;
        case 'del_date_info':
            $sChange_Log .= " -- del date info : ".$id_obj."\n"; 
            break;
        case 'del_note_age_model':
            $sChange_Log .= " -- del note age model : ".$id_obj."\n"; 
            break;      
        case 'del_note_core':
            $sChange_Log .= " -- del note core : ".$id_obj."\n"; 
            break;      
        case 'del_pub':
            $sChange_Log .= " -- del pub : ".$id_obj."\n"; 
            break;
        case 'remove_pub':
            $sChange_Log .= " -- remove pub : ".$id_obj."\n"; 
            break;
        case 'del_site':
            $sChange_Log .= " -- del site : ".$id_obj." ".$Modif_DB."\n"; 
            break; 
        default :
            $sChange_Log .=  $Modif_DB. " ". $id_obj."\n";
    }
    
    $file_name = REP_LOG."Paleofire_Change_Log.txt";
    writeFile($file_name, $sChange_Log);
    
}