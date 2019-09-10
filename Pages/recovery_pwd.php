<?php
/* 
 * fichier Pages/recovery_pwd.php 
 * Auteur : XLI 
 * permet à l'utilisateur d'enregistrer un nouveau mot de passe quand il a perdu le sien
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
    
    if (isset($_GET['e'])) { // s'il y a un mail passé dans l'url
         connectionBaseInProgress();
        // on récupère le mail passé dans l'URL
        $emailCrypt = $_GET['e'];
        $email = cryptography::decryptAES($emailCrypt);
        $email = data_securisation::toBdd($email);
        
        $where_clause="EMAIL='".$email."'";
        $contact = Contact::getObjectPaleofireFromWhere($where_clause) ;

        $id = data_securisation::toBdd($contact->getIdValue());

        connectionBaseWebapp();
        $query= "SELECT paleofire_user.ID_USER as id_user, paleofire_user.WEBAPP_LOGIN as webapp_login, paleofire_user.ID_ROLE as id_role FROM paleofire_user where id_contact=".$id;
        $result = queryToExecute($query);

        while ($row = fetchAssoc($result)) {
               $id_user = $row['id_user'];
               $login = $row['webapp_login'];
               $id_role = $row['id_role'];
               
            }  
        connectionBaseInProgress();
        //$obj = WebAppUserGCD::instantiate($id);      
    }
    

    if (isset($_POST['submitAdd'])) {
        $success_form = false;
        $errors = null;    
        
        if (testPost(WebAppUserGCD::PASSWORDFIELD) && testPost(WebAppUserGCD::PASSWORDFIELD."2")) {
            $pwd = $_POST[WebAppUserGCD::PASSWORDFIELD];
            $pwd2 = $_POST[WebAppUserGCD::PASSWORDFIELD."2"];

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
          //si c'est un contributeur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
            //si c'est un administrateur qui entre la données, cette dernière sera automatiquemenbt validée
        if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
            $contact->setStatusId(1); // la donnée est validée
            }
        else {
            $contact->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
        }   

        if (empty($errors)) {
            connectionBaseInProgress();
            $errors = $contact->save("edit");
            if (empty($errors)){

             //   $id = data_securisation::toBdd($contact->getIdValue());
                
                connectionBaseWebapp();
                $pwdc=database_encrypt_password($pwd);
                $query= "UPDATE paleofire_user SET WEBAPP_PSWD = ".$pwdc." WHERE ID_USER=".$id_user;
                $result = queryToExecute($query);
                
            } 
        }
        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>The password is changed.</strong> Go to the <a href="index.php?p=login">login page</a></div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }

    }

    if (!isset($_POST['submitAdd']) || !empty($errors)) {

    ?>
    <?php 
           echo '<h1>Enter a new password</h1>';
    ?>     
        <!-- Formulaire de saisie d'un contact-->
        <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
            <fieldset class="cadre">
                <p>
                    <label >Mail</label>
                    <?php echo $contact->getEmail() ; ?>
                </p>
                <p>
                    <label >Login</label>
                    <?php echo $login; ?>
                </p>                
                <p>
                    <label for="lastname">Lastname*</label>
                    <input type="text" name="<?php echo CONTACT::LASTNAME ; ?>" id="lastname" maxlength="50" value=<?php echo $contact->getLastName(); ?> />
                </p>
                <p>
                    <label for="firstname">Firstname*</label>
                    <input type="text" name="<?php echo CONTACT::FIRSTNAME ; ?>" id="firstname" value=<?php echo $contact->getFirstName();?> />
                </p>
                <p class="site_affiliation">
                    <label for="name_affiliation">Affiliation*</label>
                    <?php  selectHTML(AFFILIATION::ID, AFFILIATION::ID, "AFFILIATION", 1); ?>
                </p>            
                <p>
                    <label for="pwd">Password*</label>
                    <input type="password" name="<?php echo WebAppUserGCD::PASSWORDFIELD; ?>" id="pwd" value=""  />
                </p>
                <p>
                    <label for="pwd2">Repeat the password*</label>
                    <input type="password" name="<?php echo WebAppUserGCD::PASSWORDFIELD; ?>2" id="pwd2" value="" />
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
    