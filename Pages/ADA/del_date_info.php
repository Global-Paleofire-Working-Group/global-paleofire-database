<?php
/*
 * fichier Pages/ADA/del_date_info.php
 *
 */

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ( $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {

    //require_once './Models/DateInfo.php';
    require_once './Models/Sample.php';
    require_once './Library/data_securisation.php';
    require_once './Pages/EDA/change_Log.php';



    $obj = null;
    if (isset($_GET['id'])) {
        $id = data_securisation::toBdd($_GET['id']);
        $obj = DateInfo::getObjectPaleofireFromId($id);

        // ou on supprime complément la date info de tous les modèles
        if (isset($obj) && $obj != null && Age::delByDateInfo($id_date_info) && DateInfo::delObjectPaleofireFromId($id)){
              //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
              writeChangeLog("del_date_info",$id);

            echo '<div class="alert alert-success"><strong>Success ! </strong>The date info has been deleted.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error ! </strong>An error occur during the deletion</div>';
        }
        $id_sample = $obj->_sample_id;
        $sample = Sample::getObjectPaleofireFromId($id_sample);
        $id_core = $sample->_sample_core_id;
        echo '<div class="btn-toolbar" role="toolbar" align="left">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$id_core.'">
                    <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                    Go back to core page
                </a>
            </div>';
    }
}
