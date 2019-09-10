<?php
/* 
 * fichier Pages/ADA/edit_pending_affiliation.php 
 * Auteur : XLI 
*/    
$max_lenght_str = 50;

if (isset($_SESSION['started'])) {
    require_once './Models/Affiliation.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    //require_once './Pages/ADA/del_XXXXXXXXXXXXXX2.php';//xli


    $id = null;
    if (!isset($_GET['id'])) {
        $new_obj = new Affiliation();
    } else {
        // cas d'une édition, on récupère l'affiliation en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $new_obj = Affiliation::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        // todo // si on ne récupère pas d'objet rediriger
    }
    //xli
    if (isset($_POST['deleteAdd'])) {        
       del_XXXXX();         
    }
    
   

    if (isset($_POST['submitAdd'])) {
         $errors = null;
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
        }
        
        if (testPost(Affiliation::ADDRESS1)) {
            if (strlen($_POST[Affiliation::ADDRESS1]) <= $max_lenght_str) {
                    $new_obj->setAddress1(utf8_decode($_POST[Affiliation::ADDRESS1]));                
            } else { 
                $errors[] = "Address 1 must be less than " . $max_lenght_str . " characters";
            }
        }
        
        if (testPost(Affiliation::ADDRESS2)) {
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
        }
        
        if (testPost(Affiliation::STATE_PROV)) {
            if (strlen($_POST[Affiliation::STATE_PROV]) <= $max_lenght_str) {
                    $new_obj->setStateProv(utf8_decode($_POST[Affiliation::STATE_PROV]));                
            } else { 
                $errors[] = "State must be less than " . $max_lenght_str . " characters";
            }
        }
        
        if (testPost(Affiliation::STATE_PROV_CODE)) {
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
        
        if (testPost(Affiliation::ID_STATUS)) {
            if (is_numeric($_POST[Affiliation::ID_STATUS])){
                $new_obj->setStatusId($_POST[Affiliation::ID_STATUS]);
            } else {
                $errors[] = "Select a status";
            }
        }
        
        if (empty($errors)){
             
            // on tente d'enregistrer l'affiliation
            $errors = $new_obj->save();
        }
        
        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Success !</strong></div>';
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
		//xli
        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_affiliation&gcd_menu=ADA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to pending affiliations list
                    </a>
                </div>';
    }
//xli
    if ((((!isset($_POST['submitAdd'])) || !empty($errors)))&&(!isset($_POST['deleteAdd']))) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
    <?php 
        if ($id != null) {
           echo '<h1>Editing affiliation : '.$new_obj->getName().'</h1>';
        } else {
           echo '<h1>Add a new affiliation</h1>';
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
                        <label for="addAffiliation_country">Country</label>
                        <?php
                        $selectedId = (isset($new_obj))?$new_obj->getCountryId():null;
                        selectHTML(Affiliation::ID_COUNTRY, 'addAffiliation_country', 'Country', intval($selectedId));
                        ?>
                    </p>
                    <p>
                        <label for="addAffiliation_Status">Status</label>
                        <?php
                        $selectedId2 = (isset($new_obj))?$new_obj->getStatusId():null;
                        selectHTML(Affiliation::ID_STATUS, 'addAffiliation_status', 'Status', intval($selectedId2));
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
					//xli
                    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                        echo "<input type = 'submit' name = 'deleteAdd' value = 'Delete' />";
                    }
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
    