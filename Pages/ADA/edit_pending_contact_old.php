<?php
/* 
 * fichier Pages/ADA/edit_pending_contact.php 
 * Auteur : XLI 
*/
if (isset($_SESSION['started'])) {
    require_once './Models/Affiliation.php';
    require_once './Models/Contact.php';
    require_once './Models/Status.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    //require_once './Pages/ADA/del_contact2.php';
    
    $max_length_str = 255;
    $id = null;
    
    if (isset($_GET['id'])) {
        // cas d'une édition, on récupère le contact en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $obj = Contact::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        // todo // si on ne récupère pas de contact rediriger      
    } 
    else {
        $obj = new Contact();
    }
        
        // on affiche la page que 
    // si une session est ouverte et
    // ( si on est administrateur 
    // ou si on affiche la page de l'utilisateur qui est connecté )
 
    $user_contact_id = WebAppUserGCD::getContactId($_SESSION['gcd_user_id']);
    if (isset($_SESSION['gcd_user_role']) && 
            (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || 
            ($id != null && $user_contact_id == $obj->getIdValue()))){
        
        if (isset($_POST['deleteAdd'])) {        
            del_contact();         
    }
    
        
        if (isset($_POST['submitAdd'])) {
            $success_form = false;
            $errors = null;    

            //affectation avec les données postées dans le formulaire
            if (testPost(Affiliation::ID)) {
                if (Affiliation::idExistsInDatabase($_POST[Affiliation::ID])){
                    $affiliation = new Affiliation();
                    $affiliation->setIdValue($_POST[Affiliation::ID]);
                    $obj->setAffiliation($affiliation);
                } else {
                    $errors[] = "Affiliation undefined";
                }
            }

            if (testPost(Contact::LASTNAME)) {
                $last_name = $_POST[Contact::LASTNAME];
                if (strlen($last_name) < $max_length_str)
                    $obj->setNameValue($last_name);
                else $errors[] = "Lastname must be less than ". $max_length_str . " characters";
            } else {
                $errors[] = "Lastname missing";
            }

            if (testPost(CONTACT::FIRSTNAME)) {
                $first_name = $_POST[Contact::FIRSTNAME]; 
                if (strlen($first_name) < $max_length_str)
                    $obj->setFirstname($first_name);
                else $errors[] = "Firstname must be less than ". $max_length_str . " characters";
            } else {
                $errors[] = "Firstname missing";
            }

            if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
                if (testPost(CONTACT::EMAIL)) {
                    if (strlen($_POST[Contact::EMAIL]) < $max_length_str)
                        $obj->setEmail ($_POST[CONTACT::EMAIL]);
                    else $errors[] = "Email must be less than ". $max_length_str . " characters";
                } else {
                    $errors[] = "Email missing";
                }
            }

            if (testPost(CONTACT::PHONE)) {
                if (strlen($_POST[Contact::PHONE]) < $max_length_str)
                    $obj->setPhoneNumber ($_POST[CONTACT::PHONE]);
                else $errors[] = "Phone must be less than ". $max_length_str . " characters";
            }
            
            if (testPost(Contact::ID_STATUS)) {
                if (is_numeric($_POST[Contact::ID_STATUS])){
                    $obj->setStatusId($_POST[Contact::ID_STATUS]);
                } else {
                    $errors[] = "Select a status";
                }
            }
              
            
            if (empty($errors)) {
               
                $errors = $obj->save();
            }

            if (empty($errors)){
                             
                echo '<div class="alert alert-success"><strong>Success !</strong></div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
            }
            echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_contact&gcd_menu=ADA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to pending contact list
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
               echo '<h1>Editing contact : '.$obj->getFirstName().' '.$obj->getLastName().'</h1>';
            } else {
               echo '<h1>Add a new contact</h1>';
            }
        ?>     
            <!-- Formulaire de saisie d'un contact-->
            <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >

                <fieldset class="cadre">
                    <legend>Affiliation</legend>
                    <p class="site_affiliation">
                        <label for="name_affiliation">Affiliation*</label>
                        <?php
                        $selectedId = (isset($obj))?$obj->getAffiliation():null;
                        selectHTML(CONTACT::ID_AFFILIATION  , 'addcontact_affiliation', 'Affiliation', intval($selectedId));
                        ?>
                    </p>
                </fieldset>

                <!-- Cadre pour le contact-->
                <fieldset class="cadre">
                    <legend>Contact</legend>
                    <p>
                        <label for="lastname">Lastname*</label>
                        <input type="text" name="<?php echo CONTACT::LASTNAME; ?>" id="lastname" value="<?php if (isset($obj)) echo $obj->getLastName(); ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label for="firstname">Firstname*</label>
                        <input type="text" name="<?php echo CONTACT::FIRSTNAME; ?>" id="firstname" value="<?php if (isset($obj)) echo $obj->getFirstName(); ?>" />
                    </p>
                    <?php 
                       if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)  ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) ){
                        echo '<p>
                            <label for="email">Email*</label>
                            <input type="text" name="'.CONTACT::EMAIL.'" id="email" value="';
                        if (isset($obj)) echo $obj->getEmail();
                        echo '"/>
                            </p>';
                       } else {
                           echo '<p>
                               <label for="email">Email*</label>';
                           if (isset($obj)) echo $obj->getEmail();
                           echo '</p>';
                       }
                    ?>
                    <p>
                        <label for="phone">Phone</label>
                        <input type="text" name="<?php echo CONTACT::PHONE; ?>" id="phone" value="<?php if (isset($obj)) echo $obj->getPhone(); ?>"/>
                    </p>
                    <p>
                        <label for="addContact_status">Status</label>
                        <?php
                        $selectedId = (isset($obj))?$obj->getStatusId():null;
                        selectHTML(Contact::ID_STATUS, 'addContact_status', 'Status', intval($selectedId));
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
                    ?>

                </p> 
            </form>
            <?php
        } 
    } else {
        echo '<div class="alert alert-danger"><strong>Access denied</strong></div>';
    }
}
    

    function testPost($post_var) {
        return (isset($_POST[$post_var])) 
                    && $_POST[$post_var] != NULL 
                    && $_POST[$post_var] != 'NULL' 
                    && trim(delete_antiSlash($_POST[$post_var])) != "";
    }
    