<?php
/* 
 * fichier Pages/ADA/add_date_comment.php 
 * 
 */ 
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Site.php';
    require_once './Models/DateInfo.php';
    require_once './Models/DateType.php';
    require_once './Models/MatDated.php';
    require_once './Library/PaleofireHtmlTools.php';
  
    
    if (!isset($success_form)) {
        $success_form = "";
    }
    if (!isset($error_form)) {
        $error_form = array();
    }

    if (!isset($new_site)) {
        $new_site = new Site();
    }

    if (!isset($new_obj)) {
        //$new_obj = new Core();
        $new_obj = null;
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
        $error_form = array();
        $success_form = false;
        
        $new_obj = new DateInfo();
        $errors = null;
        
        //Core affectation avec les données postées dans le formulaire
        if (testPost(Core::NAME)) {
            $new_obj->setNameValue(utf8_decode($_POST[Core::NAME]));
        }
        
        if (testPost(Site::ID)) {
            $new_site = new Site();
            $new_site->setIdValue($_POST[Site::ID]);
            $new_obj->setSite($new_site);
        } else {
            $errors[] = "A site must be selected";
        }

        if (testPost(Core::LATITUDE)){
            if (is_numeric($_POST[Core::LATITUDE])) {
                $new_obj->_latitude = $_POST[Core::LATITUDE];
            } else {
                $errors[] = "Latitude must be numeric";
            }
        } // la latitude peut être null dc pas d'erreur

        if (testPost(Core::LONGITUDE)){
            if (is_numeric($_POST[Core::LONGITUDE])) {
                $new_obj->_longitude = $_POST[Core::LONGITUDE];
            } else {
                $errors[] = "Longitude must be numeric";
            }
        } // la longitude peut être null dc pas d'erreur

        if (testPost(Core::ELEVATION)){
            if (is_numeric($_POST[Core::ELEVATION])) {
                $new_obj->_elevation = $_POST[Core::ELEVATION];
            } else {
                $errors[] = "Elevation must be numeric";
            }
        } // Elevation peut être null dc pas d'erreur

        if (testPost(Core::WATER_DEPTH)) {
            if (is_numeric($_POST[Core::WATER_DEPTH])) {
                $new_obj->_water_depth_value = $_POST[Core::WATER_DEPTH];
            } else {
                $errors[] = "Water Depth must be numeric";
            }
        } // WATER_DEPTH may be null => no error

        if (testPost(Core::ID_CORE_TYPE)) {
            $new_obj->_core_type_id = $_POST[Core::ID_CORE_TYPE];
        }
        
        if (testPost(Core::CORING_DATE)) {
            $new_obj->_coring_date = new DateTime("01/".$_POST[Core::CORING_DATE]);
        }

        if (testPost(Core::ID_DEPO_CONTEXT)) {
            $new_obj->_depo_context_id = $_POST[Core::ID_DEPO_CONTEXT];
        }
        
        if (empty($errors)) {
      
            // on tente d'enregistrer la carotte
            $errors = $new_obj->save();
        }
        
        if (empty($errors)){
            
            ?>
            <p class="submit">
                
                <span class = "allIsOk">Successful record.</span><br /><br/>            
                <a href="index.php?p=ADA/add_sample&gcd_menu=ADA">Add new samples</a>
                <a href="index.php?p=ADA/add_core&gcd_menu=ADA">Add another core</a>
            </p>
            
                        <?php
        } else {
            echo "Submission impossible";
            foreach ($errors as $error) {
                echo '<div class="msg error">'.$error ."</div>";
            }
        }
        
    }
    
    if (!isset($_POST['submitAdd']) || !empty($errors)) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
        <h1>Add a new date comment</h1>     
            <!-- Formulaire de saisie d'une date info-->
            <form action="" class="form_paleofire" name="formAddSite" method="post" id="formAddSite" >
                <!-- Cadre pour les métadonnées d'une date info !-->
                <fieldset class="cadre">
                    <legend>Description</legend>
                    <p class="site_name">
                        <label for="name_site">Site name</label>
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
                        <label for="name_core">Core name</label>
                        <select id="<?php echo CORE::ID ?>" name="<?php echo CORE::ID ?>">
                        </select>
                    </p>
                    <p class="sample_name">
                        <label for="sample_name">Sample name</label>
                        <select id="<?php echo SAMPLE::ID ?>" name="<?php echo SAMPLE::ID ?>">
                        </select>
                    </p>
                    <p class="dateinfo_name">
                        <label for="sample_name">Date info</label>
                        <select id="<?php echo DateInfo::ID ?>" name="<?php echo DateInfo::ID ?>">
                        </select>
                    </p>
                </fieldset>

                <!-- Cadre pour la date info !-->
                <fieldset class="cadre">
                    <legend>Date info</legend>
                    <p class="core_name">
                        <label for="date_lab_number">Lab number</label>
                        <input type="text" name="<?php echo DateInfo::NAME; ?>" id="name_dateinfo" value="<?php if (isset($new_obj)) echo $new_obj->getName(); ?>" maxlength="50"/>
                    </p>
                    <p>
                        <label for="addDate_type">Date type</label>
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
                </fieldset>

                <!-- Boutons du formulaire !-->
                <p class="submit">
                    <input type = 'submit' name = 'submitAdd' value = 'Add' />
                    <input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />
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
        echo 'var tabCore = '. json_encode($listeSiteEtCore).';';
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
        var url = "/Paleofire/Pages/ADA/add_date_info_ajax.php";
        url += "?action=samples&core=" + $('#ID_CORE').val();
        var selectSample = $('#ID_SAMPLE').empty();
        $.getJSON(url, function(result){
            $.each(result, function(i, field){
                selectSample.append('<option value="'+ i +'">'+field+'</option>');
            });
        });
    });
</script>
<?php
    