<?php

/*
 * fichier Pages/ADA/del_age_model.php
 *
 */

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ( $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/AgeModel.php';
    require_once './Models/Site.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';


    $obj = null;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $obj = AgeModel::getObjectPaleofireFromId($id);
        if (isset($obj) && $obj != null){
            if (AgeModel::delObjectPaleofireFromId($id)){
                //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                writeChangeLog("del_age_model",$id);

                echo '<div class="alert alert-success"><strong>Success !</strong>The age model has been deleted.</div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error !</strong>An error occur during the deletion</div>';
            }
            echo '<div class="btn-toolbar" role="toolbar" align="right">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$obj->_core_id.'">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to core page
                    </a>
                </div>';
        }
    }
}
