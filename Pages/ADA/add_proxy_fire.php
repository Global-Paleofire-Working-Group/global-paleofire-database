<?php
/*
 * fichier Pages/ADA/add_proxy_fire.php
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
$pathUploadedData = "/tmp/";
//try{
if (isset($_SESSION['started'])) {

    require_once (REP_MODELS."ObjectPaleofire.php");
    require_once (REP_MODELS."Site.php");
    require_once (REP_MODELS."Core.php");
    require_once (REP_MODELS."DataSource.php");
    require_once (REP_MODELS."ProxyFireMeasurement.php");
    require_once (REP_MODELS."ProxyFire.php");
    require_once (REP_MODELS."ProxyFireData.php");
    require_once (REP_MODELS."ProxyFireMeasurement.php");
    require_once (REP_MODELS."ProxyFireMeasurementUnit.php");
    require_once (REP_MODELS."ProxyFireMethodTreatment.php");
    require_once (REP_MODELS."ProxyFireMethodEstimation.php");

    require_once './Models/Sample.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/scripts.php';

    require_once (REP_LIB."connect_database.php");
    require_once (REP_LIB."database.php");


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

    <h1>Add new fire proxy data</h1>
    <!-- Formulaire d'intégration d'un fichier de samples -->
    <form action="" method="post" enctype="multipart/form-data" class="form_paleofire" name="formAddProxyFireDataGenerate" id="formAddProxyFireDataGenerate">
        <!-- Cadre pour les métadonnées du site !-->
        <fieldset class="cadre">
            <legend>Generate template file</legend>
            <!-- site name -->
            <p class="site_name">
                <label for="name_site">Site name*</label>
                <select id="<?php echo SITE::ID ?>" name="<?php echo SITE::ID ?>" required>
                    <option value="">Select a site</option>
                    <?php
                    $listeSiteEtCore = CORE::getAllCoreBySite();

                    foreach ($listeSiteEtCore as $id_site => $site) {
                        echo '<option value = "' . $id_site . '">' . $site[0] . '</option>';
                    }
                    ?>
                </select>
            </p>
            <!-- core name  -->
            <p class="core_name">
                <label for="name_core">Core name*</label>
                <select id="<?php echo CORE::ID ?>" name="<?php echo CORE::ID ?>" required>
                    <option value="">Select a core</option>
                </select>
            </p>
            <!-- data source -->
            <p class="data_source">
                <label for="data_source">Data source</label>
                <?php
                selectHTML(Charcoal::ID_DATA_SOURCE, 'addCharcoals_data_source', 'DataSource', null, null);
                ?>
            </p>
            <!-- fire proxy -->
            <p class="fire_proxy">
                <label for="fire_proxy">Fire Proxy</label>
                <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>" onchange="AffProxyFireSelection()" required>
                    <option value="">Select a fire proxy</option>
                <?php

                $listeProxyFire = ProxyFire::getAllProxyFire();
                foreach ($listeProxyFire as $id_proxy_fire => $proxy_fire) {

                  // --- version finale --- //
                  //A Décommenter quand les autres fire proxy seront configurés.
                  //echo '<option value = "' . $proxy_fire[0] . '">' . $proxy_fire[0] . '</option>';

                  // --- version tempo --- //
                  // A Commenter quand la version finale est utilisée
                  if($proxy_fire[0] == "Charcoal"){
                    echo '<option class="Charcoal" value = "' . $proxy_fire[0] . '">' . $proxy_fire[0] . '</option>';
                  }
                  else {

                    $test='<span class="new-badge new-badge-error"> (not available)</span>';
                    echo '<option value = "' . $proxy_fire[0] . '" disabled>' . $proxy_fire[0] . $test . '</option>';

                  }
                  // -------------------- //
                }

                ?>
              </select>
            </p>

            <!-- ============================== -->
            <!-- ============================== -->
            <!-- fire proxy options -->
            <!-- <p class="fire_proxy_options"> -->
              <fieldset id="fieldset_fire_proxy_options" class="cadre" style="display:none">
                <legend id="legend_fire_proxy_options" style="display:none">Fire proxy options</legend>

                <!-- -------------------------- -->
                <!-- selection_Charcoal options -->
                <div id="selection_charcoal" style="display:none">
                    <!--  -->
                    <p class="measurement_charcoal">
                      <label for="measurement_charcoal">Measurement</label>
                      <select name="measurement" id="<?php echo ProxyFireMeasurement::ID ?>" name="<?php echo ProxyFireMeasurement::ID ?>" onchange="GetMeasurement()">
                          <option value="">Select a measurement</option>
                      <?php

                      $listeProxyFireMeasurement = ProxyFireMeasurement::getAllProxyFireMeasurement();
                      foreach ($listeProxyFireMeasurement as $id_proxy_fire_measurement => $proxy_fire_measurement) {
                        echo '<option value = "' . $proxy_fire_measurement[0] . '">' . $proxy_fire_measurement[1] .' ('. $proxy_fire_measurement[0] . ')</option>';
                      }

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="measurement_units_charcoal">
                      <label for="measurement_units_charcoal">Unit</label>

                      <!-- measurement_init -->
                      <select id="type_measurement_init" style="display:inline" name="<?php echo ProxyFireMeasurementUnit::ID ?>">
                          <option value="">select unit</option>
                      </select>
                      <!--------->
                      <!-- measurement_conc -->
                      <select id="type_measurement_conc" style="display:none" name="<?php echo ProxyFireMeasurementUnit::ID ?>" required>
                          <option value="">Select a unit</option>
                          <?php
                          $listeProxyFireMeasurementUnit = ProxyFireMeasurementUnit::getAllProxyFireMeasurementUnit("CONC");
                          foreach ($listeProxyFireMeasurementUnit as $id_proxy_fire_measurement_unit => $proxy_fire_measurement_unit) {
                            echo '<option value = "' . $proxy_fire_measurement_unit[0] . '">' . $proxy_fire_measurement_unit[0] . '</option>';
                          }
                          ?>
                      </select>
                      <!--------->
                      <!-- measurement_influx -->
                      <select id="type_measurement_influx" style="display:none" name="<?php echo ProxyFireMeasurementUnit::ID ?>" required>
                          <option value="">Select a unit</option>
                          <?php
                          $listeProxyFireMeasurementUnit = ProxyFireMeasurementUnit::getAllProxyFireMeasurementUnit("INFL");
                          foreach ($listeProxyFireMeasurementUnit as $id_proxy_fire_measurement_unit => $proxy_fire_measurement_unit) {
                            echo '<option value = "' . $proxy_fire_measurement_unit[0] . '">' . $proxy_fire_measurement_unit[2] . '</option>';
                          }
                          ?>
                      </select>
                      <!------------------------>
                      <!-- measurement_copo -->
                      <select id="type_measurement_copo" style="display:none" name="<?php echo ProxyFireMeasurementUnit::ID ?>" required>
                          <option value="">Select a unit</option>
                          <?php
                          $listeProxyFireMeasurementUnit = ProxyFireMeasurementUnit::getAllProxyFireMeasurementUnit("COPO");
                          foreach ($listeProxyFireMeasurementUnit as $id_proxy_fire_measurement_unit => $proxy_fire_measurement_unit) {
                            echo '<option value = "' . $proxy_fire_measurement_unit[0] . '">' . $proxy_fire_measurement_unit[0] . '</option>';
                          }
                          ?>
                      </select>
                      <!--------->


                    </p>
                    <!--  -->
                    <p class="particle_size_charcoal">
                      <label for="particle_size_charcoal">Particle size (micrometer)</label>
                      <input id="ID_PROXY_FIRE_PARTICLE_SIZE_MIN" type="number" name="particle_size_Charcoal_min" min=0 placeholder="Minimum value" required>
                      <input id="ID_PROXY_FIRE_PARTICLE_SIZE_MAX" type="number" name="particle_size_Charcoal_max" min=0 placeholder="Maximum value" required><br>
                    </p>
                    <!--  -->
                    <p class="method_treatment_charcoal">
                      <label for="method_treatment_charcoal">Method treatment</label>
                      <select id="method_treatment_charcoal" name="<?php echo ProxyFireMethodTreatment::ID ?>" required>
                          <option value="">Select a method treatment</option>
                      <?php
                      $listeProxyFireMethodTreatment = ProxyFireMethodTreatment::getAllProxyFireMethodTreatment("Charcoal");
                      foreach ($listeProxyFireMethodTreatment as $id_proxy_fire_method_treatment => $proxy_fire_method_treatment) {

                        // --- version tempo --- //
                        if($proxy_fire_method_treatment[0] == "GEOCHEMISTRY"){
                          echo '<option value = "' . $proxy_fire_method_treatment[0] . '" disabled>' . $proxy_fire_method_treatment[1] . '</option>';
                        }
                        else {
                            echo '<option value = "' . $proxy_fire_method_treatment[0] . '">' . $proxy_fire_method_treatment[1] . '</option>';
                        }
                        // -------------------- //


                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_estimation_charcoal">
                      <label for="method_estimation_charcoal">Method estimation</label>
                      <select id="method_estimation_charcoal" name="<?php echo ProxyFireMethodEstimation::ID ?>" required>
                          <option value="">Select a method estimation</option>
                      <?php
                      $listeProxyFireMethodEstimation = ProxyFireMethodEstimation::getAllProxyFireMethodEstimation("Charcoal");
                      foreach ($listeProxyFireMethodEstimation as $id_proxy_fire_method_estimation => $proxy_fire_method_estimation) {
                      echo '<option value = "' . $proxy_fire_method_estimation[0] . '">' . $proxy_fire_method_estimation[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="age_method_charcoal">
                        <label for="age_method_charcoal">Age method</label>
                        <?php
                        selectHTML(AgeModel::ID_AGE_MODEL_METHOD, 'ID_PROXY_FIRE_AGE_METHOD', 'AgeModelMethod', null, null);
                        ?>
                    </p>
                    <!--  -->
                    <p class="pub_charcoal">
                        <label for="pub_charcoal">Publication</label>
                        <?php
                        selectMultipleHTML(Publi::ID . '[]', 'ID_PROXY_FIRE_PUBLI', 'Publi', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                    <p class="author_charcoal" style="width:470px">
                        <label for="author_charcoal">Author</label>
                        <?php
                        selectMultipleHTML(Contact::ID . '[]', 'ID_PROXY_FIRE_AUTHOR', 'Contact', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                </div>
                <!-- --------------------------------- -->
                <!-- selection_Burnt_Phytolith options -->
                <div id="selection_burnt_phytolith" style="display:none">
                    <!--  -->
                    <p class="measurement_burnt_phytolith">
                      <label for="measurement_burnt_phytolith">Measurement</label>
                      <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>">
                          <option value="">Select a measurement</option>
                      <?php

                      //TODO

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="particle_size_burnt_phytolith">
                      <label for="particle_size_burnt_phytolith">Particle size (micrometer)</label>
                      <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>">
                          <option value="">Select particles size</option>
                      <?php

                      //TODO

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_treatment_burnt_phytolith">
                      <label for="method_treatment_burnt_phytolith">Method treatment</label>
                      <select id="method_treatment_burnt_phytolith" name="<?php echo ProxyFireMethodTreatment::ID ?>">
                          <option value="">Select a method treatment</option>
                      <?php
                      $listeProxyFireMethodTreatment = ProxyFireMethodTreatment::getAllProxyFireMethodTreatment("Burnt Phytolith");
                      foreach ($listeProxyFireMethodTreatment as $id_proxy_fire_method_treatment => $proxy_fire_method_treatment) {
                      echo '<option value = "' . $proxy_fire_method_treatment[0] . '">' . $proxy_fire_method_treatment[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_estimation_burnt_phytolith">
                      <label for="method_estimation_burnt_phytolith">Method estimation</label>
                      <select id="method_estimation_burnt_phytolith" name="<?php echo ProxyFireMethodEstimation::ID ?>">
                          <option value="">Select a method estimation</option>
                      <?php
                      $listeProxyFireMethodEstimation = ProxyFireMethodEstimation::getAllProxyFireMethodEstimation("Burnt Phytolith");
                      foreach ($listeProxyFireMethodEstimation as $id_proxy_fire_method_estimation => $proxy_fire_method_estimation) {
                      echo '<option value = "' . $proxy_fire_method_estimation[0] . '">' . $proxy_fire_method_estimation[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="age_method_burnt_phytolith">
                        <label for="age_method_burnt_phytolith">Age method</label>
                        <?php
                        selectHTML(AgeModel::ID_AGE_MODEL_METHOD, 'addAge_method', 'AgeModelMethod', null, null);
                        ?>
                    </p>
                    <!--  -->
                    <p class="pub_burnt_phytolith">
                        <label for="pub_burnt_phytolith">Publication</label>
                        <?php
                        selectMultipleHTML(Publi::ID . '[]', 'addCharcoals_publi', 'Publi', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                    <p class="author_burnt_phytolith" style="width:470px">
                        <label for="author_burnt_phytolith">Author</label>
                        <?php
                        selectMultipleHTML(Contact::ID . '[]', 'addCharcoals_author', 'Contact', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                </div>

                <!-- ----------------------------------------------- -->
                <!-- selection_Reflective_Graminoid_cuticles options -->
                <div id="selection_reflective_graminoid_cuticles" style="display:none">
                    <!--  -->
                    <p class="measurement_reflective_graminoid_cuticles">
                      <label for="measurement_reflective_graminoid_cuticles">Measurement</label>
                      <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>">
                          <option value="">Select a measurement</option>
                      <?php

                      //TODO

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="particle_size_reflective_graminoid_cuticles">
                      <label for="particle_size_reflective_graminoid_cuticles">Particle size (micrometer)</label>
                      <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>">
                          <option value="">Select particles size</option>
                      <?php

                      //TODO

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_treatment_reflective_graminoid_cuticles">
                      <label for="method_treatment_reflective_graminoid_cuticles">Method treatment</label>
                      <select id="method_treatment_reflective_graminoid_cuticles" name="<?php echo ProxyFireMethodTreatment::ID ?>">
                          <option value="">Select a method treatment</option>
                      <?php
                      $listeProxyFireMethodTreatment = ProxyFireMethodTreatment::getAllProxyFireMethodTreatment("Reflective Graminoid cuticles");
                      foreach ($listeProxyFireMethodTreatment as $id_proxy_fire_method_treatment => $proxy_fire_method_treatment) {
                      echo '<option value = "' . $proxy_fire_method_treatment[0] . '">' . $proxy_fire_method_treatment[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_estimation_reflective_graminoid_cuticles">
                      <label for="method_estimation_reflective_graminoid_cuticles">Method estimation</label>
                      <select id="method_estimation_reflective_graminoid_cuticles" name="<?php echo ProxyFireMethodEstimation::ID ?>">
                          <option value="">Select a method estimation</option>
                      <?php
                      $listeProxyFireMethodEstimation = ProxyFireMethodEstimation::getAllProxyFireMethodEstimation("Reflective Graminoid cuticles");
                      foreach ($listeProxyFireMethodEstimation as $id_proxy_fire_method_estimation => $proxy_fire_method_estimation) {
                      echo '<option value = "' . $proxy_fire_method_estimation[0] . '">' . $proxy_fire_method_estimation[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="age_method_reflective_graminoid_cuticles">
                        <label for="age_method_reflective_graminoid_cuticles">Age method</label>
                        <?php
                        selectHTML(AgeModel::ID_AGE_MODEL_METHOD, 'addAge_method', 'AgeModelMethod', null, null);
                        ?>
                    </p>
                    <!--  -->
                    <p class="pub_reflective_graminoid_cuticles">
                        <label for="pub_reflective_graminoid_cuticles">Publication</label>
                        <?php
                        selectMultipleHTML(Publi::ID . '[]', 'addCharcoals_publi', 'Publi', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                    <p class="author_reflective_graminoid_cuticles" style="width:470px">
                        <label for="author_reflective_graminoid_cuticles">Author</label>
                        <?php
                        selectMultipleHTML(Contact::ID . '[]', 'addCharcoals_author', 'Contact', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                </div>

                <!-- ------------------------------ -->
                <!-- selection_Levoglucosan options -->
                <div id="selection_levoglucosan" style="display:none">
                    <!--  -->
                    <p class="measurement_levoglucosan">
                      <label for="measurement_levoglucosan">Measurement</label>
                      <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>">
                          <option value="">Select a measurement</option>
                      <?php

                      //TODO

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="particle_size_levoglucosan">
                      <label for="particle_size_levoglucosan">Particle size (micrometer)</label>
                      <select id="<?php echo ProxyFire::ID ?>" name="<?php echo ProxyFire::ID ?>">
                          <option value="">Select particles size</option>
                      <?php

                      //TODO

                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_treatment_levoglucosan">
                      <label for="method_treatment_levoglucosan">Method treatment</label>
                      <select id="method_treatment_levoglucosan" name="<?php echo ProxyFireMethodTreatment::ID ?>">
                          <option value="">Select a method treatment</option>
                      <?php
                      $listeProxyFireMethodTreatment = ProxyFireMethodTreatment::getAllProxyFireMethodTreatment("Levoglucosan");
                      foreach ($listeProxyFireMethodTreatment as $id_proxy_fire_method_treatment => $proxy_fire_method_treatment) {
                      echo '<option value = "' . $proxy_fire_method_treatment[0] . '">' . $proxy_fire_method_treatment[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="method_estimation_levoglucosan">
                      <label for="method_estimation_levoglucosan">Method estimation</label>
                      <select id="method_estimation_levoglucosan" name="<?php echo ProxyFireMethodEstimation::ID ?>">
                          <option value="">Select a method estimation</option>
                      <?php
                      $listeProxyFireMethodEstimation = ProxyFireMethodEstimation::getAllProxyFireMethodEstimation("Levoglucosan");
                      foreach ($listeProxyFireMethodEstimation as $id_proxy_fire_method_estimation => $proxy_fire_method_estimation) {
                      echo '<option value = "' . $proxy_fire_method_estimation[0] . '">' . $proxy_fire_method_estimation[0] . '</option>';
                      }
                      ?>
                      </select>
                    </p>
                    <!--  -->
                    <p class="age_method_levoglucosan">
                        <label for="age_method_levoglucosan">Age method</label>
                        <?php
                        selectHTML(AgeModel::ID_AGE_MODEL_METHOD, 'addAge_method', 'AgeModelMethod', null, null);
                        ?>
                    </p>
                    <!--  -->
                    <p class="pub_levoglucosan">
                        <label for="pub_levoglucosan">Publication</label>
                        <?php
                        selectMultipleHTML(Publi::ID . '[]', 'addCharcoals_publi', 'Publi', null, null, 5, true);
                        ?>
                    </p>
                    <!--  -->
                    <p class="author_levoglucosan" style="width:470px">
                        <label for="author_levoglucosan">Author</label>
                        <?php
                        selectMultipleHTML(Contact::ID . '[]', 'addCharcoals_author', 'Contact', null, null, 5, true);
                        ?>
                    </p>
                </div>
                <!-- ------------------------------ -->
              </fieldset>
            <!-- </p> -->
            <!-- ============================== -->
            <!-- ============================== -->

            <p>
                <a role="btn" class="btn btn-default" id="btnFile" href=javascript:getFileTamplate()>Download template for data integration</a>
            </p>
        </fieldset>
      </form>
        <!-- Cadre pour la description de la série de samples!-->
      <form action="" method="post" enctype='multipart/form-data' class="form_paleofire" name="formAddProxyFireDataImport" id="formAddProxyFireDataImport">
        <fieldset class="cadre">
            <legend>Import samples data</legend>

            <p>
                <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo FileSize ?>" aria-describedby="fileHelp"/>
                <input type="file" name="fileToUpload" id="fileToUpload" />
                <small id="fileHelp" class="form-text text-muted">File size must be less than <?php echo FileSize/1000000; ?> M<br/>
                File type must be <?php echo implode($extension, ',');?></small>
            </p>


            <!-- Boutons du formulaire !-->
            <p class="submit">
                <button type='submit' name='submitAdd' value='Add' class="btn btn-default">Import data</button>
            </p>
      </fieldset>
    </form>
    <fieldset class="cadre">
    </fieldset>
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
        if ($values != null
            && $values->site != null
            && $values->core != null
            && $values->datasource != null
            && $values->proxy_fire != null
            && $values->proxy_fire_measurement != null
            && $values->proxy_fire_measurement_unit != null
            && $values->proxy_fire_particle_size_min != null
            && $values->proxy_fire_particle_size_max != null
            && $values->proxy_fire_method_treatment != null
            && $values->proxy_fire_method_estimation != null ){

            // récupération de l'id du core et du site et vérification de leur existence
            $site = $values->site;
            $core = $values->core;
            if (!Core::coreEtSiteExist($core, $site))
                $errors[] = "[ Cell A2 ] (1) This cell has configuration data automatically generated, should not be modified (error : Core or site undefined)";

            // récupération du code datasource si spécifié et vérification de son existance
            if ($values->datasource){
                $datasource = $values->datasource;
                //$tab = DataSource::getAllIdName();
                $tab = DataSource::getAllDataSourcesNameById($datasource);

                if ($tab[$datasource] == null)
                    $errors[] = "[ Cell A2 ] (2) This cell has configuration data automatically generated, should not be modified (error : Data source undefined)";
            } else {
                $datasource = NULL;
            }

            $agemethod = $values->proxy_fire_age_method;
            $age_model_method = AgeModelMethod::getAgeModelMethodByID($agemethod);
            if ($age_model_method == null)
                $errors[] = "[ Cell A2 ] (5) This cell has configuration data automatically generated, should not be modified (error : Age method undefined)";

            // récupération du code proxy_fire_particle_size_min si spécifié et vérification de son existance
            if ($values->proxy_fire_particle_size_min){
              $proxy_fire_particle_size_min = $values->proxy_fire_particle_size_min;
            } else {
                $proxy_fire_particle_size_min = NULL;
            }

            // récupération du code proxy_fire_particle_size_max si spécifié et vérification de son existance
            if ($values->proxy_fire_particle_size_min){
              $proxy_fire_particle_size_min = $values->proxy_fire_particle_size_min;
            } else {
                $proxy_fire_particle_size_min = NULL;
            }

            // si il y a des erreurs dans la ligne de configuration on ne parse pas le fichier
            // tentative d'intégration malveillante ou autre donc pas d'intégration
            if (empty($errors)) {
                // on récupère les données de la carotte pour pouvoir récupérer le nom par la suite
                //$obj_core = Core::getCoreDataFromId($core);
                $obj_core = Core::getCoreDataFromID($core);


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

                        // itération sur les lignes pour récupérer les données des proxy fire
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

                                // --------------TEST JULIEN ----------------- //
                                $proxyFireData = new ProxyFireData();
                                $proxyFireData->setNameValue($sample->getName());
                                $proxyFireData->_datasource_id = $datasource;
                                $proxyFireData->_proxy_fire_method_treatment_id = ProxyFireMethodTreatment::getProxyFireMethodTreatmentID($values->proxy_fire_method_treatment);
                                $proxyFireData->_proxy_fire_method_estimation_id = ProxyFireMethodEstimation::getProxyFireMethodEstimationID($values->proxy_fire_method_estimation);
                                $proxyFireData->_proxy_fire_particle_size_min = $values->proxy_fire_particle_size_min;
                                $proxyFireData->_proxy_fire_particle_size_max = $values->proxy_fire_particle_size_max;
                                $proxyFireData->_proxy_fire_id = ProxyFire::getProxyFireID($values->proxy_fire);

                                $size_value = $sheet->getCell(COL_SIZE . $id_row)->getValue();

                                if(!$size_value){
                                  $proxyFireData->_proxy_fire_size_value = "NO DATA";
                                }
                                else{
                                  if (is_numeric($size_value)){
                                    $proxyFireData->_proxy_fire_size_value = $size_value;
                                  }
                                  else{
                                    $errors[] = "[ Cell " . COL_SIZE . $id_row . " ] SIZE must be numeric or null";
                                  }
                                }

                                if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
                                    $proxyFireData->_status_id = 1; // la donnée est validée
                                } else {
                                    $proxyFireData->_status_id = 0; //la donnée apparaîtra dans la liste données à valider
                                }

                                $proxyFireData->_contact_id = $id_contributeur;
                                $proxyFireData->_database_id = $version_database;

                                // on récupère la quantité de charbon et on la teste
                                $qtVal = $sheet->getCell(COL_Q . $id_row)->getValue();
                                if ($qtVal === null)
                                    $errors[] = "[ Cell " . COL_Q . $id_row . " ] QUANTITY must be filled";
                                if ($qtVal < 0)
                                    $errors[] = "[ Cell " . COL_Q . $id_row . " ] QUANTITY must be a positive number";
                                $proxyFireData->_proxy_fire_measurement_unit_id = ProxyFireMeasurementUnit::getProxyFireMeasurementUnitID($values->proxy_fire_measurement_unit);
                                $proxyFireData->addProxyFireDataQuantity($qtVal, $proxyFireData->_proxy_fire_measurement_unit_id);

                                $proxyFireData->_list_authors = $values->proxy_fire_authors;
                                $proxyFireData->_list_publications = $values->proxy_fire_publis;

                                // si il n'y a pas d'erreurs on tente de créer le sample
                                if (empty($errors)) {

                                    // on indique à la fonction de ne pas gérer les transactions
                                    $errors = $sample->create(false);
                                    if (empty($errors)) {
                                        $proxyFireData->_sample_id = $sample->getIdValue();
                                        // la fct create créée pour l'intégration ne convient pas dans notre cas
                                        // on indique à la fonction de ne pas gérer les transactions
                                        $errors = $proxyFireData->save();
                                    }

                                    if (empty($insert_errors)) {

                                        // puis on insére les nouveaux enregistrements de la table r_site_is_referenced
                                        if ($proxyFireData->_list_publications != null){
                                            foreach ($proxyFireData->_list_publications as $publi_id) {
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
                                    writeChangeLog(" add_proxy_fire", "site " . $site." core ".$core);
                                } else {
                                    rollBack();
                                }
                                //---------------------------------------------//

                            }
                            if (count($errors) > 20){
                                throw new Exception("To many errors in proxy fire file", 1);
                            }
                        }
                    }
                } else {
                    $errors[] = "No core for this proxy fire!";
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
        var unit ='#type_measurement_' +$('#ID_PROXY_FIRE_MEASUREMENT').val().toLowerCase();
        var method_estimation = '#method_estimation_' + $('#ID_PROXY_FIRE').val().toLowerCase().replace(/\s+/g,"_");
        var method_treatment = '#method_treatment_' + $('#ID_PROXY_FIRE').val().toLowerCase().replace(/\s+/g,"_");
    if ($('#ID_SITE').val() == null || $('#ID_SITE').val() == ""
                || $('#ID_CORE').val() == null || $('#ID_CORE').val() == ""
                || $('#ID_PROXY_FIRE').val() == null || $("#ID_PROXY_FIRE").val() == "" || $("#ID_PROXY_FIRE").val() == "NULL"
                || $('#ID_PROXY_FIRE_MEASUREMENT').val() == null || $("#ID_PROXY_FIRE_MEASUREMENT").val() == "" || $("#ID_PROXY_FIRE_MEASUREMENT").val() == "NULL"
                || escape($(unit).val().toLowerCase()) == null || escape($(unit).val().toLowerCase()) == "" || escape($(unit).val().toLowerCase()) == "NULL"
                || $('#ID_PROXY_FIRE_PARTICLE_SIZE_MIN').val() == null || $("#ID_PROXY_FIRE_PARTICLE_SIZE_MIN").val() == "" || $("#ID_PROXY_FIRE_PARTICLE_SIZE_MIN").val() == "NULL"
                || $('#ID_PROXY_FIRE_PARTICLE_SIZE_MAX').val() == null || $("#ID_PROXY_FIRE_PARTICLE_SIZE_MAX").val() == "" || $("#ID_PROXY_FIRE_PARTICLE_SIZE_MAX").val() == "NULL"
                || escape($(method_estimation).val().toLowerCase()) == null || escape($(method_estimation).val().toLowerCase()) == "" || escape($(method_estimation).val().toLowerCase()) == "NULL"
                || escape($(method_treatment).val().toLowerCase()) == null || escape($(method_treatment).val().toLowerCase()) == "" || escape($(method_treatment).val().toLowerCase()) == "NULL"){
            $("#divError div").html("Site name, core name, proxy fire and proxy fire options must be selected");
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
        var url = "Pages/ADA/add_proxy_fire_create_file.php";
        var unit = '#type_measurement_' + $('#ID_PROXY_FIRE_MEASUREMENT').val().toLowerCase();
        var method_estimation = '#method_estimation_' + $('#ID_PROXY_FIRE').val().toLowerCase().replace(/\s+/g,"_");
        var method_treatment = '#method_treatment_' + $('#ID_PROXY_FIRE').val().toLowerCase().replace(/\s+/g,"_");
        url += "?is=" + $('#ID_SITE').val();
        url += "&ic=" + $('#ID_CORE').val();
        url += "&ds=" + $('#addCharcoals_data_source').val();
        url += "&pf=" + $('#ID_PROXY_FIRE').val();
        url += "&pfm=" + $('#ID_PROXY_FIRE_MEASUREMENT').val();
        url += "&pfpsmin=" + $('#ID_PROXY_FIRE_PARTICLE_SIZE_MIN').val();
        url += "&pfpsmax=" + $('#ID_PROXY_FIRE_PARTICLE_SIZE_MAX').val();
        url += "&pfmu=" + escape($(unit).val().toLowerCase());
        url += "&pfmt=" + escape($(method_treatment).val());
        url += "&pfme=" + escape($(method_estimation).val());
        url += "&a=" + $('#ID_PROXY_FIRE_AGE_METHOD').val();
        url += "&pu=" + $('#ID_PROXY_FIRE_PUBLI').val();
        url += "&au=" + $('#ID_PROXY_FIRE_AUTHOR').val();


        window.open(url, '_self');
    }


    function AffProxyFireSelection() {
      var x = document.getElementById("<?php echo ProxyFire::ID ?>").value;
      document.getElementById("legend_fire_proxy_options").innerHTML = x + " options";
      document.getElementById('selection_charcoal').style.display = (x == "Charcoal")? "inline" : "none";
      document.getElementById('selection_burnt_phytolith').style.display = (x == "Burnt Phytolith")? "inline" : "none";
      document.getElementById('selection_reflective_graminoid_cuticles').style.display = (x == "Reflective Graminoid cuticles")? "inline" : "none";
      document.getElementById('selection_levoglucosan').style.display = (x == "Levoglucosan")? "inline" : "none";
      document.getElementById('fieldset_fire_proxy_options').style.display = (x == "Charcoal"|| x == "Burnt Phytolith" || x == "Reflective Graminoid cuticles" || x == "Levoglucosan" )? "inline" : "none";
      document.getElementById('legend_fire_proxy_options').style.display = (x == "Charcoal" || x == "Burnt Phytolith" || x == "Reflective Graminoid cuticles" || x == "Levoglucosan" )? "inline" : "none";

}

    function GetMeasurement() {
      var y = document.getElementById("<?php echo ProxyFireMeasurement::ID ?>").value;

      document.getElementById('type_measurement_conc').style.display = (y == "CONC")? "inline" : "none";
      document.getElementById('type_measurement_influx').style.display = (y == "INFL")? "inline" : "none";
      document.getElementById('type_measurement_copo').style.display = (y == "COPO")? "inline" : "none";
      document.getElementById('type_measurement_init').style.display = (y != "")? "none" : "inline";
}


</script>
