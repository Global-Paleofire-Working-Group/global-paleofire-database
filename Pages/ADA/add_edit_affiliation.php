<?php
 /* 
 * fichier Pages/ADA/add_edit_affiliation.php 
 * 
 */   
function add_edit_affiliation($Operation) {
    $max_lenght_str = 50;

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
        require_once './Models/Affiliation.php';
        require_once './Models/Country.php';
        require_once './Models/Contact.php';
        require_once './Library/PaleofireHtmlTools.php';
        require_once './Library/data_securisation.php';
        
        $id = null;
        $errors = null;
        
        if (($Operation ==="edit")||($Operation==="edit_pending")) {
                // cas d'une édition, on récupère l'affiliation en fonction de l'identifiant passé dans l'URL
                $id = $_GET['id'];
                $new_obj = Affiliation::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
             
        } else if (!isset($_GET['id']) && ($Operation=="add")) {
            $new_obj = new Affiliation();            
        } else {
            $errors[] = "Acces denied";
        }    

        if ((isset($_POST['submitAdd'])) && (empty($errors))) {

            //affectation de l'objet affiliation avec les données postées dans le formulaire
            if (testPost(Affiliation::NAME)) {
                if (strlen($_POST[Affiliation::NAME]) <= $max_lenght_str) {
                    if (!isset($P_POST[Affiliation::NAME])){
                        $new_obj->setNameValue(utf8_decode($_POST[Affiliation::NAME]));
                    } else {
                        $errors[] = "Affiliation name must be filled";
                    }                
                } else { 
                    $errors[] = "Affiliation name must be less than " . $max_lenght_str . " characters";
                }                   
            } else { $errors[] = "Affiliation name must be filled"; }

            if (isset($_POST[Affiliation::ADDRESS1])) {
                if (strlen($_POST[Affiliation::ADDRESS1]) <= $max_lenght_str) {
                        $new_obj->setAddress1(utf8_decode($_POST[Affiliation::ADDRESS1]));                
                } else { 
                    $errors[] = "Address 1 must be less than " . $max_lenght_str . " characters";
                }
            }
            
            if (isset($_POST[Affiliation::ADDRESS2])) {
                if (strlen($_POST[Affiliation::ADDRESS2]) <= $max_lenght_str) {
                        $new_obj->setAddress2(utf8_decode($_POST[Affiliation::ADDRESS2]));                
                } else { 
                    $errors[] = "Address 2 must be less than " . $max_lenght_str . " characters";
                }
            }

            if (testPost(Affiliation::CITY)) {
                if (strlen($_POST[Affiliation::CITY]) <= $max_lenght_str) {
                    if (!isset($P_POST[Affiliation::CITY])){
                        $new_obj->setCity(utf8_decode($_POST[Affiliation::CITY]));
                    } else {
                        $errors[] = "City name must be filled";
                    }                
                } else { 
                    $errors[] = "City name must be less than " . $max_lenght_str . " characters";
                }
            } else { $errors[] = "City must be filled"; }

            if (isset($_POST[Affiliation::STATE_PROV])) {
                if (strlen($_POST[Affiliation::STATE_PROV]) <= $max_lenght_str) {
                        $new_obj->setStateProv(utf8_decode($_POST[Affiliation::STATE_PROV]));                
                } else { 
                    $errors[] = "State must be less than " . $max_lenght_str . " characters";
                }
            }

            if (isset($_POST[Affiliation::STATE_PROV_CODE])) {
                if (strlen($_POST[Affiliation::STATE_PROV_CODE]) <= $max_lenght_str) {
                        $new_obj->setStateProvCode(utf8_decode($_POST[Affiliation::STATE_PROV_CODE]));                
                } else { 
                    $errors[] = "State code must be less than " . $max_lenght_str . " characters";
                }
            }

            if (testPost(Affiliation::ID_COUNTRY)) {
                if (is_numeric($_POST[Affiliation::ID_COUNTRY])){
                    $new_obj->setCountryId($_POST[Affiliation::ID_COUNTRY]);
                } else {
                    $errors[] = "A country must be selected";
                }
            }
            if ($Operation=="edit_pending") {    
                if (testPost(Affiliation::ID_STATUS)) {
                    if (is_numeric($_POST[Affiliation::ID_STATUS])){
                        $new_obj->setStatusId($_POST[Affiliation::ID_STATUS]);
                    } else {
                        $errors[] = "Select a status";
                    }
                }
            }    
            else {   
                //si c'est un contributeur ou un administrateur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
                //si c'est un superadministrateur qui entre la données, cette dernière sera automatiquemenbt validée
                if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                    $new_obj->setStatusId(1); // la donnée est validée
                }
                else {
                    $new_obj->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
                } 
            }

            if (empty($errors)){

                // on tente d'enregistrer l'affiliation
                $errors = $new_obj->save($Operation);
            }

            if (empty($errors)){
                echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
                /*echo '<div class="panel panel-default">
                        <div class="panel-heading">
                            <h3 class="panel-title">What do you want to do now ?</h3>
                        </div>
                        <div class="panel-body">
                            <a href="index.php?p=ADA/add_contact&gcd_menu=ADA" class="btn btn-primary" role="button">Add new contact</a>
                            <a href="index.php?p=ADA/add_affiliation&gcd_menu=ADA" class="btn btn-default" role="button">Add another affiliation</a>
                        </div>';
                 * */
            } else {
                echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
            }
            switch($Operation) {
                case "edit":
                    echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/affiliation_list&gcd_menu=CDA">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                            Go back to affiliations
                        </a>
                    </div>';
                    break;
                case "edit_pending":
                    echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_affiliation&gcd_menu=CDA">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                            Go back to pending affiliations
                        </a>
                    </div>';
                    break;
                case "add":
                    echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_affiliation&gcd_menu=ADA">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                            Add a new affiliation
                        </a>
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/affiliation_list&gcd_menu=CDA">
                            <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                            View affiliations list
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
                    echo '<h1>Editing affiliation : '.$new_obj->getName().'</h1>';     
                    $redirection="CDA/affiliation_list&gcd_menu=CDA";
                    break;
                case "add":
                    echo '<h1>Add a new affiliation</h1>';
                    $redirection="ADA/add_affiliation&gcd_menu=ADA";                    
                    break;
                case "edit_pending":
                    echo '<h1>Editing pending affiliation : '.$new_obj->getName().'</h1>';
                    $redirection="Admin/validate_pending_affiliation&gcd_menu=ADA";                    
                    break;
                default:
                    echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php')\" value = 'Cancel' />";                       
                    break;
            }
        ?>
                <form action="" class="form_paleofire" name="formAdd" method="post" id="formAddSite" >

                    <!-- Formulaire de saisie d'une affiliation-->
                    <form action="" class="form_paleofire" name="formAdd" method="post" id="formAddSite">
                    <fieldset class="cadre">
                        <legend>Affiliation</legend>
                        <p>
                            <label for="name_affiliation">Affiliation name*</label>
                            <input type="text" name="<?php echo Affiliation::NAME; ?>" id="name_affiliation" value="<?php if (isset($new_obj)) echo $new_obj->getName(); ?>" maxlength="50"/>
                        </p>
                        <p>
                            <label for="address1_affiliation">Address</label>
                            <input type="text" name="<?php echo Affiliation::ADDRESS1; ?>" id="address1_affiliation" value="<?php if (isset($new_obj)) echo $new_obj->getAddress1(); ?>" maxlength="50"/>
                        </p>
                        <p>
                            <label for="address2_affiliation">Address 2</label>
                            <input type="text" name="<?php echo Affiliation::ADDRESS2; ?>" id="address2_affiliation" value="<?php if (isset($new_obj)) echo $new_obj->getAddress2(); ?>" maxlength="50"/>
                        </p>
                        <p>
                            <label for="city_affiliation">City*</label>
                            <input type="text" name="<?php echo Affiliation::CITY; ?>" id="city_affiliation" value="<?php if (isset($new_obj)) echo $new_obj->getCity(); ?>" maxlength="50"/>
                        </p>
                        <p>
                            <label for="state_prov_affiliation">State</label>
                            <input type="text" name="<?php echo Affiliation::STATE_PROV; ?>" id="stat_prov_affiliation" value="<?php if (isset($new_obj)) echo $new_obj->getStateProv(); ?>" maxlength="50"/>
                        </p>
                        <p>
                            <label for="state_prov_code_affiliation">State code</label>
                            <input type="text" name="<?php echo Affiliation::STATE_PROV_CODE; ?>" id="stat_prov_code_affiliation" value="<?php if (isset($new_obj)) echo $new_obj->getStateProvCode(); ?>" maxlength="50"/>
                        </p>
                        <p>
                            <label for="addAffiliation_country">Country*</label>
                            <?php
                            $selectedId = (isset($new_obj))?$new_obj->getCountryId():null;
                            selectHTML(Affiliation::ID_COUNTRY, 'addAffiliation_country', 'Country', intval($selectedId));
                            ?>
                        </p> 
                    </fieldset>
                    <?php                        
                    if ($Operation==="edit_pending") {            
                    ?>
                    <fieldset class="cadre">
                        <legend>Status</legend>
                        <p>
                            <label for="addAffiliation_status">Status</label>
                            <?php
                            $selectedId = (isset($obj))?$obj->getStatusId():null;
                            selectHTML(Affiliation::ID_STATUS, 'addAffiliation_status', 'Status', intval($selectedId));
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
                            /*if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                                $tabContacts = $new_obj->getContacts();
                                if ($tabContacts == null || count($tabContacts) <= 0){
                                        echo "<input type = 'submit' name = 'deleteAdd' value = 'Delete' />";
                                }
                            }*/  
                        }
                        echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />";
                        ?>                    
                    </p> 
                </form>
                <?php
            } 

    }
}    

    function testPost($post_var) {
        return (isset($_POST[$post_var])) 
                    && $_POST[$post_var] != NULL 
                    && $_POST[$post_var] != 'NULL' 
                    && trim(delete_antiSlash($_POST[$post_var])) != "";
    }
    
    