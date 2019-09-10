<?php
/* 
 * fichier Pages/ADA/del_contact.php 
 * 
 */ 

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))) {
    require_once './Models/Contact.php';
    require_once './Models/Core.php';
    require_once './Models/AgeModel.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';  
    require_once './Library/data_securisation.php';


    $obj = null;
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $id = $_GET['id'];
        $obj = Contact::getObjectPaleofireFromId($id);
        
        $listUser = WebAppUserGCD::getListUserForAContact($obj->getIdValue());
        $listIsAuthor = Core::getCoresWithCharcoalsFromAuthor($obj->getIdValue());
        $listIsContributor = Core::getCoresWithCharcoalsFromContributor($obj->getIdValue());
        $listAgeModel = AgeModel::getAgeModelFromModeller($obj->getIdValue());
        
        if (isset($obj) && $obj != null){
            if(empty($listUser) && empty($listIsAuthor) && empty($listIsContributor) && empty($listAgeModel)){
                if (Contact::delObjectPaleofireFromId($id)){
                    writeChangeLog("del_contact",$id); 

                    echo '<div class="alert alert-success"><strong>Success !</strong>The contact has been deleted.</div>';
                } else {
                    echo '<div class="alert alert-danger"><strong>Error !</strong> An error occur during the deletion</div>';
                }

                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/contact_list">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go to contact list 
                    </a>
                </div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error</strong> Deletion not allowed</div>';
            }
        } else {
            echo '<div class="alert alert-info"><strong>Error</strong> Unknown identifier.</div>';
        }
    } else {
        echo '<div class="alert alert-info"><strong>Error</strong> Unknown identifier.</div>';
    }
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}
