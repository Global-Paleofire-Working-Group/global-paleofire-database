<?php

/*
 * fichier Pages/ADA/del_note_age_model.php
 *
 */

    if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role'])) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    require_once './Models/NoteAgeModel.php';
    require_once './Models/Site.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    require_once './Pages/EDA/change_Log.php';



    $obj = null;
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $obj = NoteAgeModel::getObjectPaleofireFromId(data_securisation::toBdd($id));
        if (isset($obj) && $obj != null){
            if (NoteAgeModel::delObjectPaleofireFromId($id)){
                //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                writeChangeLog("del_note_age_model",$id);

                echo '<div class="alert alert-success"><strong>Success !</strong>The note has been deleted.</div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error !</strong>An error occur during the deletion</div>';
            }
            $age_model = AgeModel::getObjectPaleofireFromId($obj->getAgeModelId());
            if ($age_model != null){
                echo '<div class="btn-toolbar" role="toolbar">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$age_model->_core_id.'">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                            Go back to core page
                        </a>
                    </div>';
            }
        }
    }
}
