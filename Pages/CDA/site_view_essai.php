<?php
/*
 * fichier Pages/CDA/site_view.php
 *
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Sample.php';

    $_link_resolver = "http://dx.doi.org/";

    $array_emails = array();
    ?>

    <?php
    $current_site_id = null;
    if (isset($_GET['site_id']) && is_numeric($_GET['site_id'])) {
        $current_site_id = $_GET['site_id'];
    }

    if ($current_site_id != null) {
        $site = Site::getObjectPaleofireFromId($current_site_id);
        ?>

        <div class="row">
            <div class="col-md-9">
                <h3>Site
                    <?php
                          echo $site->getName()."<small> GCD code: ".$site->getIdValue();
                          // on affiche le old gcd code seulement pour les administrateurs
                          if ($site->getGCDAccessId() != null && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))) {
                              echo ", old GCD code: " . $site->getGCDAccessId();
                          }
                          echo "</small>";
                    ?>
                </h3>
            </div>
            <?php
            // affichage d'un menu pour modifier les sites
            // pour l'instant seul un administrateur peut modifier un site
            // les sites ne sont pas rattaché à un utilisateur particulier
                if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)) {
            ?>
            <div class="col-md-3">
                <div class="btn-toolbar" role="toolbar" align="right">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_site&gcd_menu=ADA&id=<?php echo $current_site_id; ?>">
                        <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                        Edit this site
                    </a>
                </div>
            </div>
            <?php
                } // FIN if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
            ?>
        </div>

        <div role="tabpanel" id="tabSite" style="height:300px">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li class="active"><a href="#geography" aria-controls="geography" role="tab" data-toggle="tab">Geography</a></li>
              <!--<li ><a href="#storage" aria-controls="storage" role="tab" data-toggle="tab">Storage</a></li>-->
              <li ><a href="#publications" aria-controls="publications" role="tab" data-toggle="tab">Publications</a></li>
              <li ><a href="#list_cores" aria-controls="list_cores" role="tab" data-toggle="tab">List of cores</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" style="padding-top:10px;border-color:lightgray;border-width:1px;border-style:none solid solid solid;">
                <div class="tab-pane active" id="geography">
                   <div class="row">
                        <div class="col-md-6">
                          <dl class="dl-horizontal">
                            <dt>Region</dt>
                            <dd><?php echo ($site->_site_country != null) ? $site->_site_country->getRegionName() : "no value"; ?></dd>
                            <dt>Country</dt>
                            <dd><?php echo ($site->_site_country != null) ? $site->getSiteCountry()->getName() : "no value"; ?></dd>
                            <dt>Type</dt>
                            <dd><?php echo ($site->_site_type_id != null)? SiteType::getNameFromStaticList($site->_site_type_id) : "no value";?></dd>
                            <dt>Biome</dt>
                            <dd><?php echo ($site->_biome_type_id != null) ? BiomeType::getNameFromStaticList($site->_biome_type_id) : "no value"; ?></dd>
                            <dt>Local vegetation</dt>
                            <dd><?php echo ($site->_local_veg_id != null) ? LocalVeg::getNameFromStaticList($site->_local_veg_id) : "no value"; ?></dd>
                            <dt>Regional vegetation</dt>
                            <dd><?php echo ($site->getRegionalVeg() != null) ? $site->getRegionalVeg()->getName() : "no value"; ?></dd>
                            <dt>Basin size</dt>
                            <dd><?php echo ($site->_basin_size_value != null) ? $site->_basin_size_value.'</br>' : '';
                            echo ($site->_basin_size_id != null) ? BasinSize::getNameFromStaticList($site->_basin_size_id): "no value"; ?>
                            </dd>
                            <dt>Flow type</dt>
                            <dd><?php echo ($site->_flow_type_id != null) ? FlowType::getNameFromStaticList($site->_flow_type_id) : "no value"; ?></dd>
                            <dt>Catch size</dt>
                            <dd><?php echo ($site->_catch_size_value != null) ? $site->_catch_size_value.'</br>' : '';
                                echo ($site->_catch_size_id != null) ? CatchSize::getNameFromStaticList($site->_catch_size_id) : "no value"; ?></dd>
                            <dt>Land description</dt>
                            <dd><?php echo ($site->_site_land_id != null) ? LandsDesc::getNameFromStaticList($site->_site_land_id) : "no value"; ?></dd>
                          </dl>

                            <div id="map_site" style="width:auto"></div>
                        </div>
                      </div>
                </div>
                <!--<div class="tab-pane" id="storage">
                    <?php
                        /*foreach ($site->getAllContactIds() as $contact_id) {
                            // TODO version d'Amandine charbonnier à refaire en une seule requete
                            echo Contact::getFieldValueFromWhere(Contact::NAME, sql_equal(Contact::ID, $contact_id));
                            echo Contact::getFieldValueFromWhere(Contact::FIRSTNAME, sql_equal(Contact::ID, $contact_id));
                            echo Contact::getFieldValueFromWhere(Contact::EMAIL, sql_equal(Contact::ID, $contact_id));
                        }*/
                    ?>
                </div>-->
              <div class="tab-pane" id="publications">
                <?php

                    foreach ((array)($site->getPubliReferencedBySite()) as $publi) {
                        //echo "XXXXXXX<p>".$publi["pub_citation"];
                        echo " <p>".$publi["pub_citation"];
                        if ($publi["pub_link"] != null) {echo "</br>".$publi["pub_link"];}
                        if ($publi["id_doi"] != null) {echo "</br>DOI : <a href='".$_link_resolver.$publi["id_doi"]."'>".$publi["id_doi"]."</a>";}
                        echo "</p>";
                    }
                ?>
              </div>
                <div class="tab-pane" id="list_cores">
<tbody>
                <?
                $coord = null
                        ;
                  <div class="row">
                        <div class="col-md-6">
                          <dl class="dl-horizontal">
                          <?php
                           $coord = null;
                    foreach($site->getListeCoreEtCumuls() as $core){
                        ?>

                            <dt>Name</dt>
                            <dd><?php echo '<td>'.$core[Core::NAME]."</td>";?></dd>
                            <dt>Country</dt>
                            <dd><?php echo ($site->_site_country != null) ? $site->getSiteCountry()->getName() : "no value"; ?></dd>
                            <dt>Type</dt>
                            <dd><?php echo ($site->_site_type_id != null)? SiteType::getNameFromStaticList($site->_site_type_id) : "no value";?></dd>
                            <dt>Biome</dt>
                            <dd><?php echo ($site->_biome_type_id != null) ? BiomeType::getNameFromStaticList($site->_biome_type_id) : "no value"; ?></dd>
                            <dt>Local vegetation</dt>
                            <dd><?php echo ($site->_local_veg_id != null) ? LocalVeg::getNameFromStaticList($site->_local_veg_id) : "no value"; ?></dd>
                            <dt>Regional vegetation</dt>
                            <dd><?php echo ($site->getRegionalVeg() != null) ? $site->getRegionalVeg()->getName() : "no value"; ?></dd>
                            <dt>Basin size</dt>
                            <dd><?php echo ($site->_basin_size_value != null) ? $site->_basin_size_value.'</br>' : '';
                            echo ($site->_basin_size_id != null) ? BasinSize::getNameFromStaticList($site->_basin_size_id): "no value"; ?>
                            </dd>
                            <dt>Flow type</dt>
                            <dd><?php echo ($site->_flow_type_id != null) ? FlowType::getNameFromStaticList($site->_flow_type_id) : "no value"; ?></dd>
                            <dt>Catch size</dt>
                            <dd><?php echo ($site->_catch_size_value != null) ? $site->_catch_size_value.'</br>' : '';
                                echo ($site->_catch_size_id != null) ? CatchSize::getNameFromStaticList($site->_catch_size_id) : "no value"; ?></dd>
                            <dt>Land description</dt>
                            <dd><?php echo ($site->_site_land_id != null) ? LandsDesc::getNameFromStaticList($site->_site_land_id) : "no value"; ?></dd>
                          </dl>
                          <?php
                    }
                    ?>

                            <div id="map_site" style="width:auto"></div>
                        </div>
                      </div>
                    //var_dump($coord);
                ?>
            </div>
          </div>

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>
    <script>
        var gcdIcon_OK = './images/marker_red.png';

        var map_global_site;

        //Variable pour le geocoder de Google Maps
        var geocoder;

        //Variable pour le marqueur
        var marker;

        /* initialisation de la fonction initMap */
        function initMap() {
            geocoder = new google.maps.Geocoder();
            var latlng = new google.maps.LatLng(27.888087, -42.141615);
            var mapOptions = {
                zoom: 6,
                center: latlng,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            }
            map_global_site = new google.maps.Map(document.getElementById('map_site'), mapOptions);
        }
        /* on va procéder à l'initialisation de la carte */
        initMap();

        var all_cores = <?php echo json_encode($coord) ?>;
        var i = 0;
        var tMarker = new Array();
        var latlngbounds = new google.maps.LatLngBounds();

        for (prop in all_cores) {
            var marker_info = "";
            marker_info += "<div id='contentInfoWindow' >";
            marker_info += "<b>" + all_cores[prop][0] + "</b><br/>";
            marker_info += "<a href=\"index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id=" + all_cores[prop][3] + "\">View data core...</a>";
            marker_info += "</div>";
            var object = {'lat': all_cores[prop][1], 'lon': all_cores[prop][2], 'title': all_cores[prop][0], 'info': marker_info};
            tMarker[i] = object;
            i++;

            latlngbounds.extend(new google.maps.LatLng(all_cores[prop][1],all_cores[prop][2]));
        }
        var nb = tMarker.length;
        // création des markers
        for (i = 0; i < nb; i++) {
    // création marker
            var oMarker = new google.maps.Marker({
                'numero': i,
                'position': new google.maps.LatLng(tMarker[i].lat, tMarker[i].lon),
                'map': map_global_site,
                'title': tMarker[i].title,
                icon: gcdIcon_OK
            });
    // création infobulle avec texte
            var oInfo = new google.maps.InfoWindow({maxWidth: 300
            });
    // événement clic sur le marker
            google.maps.event.addListener(oMarker, 'click', function() {
                oInfo.setContent(tMarker[this.numero].info);
                // affichage InfoWindow
                oInfo.open(this.getMap(), this);
            });
        }

        map_global_site.setCenter(latlngbounds.getCenter());
        //map_global_site.fitBounds(latlngbounds);


    </script>
    <?php
    }
}
