<?php
/*
 * fichier Pages/ADA/add_age_model.php
 *
 */
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once (REP_MODELS."Site.php");
    require_once (REP_MODELS."DateInfo.php");
    require_once (REP_MODELS."AgeModel.php");
    require_once (REP_LIB."PaleofireHtmlTools.php");

    if (!isset($success_form)) {
        $success_form = "";
    }

    if (!isset($new_site)) {
        $new_site = new Site();
    }

    $id = null;
    if (isset($_GET['id'])){
        $id = $_GET['id'];
        // mise à jour d'un modèle d'age
        $new_obj = AgeModel::getObjectPaleofireFromId($id);
    } else if (isset($_GET['id_core'])){
        $new_obj = new AgeModel();
        $new_obj->_core_id = $_GET['id_core'];
    } else {
        $new_obj = new AgeModel();
    }

    if (!isset($temp_reg_veg)) {
        $temp_reg_veg = "";
    }

    $region_id = null;
    if (isset($_GET['region_id']) && is_numeric($_GET['region_id'])) {
        $region_id = $_GET['region_id'];
        $new_site->_site_region_id = $region_id;
    }

    if (isset($_POST['submitAdd'])) {
        $success_form = false;

        //$new_obj = new AgeModel();
        $errors = null;

        if (testPost(Site::ID) && Site::idExistsInDatabase($_POST[Site::ID])) {
            $id_site = $_POST[Site::ID];
        } else {
            $errors[] = "A site must be selected";
        }

        if (testPost(Core::ID) && Core::idExistsInDatabase($_POST[Core::ID])) {
            $id_core = $_POST[Core::ID];
        } else {
            $errors[] = "A core must be selected";
        }

        if (testPost(AgeModel::NAME)) {
            if (!AgeModel::getAllIdName("AGE_MODEL_VERSION like '" . $_POST[AgeModel::NAME] . "'")){
                $version = $_POST[AgeModel::NAME];
            } else {
                $errors[] = "Version already exists";
            }
        } else {
            $errors[] = "Version missing";
        }

        if (testPost(AgeModel::ID_AGE_MODEL_METHOD)) {
            $id_method = $_POST[AgeModel::ID_AGE_MODEL_METHOD];
        } else {
            $errors[] = "A method must be selected";
        }

        $id_modeller = null;
        if (testPost(AgeModel::ID_CONTACT)) {
            $id_modeller = $_POST[AgeModel::ID_CONTACT];
        }

        //si c'est un contributeur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
        //si c'est un administrateur qui entre la données, cette dernière sera automatiquemenbt validée
        if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
            $new_obj->setStatusId(1); // la donnée est validée
        }
        else {
            $new_obj->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
        }


        if (empty($errors)) {
            // on tente d'enregistrer le modèle d'age

            $new_obj->_core_id = $id_core;
            $new_obj->_contact_id = $id_modeller;
            $new_obj->setAgeModelMethod($id_method);
            $new_obj->setNameValue(utf8_decode($version));

            $errors = $new_obj->save();
        }

        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        echo '<div class="btn-toolbar" role="toolbar" align="left">
            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$new_obj->_core_id.'">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                Go back to core page
            </a>
        </div>';
    }

    if ((((!isset($_POST['submitAdd'])) || !empty($errors)))&&(!isset($_POST['deleteAdd']))) {//
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché

    if ($id != null) {
        echo '<h1>Editing age model : '.$new_obj->getName().'</h1>';
    }   else {
        echo '<h1>Add a new age model</h1>';
    }
        ?>
            <!-- Formulaire de saisie d'un modèle d'age-->
            <form action="" class="form_paleofire" name="formAddSite" method="post" id="formAddSite" >
                <!-- Cadre pour les métadonnées d'un modèle d'age !-->
                <fieldset class="cadre">
                    <legend>Description</legend>
                    <p class="site_name">
                        <label for="name_site">Site name*</label>
                        <select id="<?php echo SITE::ID ?>" name="<?php echo SITE::ID ?>">
                            <option value="">Select a site</option>
                        <?php
                            $listeSiteEtCore = CORE::getAllCoreBySite();

                            foreach($listeSiteEtCore as $id_site => $site){
                                echo '<option value = "' . $id_site . '">' . $site[0] . '</option>';
                            }
                        ?>
                        </select>
                    </p>
                    <p class="core_name">
                        <label for="name_core">Core name*</label>
                        <select id="<?php echo CORE::ID ?>" name="<?php echo CORE::ID ?>">
                        </select>
                    </p>
                </fieldset>

                <!-- Cadre pour le modèle d'age !-->
                <fieldset class="cadre">
                    <legend>Age Model</legend>
                    <p class="core_name">
                        <label for="age_model_version">Version</label>
                        <input type="text" name="<?php echo AgeModel::NAME; ?>" id="name_agemodel" maxlength="50"/>
                    </p>
                    <p>
                        <label for="addMethod">Method*</label>
                        <?php
                        $selectedMethod = (isset($new_obj))?$new_obj->_age_model_method:NULL;
                        $selectedId = ($selectedMethod!=null)?$selectedMethod->getIdValue():NULL;
                        selectHTML(AgeModel::ID_AGE_MODEL_METHOD, 'addMethod', 'AgeModelMethod', intval($selectedId));
                        ?>
                    </p>
                    <p class="author" style="width:470px">
                        <label for="author">Modeller</label>
                        <?php
                        $selectedId = (isset($new_obj))?intval($new_obj->_contact_id):"NULL";
                        selectHTML(Contact::ID, 'addAgeModel_modeller', 'Contact', $selectedId);
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

    ?>
<script type="text/javascript">
    <?php
        if (isset($listeSiteEtCore)){
            echo 'var tabCore = '. json_encode($listeSiteEtCore).';';
        }
    ?>
    $('#ID_SITE').change(
    function(){
        $('#ID_CORE').empty();
        var id_site = $(this).val();
        var tab = tabCore[id_site][1];
        for(var key in tab){
            $('#ID_CORE').append('<option value="'+ key +'">'+tab[key]+'</option>');
        }
        $('#ID_CORE').change();
    });

    $('#ID_CORE').change(
    function(){
        if ($('#name_agemodel').val() == ""){
            $('#name_agemodel').val('age_model_'+$('#ID_CORE option:selected').text()+'_');
        }
    });

    <?php
    if (isset($new_obj) && $new_obj->_core_id != null){
        $core = CORE::getObjectPaleofireFromId($new_obj->_core_id);
        if ($core != null){
            echo '$("#ID_SITE").val('.$core->getSiteId().');';
            echo '$("#ID_SITE").change();';
            echo '$("#ID_CORE").val('.$new_obj->_core_id.');';
        }
    }
    ?>
</script>
<?php
