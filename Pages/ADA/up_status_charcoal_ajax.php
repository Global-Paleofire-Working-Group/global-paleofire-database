<?php
/* 
 * fichier \Pages\ADA\up_status_charcoal_ajax.php
 * 
 */ 

$tab = Array("result"=>"fail");

session_start();
if (isset($_SESSION['started'])) {
    require_once ("../../config.php");
    require_once ("../../Library/WebAppUser/WebAppRole.php");
    require_once ("../../Models/user/WebAppRoleGCD.php");
    
    require_once '../../Models/Charcoal.php';
    require_once('../../Library/data_securisation.php');
    require_once '../../Library/PaleofireHtmlTools.php';
    require_once '../../Library/connect_database.php';
    require_once '../../Library/database.php';
    
    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
        if (isset($_GET['id_core']) && is_numeric($_GET['id_core'])) {
            if(isset($_GET['s']) && is_numeric($_GET['s'])) {
            
                $id_core = $_GET["id_core"];
                $status = $_GET["s"];
                
                connectionBaseInProgress();
                
                // validation of the existence of the status
                $status = Status::getObjectFromStaticList($status);
                if (!empty($status)){
                    // validation of the existence of the core
                    if(Core::idExistsInDatabase($id_core)){
                        // update of the status for all charcoal of the core
                        if (Charcoal::updateStatusAllCharcoalsForCore($id_core, $status->getIdValue())){
                            $tab = Array("result"=>"success", "core_id" => $id_core, "status" => $status->getName());
                        } else {
                            $tab = Array("result"=>"fail", "msg"=>"An error occur during the deletion");
                        }
                    } else {
                        $tab = Array("result"=>"fail", "msg"=>"Unknown core identifier");
                    }
                } else {
                    $tab = Array("result"=>"fail", "msg"=>"Unknown status identifier");
                }
            } else {
                $tab = Array("result"=>"fail", "msg"=>"Unknown status identifier");
            }
        } else {
            $tab = Array("result"=>"fail", "msg"=>"Unknown core identifier");
        }   
    }
}

$json = json_encode($tab);
echo($json);
   
