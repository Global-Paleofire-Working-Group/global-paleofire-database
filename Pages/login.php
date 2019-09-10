<?php
/* 
 * fichier Pages/login.php 
 * 
 */

require_once(REP_LIB."data_securisation.php");
require_once(REP_LIB."cryptography.php");
require_once (REP_MODELS."Contact.php");
require_once (REP_MODELS."user/WebAppUserGCD.php");
require_once (REP_PAGES."Common/Password.php");

$max_length_login = 150;
// Code lu seulement si cette page a été inclue dans l'index
if (isset($_SESSION['started'])) {
    if (!isset($conn_login)) $conn_login = "";
    if (!isset($conn_password)) $conn_password = "";

    if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::VISITOR) {
        // initialisation erreur
        $error_login = "";
        $success_login = false;

        // Si le formulaire vient d'être envoyé
        if (isset($_POST['boutonLogin'])) {
            // Si le champ login a été rempli
            if (isset($_POST['login']) && $_POST['login'] != NULL) {
                $conn_login = trim($_POST['login']);
            } else {
                $error_login .= "The login must be entered";
            }

            // Si le champ mot de passe a été rempli
            if (isset($_POST['password']) && $_POST['password'] != NULL) {
                $conn_password = trim($_POST['password']);
            } else {
                if (strtolower(trim($conn_login)) != "visitor") {
                    $error_login .= "The password must be entered";
                }
            }

            // Si l'un des champs n'est pas alphanumérique
            // CBU // à revoir un login peut être un mail
            // et un mot de passe peut contenir ./$...
            // donc ce test n'est pas valide
            /*if ((!alphanumerique($conn_login)) || (strtolower($conn_login) != "visitor" && !alphanumerique($conn_password))) {
                $error_login .= "Thanks to verify your username and your password.";
            }*/

            // Si aucune erreur, on va vérifier dans la base de données si le couple login/password existe
            if ($error_login == "") {
                connectionBaseWebapp();
                $gcd_webappuser = new WebAppUserGCD(data_securisation::toBdd($conn_login), data_securisation::toBdd($conn_password));
                if ($gcd_webappuser->checkPassword()) {
                    $_SESSION['gcd_login'] = $gcd_webappuser->getWebAppUserLogin();
                    $_SESSION['gcd_user_role'] = $gcd_webappuser->getRoleNumber();
                    $_SESSION['gcd_user_id'] = $gcd_webappuser->getWebAppUserID();
                    $_SESSION['gcd_user_name'] = $gcd_webappuser->getWebAppUserLogin();
                    $success_login = true;
                } else {
                    // Le couple login/password n'a pas été trouvé dans la base de données
                    $error_login .= "Thanks to verify your username and your password.";
                }
                connectionBaseInProgress();
            }
        } else if (isset($_POST['boutonRegister'])) {
            // Si le champ login a été rempli
            if (isset($_POST['email']) && $_POST['email'] != NULL) {
                $email = trim($_POST['email']);
                if (strlen($email) < $max_length_login){
                    // on vérifie que le mail n'est pas déjà lié à un compte en base
                        $listeMailExiste = Contact::getAll(NULL, NULL, CONTACT::EMAIL."=".sql_varchar($email));
					$nbMailExiste=count($listeMailExiste);                                       
					$nbUserExiste =0;
					foreach($listeMailExiste as $mailExiste){
						connectionBaseInProgress();
						$mi=$mailExiste->getIDValue();                                              
						connectionBaseWebapp();
						$usersExists = WebAppUserGCD::getListUserForAContact($mi);                                              
                                                $nbUserExiste += count($usersExists);                                                
                                              
					}
					
			connectionBaseInProgress();														
                    if ($nbUserExiste == 0){
                        //création de l'url pour enregistrer le compte
                        if (ENVIRONNEMENT == "SERVEUR-PROD"){
                            $url = "http://www.paleofire.org/index.php?p=register&e=";
                        } else {
                            $url = "/index.php?p=register&e=";
                        }

                        $param = $email;
                        $paramCrypt = cryptography::encryptAES($param);
                        $url .= $paramCrypt;
                        // si on est en prod envoi d'un mail
                        
                        if (ENVIRONNEMENT == "SERVEUR-PROD"){
                                $headers ='From: no-reply@paleofire.org'."\n";
                                $headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
                                $headers .='Content-Transfer-Encoding: 8bit';
                                
                                $body = file_get_contents('./Pages/template_confirm_mail.html');
                                $body = str_replace('$url', $url, $body);
                                
                                $subject = '[www.paleofire.org] Confirm your email to create an account on www.paleofire.org';
                                
                                mail($email, $subject, $body, $headers);
                                
                                echo '<div class="alert alert-success"><strong>Success !</strong> An email has just been sent to check the address. Click on the link in the email to continue registration</div>';

                        } else {
                            // sinon redirection sur la page de creation du compte
                            // todo // à reprendre pour faire la redirection autrement qu'en javascript
                            echo "<script type='text/javascript'>redirection (\"".$url."\");</script>";
                        }
                    } else {
                        $error_login .= "This mail is already associated with an existing user";
                    }
                } else {
                    $error_login .= "The mail must be less than ". $max_length_login;
                }
                
            } else {
                $error_login .= "The email must be entered";
            }
        } else if (isset($_POST['boutonRecoverpwd'])) {//xli 18/5 récupération du mot de passe oublié
             // Si le champ login a été rempli
            if (isset($_POST['email']) && $_POST['email'] != NULL) {
                $email = trim($_POST['email']);
                if (strlen($email) < $max_length_login){
                    // on vérifie que le mail est déjà lié à un compte en base
                    $listeMailExiste = Contact::getAll(NULL, NULL, CONTACT::EMAIL."=".sql_varchar($email));
                    if ($listeMailExiste == NULL){//on n'a pas trouvé de correspondance en base avec le mail entré
                        $error_login .= "This email is unknown.";
                    } else {
                        //création de l'url pour enregistrer le compte
                        if (ENVIRONNEMENT == "SERVEUR-PROD"){
                            $url = "http://www.paleofire.org/index.php?p=recovery_pwd&gcd_menu=ADA&e=";
                        } else {
                            $url = "/index.php?p=recovery_pwd&gcd_menu=ADA&e=";
                        }

                        $param = "$email";
                        $paramCrypt = cryptography::encryptAES($param);
                        $url .= $paramCrypt;
                        // si on est en prod envoi d'un mail
                        
                        if (ENVIRONNEMENT == "DEV-LOCAL"){
                            echo "<script type='text/javascript'>redirection (\"".$url."\");</script>";
                        
                        } else {
                                $headers ='From: no-reply@paleofire.org'."\n";
                                $headers .='Content-Type: text/html; charset="iso-8859-1"'."\n";
                                $headers .='Content-Transfer-Encoding: 8bit';
                                
                                $body = file_get_contents('./Pages/template_new_password.html');//xli à revoir
                                $body = str_replace('$url', $url, $body);
                                
                                $subject = '[www.paleofire.org] Password recovery on www.paleofire.org';
                                
                                mail($email, $subject, $body, $headers);
                                
                                echo '<div class="alert alert-success"><strong>Success !</strong> An email has just been sent to you. Click on the link in the email to enter a new password.</div>';

                        }
                    }
                } else {
                    $error_login .= "The mail must be less than ". $max_length_login;
                }
                
            } else {
                $error_login .= "The email must be entered";
            }
            
        }
        if ($error_login != ""){
            echo '<div class="alert alert-danger"><strong>Error !</strong></br>'.$error_login."</div>";
        }
        // Connexion non réussie, affichage du formulaire de connexion
        if (!$success_login) {
            ?>
            <div class="row">
                <div class="col-md-6">
                    <fieldset class="cadre">
                        <legend>Log in</legend>
                        <div id="login">
                            <form id="connexion" method="post" class="form_paleofire" action="index.php?p=login">
                                <fieldset>
                                    <p>
                                        <label for="inputtext1">Login</label>
                                        <input id="inputtext1" type="text" name="login" value="" />
                                    </p>
                                    <p>
                                        <label for="inputtext2">Password</label>
                                        <input id="inputtext2" type="password" name="password" value="" />
                                    </p>
                                    <p class="submit"> 
                                        <input id="inputsubmit1" type="submit" name="boutonLogin" value="Submit" />
                                    </p>
                                </fieldset>
                            </form>
                        </div>
                    </fieldset>
                </div>
                <div class="col-md-6">
                    <fieldset class="cadre">
                        <legend>Register</legend>
                        <form id="register" method="post" class="form_paleofire" action="index.php?p=login">
                            <fieldset>
                                <p>
                                    <label for="inputtext1">Email</label>
                                    <input id="inputtext1" type="text" name="email" value="" />
                                </p>
                                <p class="submit"> 
                                    <input id="inputsubmit1" type="submit" name="boutonRegister" value="Register" />
                                </p>
                            </fieldset>
                        </form>
                    </fieldset>    
                    <fieldset class="cadre">
                        <legend>Recover password</legend>
                        <form id="register" method="post" class="form_paleofire" action="index.php?p=login">
                            <fieldset>
                                <p>
                                    <label for="inputtext1">Email</label>
                                    <input id="inputtext1" type="text" name="email" value="" />
                                </p>
                                <p class="submit"> 
                                    <input id="inputsubmit1" type="submit" name="boutonRecoverpwd" value="Recover" />
                                </p>
                            </fieldset>
                        </form>
                    </fieldset>
                </div>
            </div>
            <?php
        } else {
            // todo // à reprendre pour faire la redirection autrement qu'en javascript
            //header("Location: /index.php");
            //exit();
            // Connexion réussie, redirection vers l'accueil	
            echo "<script type='text/javascript'>redirection ('index.php');</script>";
        }
    } 
} else {
    // Au cas ou cette page php soit appelée en dehors de l'index, on redirige vers la page d'accueil cette fois-ci bien inclue dans l'index
    require (GLOBAL_RACINE_PALEOFIRE . "config.php");
    echo "<script type='text/javascript' src='" . GLOBAL_RACINE_PALEOFIRE . "Library/paleofire.js'>redirection ('" . GLOBAL_RACINE_PALEOFIRE . "index.php');</script>";
}

