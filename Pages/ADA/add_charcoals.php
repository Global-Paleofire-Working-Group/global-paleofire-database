<?php
/*
 * fichier Pages/ADA/add_charcoal.php
 *
 */

include 'PHPExcel/Classes/PHPExcel/IOFactory.php';

const FileSize = 4000000;
const COL_DO = 'B';
const COL_UP = 'A';
const COL_SIZE = 'E';
const COL_Q = 'F';
const COL_AUP = 'C';
const COL_ADO = 'D';

$extension = array('xls', 'xlsx');
//$pathUploadedData = REP_CHARCOALS_IMPORT;
$pathUploadedData = "/tmp/";
//try{
if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Sample.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Models/DataBaseVersion.php';
    require_once './Models/user/WebAppUserGCD.php';

    if (!isset($success_form)) {
        $success_form = "";
    }
    if (!isset($error_form)) {
        $error_form = array();
    }

    if (!isset($new_site)) {
        $obj = new Sample();
    }

    $region_id = null;
    if (isset($_GET['region_id']) && is_numeric($_GET['region_id'])) {
        $region_id = $_GET['region_id'];
        $new_site->_site_region_id = $region_id;
    }

    if (isset($_POST['submitAdd'])) {
        // on vérifie qu'un fichier a été soumis et qu'il n'y a pas d'erreur de téléchargement
        if ($_FILES['fileToUpload']['error'] > 0) {
            // gestion des erreurs d'upload
            switch ($_FILES['fileToUpload']['error']) {
                case UPLOAD_ERR_NO_FILE : $errors[] = "Select a file";
                    break;
                case UPLOAD_ERR_INI_SIZE :
                case UPLOAD_ERR_FORM_SIZE : $errors[] = "File size must be less than " . FileSize;
                    break;
                default : $errors[] = "Error while transfering file";
                    break;
            }
        } else {
            // on récupère le contributeur en premier (=personne connectée actuellement)
            // si le contributeur n'existe pas pas besoin de traiter le fichier
            $user_id = $_SESSION['gcd_user_id'];

            connectionBaseWebapp();
            $contact_contributeur = WebAppUserGCD::getContactId($user_id);
            connectionBaseInProgress();
            if ($contact_contributeur != null) {
                // on vérifie que le fichier a la bonne extension
                $ext_file = strtolower(substr(strrchr($_FILES['fileToUpload']['name'], '.'), 1));
                if (in_array($ext_file, $extension)) {
                    // on copie le fichier dans le répertoire local
                    $file_name = $pathUploadedData . $user_id . '_' . date('dMyms') . '.' . $ext_file;
                    $res = move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $file_name);
                    if ($res) {
                        $errors = traitementFichierExcel($file_name, $user_id);
                    } else {
                        $errors[] = "Error while copying file";
                    }
                } else {
                    $errors[] = "File type must be " . implode(',', $extension);
                }
            } else {
                $errors[] = "Error your user account is not linked to a contact";
            }
        }

        if (empty($errors)) {
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>' . implode('</br>', $errors) . "</div>";
        }
    }
    echo '<div class="alert alert-danger" id="divError" hidden><strong>Error!</strong><div></div></div>';
    ?>

    <h1>Add new charcoal data</h1>
    <!-- Formulaire d'intégration d'un fichier de samples -->
    <form action="" method="post" enctype="multipart/form-data" class="form_paleofire" name="formAddSite" id="formAddSite">
        <!-- Cadre pour les métadonnées du site !-->
        <fieldset class="cadre">
            <legend>Generate template file</legend>
            <p class="site_name">
                <label for="name_site">Site name*</label>
                <select id="<?php echo SITE::ID ?>" name="<?php echo SITE::ID ?>">
                    <option value="">Select a site</option>
                    <?php
                    $listeSiteEtCore = CORE::getAllCoreBySite();

                    foreach ($listeSiteEtCore as $id_site => $site) {
                        echo '<option value = "' . $id_site . '">' . $site[0] . '</option>';
                    }
                    ?>
                </select>
            </p>
            <p class="core_name">
                <label for="name_core">Core name*</label>
                <select id="<?php echo CORE::ID ?>" name="<?php echo CORE::ID ?>">
                    <option value="">Select a core</option>
                </select>
            </p>
            <p class="data_source">
                <label for="data_source">Data source</label>
                <?php
                selectHTML(Charcoal::ID_DATA_SOURCE, 'addCharcoals_data_source', 'DataSource', null, null);
                ?>
            </p>
            <p class="charcoal_method">
                <label for="charcoal_method">Method</label>
                <?php
                selectHTML(Charcoal::ID_CHARCOAL_METHOD, 'addCharcoals_method', 'CharcoalMethod', null, null);
                ?>
            </p>
            <p class="charcoal_units">
                <label for="charcoal_units">Units*</label>
                <?php
                selectHTML(Charcoal::ID_PREF_CHARCOAL_UNITS, 'addCharcoals_units', 'CharcoalUnits', null, null);
                ?>
            </p>
            <p class="age_method">
                <label for="age_method">Age method*</label>
                <?php
                selectHTML(AgeModel::ID_AGE_MODEL_METHOD, 'addAge_method', 'AgeModelMethod', null, null);
                ?>
            </p>

            <p class="pub">
                <label for="pub">Publication</label>
                <?php
                selectMultipleHTML(Publi::ID . '[]', 'addCharcoals_publi', 'Publi', null, null, 5, true);
                ?>
            </p>
            <p class="author" style="width:470px">
                <label for="author">Author</label>
                <?php
                selectMultipleHTML(Contact::ID . '[]', 'addCharcoals_author', 'Contact', null, null, 5, true);
                ?>
            </p>
            <!--<div style="width:200px"><p>Authors selected :<div id="selectedAuthors"> </div></p></div>-->
            <!-- Génération du modèle pour intégration des données !-->
            <p>
                <a role="btn" class="btn btn-default" id="btnFile" href=javascript:getFileTamplate()>Download template for data integration</a>
            </p>
        </fieldset>

        <!-- Cadre pour la description de la série de samples!-->
        <fieldset class="cadre">
            <legend>Import samples data</legend>

            <p>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo FileSize ?>" aria-describedby="fileHelp"/>
                <input type="file" name="fileToUpload" id="fileToUpload" />
                <small id="fileHelp" class="form-text text-muted">File size must be less than <?php echo FileSize/1000000; ?> M<br/>
                File type must be <?php echo implode($extension, ',');?></small>
            </p>
        </fieldset>
        <!-- Boutons du formulaire !-->
        <p class="submit">
            <button type='submit' name='submitAdd' value='Add' class="btn btn-default">Import data</button>
        </p>
    </form>
    <?php
} else {
    ?>

    <span class = "allIsOk">Successfully addition.</span><br /><br/>
    <a href = "index.php?p=ADA/index&gcd_menu=ADA">&raquo; go back to Home</a>
    <?php
}
/*} catch(Exception $e){
    echo $e->getCode();
    echo $e->getLine();
    echo $e->getTrace();
    echo $e->getTraceAsString();
    echo $e->getMessage();
}*/
?>

<?php

function testPost($post_var) {
    return (isset($_POST[$post_var])) && $_POST[$post_var] != NULL && $_POST[$post_var] != 'NULL' && trim(delete_antiSlash($_POST[$post_var])) != "";
}

function traitementFichierExcel($file_name, $id_contributeur) {
    // (ini_set n'est valable que pendant le script)
        ini_set('memory_limit', '512M');
    // si on ne rencontre aucune erreur on enregistre sinon on continue le traitement afin d'afficher
    // toutes les erreurs contenues dans le fichier à l'utilisateur
    $errors = [];

    // creates an object instance of the class, and read the excel file data
    try {
        $inputFileType = PHPExcel_IOFactory::identify($file_name);
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $excel = $objReader->load($file_name);
        $sheet = $excel->getSheet(0);

        // récupération des données saisies dans le formulaire avant génération du template
        $values = $sheet->getCell('A2')->getValue();
        $values = json_decode($values);



        // les valeurs datasource et method peuvent être null, on teste les autres
        if ($values != null && $values->site != null && $values->core != null && $values->units != null && $values->agemethod != null) {
            // récupération de l'id du core et du site et vérification de leur existence
            $site = $values->site;
            $core = $values->core;
            if (!Core::coreEtSiteExist($core, $site))
                $errors[] = "[ Cell A2 ] (1) This cell has configuration data automatically generated, should not be modified (error : Core or site undefined)";

            // récupération du code datasource si spécifié et vérification de son existance
            if ($values->datasource){
                $datasource = $values->datasource;
                $tab = DataSource::getAllIdName();
                if ($tab[$datasource] == null)
                    $errors[] = "[ Cell A2 ] (2) This cell has configuration data automatically generated, should not be modified (error : Data source undefined)";
            } else {
                $datasource = NULL;
            }
            // récupération du code de la méthode si spécifié et vérification de son existance
            if ($values->method){
                $method = $values->method;
                $tab = CharcoalMethod::getAllIdName();
                if ($tab[$method] == null)
                    $errors[] = "[ Cell A2 ] (3) This cell has configuration data automatically generated, should not be modified (error : Method undefined)";
            } else {
                $method = NULL;
            }
            // récupération des unités et vérification de leurs existance
            $units = $values->units;
            $tab = CharcoalUnits::getAllIdName();
            if ($tab[$units] == null)
                $errors[] = "[ Cell A2 ] (4) This cell has configuration data automatically generated, should not be modified (error : Units undefined)";

            $agemethod = $values->agemethod;
            $age_model_method = AgeModelMethod::getAgeModelMethodByID($agemethod);
            if ($age_model_method == null)
                $errors[] = "[ Cell A2 ] (5) This cell has configuration data automatically generated, should not be modified (error : Age method undefined)";

            // on vérifie que les publis existent
            $publis = NULL;
            if ($values->pubs != NULL){
                $publis = explode(',', $values->pubs);
                foreach($publis as $id_pub){
                    if (empty(Publi::getObjectPaleofireFromId($id_pub))){
                        $errors[] = "[ Cell A2 ] (5) This cell has configuration data automatically generated, should not be modified (error : Publication undefined)";
                    }
                }
            }

            // on vérifie que les auteurs existent
            $authors = NULL;
            if ($values->authors != NULL){
                $authors = explode(',', $values->authors);
                foreach($authors as $id_contact){
                    if (empty(Contact::getObjectFromStaticList($id_contact))){
                        $errors[] = "[ Cell A2 ] (5) This cell has configuration data automatically generated, should not be modified (error : Author undefined)";
                    }
                }
            }

            // si il y a des erreurs dans la ligne de configuration on ne parse pas le fichier
            // tentative d'intégration malveillante ou autre donc pas d'intégration
            if (empty($errors)) {
                // on récupère les données de la carotte pour pouvoir récupérer le nom par la suite
                $obj_core = Core::getCoreDataFromId($core);

                if ($obj_core != NULL) {
                    // on récupère la version de la base de données en cours
                    $version_database = DataBaseVersion::getVersionInProgress();
                    //création du modèle d'age pour tout le fichier
                    $age_model = new AgeModel();
                    $age_model->_age_model_method = $age_model_method;
                    $age_model->_core_id = $core;
                    // concatenation de "age_model_core_name_indice"
                    // on verifie si il existe déjà des ages_models du même nom
                    $result = AgeModel::getArrayFieldsValueFromWhere(AgeModel::NAME, AgeModel::NAME . " like 'age_model_" . $obj_core->getName() . "%'");
                    if ($result != null && count($result) > 0) {
                        $nb = count($result) + 1;
                        $age_model->setNameValue("age_model_" . $obj_core->getName() . '_' . $nb);
                    } else {
                        $age_model->setNameValue("age_model_" . $obj_core->getName());
                    }

                    // on récupère la liste des charcoal size pour transformer le label en code
                    // CBO // 09-01-2018 // on force par défaut au cm3 (donc ID=1), la liste de sélection du template excel pouvait être écrasée par l'utilisateur
                    //$listeCharcoalSize = CharcoalSize::getAllIdName();

                    // on commence la transaction en base de données
                    beginTransaction();

                    // création du modèle d'age
                    $err_create = $age_model->create(false);
                    if (empty($err_create)) {
                        // liste des depths une profondeur ne peut apparaitre qu'une fois dans une liste
                        $listeDepthUP = [];
                        $listeDepthDO = [];

                        // itération sur les lignes pour récupérer les données des charcoals
                        // ligne 1 : rappel, ligne 2 : valeurs à intégrées, ligne 3 entêtes des colonnes
                        $rowIterator = $sheet->getRowIterator();
                        foreach ($rowIterator as $row) {
                            $id_row = $rowIterator->key();
                            if ($id_row > 3) {
                                $sample = new Sample();

                                // on récupère les valeurs des profondeurs
                                $depthUpVal = $sheet->getCell(COL_UP . $id_row)->getValue();
                                $depthDoVal = $sheet->getCell(COL_DO . $id_row)->getValue();

                                if($depthUpVal == NULL && $depthDoVal == null){
                                    // si les valeurs sont null, on est surement à la fin du fichier et on boucle sur des lignes vides
                                    // donc on sort de la boucle
                                    throw new Exception("End of file", 1);
                                }
                                // on teste les valeurs des depth
                                if ($depthUpVal < 0)
                                    $errors[] = "[ Cell " . COL_UP . $id_row . " ] DEPTH UP must be a positive number";
                                if ($depthDoVal < 0)
                                    $errors[] = "[ Cell " . COL_DO . $id_row . " ] DEPTH DOWN must be a positive number";
                                if ($depthDoVal <= $depthUpVal)
                                    $errors[] = "[ Row " . $id_row . " ] DEPTH DOWN must be greater than DEPTH UP";
                                if (($depthUpVal == null) && ($depthUpVal != 0))
                                    $errors[] = "[ Cell " . COL_UP . $id_row . " ] DEPTH UP must be filled";
                                if (($depthDoVal == null) && ($depthUpVal != 0))
                                    $errors[] = "[ Cell " . COL_DO . $id_row . " ] DEPTH DOWN must be filled";
                                if (in_array($depthUpVal, $listeDepthUP))
                                    $errors[] = "[ Cell " . COL_UP . $id_row . " ] DEPTH UP already exist in the preceding lines";
                                if (in_array($depthDoVal, $listeDepthDO))
                                    $errors[] = "[ Cell " . COL_DO . $id_row . " ] DEPTH DOWN already exist in the preceding lines";
                                $listeDepthUP[] = $depthUpVal;
                                $listeDepthDO[] = $depthDoVal;

                                $depthUp = new Depth($depthUpVal, DepthType::DEPTH_TOP);
                                $sample->addDepth($depthUp);

                                $depthDo = new Depth($depthDoVal, DepthType::DEPTH_BOTTOM);
                                $sample->addDepth($depthDo);

                                // la middle depht ne doit plus être intégrer par le fichier elle est calculée automatiquement
                                // à partir des deux autre (UP et DO) qui sont obligatoire
                                $depthMid = new Depth(($depthUp->_depth_value + $depthDo->_depth_value) / 2, DepthType::DEPTH_MIDDLE);
                                $sample->addDepth($depthMid);
                                $sample->_default_depth = $depthMid;

                                // on récupère la valeur du nom du sample et on la teste
                                //$nameVal = $sheet->getCell(COL_NAME.$id_row)->getValue();
                                // on créé le nom à partir du core et de la profondeur moyenne
                                $nameVal = $obj_core->getName() . '_' . $depthMid->_depth_value;
                                $nbSamplesMemeName = Sample::countSamples(Sample::NAME, $nameVal);
                                if ($nbSamplesMemeName > 0)
                                    $errors[] = "[ Cell " . COL_UP . $id_row . " ] SAMPLE already exists in database";

                                $sample->setNameValue(utf8_decode($nameVal));
                                $sample->_sample_core_id = $core;
                                $sample->_sample_age_model = $age_model;

                                //on récupère les valeurs des ages
                                $ageUpVal = $sheet->getCell(COL_AUP . $id_row)->getValue();
                                //$ageUpPosError = $sheet->getCell(COL_AUP_POS_ERR.$id_row)->getValue();
                                //$ageUpNegError = $sheet->getCell(COL_AUP_NEG_ERR.$id_row)->getValue();
                                $ageDoVal = $sheet->getCell(COL_ADO . $id_row)->getValue();
                                //$ageDoPosError = $sheet->getCell(COL_ADO_POS_ERR.$id_row)->getValue();
                                //$ageDoNegError = $sheet->getCell(COL_ADO_NEG_ERR.$id_row)->getValue();
                                // on teste les valeurs des ages
                                if ($ageUpVal == null) {
                                    $errors[] = "[ Cell " . COL_AUP . $id_row . " ] AGE UP must be filled";
                                } else if (!is_numeric($ageUpVal)) {
                                    $errors[] = "[ Cell " . COL_AUP . $id_row . " ] AGE UP must be numeric";
                                }
                                if ($ageDoVal == null) {
                                    $errors[] = "[ Cell " . COL_ADO . $id_row . " ] AGE DOWN must be filled";
                                } else if (!is_numeric($ageDoVal)) {
                                    $errors[] = "[ Cell " . COL_ADO . $id_row . " ] AGE DOWN must be numeric";
                                }

                                $estimated_age_up = new EstimatedAge();
                                $estimated_age_up->_est_age = $ageUpVal;
                                $estimated_age_up->_depth = $depthUp;
                                //$estimated_age_up->_est_age_negative_error = $ageUpNegError;
                                //$estimated_age_up->_est_age_positive_error = $ageUpPosError;

                                $estimated_age_do = new EstimatedAge();
                                $estimated_age_do->_est_age = $ageDoVal;
                                $estimated_age_do->_depth = $depthDo;
                                //$estimated_age_do->_est_age_negative_error = $ageDoNegError;
                                //$estimated_age_do->_est_age_positive_error = $ageDoPosError;
                                // on calcule l'age moyen à associer à la middle depth
                                $estimated_age_mid = new EstimatedAge();
                                $estimated_age_mid->_est_age = ($estimated_age_up->_est_age + $estimated_age_do->_est_age) / 2;
                                $estimated_age_mid->_depth = $depthMid;
                                //if ($ageUpNegError != null && $ageDoNegError != null)
                                //    $estimated_age_mid->_est_age_negative_error = ($ageUpNegError + $ageDoNegError) / 2;
                                //if ($ageUpPosError != null && $ageDoPosError != null)
                                //    $estimated_age_mid->_est_age_positive_error = ($ageUpPosError + $ageDoPosError) / 2;

                                $sample->addEstimatedAge($estimated_age_up);
                                $sample->addEstimatedAge($estimated_age_do);
                                $sample->addEstimatedAge($estimated_age_mid);

                                // le sample n'est pas encore enregistré mais on commence la création du charcoal
                                // pour pouvoir afficher les eventuelles erreurs à l'utilisateur
                                $charcoal = new Charcoal();
                                $charcoal->setNameValue($sample->getName());

                                $charcoal->_charcoal_datasource_id = $datasource;
                                $charcoal->_charcoal_method_id = $method;

                                // on récupère l'id de charcoal_size
                                /*$codeCharcoalSize = array_search($sheet->getCell(COL_SIZE . $id_row)->getValue(), $listeCharcoalSize);
                                if ($codeCharcoalSize == null)
                                    $errors[] = "[ Cell " . COL_SIZE . $id_row . " ] SIZE has an unknown value";
                                $charcoal->_charcoal_size_id = $codeCharcoalSize;*/
                                // CBO // 09-01-2018 // on force par défaut en cm3 (donc ID=40), la liste de sélection du template excel pouvait être écrasée par l'utilisateur
                                // dorénavent l'utilisateur donne des données en cm3 et indique dans la colone une qt pour les cm3
                                $charcoal->_charcoal_size_id = 40;
                                $size_value = $sheet->getCell(COL_SIZE . $id_row)->getValue();
                                if (is_numeric($size_value)){
                                    $charcoal->_charcoal_size_value = $size_value;
                                } else {
                                    $errors[] = "[ Cell " . COL_SIZE . $id_row . " ] SIZE must be numeric";
                                }

                                // statut de départ => données soumises pour validation
                                //si c'est un contributeur ou un administrateur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
                                //si c'est le superadministrateur qui entre la données, cette dernière sera automatiquemenbt validée
                                if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
                                    $charcoal->_charcoal_status_id = 1; // la donnée est validée
                                } else {
                                    $charcoal->_charcoal_status_id = 0; //la donnée apparaîtra dans la liste données à valider
                                }

                                $charcoal->_charcoal_contact_id = $id_contributeur;
                                $charcoal->_charcoal_database_id = $version_database;

                                // on récupère la quantité de charbon et on la teste
                                $qtVal = $sheet->getCell(COL_Q . $id_row)->getValue();
                                if ($qtVal === null)
                                    $errors[] = "[ Cell " . COL_Q . $id_row . " ] QUANTITY must be filled";
                                if ($qtVal < 0)
                                    $errors[] = "[ Cell " . COL_Q . $id_row . " ] QUANTITY must be a positive number";
                                $charcoal->addCharcoalQuantity($qtVal, $units);
                                $charcoal->_charcoal_charcoal_units_id = $units;
                                $charcoal->_list_authors = $authors;
                                $charcoal->_list_publications = $publis;

                                // si il n'y a pas d'erreurs on tente de créer le sample
                                if (empty($errors)) {

                                    // on indique à la fonction de ne pas gérer les transactions
                                    $errors = $sample->create(false);
                                    if (empty($errors)) {
                                        $charcoal->_sample_id = $sample->getIdValue();
                                        // la fct create créée pour l'intégration ne convient pas dans notre cas
                                        // on indique à la fonction de ne pas gérer les transactions
                                        $errors = $charcoal->save();
                                    }

                                    if (empty($insert_errors)) {

                                        // puis on insére les nouveaux enregistrements de la table r_site_is_referenced
                                        if ($charcoal->_list_publications != null){
                                            foreach ($charcoal->_list_publications as $publi_id) {
                                                $result_insert = insertIntoTable("r_site_is_referenced", array("ID_PUB" => $publi_id, "ID_SITE" => $site));
                                                if (!$result_insert)
                                                    $insert_errors[] = "Insert into table r_site_is_referenced";
                                            }
                                        }
                                    }
                                }
                                // si il n'y a pas d'erreur on commite les requêtes dans la base de données
                                if (empty($errors)) {
                                    commit();
                                    //ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                                    writeChangeLog("add_charcoals", "site " . $site." core ".$core);
                                } else {
                                    rollBack();
                                }
                            }
                            if (count($errors) > 20){
                                throw new Exception("To many errors in charcoals file", 1);
                            }
                        }
                    }
                } else {
                    $errors[] = "No core for this charcoal!";
                }
            }
        } else {
            $errors[] = "[ Cell A2 ]6 This cell has configuration data automatically generated,should not be modified (error : Data missing or wrong format)";
        }
    } catch (Exception $e) {
        if($e->getCode() == 1){
            // il y a trop d'erreur ou on est arrivé à la fin du fichier
        } else {
            LogError($e->getMessage());
            return array("Error reading excel file");

        }
        // on annule toutes les mises à jours de la base de données
        rollBack();
    }

    return $errors;
}

function array_keys_exists($array_keys, $array) {
    $exist = true;
    foreach ($array_keys as $key) {
        if (!array_key_exists($key, $array)) {
            $exist = false;
        }
    }
    return $exist;
}
?>
<script type="text/javascript">
<?php
echo 'var tabCore = ' . json_encode($listeSiteEtCore) . ';';
?>
    $('#ID_SITE').change(
            function () {
                $('#ID_CORE').empty();
                var id_site = $(this).val();
                var tab = tabCore[id_site][1];
                for (var key in tab) {
                    $('#ID_CORE').append('<option value="' + key + '">' + tab[key] + '</option>');
                }
            });

    $('#btnFile').click(function (event) {
        $("#divError").hide();
        if ($('#ID_SITE').val() == null || $('#ID_SITE').val() == ""
                || $('#ID_CORE').val() == null || $('#ID_CORE').val() == ""
                || $('#addCharcoals_units').val() == null || $("#addCharcoals_units").val() == "" || $("#addCharcoals_units").val() == "NULL"
                || $('#addAge_method').val() == null || $('#addAge_method').val() == "" || $('#addAge_method').val() == "NULL") {
            $("#divError div").html("Site name, core name, units and age method must be selected");
            $("#divError").show();
            event.preventDefault();
        }
    });
    /*$('#addCharcoals_author').change(function(){
     var div_author = $("#selectedAuthors");
     div_author.empty();
     $( "#addCharcoals_author option:selected" ).each(function(){
     div_author.append($(this).text() + '<br>');
     })
     });*/
    function getFileTamplate() {
        var url = "Pages/ADA/add_charcoals_create_file.php";
        url += "?is=" + $('#ID_SITE').val();
        url += "&ic=" + $('#ID_CORE').val();
        url += "&ds=" + $('#addCharcoals_data_source').val();
        url += "&m=" + $('#addCharcoals_method').val();
        url += "&u=" + $('#addCharcoals_units').val();
        url += "&a=" + $('#addAge_method').val();
        url += "&pu=" + $('#addCharcoals_publi').val();
        url += "&au=" + $('#addCharcoals_author').val();
        window.open(url, '_self');
    }
</script>
