<?php
/*
 * fichier Pages/CDA/site_view_proxy_fire.php
 *
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Sample.php';
    require_once './Library/PaleofireHtmlTools.php';

    require_once (REP_MODELS."ProxyFireMeasurementUnit.php");
    require_once (REP_MODELS."ProxyFireData.php");

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

        <?php
        // affichage d'un menu pour modifier les sites
        // pour l'instant seul un administrateur peut modifier un site
        // les sites ne sont pas rattaché à un utilisateur particulier
        if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)) {
        ?>
        <div class="btn-toolbar" role="toolbar">
            <a role="button" class="btn btn-default btn-xs" style="float:right" href="index.php?p=ADA/edit_site&gcd_menu=ADA&id=<?php echo $current_site_id; ?>">
                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
                Edit this site
            </a>
            <?php
            if ($site->countCore() == 0){
                if ($site->getPubliReferencedBySite() == NULL){
                    // le site n'a pas de core
                    // et n'est pas référencé dans des publications
                    // donc on propose la suppression
                    ?>
                    <a role="button" class="btn btn-default btn-xs" style="float:right" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;delsite&quot;,<?php echo $site->getIdValue(); ?>]">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
                    </a>
                    <?php
                }
            }
            ?>
        </div>

        <?php
        } // FIN if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)
        ?>

        <h3 class="paleo">Site
            <?php
                  echo $site->getName()."<small> GCD code: ".$site->getIdValue();
                  // on affiche le old gcd code seulement pour les administrateurs
                  if ($site->getGCDAccessId() != null && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))) {
                      echo ", old GCD code: " . $site->getGCDAccessId();
                  }
                  echo "</small>";
            ?>
        </h3>


        <div role="tabpanel" id="tabSite" style="height:600px">
            <h4>List of cores</h4>
            <table class="table table-bordered table-condensed table-responsive">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Lat / Long</th>
                        <th>Elev</th>
                        <th>Depth</th>
                        <th>Nb samples</th>
                        <th>Nb date info</th>
                        <th>Method Treatment</th>
                        <th>Method Estimation</th>
                        <th>Unit</th>
                        <th>View more</th>
                    </tr>
                </thead>
                <tbody>
                <?php

                $coord = null;
                    foreach($site->getListeCoreEtCumulsProxyFire() as $core){
                        echo '<tr>';
                        echo '<td>'.$core[Core::NAME]."</td>";
                        echo '<td>'.$core[Core::LATITUDE]." / ".$core[Core::LONGITUDE]."</td>";
                        echo '<td>'.$core[Core::ELEVATION]."</td>";
                        echo '<td>'.$core[Core::WATER_DEPTH]."</td>";
                        echo '<td>'.$core['nb_charcoals']."</td>";
                        echo '<td>'.$core['nb_date_info']."</td>";

                        $liste_methods_treatment = [];
                        $tab_treatment = explode(',',$core['list_methods_treatment']);

                        foreach($tab_treatment as $method_treatment_id){
                            $liste_methods_treatment[] = ProxyFireMethodTreatment::getNameFromStaticList($method_treatment_id);

                        }
                        echo '<td class="small">'. implode(', ', $liste_methods_treatment)."</td>";

                        $liste_methods_estimation = [];
                        $tab_estimation = explode(',',$core['list_methods_estimation']);

                        foreach($tab_estimation as $method_estimation_id){
                            $liste_methods_estimation[] = ProxyFireMethodEstimation::getNameFromStaticList($method_estimation_id);
                        }

                        echo '<td class="small">'. implode(', ', $liste_methods_estimation)."</td>";
                        $liste_units = [];
                        $tab = explode(',',$core['list_units']);
                        foreach($tab as $unit_id){
                            $liste_units[] = ProxyFireMeasurementUnit::getUnitFromStaticList($unit_id);
                        }
                        echo '<td class="small">'. implode(', ', $liste_units)."</td>";
                        echo '<td><a class="btn btn-default btn-lg active btn-xs" role="button" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$core[Core::ID].'">'
                                . '<span class="glyphicon glyphicon-search" aria-hidden="true"></span>'
                                . '</a></td>';
                        echo '</tr>';
                        $coord[] = array($core[Core::NAME], $core[Core::LATITUDE], $core[Core::LONGITUDE], $core[Core::ID]);
                    }
                    //var_dump($coord);
                ?>
                </tbody>
            </table>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
              <li class="active"><a href="#geography" aria-controls="geography" role="tab" data-toggle="tab">Geography</a></li>
              <!--<li ><a href="#storage" aria-controls="storage" role="tab" data-toggle="tab">Storage</a></li>-->
              <li ><a href="#publications" aria-controls="publications" role="tab" data-toggle="tab">Publication</a></li>
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
                            <dd><?php echo ($site->_regional_veg_id != null) ? RegionalVeg::getNameFromStaticList($site->_regional_veg_id) : "no value"; ?></dd>
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
                        </div>
                        <div class="col-md-6">
                            <div id="map_site" style="width:auto"></div>
                        </div>
                      </div>
                </div>

              <div class="tab-pane" id="publications">
                    <?php
                    if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)) {
                    ?>
                    <div class="btn-toolbar" role="toolbar" align="right">
                        <a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;addpubli&quot;]">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                            Add a publication
                        </a>
                    </div>
                    <?php
                    }// end if droit pour ajouter des publis

                    foreach ((array)($site->getPubliReferencedBySite()) as $publi) {
                        echo '<div class="panel panel-info">';
                        if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))) {
                            echo '<div class="btn-toolbar" role="toolbar" align="right">
                                    <a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;removepubli&quot;,&quot;'.$publi["ID_PUB"].'&quot;]">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        Remove
                                    </a>
                                </div>';
                        }
                        echo '<div id="pub' . $publi["ID_PUB"] . '">';
                        echo $publi["pub_citation"];
                        echo '</div>';

                        if ($publi["pub_link"] != null) {echo "</br>".$publi["pub_link"];}
                        if ($publi["id_doi"] != null) {echo "</br>DOI : <a href='".$_link_resolver.$publi["id_doi"]."'>".$publi["id_doi"]."</a>";}

                        echo '</div>';
                    }
                ?>
              </div>
            </div>
          </div>

    <!-- SCRIPT DE GESTION DE LA CARTE !-->
	<?php
		if (GOOGLE_API_KEY != ""){
			echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key='.GOOGLE_API_KEY.'"></script>';
		} else {
                    echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp"></script>';
                }
	?>
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

        var recipient;
        $(function(){
            $('#dialog-paleo').on('shown.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                recipient = button.data('whatever');
                var modal = $(this);

                if (recipient[0] === 'addpubli'){
                    modal.find('.modal-title').html('<p>Add a publication<p>');
                    modal.find('.modal-body').html('<h3>Select a publication to link to site <?php echo $site->getName(); ?> </h3>\n');

                    // todo // remplacer par de l'ajax
                    var tabPubli = <?php echo getTabJavascriptHTML(Publi::ID.'[]', 'addSite_publi', 'Publi', null, null, 5, 20); ?>;

                    var select = $('<select style="max-width:100%;width:550px" id="selectAddPubli" name="selectAddPubli"></select>');
                    if(select.prop) {
                      var options = select.prop('options');
                    }
                    else {
                      var options = select.attr('options');
                    }

                    $.each(tabPubli, function(index, elt) {

                        options[options.length] = new Option(elt.label, elt.id);
                    });

                    var divselect = $('<div style="width:550px;max-width:100%;"></div>').append(select);
                    modal.find('.modal-body').append(divselect);

                    //modal.find('#dialog-btn-yes').attr('href', "javascript:getURLAddPubli();");

                } else if (recipient[0] === 'removepubli'){
                    modal.find('.modal-body').html('<h3>Confirm the remove from "<?php echo $site->getName(); ?>" the following publication ?</h3><p>' + $("#pub" + recipient[1]).text()  + '</p>');
                    modal.find('#dialog-btn-yes').attr('href', "index.php?p=CDA/site_view_ajax&gcd_menu=CDA&act=rem&site=<?php echo $site->getIdValue(); ?>&publi=" + recipient[1]);
                } else if (recipient[0] === 'delsite'){
                    // suppression d'un site
                    modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');
                    modal.find('.modal-body').html('<h3>Confirm the deletion of the following site ?</h3><p><?php echo $site->getName(); ?></p>');
                    modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_site&id=<?php echo $site->getIdValue(); ?>");
                }
              });

              $("#dialog-btn-yes").click(function(){
                if (recipient[0] === 'addpubli'){
                    $('#dialog-btn-yes').attr('href', "index.php?p=CDA/site_view_ajax&gcd_menu=CDA&act=add&site=" + <?php echo $site->getIdValue(); ?> + "&publi=" + $("#selectAddPubli").val());
                }
              });
          });
    </script>
    <?php
    }
}
