<?php
/* 
 * fichier Pages/register.php 
 * ENREGISTREMENT D'UN NOUVEL UTILISATEUR
 */
if (isset($_SESSION['started'])) {
    require_once './Models/Affiliation.php';
    require_once './Models/Contact.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    require_once './Library/cryptography.php';

    $max_length_login = 150;
    $max_length_str = 255;
    $max_length_pwd = 15;
    $email = null;
    $Operation = "none";
    
    if (isset($_GET['e'])) {
        // on récupère le mail passé dans l'URL
        $emailCrypt = $_GET['e'];
        $email = cryptography::decryptAES($emailCrypt);
        $email = data_securisation::toBdd($email);
        $obj = null;
        
        $contactExiste=Contact::getObjectsPaleofireFromWhere(Contact::EMAIL .' = \'' .$email."'");
            if (count ($contactExiste) > 0) {
                $Operation="edit";
                $contact = Contact::getObjectPaleofireFromId($contactExiste[0]->getIdValue()); 
            }
            else {
                $Operation="add";
                $contact = new Contact();
                $contact->setEmail($email);
            }
            echo '<h1>Register : '.$contact->getEmail().'</h1>';
    }
    
    // on affiche la page uniquement 
    // si une session est ouverte et
    // ( si on est administrateur 
    // ou si on affiche la page de l'utilisateur qui est connecté )
    if (isset($_POST['submitAdd'])) {
        $success_form = false;
        $errors = null;    

        //affectation avec les données postées dans le formulaire
        if (testPost(WebAppUserGCD::LOGINFIELD)) {
            $login = $_POST[WebAppUserGCD::LOGINFIELD];

            // RM
            if (!preg_match('/^[[:alnum:]]*$/', $login)) {
                $errors[] = "Login must contents only alphanumeric characters";
            }

            if (strlen($login) > $max_length_login) {
                $errors[] = "Login must be less than ". $max_length_login . " characters";
            } else {
                // on vérifie que le login n'est pas déjà utilisé par un autre compte           
                connectionBaseWebapp();
                if (WebAppUserGCD::loginExists($login)){
                    $errors[] = "This login is already associated with an existing account";
                }
                connectionBaseWebapp();
            }
        } else {
            $errors[] = "Login missing";
        }

        if (testPost(WebAppUserGCD::PASSWORDFIELD) && testPost(WebAppUserGCD::PASSWORDFIELD."2")) {
            $pwd = $_POST[WebAppUserGCD::PASSWORDFIELD];
            $pwd2 = $_POST[WebAppUserGCD::PASSWORDFIELD."2"];

            // RM
            if (!preg_match('/^[[:alnum:]]*$/', $pwd)) {
                $errors[] = "Password must contents only alphanumeric characters";
            }

            if (strlen($pwd) <= $max_length_pwd){
                if ($pwd != $pwd2) $errors[] = "Passwords must be identical";
            }
            else $errors[] = "Password must be less than ". $max_length_pwd . " characters";
        } else {
            $errors[] = "Password must be filled twice";
        }

        if (testPost(Affiliation::ID)) {
            connectionBaseInProgress();
            if (Affiliation::idExistsInDatabase($_POST[Affiliation::ID])){
                $affiliation = new Affiliation();
                $affiliation->setIdValue($_POST[Affiliation::ID]);
                $contact->setAffiliation($affiliation);
            } else {
                $errors[] = "Affiliation undefined";
            }
        }

        if (testPost(Contact::LASTNAME)) {
            $last_name = $_POST[Contact::LASTNAME];
            if (strlen($last_name) < $max_length_str)
                $contact->setNameValue($last_name);
            else $errors[] = "Lastname must be less than ". $max_length_str . " characters";
        } else {
            $errors[] = "Lastname missing";
        }

        if (testPost(CONTACT::FIRSTNAME)) {
            $first_name = $_POST[Contact::FIRSTNAME]; 
            if (strlen($first_name) < $max_length_str)
                $contact->setFirstname($first_name);
            else $errors[] = "Firstname must be less than ". $max_length_str . " characters";
        } else {
            $errors[] = "Firstname missing";
        }

        /*if (testPost(CONTACT::PHONE)) {
            if (strlen($_POST[Contact::PHONE]) < $max_length_str)
                $contact->setPhoneNumber ($_POST[CONTACT::PHONE]);
            else $errors[] = "Phone must be less than ". $max_length_str . " characters";
        }*/
          //si c'est un contributeur ou un administrateur qui entre la données, cette dernière sera mise en attente de validation r
            //si c'est un superadministrateur qui entre la données, cette dernière sera automatiquemenbt validée
            if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                $contact->setStatusId(1); // la donnée est validée
            }
            else {
                $contact->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
            }               

        if (empty($errors)) {
            $errors = $contact->save($Operation);
            if (empty($errors)){
                $obj = new WebAppUserGCD($login, $pwd, WebAppRoleGCD::CONTRIBUTOR, $contact->getIdValue());
                connectionBaseWebapp();
                $errors = $obj->save();
                connectionBaseInProgress();
                if (empty($errors)){/*
                    if (PRODUCTION == false){
                        // on envoi un mail d'information aux administrateurs
                        $headers ='From: no-reply@paleofire.org'."\n";
                        $headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
                        $headers .='Content-Transfer-Encoding: 8bit';
                        // on récupère toutes les adresses des admins
                        connectionBaseWebapp();
                        $listeAdmin = WebAppUserGCD::getListUserForARole(WebAppRoleGCD::ADMINISTRATOR);
                        $listeAdmin .= WebAppUserGCD::getListUserForARole(WebAppRoleGCD::SUPERADMINISTRATOR);
                        
                        $mailAdmin = null;
                        connectionBaseInProgress();
                        foreach ($listeAdmin as $admin){
                            if($admin->_webAppContact_id != NULL){
                                $contact_admin = Contact::getObjectPaleofireFromId($admin->_webAppContact_id);
                                if ($contact_admin->getEmail() != null && $contact_admin->getEmail() != ""){
                                    $mailAdmin[] = $contact_admin->getEmail();
                                }
                            }
                        }
                        $to = implode(',', $mailAdmin);
                        $subject = '[www.paleofire.org] New contributor '.$obj->getWebAppUserLogin();
                        
                        $body = file_get_contents('./Pages/template_new_user_mail.html');
                        $body = str_replace('$firstname', $contact->getFirstName(), $body);
                        $body = str_replace('$lastname', $contact->getLastName(), $body);
                        $body = str_replace('$login', $obj->getWebAppUserLogin(), $body);
                        mail($to, $subject, $body, $headers); 
                    }*/
                } else {
                    // l'enregistrement du user n'a pas fonctionné on supprime l'enregistrement du contact
                    connectionBaseInProgress();
                    Contact::delObjectPaleofireFromId($contact->getIdValue());
                }
            }
        }

        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Registration is successful !</strong> Go to the <a href="index.php?p=login">login page</a></div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }

    }

    if (!isset($_POST['submitAdd']) || !empty($errors)) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
    

        <!-- Formulaire de saisie d'un contact-->
        <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
            <fieldset class="cadre">
                <legend>Account data</legend>
                <p>
                    <label >Mail</label>
                    <?php echo $contact->getEmail(); ?>
                </p>
                <p>
                    <label for="lastname">Lastname*</label>
                    <input type="text" name="<?php echo CONTACT::LASTNAME; ?>" id="lastname" value=" <?php if ($Operation==="edit") { echo $contactExiste[0]->getLastName(); } ?>" maxlength="50"/>
                </p>
                <p>
                    <label for="firstname">Firstname*</label>
                    <input type="text" name="<?php echo CONTACT::FIRSTNAME; ?>" id="firstname" value=" <?php if ($Operation==="edit") { echo $contactExiste[0]->getFirstName(); } ?>" />
                </p>
                <?php /*<p>
                    <label for="phone">Phone</label>
                    <input type="text" name="<?php echo CONTACT::PHONE; ?>" id="phone" value=" <?php if ($Operation==="edit") { echo $contactExiste[0]->getPhone(); } ?>" />
                </p>*/ 
                ?>
                <p class="site_affiliation">
                    <label for="name_affiliation">Affiliation*</label>
                    <?php  if ($Operation==="edit") { connectionBaseInProgress(); selectHTML(AFFILIATION::ID, AFFILIATION::ID, "AFFILIATION", $contactExiste[0]->getAffiliation());  
                           } else { 
                               connectionBaseInProgress(); selectHTML(AFFILIATION::ID, AFFILIATION::ID, "AFFILIATION", 1); 
                           }
                    ?>
                </p>
                <p>
                    <label for="login">Login*</label>
                    <input type="text" name="<?php echo WebAppUserGCD::LOGINFIELD; ?>" id="login" maxlength="150"/> (alphanumeric characters)
                </p>
                <p>
                    <label for="pwd">Password*</label>
                    <input type="password" name="<?php echo WebAppUserGCD::PASSWORDFIELD; ?>" id="pwd" /><!-- // RM was "maxlength="10" size="10"" --> (6 to 10 alphanumeric characters)
                </p>
                <p>
                    <label for="pwd2">Repeat the password*</label>
                    <input type="password" name="<?php echo WebAppUserGCD::PASSWORDFIELD; ?>2" id="pwd2" /><!-- // RM was "maxlength="10" size="10"" -->
                </p>
            </fieldset>
            <!-- Boutons du formulaire !-->
            <p class="submit">
                <input type = 'submit' name = 'submitAdd' value = 'Submit' />
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