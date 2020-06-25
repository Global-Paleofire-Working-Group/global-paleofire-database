<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($_SESSION['started'])) {
	require_once './Models/Site.php';
	require_once './Models/Contact.php';
	?>
	<div class="alert alert-danger" role="alert" id="err" style="display:none">...</div>
	<form action="Pages/EDA/ajax.php" class="form_paleofire formExportCharcoals" name="formExportCharcoals" method="post" target="theWindow" id="formExportCharcoals" >
		<div class="row">
			<div class="col-md-12">
				<fieldset class="cadre">
					<legend style="width:300px;margin-bottom:5px">Select sites on map to export data</legend>
					<div class="row">
						<div class="col-md-10">
							<p>Click on the polygon button to start a drawing session. Each site in the polygon will be selected for the data export.</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div id="map" style="width:100%;height:600px"></div>
						</div>
					</div>
				</fieldset>
			</div>
		</div>
		<fieldset class="cadre">
			<legend style="width:300px">Select optional fields to export</legend>
			<div class="row">
				<div class="col-md-10"><p>A few fields are exported by default. From sites data : name, ID, country and region. From cores data : name and ID. From samples data : name, ID, depth and depth type.
						From dates info data : name, ID, depth, depth type and age value.</p></div>
				<div class="col-md-2">
					<a id="buttonSelectAll" style="margin-bottom:5px;float:right" class="btn btn-default enabled" role="button">Select all fields</a>
				</div>
			</div>
			<?php
			$tabCBSites = Array("cb_rv"=>"Regional vegetation",
				"cb_lv"=>"Local vegetation",
				"cb_ld"=>"Lands description",
				"cb_st"=>"Site type",
				"cb_bs"=>"Basin size",
				"cb_cs"=>"Catch size",
				"cb_ft"=>"Flow type",
				"cb_bt"=>"Biome type");

			$tabCBCores = Array("cb_la"=>"Latitude",
				"cb_lo"=>"Longitude",
				"cb_el"=>"Elevation",
				"cb_wd"=>"Water depth",
				"cb_cd"=>"Coring date");

			$tabCBSamples = Array("cb_cs"=>"Charcoal size",
				"cb_ds" => "Data source",
				"cb_cm" => "Charcoal method",
				"cb_pcu" => "Prefered charcoal unit",
				"cb_db" => "Database version",
				"cb_am" => "Estimated age and age model");

			$tabCBDateInfo = Array("cb_dt" => "Date type",
				"cb_md" => "Material dated",
				"cb_am" => "Age Model",
				//"cb_av" => "Age value", // valeur par défaut
				"cb_au" => "Age units and calibrated or not",
				"cb_cm" => "Calibration method",
				"cb_cv" => "Calibration version");
			?>
			<div class="row">
				<div class="col-lg-3 col-md-6 col-sm-6">
					<label class="listCB">Data from sites</label>
					<div>
						<?php
						foreach($tabCBSites as $key=>$elt){
							echo '<p><input type="checkbox" name="cb_fields[]" value="'.$key.'"/>'.$elt.'</p>';
						}
						?>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<label class="listCB">Data from cores</label>
					<div>
						<?php
						foreach($tabCBCores as $key=>$elt){
							echo '<p><input type="checkbox" name="cb_fields[]" value="'.$key.'"/>'.$elt.'</p>';
						}
						?>
					</div>
				</div>
				<div class="clearfix visible-md-block visible-sm-block"></div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<label class="listCB">Data from samples</label>
					<div>
						<?php
						foreach($tabCBSamples as $key=>$elt){
							echo '<p><input type="checkbox" name="cb_fieldsSamples[]" value="'.$key.'"/>'.$elt.'</p>';
						}
						?>
					</div>
				</div>
				<div class="col-lg-3 col-md-6 col-sm-6">
					<label class="listCB">Data from dates info</label>
					<div>
						<?php
						foreach($tabCBDateInfo as $key=>$elt){
							echo '<p><input type="checkbox" name="cb_fieldsDateInfo[]" value="'.$key.'"/>'.$elt.'</p>';
						}
						?>
					</div>
				</div>
			</div>
		</fieldset>
		<fieldset class="cadre">
			<legend style="width:300px">Choose a name for the created file</legend>
			<p>
				<label for="filename">File name</label>
				<input type="text" name="filename" id="filename" />
			</p>
		</fieldset>
		<!-- Exportation des données !-->
		<p>
			<a role="btn" class="btn btn-default" id="btnFile" href="javascript:exportData()">Export</a>
		</p>
		<input type="hidden" id="action" name="action" value="export">
		<input type="hidden" id="ids" name="ids">
		<input type="hidden" id="f" name="f">
		<input type="hidden" id="fs" name="fs">
		<input type="hidden" id="fd"name="fd">
		<input type="hidden" id="fn"name="fn">
	</form>
	<?php
	$icon_published = "./images/marker-red.png";
	$icon_unpublished_without_charcoals = "./images/marker-green.png";

	$all_sites_with_coord = Core::getAllCoreForMap();
	$tabIcon = array();
	$list_published_data = Core::getListIDPublishedCore();
	$list_in_progress_data = Core::getListIDInProgressCore();
	foreach($all_sites_with_coord as  $id=>$core){
		if(in_array($id, $list_published_data)){
			$tabIcon[$id] = [$icon_published];
		} else if (in_array($id, $list_in_progress_data)){
			$tabIcon[$id] = [$icon_published];
		} else {
			$tabIcon[$id] = [$icon_unpublished_without_charcoals];
		}
	}
	?>
	<!-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=drawing,geometry"></script>  -->

	<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js" integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og==" crossorigin=""></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js" integrity="sha256-siofc4Uwjlra3YWkwthOn8Uj69cNN4aMug/iOHNiRgs=" crossorigin="anonymous"></script>
	<script type="text/javascript">

		var mymap = L.map('map').setView([20, 0], 2);
		var osmUrl='http://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
		var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors. Tiles courtesy of OSM France';
		L.tileLayer(osmUrl, {
			attribution: osmAttrib,
			maxZoom: 18,
			id: 'osm',
		}).addTo(mymap);

		var drawnItems = new L.FeatureGroup();
		mymap.addLayer(drawnItems);

		var drawControl = new L.Control.Draw({
			position: 'topright',
			draw: {
				polyline: false,
				polygon: true,
				circle: false,
				marker: false,
				circlemarker: false,
				rectangle: false
			},
			edit: {
				featureGroup: drawnItems,
				remove: true
			}
		});

		mymap.addControl(drawControl);

		var all_sites_with_coord_json = <?php echo json_encode($all_sites_with_coord); ?>;
		all_sites_for_icon = <?php echo json_encode($tabIcon); ?>;
		var arrayByCoord = new Array();
		var tabMarkerInfo = new Array();

		var arrayMarkers = new Array();
		let polygonSelected = null;
		let tabIds = new Array();

		for (key in all_sites_with_coord_json) {
			if (arrayByCoord[all_sites_with_coord_json[key][1] + all_sites_with_coord_json[key][2]] != null) {
				arrayByCoord[all_sites_with_coord_json[key][1] + all_sites_with_coord_json[key][2]]++;
				all_sites_with_coord_json[key][1] = parseFloat(all_sites_with_coord_json[key][1]) + (0.001 * arrayByCoord[all_sites_with_coord_json[key][1] + all_sites_with_coord_json[key][2]]);
			} else {
				arrayByCoord[all_sites_with_coord_json[key][1] + all_sites_with_coord_json[key][2]] = 0;
			}

			var marker_info = "";
			marker_info += "<div id='contentInfoWindow' >";
			marker_info += "<b>" + all_sites_with_coord_json[key][0] + "</b><br/>";
			marker_info += "<br/>";
			marker_info += "Latitude : " + all_sites_with_coord_json[key][1] + ", ";
			marker_info += "Longitude : " + all_sites_with_coord_json[key][2] + "<br/>";
			marker_info += "Elevation : " + all_sites_with_coord_json[key][3] + "<br/>";
			marker_info += "Type : " + all_sites_with_coord_json[key][6] + "<br/>";
			marker_info += "Country : " + all_sites_with_coord_json[key][5] + "<br/>";
			marker_info += "<a href=\"index.php?p=CDA/site_view&site_id=" + all_sites_with_coord_json[key][4] + "\">View data site...</a>";
			marker_info += "</div>";
			marker_info += "</div>";
			tabMarkerInfo[key] = marker_info;

			var coreicon = L.icon({
				iconUrl: all_sites_for_icon[key][0]
			});

			var marker = L.marker(
				[all_sites_with_coord_json[key][1], all_sites_with_coord_json[key][2]],
				{
					title: all_sites_with_coord_json[key][0],
					id_core: key,
					icon: coreicon
				}
			).addTo(mymap);

			marker.bindPopup(marker_info);
			arrayMarkers.push(marker);
		}

		mymap.on(L.Draw.Event.CREATED, function (e) {
			drawnItems.clearLayers();
			layer = e.layer;
			drawnItems.addLayer(layer);
			polygonSelected = first(drawnItems["_layers"]);
		});

		mymap.on(L.Draw.Event.DELETED, function (e) {
			layer = e.layer;
			polygonSelected = null;
			drawnItems.clearLayers();

		});


		// Function to verify if a point is inside a polygon
		function isMarkerInsidePolygon(m) {
			var inside = false;
			if (m instanceof L.Layer) {
				var polyPoints = polygonSelected.getLatLngs()[0];
				var x = m.getLatLng().lat, y = m.getLatLng().lng;
				for (var i = 0, j = polyPoints.length - 1; i < polyPoints.length; j = i++) {
					var xi = polyPoints[i].lat, yi = polyPoints[i].lng;
					var xj = polyPoints[j].lat, yj = polyPoints[j].lng;

					var intersect = ((yi > y) != (yj > y))
						&& (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
					if (intersect) inside = !inside;
				}
			}
			return inside;
		}

		// on appelle en ajax la création du fichier de données
		function exportData(){
			$('#err').empty();
			$('#err').hide();
			if (jQuery.isEmptyObject(drawnItems["_layers"]) === false){
				// on fait l'export à partir de la carte
				arrayMarkers.forEach(function(y){
					contientMarker = isMarkerInsidePolygon(y);
					if (contientMarker === true) {
						tabIds.push(y["options"]["id_core"]);

					}
				});

				//on teste s'il y a un site dans le polygone
				if (tabIds.length>0)        {
					var ids = JSON.stringify(tabIds);
					console.log(ids);
					// récupération des champs à exporter pour les sites
					var tab_fields = [];
					$("input[name='cb_fields[]']:checked").each(
						function(){ tab_fields.push($(this).val());}
					);
					var fields = JSON.stringify(tab_fields);
					//récupération des champs pour la table charcoal
					var tab_fields_samples = [];
					$("input[name='cb_fieldsSamples[]']:checked").each(
						function(){ tab_fields_samples.push($(this).val());}
					);
					var fields_samples = JSON.stringify(tab_fields_samples);
					// récupération des champs pour la table date info
					var tab_fields_dateinfo = [];
					$("input[name='cb_fieldsDateInfo[]']:checked").each(
						function(){ tab_fields_dateinfo.push($(this).val());}
					);
					var fields_dateinfo = JSON.stringify(tab_fields_dateinfo);

					var url = "Pages/EDA/ajax.php";
					/*url += "?action=export";
					url += "&ids=" + ids;
					url += "&f=" + fields;
					url += "&fs=" + fields_samples;
					url += "&fd=" + fields_dateinfo;
					url += "&fn=" + $('#filename').val();*/
					$("#ids").val(ids);
					$("#f").val(fields);
					$("#fs").val(fields_samples);
					$("#fd").val(fields_dateinfo);
					$("#fn").val($("#filename").val());

					/*$.post(url, { "ids": ids, "f" : fields, "fs" : fields_samples, "fd" : fields_dateinfo, "fn" : fields_dateinfo, "fn" : $("#filename").val()},
						function(returnedData){
							 console.log(returnedData);
					});*/
					//window.open(url, '_self');
					window.open('', 'theWindow');
					$("#formExportCharcoals").submit();
				}
				else {//xli 01/04/16
					//il n'y a pas de site dans le polygone

					$('#err').text('There is no site in the polygon ');
					$('#err').show();
				}
			}
			else {
				// le polygone n'a pas été tracé
				$('#err').text('The selection of sites on the map must not be empty');
				$('#err').show();
			}
		}

		$("#buttonSelectAll").click(function(){
			if ($(this).text() === "Select all fields"){
				$("input[name='cb_fields[]']").each(function(){ $(this).prop('checked', true); });
				$("input[name='cb_fieldsCores[]']").each(function(){ $(this).prop('checked', true); });
				$("input[name='cb_fieldsSamples[]']").each(function(){ $(this).prop('checked', true); });
				$("input[name='cb_fieldsDateInfo[]']").each(function(){ $(this).prop('checked', true); });
				$(this).text("Deselect all fields");
			} else {
				$("input[name='cb_fields[]']").each(function(){ $(this).prop('checked', false); });
				$("input[name='cb_fieldsCores[]']").each(function(){ $(this).prop('checked', false); });
				$("input[name='cb_fieldsSamples[]']").each(function(){ $(this).prop('checked', false); });
				$("input[name='cb_fieldsDateInfo[]']").each(function(){ $(this).prop('checked', false); });
				$(this).text("Select all fields");
			}
		});

		// Function to select first non-undefined element of an array
		function first(p) {
			for (let i in p) return p[i];
		}
	</script>
	<?php
}