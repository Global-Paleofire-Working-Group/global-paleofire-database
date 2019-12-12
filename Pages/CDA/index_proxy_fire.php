<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
	<style>
		.legend {
			font-family: Arial, sans-serif;
			background: #fff;
			padding: 10px;
			margin: 10px;
			border: 1px solid #000;
			width: 250px;
		}
		.legend h5 {
			margin-top: 0;
		}
		.legend img {
			vertical-align: middle;
		}
	</style>

<?php
if (isset($_SESSION['started'])) {
	require_once './Models/Site.php';
	require_once './Models/Sample.php';
	require './Library/Pagination.php';
	$array_emails = array();

	$icon_published = "./images/marker-red.png";
	$icon_unpublished_without_charcoals = "./images/marker-green.png";

	$all_sites_with_coord = Core::getAllCoreForMap();
	$total = count($all_sites_with_coord);

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
	$totalProxyFire = ProxyFireData::countPaleofireObjects();

	?>

	<h4>The database contains : <?php echo $total; ?> cores, <?php echo $totalProxyFire; ?> charcoal's fire proxy samples </h4>
	<div id="map_leaflet"></div>

	<script src="https://unpkg.com/leaflet@1.5.1/dist/leaflet.js"
	        integrity="sha512-GffPMF3RvMeYyc1LWMHtK8EbPv0iNZ8/oTtHPx9/cc2ILxQ+u905qIwdpULaqDkyBKgOaB57QTMg7ztg8Jm2Og=="
	        crossorigin=""></script>
	<script>
		// =========================== Leaflet map creation =======================================
		var mymap = L.map('map_leaflet').setView([27.888087, -42.141615], 2);

		var osmUrl='http://{s}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png';
		var osmAttrib='Map data © <a href="http://openstreetmap.org">OpenStreetMap</a> contributors. Tiles courtesy of OSM France';

		L.tileLayer(osmUrl, {
			attribution: osmAttrib,
			maxZoom: 18,
			id: 'osm',
		}).addTo(mymap);

		// =========================== Get site points =======================================
		all_sites_with_coord_json = <?php echo json_encode($all_sites_with_coord) ?>;
		all_sites_for_icon = <?php echo json_encode($tabIcon); ?>;
		var arrayByCoord = new Array();
		var tabMarkerInfo = new Array();

		for (key in all_sites_with_coord_json) {
			if (arrayByCoord[all_sites_with_coord_json[key][1]+all_sites_with_coord_json[key][2]] != null){
				arrayByCoord[all_sites_with_coord_json[key][1]+all_sites_with_coord_json[key][2]]++;
				all_sites_with_coord_json[key][1] = parseFloat(all_sites_with_coord_json[key][1]) + (0.001 * arrayByCoord[all_sites_with_coord_json[key][1]+all_sites_with_coord_json[key][2]]);
			} else {
				arrayByCoord[all_sites_with_coord_json[key][1]+all_sites_with_coord_json[key][2]] = 0;
			}

			// =========================== Pop-up creation =======================================
			var marker_info = "";
			marker_info += "<div id='contentInfoWindow' >";
			marker_info += "<b>" + all_sites_with_coord_json[key][0] + "</b><br/>";
			marker_info += "<br/>";
			marker_info += "Latitude : " + all_sites_with_coord_json[key][1] + ", ";
			marker_info += "Longitude : " + all_sites_with_coord_json[key][2] + "<br/>";
			marker_info += "Elevation : " + all_sites_with_coord_json[key][3] + "<br/>";
			marker_info += "Type : " + all_sites_with_coord_json[key][6] + "<br/>";
			marker_info += "Country : " + all_sites_with_coord_json[key][5] + "<br/>";
			marker_info += "<a href=\"index.php?p=CDA/site_view_proxy_fire&site_id=" + all_sites_with_coord_json[key][4] + "\">View data site...</a>";
			marker_info += "</div>";
			marker_info += "</div>";
			tabMarkerInfo[key] = marker_info;

			// =========================== Marker creation =======================================
			var coreicon = L.icon({
				iconUrl: all_sites_for_icon[key][0]
			});

			var marker = L.marker(
				[all_sites_with_coord_json[key][1], all_sites_with_coord_json[key][2]],
				{
					title: all_sites_with_coord_json[key][0],
					icon: coreicon
				}
			).addTo(mymap);
			marker.bindPopup(marker_info);
		}

		var icons = {
			parking: {
				name: 'sites with charcoals',
				icon: '<?php echo $icon_published;?>'
			},
			info: {
				name: 'Sites without charcoals',
				icon: '<?php echo $icon_unpublished_without_charcoals;?>'
			}
		};

		// =========================== Legend creation =======================================
		let legend = L.control({position: 'bottomleft'});
		legend.onAdd = function(map){
			let div = L.DomUtil.create('div', 'legend');
			div.innerHTML += '<h5>Legend</h5>'
			for(let key in icons) {
				let type = icons[key];
				let name = type.name;
				let icon = type.icon;
				div.innerHTML += '<div><img src="' + icon +  '" />' + name + '</div>'
			}
			return div;
		};
		legend.addTo(mymap)
	</script>
	<?php
}
