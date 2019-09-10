<?php
/* 
 * fichier Pages/CDA/site_view_ajax.php 
 * ajout/suppression d'une publication, page appelée depuis la visualisation d'un site
 */

if ((isset($_SESSION['gcd_user_role'])) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
    require_once './Models/NoteCore.php';
    require_once './Models/Site.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';


    if (isset($_GET['site']) && isset($_GET['publi']) && isset($_GET['act'])){
        $action = $_GET['act'];
        $id_site = $_GET['site'];
        $site = SITE::getObjectPaleofireFromId($id_site);
        $id_publi = $_GET['publi'];
        $publi = Publi::getObjectPaleofireFromId($id_publi);
        if ($site != NULL && $publi != NULL){
            // ajout d'une publication à un site
            if ($action == "add"){
                $site->_liste_publi_id[]  = $id_publi;
                $erreur = $site->save("Modify");
                if ($erreur != NULL && count($erreur) > 0){
                    echo '<div class="alert alert-danger"><strong>Error !</strong>Error occurs while recording</div>';
                    logError("site_view_ajax - site->save return error" . $erreur);
                } else {          
                    echo '<div class="alert alert-success"><strong>Success !</strong>The publication has been linked.</div>';

                };
            } else if ($action == "rem"){
                // suppression du lien entre une publication et un site
                $erreur = $site->removeReferencePubli($id_publi);
                if (empty($erreur)){          
                    echo '<div class="alert alert-success"><strong>Success !</strong>The publication has been unlinked.</div>';
                } else {
                    echo '<div class="alert alert-danger"><strong>Error !</strong>Error occurs while removing the publication</div>';
                    logError("site_view_ajax - site->remove publication return error" . $erreur);
                }
            }
        } else { 
            echo '<div class="alert alert-danger"><strong>Error !</strong>Site id and/or publication id are unknown</div>';
            logError("site_view_ajax - unknown ids");
        }
    }else { 
        echo '<div class="alert alert-danger"><strong>Error !</strong>Site id and/or publication id are unknown</div>';
        logError("site_view_ajax - unset ids");
    }
    echo '<div class="btn-toolbar" role="toolbar" align="right">
        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/site_view&gcd_menu=CDA&site_id='.$id_site.'">
            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
            Go back to site page
        </a>
    </div>';
}