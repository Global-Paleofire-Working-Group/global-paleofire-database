<?php
/* 
 * fichier Pages/ADA/del_core.php 
 * 
 */
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ( $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Core.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';

    $obj = null;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        if (is_numeric($id)){
            $obj = Core::getObjectPaleofireFromId($id);
            if ($obj != null){
                // si le core a des samples on ne supprime pas
                if ($obj->countSamples() == 0){
                    if (NoteCore::countPaleofireObjects(NoteCore::ID_CORE." = ".$id) == 0){
                        $core_name = $obj->getName();
                        if (Core::delObjectPaleofireFromId($id)){
                            //ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                            writeChangeLog("-- del_core ".$core_name, $id); 

                            echo '<div class="alert alert-success"><strong>Success ! </strong>The core has been deleted.</div>';
                        } else {
                            echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion</div>';
                        }
                        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                                <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/site_view&site_id='.$obj->getSiteId().'">
                                    <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                                    Go to site view
                                </a>
                            </div>';
                    } else {
                        echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (the core has note(s))</div>';
                    }
                } else {
                    echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (the core has sample(s))</div>';
                }
            } else {
                echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (unknown core)1</div>';
            }
        } else {
            echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion (unknown core)2</div>';
        }
    }else {
        //TODO redirection
    }
}