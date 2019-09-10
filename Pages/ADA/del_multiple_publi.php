<?php
/* 
 * fichier Pages/ADA/del_multiple_publi.php 
 * 
 */

function del_multiple_publi() {
    if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role'])) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
        if (isset($_POST["cb_publi"]) && (!empty($_POST['cb_publi']))) {
            require_once './Models/Publi.php';
            require_once './Library/PaleofireHtmlTools.php';
            require_once './Pages/EDA/change_Log.php';
            require_once './Library/data_securisation.php';

            $publi_to_delete = $_POST['cb_publi'];
            $id_list = implode("','",$publi_to_delete);
            $del_query1 = "DELETE FROM `r_site_is_referenced` WHERE `ID_PUB` IN ('$id_list')";
            queryToExecute($del_query1);
            $del_query2 = "DELETE FROM `r_has_pub` WHERE `ID_PUB` IN ('$id_list')";
            queryToExecute($del_query2);
            $del_query = "DELETE FROM `t_pub` WHERE `ID_PUB` IN ('$id_list')";
            queryToExecute($del_query);
        }else {
             $errors[] = "Nothing to delete";
        }
    }else {
         $errors[] = "Access denied";
    }
    
    if (empty($errors)){
        echo '<div class="alert alert-success"><strong>Success !</strong> Data have been deleted.</div>';
    } else {
            echo '<div class="alert alert-danger"><strong>Error !</strong></br>'.implode('</br>', $errors)."</div>";
    }
    echo '<div class="btn-toolbar" role="toolbar" style="float:left">
        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/publication_list&gcd_menu=CDA">
            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
            Go back to publications
        </a>
    </div>';
}

