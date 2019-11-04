<?php
/*
 * fichier Pages/ADA/add_date_info.php
 *
 */
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Site.php';
    require_once './Models/DateInfo.php';
    require_once './Models/DateType.php';
    require_once './Models/MatDated.php';
    require_once './Models/DateComment.php';
    require_once './Models/Age.php';
    require_once './Models/AgeUnits.php';
    require_once './Models/CalibrationVersion.php';
    require_once './Models/CalibrationMethod.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';

    $id = null;
    if (isset($_GET["id_age"])){
        $new_obj = new DateInfo();
        // on récupère le modèle d'age
        $param_age_model = AgeModel::getObjectPaleofireFromId($_GET["id_age"]);
        if ($param_age_model != null){
            $new_age = new Age();
            $new_obj->addHasAge($new_age);
            $new_age->_age_model_id = $param_age_model->getIdValue();
            $param_id_core = $param_age_model->_core_id;
            $param_core = Core::getObjectPaleofireFromId($param_id_core);
            $param_id_site = $param_core->getSiteId();
        }

    } else if (isset($_GET["id"])){
        $id = $_GET['id'];
        $new_obj = DateInfo::getObjectPaleofireFromId($id);
    }else {
        $new_obj = new DateInfo();
    }

    if (isset($_POST['submitAdd'])) {
        $errors = null;

        //vérifie que les valeurs site et core sont correctes
        if (!testPost(Site::ID)) $errors[] = "A site must be selected";
        else if (!Site::idExistsInDatabase($_POST[Site::ID])) $errors[] = "Site undefined";

        if (!testPost(Core::ID)) $errors[] = "A core must be selected";
        else if (!Core::idExistsInDatabase($_POST[Core::ID])) $errors[] = "Core undefined";

        if (!testPost(AgeModel::ID)) $errors[] = "An age model must be selected";
        else if (!AgeModel::idExistsInDatabase($_POST[AgeModel::ID])) $errors[] = "Age model undefined";

        // test et création du sample
        $sample = null;
        $depth = null;
        $age = null;
        $depth_type = null;
        $age_model_id = null;

        // le date info name va être généré automatiquement
        /*if (testPost(Sample::NAME)) {
            $sample_name = $_POST[Sample::NAME];
            $sample = new Sample();
            $sample->setNameValue($sample_name);
            $sample->_sample_core_id = $_POST[Core::ID];
            $sample->_sample_age_model = $age_model_id;
        } else {
            $errors[] = "Date info name missing";
        }*/

        // test et création de la profondeur
        if (testPost(Depth::NAME) && testPost(DepthType::ID)) {
            $depth_value = $_POST[Depth::NAME];
            if ($depth_value == "") { $errors[] = "Depth value must be filled"; }
            else if (!is_numeric($depth_value)) { $errors[] = "Depth value must be a number"; }
            else if ($depth_value < 0) { $errors[] = "Depth value must be a positive number"; }
            else {
                $depth_type = $_POST[Depth::ID_DEPTH_TYPE];
                if ($depth_type != null){
                    $listeDepthType = DepthType::getAllIdName();
                    $depth_type_name = $listeDepthType[$depth_type];
                    $depth = new Depth($depth_value, $depth_type_name);
                } else {
                    $errors[] = "Depth type must be selected";
                }
            }
        } else {
            $errors[] = "Depht value or depth type missing";
        }

        if (testPost(Age::NAME)) {
            $age_value = $_POST[Age::NAME];
            if ($age_value == "") { $errors[] = "Age value must be filled"; }
            else if (!is_numeric($age_value)) { $errors[] = "Age value must be a number"; }
            else if ($age_value < 0) { $errors[] = "Age value must be a positive number"; }
            else {}
        } else {
            $errors[] = "Age value missing";
        }
        if (testPost(Age::AGE_NEGATIVE_ERROR)){
            if (!is_numeric($_POST[Age::AGE_NEGATIVE_ERROR])) $errors[] = "Age error value must be a number";
            else if ($_POST[Age::AGE_NEGATIVE_ERROR] < 0) $errors[] = "Age error value must be a positive number";
        }
        if (testPost(Age::AGE_POSITIVE_ERROR)){
            if (!is_numeric($_POST[Age::AGE_POSITIVE_ERROR])) $errors[] = "Age error value must be a number";
            else if ($_POST[Age::AGE_POSITIVE_ERROR] < 0) $errors[] = "Age error value must be a positive number";
        }        if (testPost(Age::AGE_NEGATIVE_ERROR)){
            if (!is_numeric($_POST[Age::AGE_NEGATIVE_ERROR])) $errors[] = "Age error value must be a number";
            else if ($_POST[Age::AGE_NEGATIVE_ERROR] < 0) $errors[] = "Age error value must be a positive number";
        }

        if (empty($errors)){
            $age_model_id = $_POST[AgeModel::ID];
            // on créé le nom à partir du core et de la profondeur moyenne
            // on récupère les données de la carrote pour pouvoir récupérer le nom par la suite
            $obj_core = Core::getCoreDataFromId($_POST[Core::ID]);
            $sample_name = $obj_core->getName().'_'.$depth->_depth_value;
            $sample = new Sample();
            $sample->setNameValue($sample_name);
            $sample->_sample_core_id = $_POST[Core::ID];
            $sample->_sample_age_model = $age_model_id;
            $sample->addDepth($depth);
            $sample->_default_depth = $depth;
        }

        // test et création de l'age
        if (testPost(Age::NAME)) {
            $age_value = $_POST[Age::NAME];
            $age = new Age($age_value);
            if (testPost(Age::AGE_NEGATIVE_ERROR)) $age->_age_negative_error = $_POST[Age::AGE_NEGATIVE_ERROR];
            if (testPost(Age::AGE_POSITIVE_ERROR)) $age->_age_positive_error = $_POST[Age::AGE_POSITIVE_ERROR];
            if (testPost(AgeUnits::ID) && AgeUnits::idExistsInDatabase($_POST[AgeUnits::ID]))
                $age->_age_units_id = $_POST[AgeUnits::ID];
            if (testPost(CalibrationMethod::ID) && CalibrationMethod::idExistsInDatabase($_POST[CalibrationMethod::ID]))
                $age->_age_calibration_method_id = $_POST[CalibrationMethod::ID];
            if (testPost(CalibrationVersion::ID) && CalibrationVersion::idExistsInDatabase($_POST[CalibrationVersion::ID]))
                $age->_age_calibration_version_id = $_POST[CalibrationVersion::ID];
            $age->_age_model_id = $age_model_id;
            $new_obj->addHasAge($age);
        } else {
            $errors[] = "Age value missing";
        }

        // ajout de données à dateinfo
        if (testPost(DateInfo::NAME)){
            $new_obj->_date_lab_number = $_POST[DateInfo::NAME];
        }
        if (testPost(DateInfo::ID_DATE_TYPE)){
            $new_obj->_date_type_id = $_POST[DateInfo::ID_DATE_TYPE];
        } else {
            $errors[] = "Date type missing";
        }
        if (testPost(DateInfo::ID_MAT_DATED)){
            $new_obj->_mat_dated_id = $_POST[DateInfo::ID_MAT_DATED];
        }

        // ajout d'un date comment
        if (testPost(DateComment::ID)){
            $new_obj->addDateComment($_POST[DateComment::ID]);
        }

        if (empty($errors)) {
            // on démarre la transaction
            beginTransaction();

            // si il existe un sample on rattache la date info au sample existant
            $samplesMemeName = Sample::getAllIdName(Sample::NAME . " = '".$sample_name ."'");
            if (count($samplesMemeName) > 0) {
                $sample->setIdValue(key($samplesMemeName));
            } else {
                // on tente d'enregistrer le sample
                $errors = $sample->create(false);
                if (!empty($errors)) array_unshift($errors, "Error while recording sample");
            }

            // on tente d'enregistrer la date info
            if (empty($errors)){
                $new_obj->_sample_id = $sample->getIdValue();
                $errors = $new_obj->create(false);
                if (!empty($errors)) array_unshift($errors, "Error while recording date_info");
            }

            //si c'est un contributeur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
            //si c'est un administrateur qui entre la données, cette dernière sera automatiquemenbt validée
            //CBO // 31/10/2017 // gestion des status à reprendre
            /*if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
                $new_obj->setStatusId(1); // la donnée est validée
            }
            else {
                $new_obj->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
            } */

            // on termine la transaction
            if (empty($errors)){

                commit();
            } else {
                rollBack();
            }

        }

        if (empty($errors)){


            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        if (isset($param_id_core) && $param_id_core != null){
            echo '<div class="btn-toolbar" role="toolbar" align="left">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$param_id_core.'">
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

    if ($id != null){
        echo '<h1>Edit date info</h1>';
    } else {
        echo '<h1>Add a new date info</h1>';
    }
    ?>

            <!-- Formulaire de saisie d'une date info-->
            <form action="" class="form_paleofire" name="formAddSite" method="post" id="formAddSite" >
                <!-- Cadre pour les métadonnées d'une date info !-->
                <fieldset class="cadre">
                    <legend>Description</legend>
                    <p class="site_name">
                        <label for="<?php echo SITE::ID ?>">Site name*</label>
                        <select id="<?php echo SITE::ID ?>" name="<?php echo SITE::ID ?>">
                            <option value="">Select a site</option>
                        <?php
                            $listeSiteEtCore = CORE::getAllCoreBySite();
                            if (isset($_POST[Site::ID])){
                                foreach($listeSiteEtCore as $id_site => $site){
                                    //$selected = ($_POST[Site::ID] == $id_site)? "selected":null;
                                    $selected = ($param_id_site == $id_site)? "selected":null;
                                    echo '<option value = "' . $id_site . '" '. $selected .'>' . $site[0] . '</option>';
                                }
                            } else {
                                foreach($listeSiteEtCore as $id_site => $site){
                                    echo '<option value = "' . $id_site . '">' . $site[0] . '</option>';
                                }
                            }
                        ?>
                        </select>
                    </p>
                    <p>
                        <label>Core name*</label>
                        <select id="<?php echo CORE::ID ?>" name="<?php echo CORE::ID ?>">
                        </select>
                    </p>
                    <?php
                    /*<p>
                        <label>Date info name</label>
                        <input type="text" name="<?php echo SAMPLE::NAME; ?>" id="name_sample" value="<?php if (isset($sample)) echo $sample->getName(); ?>" maxlength="50"/>
                    </p>*/
                    ?>
                    <p>
                        <label>Age Model</label>
                        <select id="<?php echo AgeModel::ID ?>" name="<?php echo AgeModel::ID ?>">
                        </select>
                    </p>
                </fieldset>

                <!-- Cadre pour la profondeur !-->
                <fieldset class="cadre">
                    <legend>Depth</legend>
                    <p>
                        <label>Depth value*</label>
                        <input type="text" name="<?php echo Depth::NAME; ?>" id="name_depth" value="<?php if (isset($depth)) echo $depth->_depth_value; ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label>Detph type*</label>
                        <?php
                        $selectedId = (isset($depth_type))? $depth_type:null;
                        selectHTML(DepthType::ID, 'addDepth_type', 'DepthType', $selectedId);
                        ?>
                    </p>
                </fieldset>

                <!-- Cadre pour l'age !-->
                <fieldset class="cadre">
                    <legend>Age</legend>
                    <p>
                        <label>Age value*</label>
                        <input type="text" name="<?php echo Age::NAME; ?>" id="has_age" value="<?php if (isset($age)) echo $age->_age_value; ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label>Positive error</label>
                        <input type="text" name="<?php echo Age::AGE_POSITIVE_ERROR; ?>" id="has_age_pos_error" value="<?php if (isset($age)) echo $age->_age_positive_error; ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label>Negative error</label>
                        <input type="text" name="<?php echo Age::AGE_NEGATIVE_ERROR; ?>" id="has_age_neg_error" value="<?php if (isset($age)) echo $age->_age_negative_error; ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label>Age units</label>
                        <?php
                        $selectedId = (isset($age))?$age->_age_units_id:null;
                        selectHTML(AgeUnits::ID, 'addAge_units', 'AgeUnits', $selectedId);
                        ?>
                    </p>
                    <p>
                        <label>Calibration method</label>
                        <?php
                        $selectedId = (isset($age))?$age->_age_calibration_method_id:null;
                        selectHTML(CalibrationMethod::ID, 'addCalibration_method', 'CalibrationMethod', $selectedId);
                        ?>
                    </p>
                    <p>
                        <label>Calibration version</label>
                        <?php
                        $selectedId = (isset($age))?$age->_age_calibration_method_id:null;
                        selectHTML(CalibrationVersion::ID, 'addCalibration_version', 'CalibrationVersion', $selectedId);
                        ?>
                    </p>
                </fieldset>

                <!-- Cadre pour la date info !-->
                <fieldset class="cadre">
                    <legend>Date info</legend>
                    <p class="core_name">
                        <label for="date_lab_number">Lab number</label>
                        <input type="text" name="<?php echo DateInfo::NAME; ?>" id="name_dateinfo" value="<?php if (isset($new_obj)) echo $new_obj->_date_lab_number; ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label for="addDate_type">Date type*</label>
                        <?php
                        $selectedId = (isset($new_obj))?$new_obj->_date_type_id:null;
                        selectHTML(DateInfo::ID_DATE_TYPE, 'addDate_type', 'DateType', $selectedId);
                        ?>
                    </p>
                    <p>
                        <label for="addMat_dated">Material dated</label>
                        <?php
                        $selectedId = (isset($new_obj))?$new_obj->_mat_dated_id:null;
                        selectHTML(DateInfo::ID_MAT_DATED, 'addMat_dated', 'MatDated', $selectedId);
                        ?>
                    </p>
                    <p>
                        <label for="addComment_type">Comment</label>
                        <?php
                            $dateComment = $new_obj->getDateComment();
                            $comment = null;
                            if ($dateComment != null) $comment->_date_comments_code = $dateComment;
                            $selectedId = (isset($comment))?$comment->_date_comments_code:null;
                            selectHTML(DateComment::DATE_COMMENTS_CODE, 'addComment_type', 'DateComment', $selectedId);
                        ?>
                    </p>
                </fieldset>

                <!-- Boutons du formulaire !-->
                <p class="submit">
                    <?php
                        if ($id == null){
                            echo '<input type = "submit" name = "submitAdd" value = "Add" class="btn btn-default"/>';
                        } else {
                            echo '<input type = "submit" name = "submitAdd" value = "Save" class="btn btn-default"/>';
                        }
                        /* CBU 31/10/2017 la suppression sera faite au niveau du tableau des dates infos
                        if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                           echo "<input type = 'submit' name = 'deleteAdd' value = 'Delete' />";
                        }
                        */
                        //<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' class="btn btn-default"/>
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

    $('#ID_CORE').change(function(){
        var url = "/Pages/ADA/add_date_info_ajax.php";
        url += "?action=agemodel&core=" + $('#ID_CORE').val();
        var selectAgemodel = $('#ID_AGE_MODEL').empty();
        $.getJSON(url, function(result){
            $.each(result, function(i, field){
                selectAgemodel.append('<option value="'+ i +'">'+field+'</option>');
            });})
            .fail(function( jqxhr, textStatus, error ) {
            var err = textStatus + ", " + error;
            console.log( "Request Failed: " + err );
        });
    });

    <?php
        if (isset($_POST[SITE::ID])){
            echo "$('#ID_SITE').change();";
        }
        if (isset($_POST[CORE::ID])){
            echo "$('#ID_CORE').val(".$_POST[CORE::ID].");";
        }
    ?>
    <?php
    if (isset($new_obj) && isset($param_id_core) && $param_id_core != null && isset($param_id_site) && $param_id_site != null){
        echo '$("#ID_SITE").val('.$param_id_site.');';
        echo '$("#ID_SITE").change();';
        echo '$("#ID_CORE").val('.$param_id_core.');';
    }
    ?>
</script>
<?php
