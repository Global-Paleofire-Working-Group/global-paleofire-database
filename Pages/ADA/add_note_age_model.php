<?php
/* 
 * fichier Pages/ADA/add_note_age_model.php 
 * 
 */ 
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/AgeModel.php';
    require_once './Models/NoteAgeModel.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';

    $max_length_str = 50;
    $max_length_text = 300;
    
    connectionBaseInProgress();
    
    if (!isset($success_form)) {
        $success_form = "";
    }
    
    $id = null;
    if (isset($_GET['id_age_model'])){
        $obj = new NoteAgeModel();
        $param_id_age_model = data_securisation::toBdd($_GET['id_age_model']);
        $param_age_model = AgeModel::getObjectPaleofireFromId($param_id_age_model);
        if ($param_age_model != null){
            $obj->setAgeModelId($param_id_age_model);
        }
    } else if (isset($_GET['id'])){
        $id = data_securisation::toBdd($_GET['id']);
        $obj = NoteAgeModel::getObjectPaleofireFromId($id);
    } else {
        $obj = new NoteAgeModel();
    }
    
    if (isset($_POST['deleteAdd'])) {        
       del_note_age_model();         
    }   
    
    if (isset($_POST['submitAdd'])) {
        $success_form = false;
        $errors = null;
        
        //affectation avec les données postées dans le formulaire
        if (testPost(NoteAgeModel::NAME)) {
            if (strlen($_POST[NoteAgeModel::NAME]) < $max_length_text){
                if ($_POST[NoteAgeModel::NAME] != ""){
                    $obj->setNameValue($_POST[NoteAgeModel::NAME]);
                } else {
                    $errors[] = "What must be filled";
                }
            } else {
                 $errors[] = "What must be less than ". $max_length_text . " characters";
            }
        } else {
            $errors[] = "What must be filled";
        }
        
        if (testPost(NoteAgeModel::ID_AGE_MODEL)) {
            $obj->setAgeModelId($_POST[NoteAgeModel::ID_AGE_MODEL]);
        } else {
            $errors[] = "Age model must be selected";
        }
        
        // on récupère le contributeur (personne connecté actuellement)
        $user_id = $_SESSION['gcd_user_id'];
        $contact_contributeur = WebAppUserGCD::getContactId($user_id);
        if ($contact_contributeur != null){
            $obj->setAgeModelNoteWho($contact_contributeur);
        } else {
            $errors[] = "Error your user account is not linked to a contact";
        }
       
        if (empty($errors)) {
            // on tente d'enregistrer la note
            $errors = $obj->save();
        }
       
        if (empty($errors)){

            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        $age_model = AgeModel::getAgeModelByID($obj->getAgeModelId());
        if ($age_model != null){
            echo '<div class="btn-toolbar" role="toolbar" align="left">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view&gcd_menu=CDA&core_id='.$age_model->_core_id.'">
                    <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                    Go back to core page
                </a>
            </div>';
        }
    }
    
    if ((((!isset($_POST['submitAdd'])) || !empty($errors)))&&(!isset($_POST['deleteAdd']))) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
        <h1>Add a new note for an age model</h1>     
            <!-- Formulaire de saisie d'une note-->
            <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
                <!-- Cadre pour la note-->
                <fieldset class="cadre">
                    <legend>Note</legend>
                    <p class="site_name">
                        <label for="addAgeModel">Age model*</label>
                        <?php
                        $selectedId = (isset($obj))?$obj->getAgeModelId():null;
                        selectHTML(NoteAgeModel::ID_AGE_MODEL, 'addAgeModel', 'AgeModel', intval($selectedId));
                        ?>
                    </p>
                    <p>
                        <label for="what">What*</label>
                        <textarea name="<?php echo NoteAgeModel::NAME; ?>" id="what" maxlength="300"/><?php if (isset($obj)) echo $obj->getName(); ?></textarea>
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
                    ?>
                    <!--<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />-->
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
    