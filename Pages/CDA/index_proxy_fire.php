<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<style>
      #legend {
        font-family: Arial, sans-serif;
        background: #fff;
        padding: 10px;
        margin: 10px;
        border: 1px solid #000;
        width: 250px;
      }
      #legend h5 {
        margin-top: 0;
      }
      #legend img {
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
    $icon_unpublished = "./images/marker-green.png";
    $icon_unpublished_without_charcoals = "./images/marker-grey.png";

    $all_sites_with_coord = Core::getAllCoreForMap();
    $total = count($all_sites_with_coord);

    $tabIcon = array();
    $list_published_data = Core::getListIDPublishedCore();
    $list_in_progress_data = Core::getListIDInProgressCore();
    foreach($all_sites_with_coord as  $id=>$core){
        if(in_array($id, $list_published_data)){
            $tabIcon[$id] = [$icon_published];
        } else if (in_array($id, $list_in_progress_data)){
            $tabIcon[$id] = [$icon_unpublished];
        } else {
            $tabIcon[$id] = [$icon_unpublished_without_charcoals];
        }
    }
    $totalProxyFire = ProxyFireData::countPaleofireObjects();

    ?>

    <h4>The database contains : <?php echo $total; ?> cores, <?php echo $totalProxyFire; ?> charcoal's fire proxy samples </h4>
    <div id="map_global_site"></div>
    <div id="legend"><h5>Legend</h5></div>
	<?php
		if (GOOGLE_API_KEY != ""){
                    echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key='.GOOGLE_API_KEY.'"></script>';
                    //  JdT 14/06/2019 mail from Fabbrice Mendes et Anne-Laure Daniau du 14/06/2019 à 10h58
                    //echo '<script async defer src="https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY.'&callback=initMap" type="text/javascript"></script>';

		} else {
                    echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>';
                }
	?>
    <script>
        var map_global_site;

        //Variable pour le geocoder de Google Maps
        var geocoder;

        //Variable pour le marqueur
        //var marker;

        /* initialisation de la fonction initMap */
        function initMap() {
            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(27.888087, -42.141615);
            var mapOptions = {
                zoom: 2,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            map_global_site = new google.maps.Map(document.getElementById('map_global_site'), mapOptions);
        }
        /* on va procéder à l'initialisation de la carte */
        initMap();

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

            var marker_info = "";
            marker_info += "<div id='contentInfoWindow' >";
            marker_info += "<b>" + all_sites_with_coord_json[key][0] + "</b><br/>";
            marker_info += "<br/>";
            marker_info += "Latitude : " + all_sites_with_coord_json[key][1] +", ";
            marker_info += "Longitude : " + all_sites_with_coord_json[key][2] + "<br/>";
            marker_info += "Elevation : " + all_sites_with_coord_json[key][3] + "<br/>";
            marker_info += "Type : " + all_sites_with_coord_json[key][6] + "<br/>";
            marker_info += "Country : " + all_sites_with_coord_json[key][5] + "<br/>";
            marker_info += "<a href=\"index.php?p=CDA/site_view_proxy_fire&site_id=" + all_sites_with_coord_json[key][4] + "\">View data site...</a>";
            marker_info += "</div>";
            marker_info += "</div>";
            tabMarkerInfo[key] = marker_info;
            // création marker
            var oMarker = new google.maps.Marker({
                'numero':key,
                'position': new google.maps.LatLng(all_sites_with_coord_json[key][1], all_sites_with_coord_json[key][2]),
                'map': map_global_site,
                'title': all_sites_with_coord_json[key][0],
                'icon': all_sites_for_icon[key][0]
            });

            // création infobulle avec texte
            var oInfo = new google.maps.InfoWindow({maxWidth: 300});
            // événement clic sur le marker
            google.maps.event.addListener(oMarker, 'click', function() {
                oInfo.setContent(tabMarkerInfo[this.numero]);
                // affichage InfoWindow
                oInfo.open(this.getMap(), this);
            });
        }

        var icons = {
          parking: {
            name: 'sites',
            icon: '<?php echo $icon_published;?>'
          },
          library: {
            name: 'sites with missing information - please edit!',
            icon: '<?php echo $icon_unpublished;?>'
          },
          info: {
            name: 'Identified site - Please contribute!',
            icon: '<?php echo $icon_unpublished_without_charcoals;?>'
          }
        };


        var legend = document.getElementById('legend');
        for (var key in icons) {
          var type = icons[key];
          var name = type.name;
          var icon = type.icon;
          var div = document.createElement('div');
          div.innerHTML = '<img src="' + icon + '"> ' + name;
          legend.appendChild(div);
        }

        map_global_site.controls[google.maps.ControlPosition.BOTTOM_LEFT].push(legend);


    </script>
    <?php
}
