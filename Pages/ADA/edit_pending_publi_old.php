<?php
/* 
 * fichier Pages/ADA/edit_pending_publi.php 
 * Auteur : XLI 
*/
if (isset($_SESSION['started'])) {
    require_once './Models/Publi.php';
    require_once './Models/Status.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    //require_once './Pages/ADA/del_publi2.php';


    $max_length_text = 255;
    
    $id = null;
    if (isset($_GET['id'])) {
        // cas d'une édition, on récupère la publication en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $obj = Publi::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        // todo // si on ne récupère pas de publi rediriger
    } else {
        $obj = new Publi();
    }
    
    if (isset($_POST['deleteAdd'])) {
        
       del_publi();
             
    }
    if (isset($_POST['submitAdd'])) {
        
        $errors = null;
        
        //affectation avec les données postées dans le formulaire
        if (testPost(Publi::NAME)) {
            $citation = $_POST[Publi::NAME]; 
            if (strlen($citation) < $max_length_text) $obj->setNameValue(utf8_decode($citation));
            else $errors[] = "Citation must be less than ". $max_length_text . " characters";
        } else {
            $errors[] = "Citation missing";
        }
        
        if (testPost(Publi::PUB_ABBREV)){
            $obj->_publi_abbrev = utf8_decode($_POST[Publi::PUB_ABBREV]);
        }
        
        if (testPost(Publi::PUB_LINK)){
            $obj->_publi_link = utf8_decode($_POST[Publi::PUB_LINK]);
        }
        
        if (testPost(Publi::DOI)){
            $obj->_doi = utf8_decode($_POST[Publi::DOI]);
        }
        
        if (testPost(Publi::ID_STATUS)) {
            if (is_numeric($_POST[Publi::ID_STATUS])){
                $obj->setStatusId($_POST[Publi::ID_STATUS]);
            } else {
                $errors[] = "Select a status";
            }
        }
        
        if (empty($errors)){
            $errors = $obj->save();
        }
        
        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Success !</strong></div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }  
         echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_publi&gcd_menu=ADA">
                 
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to publications list
                    </a>
                </div>';
    }
    
    if ((((!isset($_POST['submitAdd'])) || !empty($errors)))&&(!isset($_POST['deleteAdd']))) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
        <?php 
            if ($id != null) {
               echo '<h1>Editing publication : '.$obj->getName().'</h1>';
            } else {
               echo '<h1>Add a new publication</h1>';
            }
        ?>
            <!-- Formulaire de saisie d'une publication-->
            <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
                <!-- Cadre pour la note-->
                <fieldset class="cadre">
                    <legend>Publication</legend>
                    <p>
                        <label for="citation">Citation *</label>
                        <textarea name="<?php echo PUBLI::NAME; ?>" id="citation" maxlength="300"/><?php if (isset($obj)) echo $obj->getName(); ?></textarea>
                    </p>
                    <p>
                        <label for="link">Link</label>
                        <input type="text" name="<?php echo PUBLI::PUB_LINK; ?>" id="link" value="<?php if (isset($obj)) echo $obj->_publi_link; ?>"/>
                    </p>
                    <p>
                        <label for="doi">DOI (Digital Object Identifier)</label>
                        <input type="text" name="<?php echo PUBLI::DOI; ?>" id="doi" value="<?php if (isset($obj)) echo $obj->_doi; ?>"/>
                    </p>
                    <p>
                        <label for="addPubli_Status">Status</label>
                        <?php
                        $selectedId = (isset($obj))?$obj->getStatusId():null;
                        selectHTML(Publi::ID_STATUS, 'addPubli_Status', 'Status', intval($selectedId));
                        ?>
                    </p>
                </fieldset>

                <!-- Boutons du formulaire !-->
                <p class="submit">
                    <?php
                    if ($id == null){
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Add' />";
                    } else {
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Save' />";                        
                    }
                    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                        echo "<input type = 'submit' name = 'deleteAdd' value = 'Delete' />";
                    }
                  /*  echo '<div class="btn-toolbar" role="toolbar">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_data&gcd_menu=ADA" >
                        Cancel
                        </a>
                        </div>';*/
                    
                    //echo "<input type = 'cancel' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />";
                    ?>
                </p> 
            </form>
            <?php
        } 
}
    

    function testPost($post_var) {
        return (isset($_POST[$post_var])) 
                    && $_POST[$post_var] != NULL 
                    && $_POST[$post_var] != 'NULL' 
                    && trim(delete_antiSlash($_POST[$post_var])) != "";
    }