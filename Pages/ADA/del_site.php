<?php
/* 
 * fichier Pages/ADA/del_site.php 
 * 
 */
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role'])) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    require_once './Models/Site.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';

    $obj = null;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        if (is_numeric($id)){
            $site = Site::getObjectPaleofireFromId($id);
            if ($site != NULL){
                // si le site a des carottes on ne supprime pas
                if ($site->countCore() == 0){
                    // si le site a des publis pas de suppression
                    if ($site->getPubliReferencedBySite() == NULL){
                        // suppression du site
                        $site_name = $site->getName();
                        if (Site::delObjectPaleofireFromId($id)){
                            //ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                            writeChangeLog("-- del_site ".$site_name,$id); 
                            echo '<div class="alert alert-success"><strong>Success ! </strong>The site has been deleted.</div>';
                        } else {
                            echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion</div>';
                        }
                        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/site_view_list">
                                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                                Go to sites list
                            </a>
                        </div>';
                    } else {
                        echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (the site is referenced by publication(s)</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (the site has core(s))</div>';
                }
            } else {
                echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (unknown site)</div>';
            }
        }else {
            echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (unknown site)</div>';
        }
    } else {
        // TODO REDIRECTION
    }
}
