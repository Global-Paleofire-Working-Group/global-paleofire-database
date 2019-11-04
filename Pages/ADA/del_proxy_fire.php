<?php
/*
 * fichier Pages/ADA/del_proxy_fire.php
 *
 */

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ( $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/ProxyFireData.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';


    $obj = null;
    $id_core = null;
    if (isset($_GET['id']) && isset($_GET['id_core'])) {
        $id = $_GET['id'];
        $id_core = $_GET['id_core'];
        if (is_numeric($id) && is_numeric($id_core)){
            if(ProxyFireData::delObjectPaleofireFromId($id)){
                writeChangeLog(" del_proxy_fire id_proxy_fire_data=",$id);
                echo '<div class="alert alert-success"><strong>Success !</strong> The fire proxy have been deleted.</div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error !</strong> An error occur during the deletion</div>';
            }
        } else {
            echo '<div class="alert alert-danger"><strong>Error !</strong> Unknown identifier</div>';
        }
    } else if (isset($_GET['id_core'])){
        $id_core = $_GET['id_core'];
        if (is_numeric($id_core)){
            if(ProxyFireData::delAllProxyFireForCore($id_core)){
                //fichier log envoyé par mail aux administrateurs
                writeChangeLog(" del_proxy_fire id_core=",$id_core);
                echo '<div class="alert alert-success"><strong>Success !</strong> Charcoal have been deleted.</div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error !</strong> An error occur during the deletion of one or more charcoal</div>';
            }
        } else {
            echo '<div class="alert alert-danger"><strong>Error !</strong> Unknown identifier</div>';
        }
    }
    echo '<div class="btn-toolbar" role="toolbar" style="float:left">
        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&core_id='.$id_core.'&gcd_menu=CDA"&gcd_menu=CDA">
            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
            Go back to core page
        </a>
    </div>';
}
