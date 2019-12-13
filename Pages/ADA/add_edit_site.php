<?php
/* 
 * fichier Pages/ADA/add_edit_site.php 
 * 
 */ 

function add_edit_site($Operation) {
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR) || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
    require_once './Models/Site.php';
    require_once './Models/Status.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Pages/EDA/change_Log.php';
    require_once './Library/data_securisation.php';
    require_once './Pages/ADA/del_publi_site.php';

    if (!isset($success_form)) {
        $success_form = "";
    }
    
    $id = null;
    $errors = null;
    
    if (($Operation ==="edit")||($Operation==="edit_pending")) {
        // cas d'une édition, on récupère le site en fonction de l'identifiant passé dans l'URL
        $id = $_GET['id'];
        $new_site = Site::getObjectPaleofireFromId(data_securisation::toBdd($_GET['id']));
        // todo // si on ne récupère pas de site rediriger
    } else if (!isset($_GET['id']) && ($Operation=="add")) {
        $new_site = new Site();
    } else {
            $errors[] = "Acces denied";
    }    

    if (!isset($temp_local_veg)) {
        $temp_local_veg = "";
    }

    if (!isset($temp_reg_veg)) {
        $temp_reg_veg = "";
    }

    $region_id = null;
    if (isset($_GET['region_id']) && is_numeric($_GET['region_id'])) {
        $region_id = $_GET['region_id'];
        $new_site->_site_region_id = $region_id;
    }

    //creation du tableau contenant l'alpha2 de country et la région associée
    $data_countries = array();
// c'est une blague ? une requete par pays pour avoir la région ?
//    $list_data_countries = Country::getArrayFieldsValueFromWhere(array(Country::COUNTRY_ISO_ALPHA2, Country::ID_REGION));
//    foreach ($list_data_countries as $data_country) {
//        $data_countries[$data_country[Country::COUNTRY_ISO_ALPHA2]] = Region::getFieldValueFromWhere(Region::NAME, sql_equal(Region::ID, $data_country[Country::ID_REGION]));
//    }
    $data_countries = COUNTRY::getArrayISORegion();
    
    
    if (isset($_POST['deletePubliSite']) && (empty($errors))) {        
       del_publi_site($Operation);         
    } 
    
    if ((isset($_POST['submitAdd'])) && (empty($errors))) {

        $success_form = false;        
    //    $errors = null;
        
        if ((isset($_POST[Site::NAME])) && $_POST[Site::NAME] != NULL && trim(delete_antiSlash($_POST[Site::NAME])) != "") {
            $new_site->setNameValue($_POST[Site::NAME]);
            if ($id == null){
                $result_exists = Site::getAllIds(null, null, sql_equal(Site::NAME, utf8_decode($new_site->getName())));
                if (count($result_exists) > 0) {
                    $errors[] .= '<br /> -The site name "' . $new_site->getName() . '" already exists';
                }
            }
        } else {
            $errors[] = "<br /> -Missing data : name of site";
        }

        if (testPost(Site::GCD_ACCESS_ID)) {
            $new_site->setGCDAccessId($_POST[Site::GCD_ACCESS_ID]);
        }

        if (testPost(Site::ID_COUNTRY)) {
            $new_site->_site_country = Country::getObjectPaleofireFromWhere(sql_equal(Country::COUNTRY_ISO_ALPHA2, $_POST[Site::ID_COUNTRY]));
        }
        else
            $new_site->_site_country = NULL;

        if (testPost(Site::ID_REGION)) {
            $new_site->_site_region_id = $_POST[Site::ID_REGION];
        }
        else
            $new_site->_site_region_id = NULL;

        if (testPost(Site::ID_BASIN_SIZE)) {
            $new_site->_basin_size_id = $_POST[Site::ID_BASIN_SIZE];
        }
        else
            $new_site->_basin_size_id = NULL;
        
        if (testPost(Site::BASIN_SIZE_VALUE)) {
            if (is_numeric($_POST[Site::BASIN_SIZE_VALUE])) {
                $new_site->_basin_size_value = $_POST[Site::BASIN_SIZE_VALUE];
            } else {
                $errors[] = "Catchment size value must be numeric";
            }
        }
        else
            $new_site->_basin_size_value = NULL;

        if (testPost(Site::ID_BIOME_TYPE)) {
            $new_site->_biome_type_id = $_POST[Site::ID_BIOME_TYPE];
        }
        else
            $new_site->_biome_type_id = NULL;

        if (testPost(Site::ID_CATCH_SIZE)) {
            $new_site->_catch_size_id = $_POST[Site::ID_CATCH_SIZE];
        }
        else
            $new_site->_catch_size_id = NULL;
        
        if (testPost(Site::CATCH_SIZE_VALUE)) {
            if (is_numeric($_POST[Site::CATCH_SIZE_VALUE])) {
                $new_site->_catch_size_value = $_POST[Site::CATCH_SIZE_VALUE];
            } else {
                $errors[] = "Size value must be numeric";
            }
        }
        else
            $new_site->_catch_size_value = NULL;

        if (testPost(Site::ID_FLOW_TYPE)) {
            $new_site->_flow_type_id = $_POST[Site::ID_FLOW_TYPE];
        }
        else
            $new_site->_flow_type_id = NULL;

        if (testPost(Site::ID_LANDS_DESC)) {
            $new_site->_site_land_id = $_POST[Site::ID_LANDS_DESC];
        }
        else if (isset($_POST[Site::ID_LANDS_DESC."_new"])){
            //on créé la nouvelle regional Veg
            $new_elt = $_POST[Site::ID_LANDS_DESC."_new"];
            $new_obj = new LandsDesc();
            $new_obj->setNameValue($new_elt);
            $new_obj->create();
            $new_site->_site_land_id = $new_obj->getIdValue();
        }
        else
            $new_site->_site_land_id = NULL;

        if (testPost(Site::ID_SITE_TYPE)) {
            //$new_site->_site_type_id = $_POST["addSite_siteType"];
            $new_site->_site_type_id = $_POST[Site::ID_SITE_TYPE];
        }
        else if (isset($_POST[Site::ID_SITE_TYPE."_new"])){
            //on créé la nouvelle regional Veg
            $new_elt = $_POST[Site::ID_SITE_TYPE."_new"];
            $new_obj = new SiteType();
            $new_obj->setNameValue($new_elt);
            $new_obj->create();
            $new_site->_site_type_id = $new_obj->getIdValue();
        }
        else
            $new_site->_site_type_id = NULL;

        if (testPost(Site::ID_LOCAL_VEG)) {
            if (LocalVeg::idExistsInDatabase($_POST[Site::ID_LOCAL_VEG])){
                $new_site->_local_veg_id = $_POST[Site::ID_LOCAL_VEG];
            } 
        }
        else if (isset($_POST[Site::ID_LOCAL_VEG."_new"])){
            // on crée la nouvelle local veg
            $loc_veg = $_POST[Site::ID_LOCAL_VEG."_new"];
            $new_veg = new LocalVeg();
            $new_veg->setNameValue($loc_veg);
            $new_veg->create();
            $new_site->_local_veg_id = $new_veg->getIdValue();
        }
        else
            $new_site->_local_veg_id = NULL;

        if (testPost(Site::ID_REGIONAL_VEG)) {
            if (RegionalVeg::idExistsInDatabase($_POST[Site::ID_REGIONAL_VEG])){
                $objRegVeg = new RegionalVeg();
                $objRegVeg->setIdValue($_POST[Site::ID_REGIONAL_VEG]);
                $new_site->_regional_veg_id = $objRegVeg->getIdValue();
            } 
        }
        else if (isset($_POST[Site::ID_REGIONAL_VEG."_new"])){
            //on créé la nouvelle regional Veg
            $reg_veg = $_POST[Site::ID_REGIONAL_VEG."_new"];
            $new_veg = new RegionalVeg();
            $new_veg->setNameValue($reg_veg);
            $new_veg->create();
            $new_site->_regional_veg_id = $new_veg->getIdValue();
        }
        else
            $new_site->_regional_veg_id = NULL;

        if (testPost(Publi::ID)) {
            $listeID = $_POST[Publi::ID];
                       
            foreach($listeID as $pub_id){
                if(Publi::idExistsInDatabase($pub_id)){
                    $new_site->_liste_publi_id[] = $pub_id;
                }
            }
        }

        if ($Operation=="edit_pending") {    
            if (is_numeric($_POST[Site::ID_STATUS])){
                    $new_site->setStatusId($_POST[Site::ID_STATUS]);
            } else {
                    $errors[] = "Select a status";
            }
        } else {   
            /*si c'est un contributeur ou un administrateur qui entre la données, cette dernière sera mise en attente de validation par un administrateur
            //si c'est un superadministrateur qui entre la données, cette dernière sera automatiquemenbt validée
            //if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){ */
                $new_site->setStatusId(1); // la donnée est validée
            /*}
            //else {
            //    $new_site->setStatusId(0); //la donnée apparaîtra dans la liste données à valider
            //} */
        }                             
        
        if (empty($errors)) {
            $errors = $new_site->save($Operation);
        }
        
        if (empty($errors)){
                        
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
            $success_form = true;
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        switch($Operation) {
            case "edit":
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/site_view_list&gcd_menu=CDA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to site
                    </a>
                </div>';
                break;
            case "edit_pending":
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=Admin/validate_pending_site&gcd_menu=CDA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to pending site
                    </a>
                </div>';
                break;
            case "add":
                echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_site&gcd_menu=ADA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Go back to site
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
    ?>

    <?php
    if (($success_form != true)&&(!isset($_POST['deleteAdd']))) {
        ?>
        <?php 
            switch ($Operation) {
                case "edit":
                    echo '<h1>Editing site : '.$new_site->getName().'</h1>';     
                    $redirection="CDA/site_view_list&gcd_menu=CDA";
                    break;
                case "add":
                    echo '<h1>Add a new site</h1>';
                    $redirection="ADA/add_site&gcd_menu=ADA";                    
                    break;
                case "edit_pending":
                    echo '<h1>Editing pending site : '.$new_site->getName().'</h1>';
                    $redirection="Admin/validate_pending_site&gcd_menu=ADA";                    
                    break;
                default:
                    echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php')\" value = 'Cancel' />";                       
                    break;
            }
        ?>
        <!-- SCRIPT DE GESTION DE LA CARTE !-->

        <script>
	        /* tableau contenant les infos des pays */
	        var monTableauJS = <?php echo json_encode($data_countries); ?> ;
<?php 
    if (isset($new_site->_site_country) && $new_site->_site_country->_country_iso_alpha2 != null) { 
        echo 'displaydatacountry("'.$new_site->_site_country->_country_iso_alpha2.'", monTableauJS);';
    } else {
        echo 'displaydatacountry("NULL", monTableauJS);';
    }
?>

            /*Fonction qui affiche les données liées au pays lorsqu'il est selectionné sans reload de la page*/
            function displaydatacountry(currentcountry =null, tab ={}) {
                $("#addSite_country").val(currentcountry);
                $("#info_country").show();
                $("#country_region").val(tab[currentcountry]);
            }
            
            function gestionMarine(elt){
                if (elt.checked) {
                    $("#info_marine").show();
                    displaydatacountry('NULL', monTableauJS);
                }
                else $("#info_marine").hide();
            }
        </script>

        <!-- Formulaire de saisie du site !-->
        <form action="" class="form_paleofire" name="formAddSite" method="post" id="formAddSite">
            <!-- Cadre pour les métadonnées du site !-->
            <fieldset class="cadre">
                <legend>Site metadata</legend>
                <p class="site_name">

                    <label for="name_site">Site name*</label>
                    <input type="text" name="<?php echo Site::NAME; ?>" id="name_site" value="<?php echo $new_site->getName(); ?>"> <!-- onchange="updateCoreName(this.value)" -->
                </p>
                <!-- Affichage carte !-->
                <p>
                    <label>Select a country</label>
                    <div id="map_addSite"></div>
                </p>
                <p>
                    <label for="addSite_country">Country</label>
                    <select name = '<?php echo Site::ID_COUNTRY; ?>' id = 'addSite_country' onchange="displaydatacountry($(this).val(), monTableauJS)">
                        <option value = 'NULL'>Select a value</option>
                        <?php
                        $result_all_objects = Country::getArrayFieldsValueFromWhere(array(Country::COUNTRY_ISO_ALPHA2, Country::NAME));
                        foreach ($result_all_objects as $object) {
                            if ($new_site->_site_country != null && $object[Country::COUNTRY_ISO_ALPHA2] == $new_site->_site_country->_country_iso_alpha2) {
                                $select = "selected";
                            } else {
                                $select = "";
                            }
                            echo "<option ".$select." value='" . $object[Country::COUNTRY_ISO_ALPHA2]. "'>" . htmlentities($object[Country::NAME]) . "</option>";
                        }
                        unset($result_all_objects);
                        ?>
                    </select>
                </p>

                <p id="info_country">
                    <label for="info_country">Country region</label> 
                    <input type="text"  id="country_region" disabled=""/>
                </p>
                <p id="select_region">
                    <label for="marine_region">Marine Site</label>  
                    <input type="checkbox" value="" onclick="javascript:gestionMarine(this)"/>
                </p>
                <p id="info_marine" hidden>
                    <label for="addSite_region">Marine region</label>
                    <?php
                    selectHTML(Site::ID_REGION, 'addSite_region', 'Region', intval($new_site->_site_region_id), sql_equal(Region::IS_MARINE_REGION, 1));
                    ?>
                </p>
                <?php echo '<script type="text/javascript">displaydatacountry();</script>'; ?>
            </fieldset>

            <!-- Cadre pour les données géomorphologiques du site !-->
            <fieldset class="cadre">
                <legend>Geomorphology</legend>
                <p>
                    <label for="addSite_siteType">Site geomorphology</label>
                    <?php
                    selectHTML(Site::ID_SITE_TYPE, 'addSite_siteType', 'SiteType', intval($new_site->_site_type_id));
                    ?>
                </p>
                <p>
                    <label for="formcache">New site geomorphology</label>  
                    <input type="checkbox" value="" onclick="document.getElementById('formcache').style.display = (this.checked ? 'block' : 'none');"/>
                </p>
                <p id="formcache" hidden>
                    <label>&nbsp;</label>  
                    <input type="text" name="<?php echo Site::ID_SITE_TYPE . "_new"; ?>" placeholder="New site geomorphology value" />
                </p>
                <p>
                    <label for="addSite_basinSize">Size of the site</label>
                    <?php
                    //$basin_id = (isset($_POST[Site::ID_BASIN_SIZE])) ? $_POST[Site::ID_BASIN_SIZE] : null;
                    //selectHTML(Site::ID_BASIN_SIZE, 'addSite_basinSize', 'BasinSize', $basin_id);
                    selectHTML(Site::ID_BASIN_SIZE, 'addSite_basinSize', 'BasinSize', intval($new_site->_basin_size_id));
                    ?>
                </p>
                <p>
                    <label for="addSite_basinSizeValue">Size value</label>
                    <input type="text" name="<?php echo Site::BASIN_SIZE_VALUE; ?>" id="basin_size_value" value="<?php echo $new_site->_basin_size_value; ?>">
                </p>
                <p>
                    <label for="addSite_catchSize">Catchment size</label>
                    <?php
                    selectHTML(Site::ID_CATCH_SIZE, 'addSite_catchSize', 'CatchSize', intval($new_site->_catch_size_id));
                    ?>
                </p>
                <p>
                    <label for="addSite_catchSizeValue">Catchment size value</label>
                    <input type="text" name="<?php echo Site::CATCH_SIZE_VALUE; ?>" id="catch_size_value" value="<?php echo $new_site->_catch_size_value; ?>">
                </p>
                <p>
                    <label for="addSite_flowType">Inflow/Outflow</label>
                    <?php
                    selectHTML(Site::ID_FLOW_TYPE, 'addSite_flowType', 'FlowType', intval($new_site->_flow_type_id))
                    ?>
                </p>

            </fieldset>

            <!-- Cadre pour les infos environnementales !-->
            <fieldset class="cadre">
                <legend>Environment</legend>
                <p>
                    <label for="addSite_biomeType">Potential Biome type</label>
                    <?php selectHTML(Site::ID_BIOME_TYPE, 'addSite_biomeType', 'BiomeType', intval($new_site->_biome_type_id)); ?>
                </p>
                <p>
                    <label for="addSite_landDesc">Land description</label>
                    <?php selectHTML(Site::ID_LANDS_DESC, 'addSite_landDesc', 'LandsDesc', intval($new_site->_site_land_id)) ?>
                </p>
                <p>
                    <label for="formcache2">New land</label>  
                    <input type="checkbox" value="" onclick="document.getElementById('formcache2').style.display = (this.checked ? 'block' : 'none');"/>
                </p>
                <p id="formcache2" hidden>
                    <label>&nbsp;</label>
                    <input type="text" placeholder="New land description value" name="<?php echo Site::ID_LANDS_DESC . "_new"; ?>"/>
                </p>

                <p>
                    <label for="addSite_localVeg">Local vegetation</label>
                    <?php selectHTML(Site::ID_LOCAL_VEG, 'addSite_localVeg', 'LocalVeg', intval($new_site->_local_veg_id)) ?>
                </p>
                <p>
                    <label for="formcache3">New local vegetation</label>  
                    <input type="checkbox" value="" onclick="document.getElementById('formcache3').style.display = (this.checked ? 'block' : 'none');"/>
                </p>
                <p id="formcache3" hidden>
                    <label>&nbsp;</label>  
                    <input type="text" name="<?php echo Site::ID_LOCAL_VEG . "_new"; ?>"  placeholder="New local vegetation" />
                </p>

                <p>
                    <label for="addSite_regionalVeg">Regional vegetation</label>
                    <?php selectHTML(Site::ID_REGIONAL_VEG, 'addSite_RegionalVeg', 'RegionalVeg', intval($new_site->_regional_veg_id)) ?>
                </p>
                <p>
                    <label for="formcache4">New regional vegetation</label>  
                    <input type="checkbox" value="" onclick="document.getElementById('formcache4').style.display = (this.checked ? 'block' : 'none');"/>
                </p>
                <p id="formcache4" hidden>
                    <label>&nbsp;</label>  
                    <input type="text" name="<?php echo Site::ID_REGIONAL_VEG . "_new"; ?>"  placeholder="New regional vegetation" />
                </p>
            </fieldset>
            <!-- // TODO à reprendre // on enlève les publis pour l'instant ça ne fonctionne pas
            <fieldset class="cadre">
                    <legend>Publications</legend>
                    <p class="pub">
                        <?php
                       /*if (($Operation ==="edit")||($Operation==="edit_pending")) {
                           	$tabPubli = $new_site->getPubliReferencedBySite();
							if ($tabPubli != NULL) {
								if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                                */
                        ?>
                                <label for="pub">Publications</label>
		                        </br>
								<div class="btn-toolbar" role="toolbar" align="right" >
                                    <button type="submit" name = 'deletePubliSite' class="btn btn-xs"><span class="glyphicon glyphicon-minus"></span>Remove selected publication(s)</button> 
                                </div> 
                            	<?php/*
                            	}
                                    foreach ((array)($tabPubli) as $publi) {
                                        echo '<div class="form-check">';
                                        echo '<input type="checkbox" name="cb_publi[]" value="'.$publi["ID_PUB"].'">';
                                        echo $publi["pub_citation"];
                                        echo '</div>';
                                    }
				} else {
				*/?>
				<label for="pub">No related publications</label></br>
				<?php/*
				}
                            }
                            else {
				selectMultipleHTML(Publi::ID.'[]', 'addSite_publi', 'Publi', null, null, 5, 20);
                            }
                        */?>
                    </p>
            </fieldset>
            -->
            <?php                        
            if ($Operation==="edit_pending") {            
            ?>
            <fieldset class="cadre">
                <legend>Status</legend>
                <p>
                    <label for="addSite_status">Status</label>
                    <?php
                    $selectedId = (isset($obj))?$new_site->getStatusId():null;
                    selectHTML(Site::ID_STATUS, 'addSite_status', 'Status', intval($selectedId));
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
                    /* CBO // 16/10/2017 // pas de suppression en même temps que l'édition => pour éviter un mauvais clic
						if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                        echo "<input type = 'submit' name = 'deleteAdd' value = 'Delete' />";

                    }
					*/
                }
                echo "<input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />";
              ?>
            </p> 
        </form>
	    <script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
	            integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
	            crossorigin=""></script>
	    <script>
		    // =========================== Leaflet Map initialisation =======================================
		    var mymap = L.map('map_addSite').setView([47.245724, 5.984663], 2)

		    var osmUrl='http://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png';
		    var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors. Tiles courtesy of HOT';

		    L.tileLayer(osmUrl, {
			    attribution: osmAttrib,
			    maxZoom: 18,
			    id: 'osm',
		    }).addTo(mymap);

		    // =========================== Event on click  =======================================
		    function onMapClick(e) {
		    	let apiurl = 'https://www.mapquestapi.com/geocoding/v1/reverse?key=<?php echo MAPQUEST_KEY; ?>&location=' + e.latlng.lat + '%2C'+ e.latlng.lng + '&outFormat=json&thumbMaps=false'
			    let request = new XMLHttpRequest();
			    request.open('GET', apiurl, true);
			    request.onload = function() {
				    let data = JSON.parse(this.response);
				    if (request.status >= 200 && request.status < 400) {
					    let code = (data['results'][0]['locations'][0]['adminArea1']);
					    displaydatacountry(code, monTableauJS);
				    }
				    else {
					    console.log('error');
				    }
			    }
			    request.send();

		    }
		    mymap.on('click', onMapClick);
		    //=========================================================================================
	    </script>
        <?php
        }   
    }
}
    function testPost($post_var) {
        //xli 
        if ($post_var=='ID_PUB')
            return (isset($_POST[$post_var])) && $_POST[$post_var] != NULL && $_POST[$post_var] != 'NULL';
        else
        return (isset($_POST[$post_var])) && $_POST[$post_var] != NULL && $_POST[$post_var] != 'NULL' && trim(delete_antiSlash($_POST[$post_var])) != "";
    }
    
