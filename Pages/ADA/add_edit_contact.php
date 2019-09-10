<?php
/* 
 * fichier Pages/ADA/add_edit_contact.php 
 * 
 */ 

function add_edit_contact($Operation) {
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
        require_once './Models/Affiliation.php';
        require_once './Models/Contact.php';
        require_once './Library/PaleofireHtmlTools.php';
        require_once './Library/data_securisation.php';

        $max_length_str = 255;
        $id = null;

        if (($Operation ==="edit")||($Operation==="edit_pending")) {
            // cas d'une édition, on récupère le contact en fonction de l'identifiant passé dans l'URL
            
            $id = $_GET['id'];
            $obj = Contact::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id'])); 
            

        }else if (isset($_GET['id_affiliation']) && ($Operation=="add")) {
            $id_affiliation = $_GET['id_affiliation'];
            $affiliation = Affiliation::getObjectPaleofireFromId($id_affiliation);
            $obj = new Contact();
            if ($affiliation != null){
                $obj->setAffiliation($affiliation);
            }
        } else if (!isset($_GET['id']) && ($Operation=="add")) {
            $obj = new Contact();
        } else {
                //$errors[] = "Acces denied";
        }    

            // on affiche la page uniquement
        // si une session est ouverte et
        // ( si on est administrateur 
        // ou si on affiche la page de l'utilisateur qui est connecté )
        $user_contact_id = WebAppUserGCD::getContactId($_SESSION['gcd_user_id']);

        //if (isset($_SESSION['gcd_user_role']) && 
        //        ((($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) && ($id != null && $user_contact_id == $obj->getIdValue())) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
        if (isset($_SESSION['gcd_user_role']) && 
                ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){

            if ((isset($_POST['submitAdd'])) && (empty($errors))) {
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

                //if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
                if (testPost(CONTACT::EMAIL)) {
                    if (strlen($_POST[Contact::EMAIL]) < $max_length_str)
                        $obj->setEmail ($_POST[CONTACT::EMAIL]);
                    else $errors[] = "Email must be less than ". $max_length_str . " characters";
                } else {
                    $errors[] = "Email missing";
                }
                //}
                    
                if ($Operation=="edit_pending") {    
                    if (is_numeric($_POST[Contact::ID_STATUS])){
                            $obj->setStatusId($_POST[Contact::ID_STATUS]);
                        } else {
                            $errors[] = "Select a status";
                        }
                }    
                else {  
                    //si c'est un contributeur ou un administrateur qui entre la donnée, cette dernière sera mise en attente de validation par un administrateur
                    //si c'est un superadministrateur qui entre la données, cette dernière sera automatiquemenbt validée
                    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                        $obj->setStatusId(1); // la donnée est validée
                    }
                    else {
                        $obj->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
                    } 
                }

                if (empty($errors)) {

                    $errors = $obj->save($Operation);
                }

                if (empty($errors)){

                    echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
                } else {
                    echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
                }
                switch($Operation) {
                    case "edit":
                        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/contact_list&gcd_menu=CDA">
                                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                                Go back to contacts
                            </a>
                        </div>';
                        break;
                    case "edit_pending":
                        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                            <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_contact&gcd_menu=CDA">
                                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                                Go back to pending contacts
                            </a>
                        </div>';
                        break;
                    case "add":
                        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                            <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_contact&gcd_menu=ADA">
                                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                                Go back to contacts
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

        if (((!isset($_POST['submitAdd'])) || !empty($errors))) {
                // si on arrive sur la page la première fois
                // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
                // le formulaire est affiché
            ?>
            <?php 
            switch ($Operation) {
                case "edit":
                    echo '<h1>Editing contact : '.$obj->getFirstName().' '.$obj->getLastName().'</h1>';
                    $redirection="CDA/contact_list&gcd_menu=CDA";
                    break;
                case "add":
                    echo '<h1>Add a new contact</h1>';
                    $redirection="ADA/add_contact&gcd_menu=ADA";                    
                    break;
                case "edit_pending":
                    echo '<h1>Editing pending contact : '.$obj->getName().'</h1>';
                    $redirection="Admin/validate_pending_contact&gcd_menu=ADA";                    
                    break;
                default:
                    echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php')\" value = 'Cancel' />";                       
                    break;
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
                           //if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)  ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) ){
                            echo '<p>
                                <label for="email">Email*</label>
                                <input type="text" name="'.CONTACT::EMAIL.'" id="email" value="';
                            if (isset($obj)) echo $obj->getEmail();
                            echo '"/>
                                </p>';
                           /*} else {
                               echo '<p>
                                   <label for="email">Email*</label>';
                               if (isset($obj)) echo $obj->getEmail();
                               echo '</p>';
                           }*/
                        ?>
                        <?php // CBO // suppression du tél dans contact car inutile
                        /*<p>
                            <label for="phone">Phone</label>
                            <input type="text" name="<?php echo CONTACT::PHONE; ?>" id="phone" value="<?php if (isset($obj)) echo $obj->getPhone(); ?>"/>
                        </p>*/
                        ?>
                    </fieldset>
                    <?php                        
                    if ($Operation==="edit_pending") {            
                    ?>
                    <fieldset class="cadre">
                        <legend>Status</legend>
                        <p>
                            <label for="addContact_status">Status</label>
                            <?php
                            $selectedId = (isset($obj))?$obj->getStatusId():null;
                            selectHTML(Contact::ID_STATUS, 'addContact_status', 'Status', intval($selectedId));
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
                            echo "<input type = 'submit' name = 'submitAdd' value = 'Edit' />";
                        }
                        echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />";
                        ?>
                    </p> 
                </form>
                <?php
            } 
        } else {
            //echo '<div class="alert alert-danger"><strong>Access denied</strong></div>';
        }
    }
}

        function testPost($post_var) {
            return (isset($_POST[$post_var])) 
                        && $_POST[$post_var] != NULL 
                        && $_POST[$post_var] != 'NULL' 
                        && trim(delete_antiSlash($_POST[$post_var])) != "";
        }
