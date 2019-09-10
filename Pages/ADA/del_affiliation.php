<?php

/* 
 * fichier Pages/ADA/del_affiliation.php 
 * 
 */ 

//function del_affiliation(){
    if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ( $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
        require_once './Models/Affiliation.php';
        require_once './Models/Contact.php';
        require_once './Library/PaleofireHtmlTools.php';
        require_once './Pages/EDA/change_Log.php';
        require_once './Library/data_securisation.php';

        $obj = null;
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $obj = Affiliation::getObjectPaleofireFromId($id);
            if (isset($obj) && $obj != null){
                // on vérifie que l'affiliation n'a pas de contact, sinon la suppression est interdite
                $tabContacts = $obj->getContacts();
                if (empty($tabContacts)){

                    if (Affiliation::delObjectPaleofireFromId($id)){
                        //xli ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                        writeChangeLog("del_affiliation", $id); 
                        echo '<div class="alert alert-success"><strong>Success !</strong>The affiliation is deleted.</div>';
                    } else {
                        echo '<div class="alert alert-danger"><strong>Error !</strong>An error occur during the deletion</div>';
                    }
                }
            }
        }
        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/affiliation_list&gcd_menu=CDA">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                Go back to affiliations
            </a>
        </div>';
    }
//}