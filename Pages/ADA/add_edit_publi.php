<?php
/* 
 * fichier Pages/ADA/add_edit_publi.php 
 * 
 */ 
function add_edit_publi($Operation) {
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Publi.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';

    $max_length_text = 2000;

    $id = null;
    $errors = null;
    
    if (($Operation ==="edit")||($Operation==="edit_pending")) {    
        // cas d'une édition, on récupère la publication en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $obj = Publi::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        $obj->_status_id= 0;//la publication repasse en état "pending"
        // todo // si on ne récupère pas de publi rediriger
    } else if (!isset($_GET['id']) && ($Operation=="add")) {
        $obj = new Publi();
    } else {
        $errors[] = "Acces denied";
    }    
    
    if (isset($_POST['submitAdd']) && (empty($errors))) {   
        
        //affectation avec les données postées dans le formulaire
        if (isset($_POST[Publi::NAME])) {
            $citation = escapeString($_POST[Publi::NAME]);
            if (strlen($citation) < $max_length_text) {
                if($id == NULL && !empty(Publi::getObjectsFromNameValue(utf8_decode($citation)))){
                    // a publication with the same description already exists
                    $errors[] = "A publication with identical citation already exists";
                } else {
                    $obj->setNameValue(utf8_decode($citation));
                }
            }
            else $errors[] = "Citation must be less than ". $max_length_text . " characters";
        } else {
            $errors[] = "Citation missing";
        }

        if (isset($_POST[Publi::PUB_LINK])){
            $obj->_publi_link = utf8_decode(escapeString(($_POST[Publi::PUB_LINK])));
        }
        
        if (isset($_POST[Publi::DOI])){
            $obj->_doi = utf8_decode(escapeString($_POST[Publi::DOI]));
        }
        
        if ($Operation=="edit_pending") {    
            if (isset($_POST[Publi::ID_STATUS]) && is_numeric($_POST[Publi::ID_STATUS])){
                $obj->setStatusId($_POST[Publi::ID_STATUS]);
            } else {
                $errors[] = "Select a status";
            }
        } else {   
            //si c'est un contributeur ou un administrateur qui entre la donnée, cette dernière sera mise en attente de validation par un super-administrateur
            //si c'est un superadministrateur qui entre la donnée, cette dernière sera automatiquemenbt validée.
            if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                $obj->setStatusId(1); // la donnée est validée
            } else {
                $obj->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
            } 
        }

        if (empty($errors)){
            $errors = $obj->save($Operation);
        }

        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong><br>'.implode('</br>', $errors)."</div>";
        }

        switch($Operation) {
            case "edit":
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/publication_list&gcd_menu=CDA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to publications
                    </a>
                </div>';
                break;
            case "edit_pending":
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_publi&gcd_menu=CDA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to pending publications
                    </a>
                </div>';
                break;
            case "add":
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/publication_list&gcd_menu=CDA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to publications
                    </a>
                </div>';
                break;
            default:
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back 
                    </a>
                </div>';
                break;
        }
    }   

    if ((((!isset($_POST['submitAdd'])) || !empty($errors)))&&(!isset($_POST['deleteAdd']))) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
    <?php 
        switch ($Operation) {
            case "edit":
                echo '<h1>Editing publication : '.$obj->getName().'</h1>';     
                $redirection="CDA/publication_list&gcd_menu=CDA";
                break;
            case "add":
                echo '<h1>Add a new publication</h1>';
                $redirection="ADA/add_publi&gcd_menu=ADA";                    
                break;
            case "edit_pending":
                echo '<h1>Editing pending publication : '.$obj->getName().'</h1>';
                $redirection="Admin/validate_pending_publi&gcd_menu=ADA";                    
                break;
            default:
                echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php')\" value = 'Cancel' />";                       
                break;
        }
    ?>
            <!-- Formulaire de saisie d'une publication-->
            <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
                <!-- Cadre pour la note-->
                <fieldset class="cadre">
                    <legend>Publication</legend>
                    <p>
                        <label for="citation">Citation*</label>
                        <textarea name="<?php echo PUBLI::NAME; ?>" id="citation" maxlength="<?php echo $max_length_text; ?>"/><?php if (isset($obj)) echo $obj->getName(); ?></textarea>
                    </p>
                    <p>
                        <label for="link">Link</label>
                        <input type="text" name="<?php echo PUBLI::PUB_LINK; ?>" id="link" value="<?php if (isset($obj)) echo $obj->_publi_link; ?>"/>
                    </p>
                    <p>
                        <label for="doi">DOI <small>(Digital Object Identifier)</small></label>
                        <input type="text" name="<?php echo PUBLI::DOI; ?>" id="doi" value="<?php if (isset($obj)) echo $obj->_doi; ?>"/>
                    </p>
                </fieldset>
                <?php                        
                if ($Operation==="edit_pending") {            
                ?>
                <fieldset class="cadre">
                    <legend>Status</legend>
                    <p>
                        <label for="addPubli_status">Status</label>
                        <?php
                        $selectedId = (isset($obj))?$obj->getStatusId():null;
                        selectHTML(Publi::ID_STATUS, 'addPubli_status', 'Status', intval($selectedId));
                        ?>
                    </p>  
                </fieldset>
                <?php    
                }
                ?>  
                <!-- Boutons du formulaire !-->
                <p class="submit">
                    <?php
                    if ($Operation=="add"){
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Add' />";
                    } else {
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Save' />";
                    }
                    echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />";
                    ?>
                </p> 
            </form>
            <?php
        }
    } 
}
