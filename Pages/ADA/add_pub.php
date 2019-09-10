<?php
/* 
 * fichier Pages/ADA/add_pub.php 
 * 
 */ 
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Publi.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    require_once './Pages/ADA/del_publi.php';

    $max_length_text = 255;
    
    $id = null;
    if (isset($_GET['id'])) {
        // cas d'une édition, on récupère la publication en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $obj = Publi::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        $obj->_status_id= 0;//la publication repasse en état "pending"
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
        
        //si c'est un contributeur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
        //si c'est un administrateur qui entre la données, cette dernière sera automatiquemenbt validée
        if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
            $obj->setStatusId(1); // la donnée est validée
        }
        else {
            $obj->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
        } 
                                    
        if (empty($errors)){
            $errors = $obj->save();
        }
        
        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        } 
        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/publication_list&gcd_menu=CDA">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                Go back to Publications
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
                        <label for="citation">Citation*</label>
                        <textarea name="<?php echo PUBLI::NAME; ?>" id="citation" maxlength="300"/><?php if (isset($obj)) echo $obj->getName(); ?></textarea>
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
                    //<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />
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