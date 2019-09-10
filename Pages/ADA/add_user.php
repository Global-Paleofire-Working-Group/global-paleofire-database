<?php
/* 
 * fichier Pages/ADA/add_user.php 
 * 
 */ 
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Affiliation.php';
    require_once './Models/Contact.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';

    $max_length_login = 150;
    $min_length_pwd = 6;
    $max_length_pwd = 10;
    $id = null;
    
    connectionBaseWebapp();
    $rolesList = WebAppRoleGCD::getStaticList();
    if (isset($_GET['id'])) {
        // cas d'une édition, on récupère l'utilisateur en fonction de l'identifiant passé dans l'URL
        $id = data_securisation::toBdd($_GET['id']);
        $obj = WebAppUserGCD::instantiate($id);
        // todo // si on ne récupère pas de contact rediriger  
    } else {
        $obj = null;
    }
    
    // on affiche la page que 
    // si une session est ouverte et
    // si on est administrateur 
    // ou si on affiche la page de l'utilisateur qui est connecté )
    
    if (isset($_SESSION['gcd_user_role']) && 
            (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || 
            ($id != null && $_SESSION['gcd_user_id'] == $id))){
        
        if (isset($_POST['submitAdd'])) {
            $success_form = false;
            $errors = null;    

            //affectation avec les données postées dans le formulaire
            if (testPost(WebAppUserGCD::LOGINFIELD)) {
                $login = $_POST[WebAppUserGCD::LOGINFIELD];
                if (strlen($login) <= $max_length_login){
                    if ($obj == null)
                        $obj = new WebAppUserGCD($login, null);
                    else {
                        $obj->setWebAppUserLogin ($login);
                    }
                }
                else $errors[] = "Login must be less than ". $max_length_login . " characters";
            } else {
                $errors[] = "Login missing";
            }

            if (testPost(WebAppUserGCD::PASSWORDFIELD) && testPost(WebAppUserGCD::PASSWORDFIELD."2")) {
                $pwd = $_POST[WebAppUserGCD::PASSWORDFIELD];
                $pwd2 = $_POST[WebAppUserGCD::PASSWORDFIELD."2"];
                if (strlen(utf8_decode($pwd)) <= $max_length_pwd && strlen(utf8_decode($pwd)) >= $min_length_pwd){
                    if ($pwd == $pwd2){
                        if ($obj != null) $obj->setWebAppUserPassword($pwd);
                    } else $errors[] = "Passwords must be identical";
                }
                else {
                    $errors[] = "Password must be between ".$min_length_pwd." and ". $max_length_pwd . " characters";
                }
            } else if ($id != null && $_POST[WebAppUserGCD::PASSWORDFIELD] == NULL && $_POST[WebAppUserGCD::PASSWORDFIELD."2"] == NULL){
                // on est en modification sur un utilisateur
                // si pas de mot de passe saisi alors pas de modif du mot de passe en base
                if ($obj != null) $obj->setWebAppUserPassword(NULL);
            }
            else {
                $errors[] = "Password must be filled twice";
            }

            if (testPost(WebAppUserGCD::ID_ROLE)) {
                if (key_exists($_POST[WebAppUserGCD::ID_ROLE], $rolesList)){
                    $id_role = $_POST[WebAppUserGCD::ID_ROLE]; 
                    if ($obj != null) $obj->setWebAppUserRole($id_role);
                } else {
                    $errors[] = "Role undefined";
                }
            }

            if (testPost(WebAppUserGCD::ID_CONTACT)) {
                connectionBaseInProgress();
                if (Contact::idExistsInDatabase(data_securisation::toBdd($_POST[WebAppUserGCD::ID_CONTACT]))){
                    if ($obj != null) $obj->_webAppContact_id = $_POST[WebAppUserGCD::ID_CONTACT];
                } else {
                    $errors[] = "Contact undefined";
                }
                connectionBaseWebapp();
            }

            if (empty($errors) && $obj != null){
                $errors = $obj->save();
            }
            
            if (empty($errors)){
               echo '<div class="alert alert-success"><strong>Success !</strong> Data have been updated.</div>';
                
            } else {
                echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
            }

        }

        if (!isset($_POST['submitAdd']) || !empty($errors)) {
            // si on arrive sur la page la première fois
            // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
            // le formulaire est affiché
        ?>
        <?php 
            if ($id != null) {
               echo '<h1>Editing user : '.$obj->getWebAppUserLogin().'</h1>';
            } else {
               echo '<h1>Add a new user</h1>';
            }
        ?>     
            <!-- Formulaire de saisie d'un contact-->
            <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
                <fieldset class="cadre">
                    <legend>User</legend>
                    <p>
                        <label for="login">Login*</label>
                        <input type="text" name="<?php echo WebAppUserGCD::LOGINFIELD; ?>" id="login" value="<?php if (isset($obj)) echo data_securisation::tohtml ($obj->getWebAppUserLogin()); ?>" maxlength="150"/>
                    </p>
                    <p>If you don't want to change your password, let the following fields empty.
                    <p>
                        <label for="pwd">Password</label>
                        <input type="password" name="<?php echo WebAppUserGCD::PASSWORDFIELD; ?>" id="pwd" maxlength="10" size="10" />
                    </p>
                    <p>
                        <label for="pwd2">Repeat the password</label>
                        <input type="password" name="<?php echo WebAppUserGCD::PASSWORDFIELD; ?>2" id="pwd2" maxlength="10" size="10" />
                    </p>
                    <?php 
                    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR||$_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                    ?>
                    <p>
                        <label for="ROLE">Role*</label>
                        <?php
                        $id_selected = (isset($obj))? $obj->getWebAppUserRole():"";
                        echo '<select name = '.WebAppRoleGCD::IDENTIFIER.' id = '.WebAppRoleGCD::IDENTIFIER.' >';
                        echo '<option value = "NULL">Select</option>';
                        
                        foreach ($rolesList as $values) {
                            if ($values[WebAppRoleGCD::IDENTIFIER] == $id_selected) {
                                $select = " selected ";
                            } else {
                                $select = "";
                            }
                            echo '<option value="' . $values[WebAppRoleGCD::IDENTIFIER] . '"'. $select . ">" . $values[WebAppRoleGCD::NAMEFIELD] . "</option>";
                        }
                        echo '</select>';
                        ?>
                    </p>
                    <?php 
                    }
                    ?>
                </fieldset>
                <?php 
                    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                ?>
                <fieldset class="cadre">
                    <legend>Contact</legend>
                    <p>
                        <label for="contact">Contact*</label>
                        <?php
                        $id_selected = (isset($obj))?$obj->_webAppContact_id:"";
                        connectionBaseInProgress();
                        selectHTML(Contact::ID, 'id_contact', 'Contact', intval($id_selected));
                        connectionBaseWebapp();
                        ?>
                    </p>
                </fieldset>
                <?php 
                    }
                ?>
                <!-- Boutons du formulaire !-->
                <p class="submit">
                    <?php
                    if ($id == null){
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Add' />";
                    } else {
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Save' />";
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
    