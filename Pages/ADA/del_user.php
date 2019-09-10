<?php
/* 
 * fichier Pages/ADA/del_user.php 
 * 
 */ 

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))) {
    require_once './Models/Contact.php';
    require_once './Pages/EDA/change_Log.php';  
    require_once './Library/data_securisation.php';

    $obj = null;
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $user_id = $_GET['id'];
        
        if(WebAppUserGCD::delete($user_id))
        {
            writeChangeLog("del_user",$user_id); 

            echo '<div class="alert alert-success"><strong>Success !</strong> The user has been deleted.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error !</strong> An error occur during the deletion</div>';
        }

        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/user_view">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                Go to user list 
            </a>
        </div>';
        
    } else {
        echo '<div class="alert alert-info"><strong>Error</strong> Unknown identifier.</div>';
    }
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}
