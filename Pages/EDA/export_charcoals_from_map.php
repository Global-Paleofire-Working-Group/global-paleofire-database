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
                        <p>Clic on the map to draw a polygon. Clic on the first point to close the polygon. 
                            Each site in the polygon will be selected for the data export.</p>
                    </div>
                    <div class="col-md-2">
                        <a id="buttonClear" style="margin-bottom:5px;float:right" class="btn btn-default disabled" role="button">Clear the polygon</a>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div id="map_selection" style="width:100%;height:600px"></div>
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
    $all_sites_with_coord = Core::getAllCoreForMap();
?>
<!-- <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true&libraries=drawing,geometry"></script>  -->
<?php
	if (GOOGLE_API_KEY != ""){
		echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&key='.GOOGLE_API_KEY.'&libraries=drawing,geometry"></script>';			
	} else {
            echo '<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&libraries=drawing,geometry"></script>';
        }
?>
<script type="text/javascript">
    
    var gcdIcon_OK = './images/marker_red.png';
    var map;
    var drawingManager;
    var polygonSelected = null;
    var arrayMarkers = new Array();
    
    // on appelle en ajax la création du fichier de données
    function exportData(){
        $('#err').empty();
        $('#err').hide();
        if (polygonSelected !== null){
            // on fait l'export à partir de la carte
            var key;
            var maxKey = arrayMarkers.length;
            var tabIds = [];
            for (key = 0; key < maxKey; key++) {
                elt = arrayMarkers[key];
                contientMarker = google.maps.geometry.poly.containsLocation(elt.getPosition(), polygonSelected);
                if (contientMarker === true) tabIds.push(elt.numero);
            }
            
            //on teste s'il y a un site dans le polygone
            if (tabIds.length>0)        {
                var ids = JSON.stringify(tabIds);
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
    
    function affichageSitesExistants(){
        all_sites_with_coord_json = <?php echo json_encode($all_sites_with_coord) ?>;
        var marker_info;
        var oInfo = new google.maps.InfoWindow({maxWidth: 300});
        for (prop in all_sites_with_coord_json) {
            marker_info = "<div id='contentInfoWindow' >";
            marker_info += "<b>" + all_sites_with_coord_json[prop][0] + "</b><br/>";
            marker_info += "<br/>";
            marker_info += "Latitude : " + all_sites_with_coord_json[prop][1] + "<br/>";
            marker_info += "Longitude : " + all_sites_with_coord_json[prop][2] + "<br/>";
            marker_info += "Elevation : " + all_sites_with_coord_json[prop][3] + "<br/>";
            marker_info += "Type : " + all_sites_with_coord_json[prop][6] + "<br/>";
            marker_info += "Country : " + all_sites_with_coord_json[prop][5] + "<br/>";
            marker_info += "<a href=\"index.php?p=CDA/site_view&gcd_menu=CDA&site_id=" + all_sites_with_coord_json[prop][4] + "\">View data site...</a>";
            marker_info += "</div>";
            
            var oMarker = new google.maps.Marker({
                'numero': all_sites_with_coord_json[prop][4],
                'position': new google.maps.LatLng(all_sites_with_coord_json[prop][1], all_sites_with_coord_json[prop][2]),
                'map': map,
                'title': all_sites_with_coord_json[prop][0],
                icon: gcdIcon_OK
            });
            arrayMarkers.push(oMarker);
        }
    }
    
    function initialize() {
        //var latlng = new google.maps.LatLng(27.888087, -42.141615);
        var latlng = new google.maps.LatLng(0, 0);
        var mapOptions = {
          zoom: 2,
          center: latlng,
          mapTypeId: google.maps.MapTypeId.ROADMAP
        };

        map = new google.maps.Map(document.getElementById('map_selection'),mapOptions);
      
        drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.POLYGON,
        drawingControl: false, // on n'affiche pas le menu
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: [google.maps.drawing.OverlayType.POLYGON]
        }});

        drawingManager.setMap(map);
        google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
            drawingManager.setDrawingMode(null);
            $("#buttonClear").removeClass("disabled");
            $("#buttonClear").addClass("active");
            polygonSelected = polygon;
            polygon.setOptions({editable:true, draggable:true});
        });
        
        affichageSitesExistants();
    }

    google.maps.event.addDomListener(window, 'load', initialize);
         
    $("#buttonClear").click(function(){
        polygonSelected.setMap(null);
        polygonSelected = null;
        $("#buttonClear").removeClass("active");
        $("#buttonClear").addClass("disabled");
        drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
    });
    
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
</script>
    <?php
}