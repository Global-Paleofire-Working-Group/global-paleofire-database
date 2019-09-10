<?php
/* 
 * fichier Pages/ADA/del_publi.php 
 * 
 */

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role'])) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    require_once './Models/Publi.php';
    require_once './Models/Site.php';
    require_once './Models/Core.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';
    if (isset($_GET['id'])) {
        $id = data_securisation::toBdd($_GET['id']);
        $obj = Publi::getObjectPaleofireFromId($id);
        if (isset($obj) && $obj != null){
            // deletion is possible if the publication is not bind to a site or charcoal
            if(empty(Site::getSitesReferencedByPublication($obj->getIdValue()))){
                if(empty(Core::getCoresWithCharcoalsReferencedByPublication($obj->getIdValue()))){
                    if (Publi::delObjectPaleofireFromId($id)){
                        writeChangeLog("del_pub",$id); 
?>
    <div class="alert alert-success"><strong>Success ! </strong>The publication has been deleted.</div>
<?php
                    } else {
?>
    <div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion</div>
<?php 
                    }
?>
    <div class="btn-toolbar" role="toolbar" style="float:left">
        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/publication_list&gcd_menu=CDA">
            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
            Go to publication list
        </a>
    </div>
<?php
                } else {
                    echo '<div class="alert alert-danger"><strong>Error</strong> Deletion not allowed</div>';
                }
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