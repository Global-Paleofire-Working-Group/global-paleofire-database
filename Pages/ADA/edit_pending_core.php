<?php
/* 
 * fichier Pages/ADA/edit_pending_core.php 
 * Auteur : XLI  
 */ 

if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Site.php';
    require_once './Models/Status.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    //require_once './Pages/ADA/del_core2.php';

    if (!isset($success_form)) {
        $success_form = "";
    }

    $id = null;
    if (!isset($_GET['id'])) {
        $new_core = new Core();
        $new_site = new Site();
    } else {
        // cas d'une édition, on récupère le site en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $new_core = Core::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        $new_site = $new_core->getSite();
        // todo // si on ne récupère pas de core rediriger
    }
    
    /*if (!isset($new_site)) {
        $new_site = new Site();
    }*/

    $region_id = null;
    if (isset($_GET['region_id']) && is_numeric($_GET['region_id'])) {
        $region_id = $_GET['region_id'];
        $new_site->_site_region_id = $region_id;
    }

    if (isset($_POST['deleteAdd'])) {        
       del_core();         
    }
    
    if (isset($_POST['submitAdd'])) {
        $success_form = false;
        $errors = null;
        
        //Core affectation avec les données postées dans le formulaire
        if (testPost(Core::NAME)) {
            $new_core->setNameValue(utf8_decode($_POST[Core::NAME]));
        }
        
        if (testPost(Site::ID)) {
            $new_site->setIdValue($_POST[Site::ID]);
            $new_core->setSite($new_site);
        } else {
            $errors[] = "A site must be selected";
        }

        if (testPost(Core::LATITUDE)){
            if (is_numeric($_POST[Core::LATITUDE])) {
                $new_core->_latitude = $_POST[Core::LATITUDE];
            } else {
                $errors[] = "Latitude must be numeric";
            }
        } // la latitude peut être null dc pas d'erreur

        if (testPost(Core::LONGITUDE)){
            if (is_numeric($_POST[Core::LONGITUDE])) {
                $new_core->_longitude = $_POST[Core::LONGITUDE];
            } else {
                $errors[] = "Longitude must be numeric";
            }
        } // la longitude peut être null dc pas d'erreur

        if (testPost(Core::ELEVATION)){
            if (is_numeric($_POST[Core::ELEVATION])) {
                $new_core->_elevation = $_POST[Core::ELEVATION];
            } else {
                $errors[] = "Elevation must be numeric";
            }
        } // Elevation peut être null dc pas d'erreur

        if (testPost(Core::WATER_DEPTH)) {
            if (is_numeric($_POST[Core::WATER_DEPTH])) {
                $new_core->_water_depth_value = $_POST[Core::WATER_DEPTH];
            } else {
                $errors[] = "Water Depth must be numeric";
            }
        } // WATER_DEPTH may be null => no error

        if (testPost(Core::ID_CORE_TYPE)) {
            $new_core->_core_type_id = $_POST[Core::ID_CORE_TYPE];
        }
        
        if (testPost(Core::CORING_DATE)) {
            $new_core->_coring_date = new DateTime("01/".$_POST[Core::CORING_DATE]);
        }

        if (testPost(Core::ID_DEPO_CONTEXT)) {
            $new_core->_depo_context_id = $_POST[Core::ID_DEPO_CONTEXT];
        }
        
        if (testPost(Core::ID_STATUS)) {
            if (is_numeric($_POST[Core::ID_STATUS])){
                $new_site->setStatusId($_POST[Core::ID_STATUS]);
            } else {
                $errors[] = "Select a status";
            }
        }
        
        if (empty($errors)) {
                
            // on tente d'enregistrer la carotte
            $errors = $new_core->save();
        }
        
        ?>
        <!--    
            <p class="submit">
                <span class = "allIsOk">Successful record.</span><br /><br/>            
                <a href="index.php?p=ADA/add_sample&gcd_menu=ADA">Add new samples</a>
                <a href="index.php?p=ADA/add_core&gcd_menu=ADA">Add another core</a>
            </p>
        -->
        <?php
        
        if (empty($errors)){

                    
            echo '<div class="alert alert-success"><strong>Success !</strong></div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        echo '<div class="btn-toolbar" role="toolbar" style="float:left">
           <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_core&gcd_menu=ADA">
               <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
               Go back to core list
           </a>
        </div>';
    }
    // on créer une div d'affichage d'erreur caché que l'on pourra utiliser en javascript
    echo '<div class="alert alert-danger" id="divErreur" hidden><strong>Error!</strong><div></div></div>';
    
    if ((((!isset($_POST['submitAdd'])) || !empty($errors)))&&(!isset($_POST['deleteAdd']))) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>
        <!-- Formulaire de saisie d'une carotte-->
        <?php 
            if ($id != null) {
               echo '<h1>Editing core : '.$new_core->getName().'</h1>';
            } else {
               echo '<h1>Add a new core</h1>';
            }
        ?>
     
        <form action="" class="form_paleofire" name="formAddCore" method="post" id="formAddCore" >
            <!-- Cadre pour la sélection du site !-->
            <fieldset class="cadre">
                <legend>Site</legend>
                <p class="site_name">
                    <label for="name_site">Site name*</label>
                    <?php
                    if (isset($_GET['id']) && $new_site->getIdValue() != null) {
                        selectHTML(SITE::ID, SITE::ID, "SITE", intval($new_site->getIdValue()));
                    } else {
                        selectHTML(SITE::ID, SITE::ID, "SITE", "");
                    }
                    ?>
                </p>
            </fieldset>

            <!-- Cadre pour la carotte!-->
            <fieldset class="cadre">
                <legend>Core</legend>
                <p class="core_name">
                    <label for="name_core">Core name*</label>
                    <input type="text" name="<?php echo Core::NAME; ?>" id="name_core" value="<?php if (isset($new_core)) echo $new_core->getName(); ?>" maxlength="50"/>
                </p>
                <p>
                    <label>Specify location</label>
                    <div id="map_addCore"></div>
                </p>
                <p class="core_lat" >
                    <label for="core_lat">Latitude*</label>
                    <input type="text" name="<?php echo Core::LATITUDE; ?>" id="core_lat" value="<?php if (isset($new_core)) echo $new_core->_latitude; ?>" />
                </p>
                <p class="core_long" >
                    <label for="core_long">Longitude*</label>
                    <input type="text" name="<?php echo Core::LONGITUDE; ?>" id="core_long" value="<?php if (isset($new_core)) echo $new_core->_longitude; ?>"/>
                </p>
                <p class="elevation">
                    <label for="name_core">Elevation (m)</label>
                    <input type="text" name="<?php echo Core::ELEVATION; ?>" id="elevation" value="<?php if (isset($new_core)) echo $new_core->_elevation; ?>"/>
                </p>
                <p class="water_depth">
                    <label for="name_core">Water Depth (m)</label>
                    <input type="text" name="<?php echo Core::WATER_DEPTH; ?>" id="water_depth" value="<?php if (isset($new_core)) echo $new_core->_water_depth_value; ?>"/>
                </p>
                <p>
                    <label>Coring date (MM/YYYY)</label>
                    <input type="text" name="<?php echo Core::CORING_DATE; ?>" pattern="\d{1,2}/\d{4}" title="MM/YYYY" value="<?php if (isset($new_core) && $new_core->_coring_date != null) { $d = new DateTime($new_core->_coring_date); echo date_format($d,'m/Y'); } ?>">
                </p>
                <p>
                    <label for="addCore_type">Core type</label>
                    <?php
                    $selectedId = (isset($new_core))?$new_core->_core_type_id:null;
                    selectHTML(Core::ID_CORE_TYPE, 'addCore_type', 'CoreType', intval($selectedId));
                    ?>
                </p>
                <p>
                    <label for="addCore_depoContext">Depositional context</label>
                    <?php
                    $selectedId = (isset($new_core))?$new_core->_depo_context_id:null;
                    selectHTML(Core::ID_DEPO_CONTEXT, 'addCore_depoContext', 'DepoContext', intval($selectedId));
                    ?>
                </p>
                <p>
                    <label for="addCore_status">Status</label>
                    <?php
                    $selectedId = (isset($new_site))?$new_core->getStatusId():null;
                    selectHTML(Core::ID_STATUS, 'addCore_status', 'Status', intval($selectedId));
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
<!-- SCRIPT DE GESTION DE LA CARTE !-->
<script>
    //Variable pour le geocoder de Google Maps
    var geocoder;
    // Variable pour la carte
    var map_addCore;
    // coordonnées du site sélectionné
    var latlngSelectedSite;
    // latitude longitude d'origine
    var latlngOrigine = new google.maps.LatLng(47.245724, 5.984663);
    // variable pour le marqueur
    var marker;
    //Function d'initialisation de la carte à l'affichage
    function initialize() {
        var latlng = new google.maps.LatLng(47.245724, 5.984663);
        var mapOptions = {
            zoom: 3,
            //center: latlng,
            center: latlng,
            mapTypeId: 'roadmap'
        }
        map_addCore = new google.maps.Map(document.getElementById("map_addCore"), mapOptions);
        google.maps.event.addListener(map_addCore, 'click', function(mouseEvent){
            // suppression du marker déjà sur la carte
            if (marker != null) marker.setMap(null);
            // récupération des nouvelles coordonnées
            var latlng = mouseEvent.latLng;
            $("#core_lat").val(latlng.lat());
            $("#core_long").val(latlng.lng());
            // affichage d'un nouveau marqueur
            marker = new google.maps.Marker({
                position: latlng,
                map: map_addCore
            });
            verificationPays();
        });
        
        <?php
            if ($id != null){
        ?>
        if ($("#core_lat").val() != "" && $("#core_long").val() != ""){
            var latlng = new google.maps.LatLng($("#core_lat").val(), $("#core_long").val());
            // affichage d'un nouveau marqueur
            marker = new google.maps.Marker({
                position: latlng,
                map: map_addCore
            });
            map_addCore.setCenter(latlng);
        }
        <?php 
            // if ($id != null){
            }
        ?>
    }

    //chargement de la carte à l'initialisation de la page
    google.maps.event.addDomListener(window, 'load', initialize);
    geocoder = new google.maps.Geocoder();
    
    var tabSiteCountry = jQuery.parseJSON('<?php echo json_encode(SITE::getAllSitesWithCountry());?>');
    $("#ID_SITE").change(function(){
        // on supprime le marqueur de la carte et on vide les coordonnées
        $("#core_long").val('');
        $("#core_lat").val('');
        if (marker != null) marker.setMap(null);
        // on tente de récupérer la latitude longitude du site selectionné
        var site = $(this).val();
        var country = tabSiteCountry[site];
        //geocoder = new google.maps.Geocoder();
        geocoder.geocode({'address': country}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var ne;
                var sw;
                if (results != null){
                    if (results[0].geometry.viewport != null){
                        ne = results[0].geometry.viewport.getNorthEast();
                        sw = results[0].geometry.viewport.getSouthWest();
                    } else {
                        ne = results[0].geometry.bounds.getNorthEast();
                        sw = results[0].geometry.bounds.getSouthWest();
                    }
                    var latlngBounds = new google.maps.LatLngBounds(sw, ne);
                    map_addCore.setCenter(latlngBounds.getCenter());
                    map_addCore.panToBounds(latlngBounds);
                    map_addCore.fitBounds(latlngBounds);
                } else {
                    map_addCore.setCenter(latlngOrigine);
                    map_addCore.setZoom(1);
                }
            } else {
                map_addCore.setCenter(latlngOrigine);
                map_addCore.setZoom(1);
            }
        });
    });
    
    $('#core_lat').change(function(){
        // suppression du marker déjà sur la carte
        if (marker != null) marker.setMap(null);
        // récupération des nouvelles coordonnées
        var latlng = new google.maps.LatLng($(this).val(), $("#core_long").val());
        // affichage d'un nouveau marqueur
        marker = new google.maps.Marker({
            position: latlng,
            map: map_addCore
        });
        map_addCore.setCenter(latlng);
        verificationPays();
    });
    
    $('#core_long').change(function(){
        // suppression du marker déjà sur la carte
        if (marker != null) marker.setMap(null);
        // récupération des nouvelles coordonnées
        var latlng = new google.maps.LatLng($("#core_lat").val(), $(this).val());
        // affichage d'un nouveau marqueur
        marker = new google.maps.Marker({
            position: latlng,
            map: map_addCore
        });
        map_addCore.setCenter(latlng);
        verificationPays();
    });
    
    var retourVerificationPays = true;
    // fonction qui vérifie que le site sélectionnées et les coordonnées sont dans le même pays
    function verificationPays(){
        $("#divErreur").hide();
        // on récupère la position du marqueur
        var latlng = marker.getPosition();
        retourVerificationPays = true;
        //function du geocoder qui recupere le pays
        geocoder.geocode({'latLng': latlng}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
                var code_country = "";
                if (results[1]) {
                    for (var i = 0; i < results[1].address_components.length; i++)
                    {
                        var addr = results[1].address_components[i];
                        if (addr.types[0] == 'country') code_country = addr.short_name;
                    }
                    if (code_country !=  tabSiteCountry[$('#ID_SITE').val()]){
                        $("#divErreur div").html('Latitude and longitude must be in the same country as the site.');
                        $("#divErreur").show();
                        retourVerificationPays = false;
                    }
                } else {
                    $("#divErreur div").html('An error occur while checking if latitude and longitude were in the same country as the site.');
                    $("#divErreur").show();
                    retourVerificationPays = false;
                }
            } else {
                $("#divErreur div").html('An error occur while checking if latitude and longitude were in the same country as the site.');
                $("#divErreur").show();
                retourVerificationPays = false;
            }
        });
    }
    
    // avant la soumission du formulaire on vérfie que le site et les coordonnées sont correctes
    $('#formAddCore').submit(function(event){
       if (retourVerificationPays === false) {event.preventDefault();}       
    });
    
</script>
<?php    


