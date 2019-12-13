<?php
/* 
 * fichier Pages/ADA/add_core.php 
 * 
 */ 
function add_edit_core($Operation) {
if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Library/data_securisation.php';
    
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
        else
            $new_core->_latitude = NULL;

        if (testPost(Core::LONGITUDE)){
            if (is_numeric($_POST[Core::LONGITUDE])) {
                $new_core->_longitude = $_POST[Core::LONGITUDE];
            } else {
                $errors[] = "Longitude must be numeric";
            }
        } // la longitude peut être null dc pas d'erreur
        else
            $new_core->_longitude = NULL;
        
        if (isset($_POST[Core::ELEVATION])){
            if ($_POST[Core::ELEVATION] != ""){
                if (is_numeric($_POST[Core::ELEVATION])) {
                    $new_core->_elevation = $_POST[Core::ELEVATION];
                } else {
                    $errors[] = "Elevation must be numeric";
                }
            } else {
                $new_core->_elevation = NULL;
            }
        } // Elevation peut être null dc pas d'erreur
        
        if (isset($_POST[Core::WATER_DEPTH])) {
            if ($_POST[Core::WATER_DEPTH] != ""){
                if (is_numeric($_POST[Core::WATER_DEPTH])) {
                    $new_core->_water_depth_value = $_POST[Core::WATER_DEPTH];
                } else {
                    $errors[] = "Water Depth must be numeric";
                }
            } else {
                $new_core->_water_depth_value = NULL;
            }
        } // WATER_DEPTH may be null => no error

        if (isset($_POST[Core::ID_CORE_TYPE])) {
            $new_core->_core_type_id = $_POST[Core::ID_CORE_TYPE];
        } else $new_core->_core_type_id = NULL;
        
        if (isset($_POST[Core::CORING_DATE])) {
            $str_date = $_POST[Core::CORING_DATE];
            if (!empty($str_date))
                $new_core->_coring_date = new DateTime("01/".$_POST[Core::CORING_DATE]);
            else $new_core->_coring_date = NULL;
        }

        if (isset($_POST[Core::ID_DEPO_CONTEXT])) {
            $new_core->_depo_context_id = $_POST[Core::ID_DEPO_CONTEXT];
        } else $new_core->_depo_context_id = NULL;
        
        //si c'est un contributeur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
        //si c'est un administrateur qui entre la données, cette dernière sera automatiquemenbt validée
       // if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
            $new_core->setStatusId(1); // la donnée est validée
       // }
       // else {
         //   $new_core->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
        //} 
            
        if (empty($errors)) {
            // on tente d'enregistrer la carotte
            $errors = $new_core->save($Operation);
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

                    
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        if (isset($_SESSION['gcd_user_role']) && 
                            (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)){
            $redirection="CDA/core_view_list&gcd_menu=CDA";
            echo '<div class="btn-toolbar" role="toolbar" style="float:left">
               <a role="button" class="btn btn-default btn-xs" href="index.php?p='.$redirection.'">
                   <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                   Go back to core view
               </a>
           </div>';            
         }else {
             // todo voir ce que l'on propose à un contributeur
            //$redirection="CDA/index";
         }

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
            </fieldset>

            <!-- Boutons du formulaire !-->
            <p class="submit">
                <?php
                    if ($id == null){
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Add' />";
                    } else {
                        echo "<input type = 'submit' name = 'submitAdd' value = 'Save' />";
                    }
                    
                    if (isset($_SESSION['gcd_user_role']) && 
                            (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)){
                       $redirection="CDA/core_view_list&gcd_menu=CDA";
                    }else {
                       $redirection="CDA/index";
                    }
                ?>

                <input type = 'button' name = 'cancelAdd' onclick="redirection('index.php?p=<?php echo $redirection; ?>')" value = 'Cancel' />
            </p> 
        </form>
            <?php
        } 
    }
    

?>
<!-- SCRIPT DE GESTION DE LA CARTE !-->
	<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
	        integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
	        crossorigin=""></script>
<script>
	// =========================== Leaflet Map initialisation =======================================
	var mymap = L.map('map_addCore', {attributionControl: false}).setView([47.245724, 5.984663], 2)

	var osmUrl='http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
	var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors. Tiles courtesy of HOT';

	L.tileLayer(osmUrl, {
		attribution: osmAttrib,
		maxZoom: 18,
		id: 'osm',
	}).addTo(mymap);

	// =========================== Event on click  =======================================
	let marker;
	function onMapClick(e) {
		if (marker != null) {
			mymap.removeLayer(marker);
		}
		marker = new L.Marker(e.latlng);
		mymap.addLayer(marker);
		$("#core_lat").val(e.latlng.lat);
		$("#core_long").val(e.latlng.lng);
		verificationPays();
	}
	mymap.on('click', onMapClick);

	// ============================ Zoom to country on site change  ================================
    var tabSiteCountry = jQuery.parseJSON('<?php echo json_encode(SITE::getAllSitesWithCountry());?>');
    $("#ID_SITE").change(function(){
        // Marker deleted and coordinates emptied
        $("#core_long").val('');
        $("#core_lat").val('');
        if (marker != null) mymap.removeLayer(marker);

	    var site = $(this).val();
	    var country = tabSiteCountry[site];

        // On site selection, country center coordinates stored, and map centered on these coordinates
	    let apiurl = "https://restcountries.eu/rest/v2/alpha/" + country;
	    let request = new XMLHttpRequest();
	    request.open('GET', apiurl, true);
	    request.onload = function() {
		    let data = JSON.parse(this.response);
		    if (request.status >= 200 && request.status < 400) {
			    let lat = data['latlng'][0];
			    let lng = data['latlng'][1];
			    mymap.panTo(new L.LatLng(lat, lng));
		    }
		    else {
			    console.log('error');
		    }
	    }
	    request.send();
    });

	// ============================ map moved on coordinate change  ================================
    $('#core_lat').change(function(){
        if (marker != null) mymap.removeLayer(marker); // marker already on map deleted
        var latlng = new L.LatLng($(this).val(), $("#core_long").val()); // new coordinates stored
        marker = new L.Marker(latlng); // new marker created
	    mymap.addLayer(marker).panTo(latlng);
        verificationPays();
    });
    
    $('#core_long').change(function(){
	    if (marker != null) mymap.removeLayer(marker); // marker already on map deleted
	    var latlng = new L.LatLng($("#core_lat").val(), $(this).val()); // new coordinates stored
	    marker = new L.Marker(latlng); // new marker created
	    mymap.addLayer(marker).panTo(latlng);
        verificationPays();
    });
    
    var retourVerificationPays = true;

    // fonction qui vérifie que le site sélectionnées et les coordonnées sont dans le même pays
    function verificationPays(){
        $("#divErreur").hide();

	    // on récupère la position du marqueur
	    var latlng = marker.getLatLng();
	    retourVerificationPays = true;
	    let apiurl = 'https://www.mapquestapi.com/geocoding/v1/reverse?key=<?php echo MAPQUEST_KEY; ?>&location=' + latlng.lat + '%2C'+ latlng.lng + '&outFormat=json&thumbMaps=false'
	    let request = new XMLHttpRequest();
	    request.open('GET', apiurl, true);
	    request.onload = function() {
		    let data = JSON.parse(this.response);
		    if (request.status >= 200 && request.status < 400) {
			    let code = (data['results'][0]['locations'][0]['adminArea1']);
			    if (code !=  tabSiteCountry[$('#ID_SITE').val()]){
				    $("#divErreur div").html('Latitude and longitude must be in the same country as the site.');
				    $("#divErreur").show();
				    retourVerificationPays = false;
			    }
		    }
		    else {
			    console.log('error');
		    }
	    }
	    request.send();
    }
    
    // avant la soumission du formulaire on vérfie que le site et les coordonnées sont correctes
    $('#formAddCore').submit(function(event){
       if (retourVerificationPays === false) {event.preventDefault();}       
    });
    
</script>
        <?php
}
    function testPost($post_var) {
        return (isset($_POST[$post_var])) 
                    && $_POST[$post_var] != NULL 
                    && $_POST[$post_var] != 'NULL' 
                    && trim(delete_antiSlash($_POST[$post_var])) != "";
    }
  


