<?php
/*
 * fichier \Pages\EDA\export_charcoals.php
 *
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Contact.php';
    ?>
<div class="alert alert-danger" role="alert" id="err" style="display:none"></div>
<form action="" class="form_paleofire formExportCharcoals" name="formExportCharcoals" method="post" id="formExportCharcoals" >
    <div class="row">
        <div class="col-md-12">
            <fieldset class="cadre">
                <legend>Select data from list</legend>
                <p>
                    <label for="select_region">Region*</label>
                    <select name="select_region" id="select_region" required multiple>
                        <option value="NULL">Select a region</option>
                        <?php
                        $result_all_objects = Region::getStaticList();

                        //xli 25/03/16 tri selon le code GCD de la région
                        //but : lorsqu'on affiche la liste des régions (menu "export from list"
                        // on a l'un en dessous de l'autre Northern Pacific et Southern Pacific
                        // northern atlantic et Southern Atlantic    etc
                        $tab_order = array_column($result_all_objects, "REGION_GCD_CODE");
                        array_multisort($tab_order, SORT_ASC, $result_all_objects);
                        foreach ($result_all_objects as $object) {
                                echo "<option value='" . $object[Region::ID] . "'>" . htmlentities($object[Region::NAME]) . "</option>";
                        }
                        ?>
                    </select>
                </p>
                <p>
                    <label for="select_country">Country</label>
                    <select name="select_country" id="select_country" required multiple>
                        <option value="NULL">Select a country</option>
                    </select>
                </p>
                <p>
                    <label for="select_site">Site</label>
                    <select name="select_site" id="select_site" multiple>
                        <option value="NULL">Select a site</option>
                    </select>
                </p>
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

            $tabCBSamples = Array("cb_ds" => "Data source",
                    "cb_cm" => "Proxy fire method treatment",
                    "cb_pcu" => "Prefered proxy fire measurement unit",
                    "cb_db" => "Database version");
                    // "cb_am" => "Estimated age and age model");

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
                <p>
                    <label class="listCB">Data from sites</label>
                    <div>
                    <?php
                        foreach($tabCBSites as $key=>$elt){
                            echo '<p><input type="checkbox" name="cb_fields[]" value="'.$key.'"/>'.$elt.'</p>';
                        }
                    ?>
                    </div>
                </p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <p>
                    <label class="listCB">Data from cores</label>
                    <div>
                    <?php
                        foreach($tabCBCores as $key=>$elt){
                            echo '<p><input type="checkbox" name="cb_fields[]" value="'.$key.'"/>'.$elt.'</p>';
                        }
                    ?>
                    </div>
                </p>
            </div>
            <div class="clearfix visible-md-block visible-sm-block"></div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <p>
                    <label class="listCB">Data from samples</label>
                    <div>
                    <?php
                        foreach($tabCBSamples as $key=>$elt){
                            echo '<p><input type="checkbox" name="cb_fieldsSamples[]" value="'.$key.'"/>'.$elt.'</p/>';
                        }
                    ?>
                    </div>
                </p>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <p>
                    <label class="listCB">Data from dates info</label>
                    <div>
                    <?php
                        foreach($tabCBDateInfo as $key=>$elt){
                            echo '<p><input type="checkbox" name="cb_fieldsDateInfo[]" value="'.$key.'"/>'.$elt.'</p>';
                        }
                    ?>
                    </div>
                </p>
            </div>
        </div>
    </fieldset>
    <fieldset class="cadre">
        <legend style="width:500px">Choose a time interval (between -60 and 1500000 years)</legend>
          <p class="interval_time">
            <label for="interval_time">Interval time (yr cal BP)</label>
            <input id="interval_time_min" type="number" name="interval_time_min" min=-60 max=1500000 placeholder="Minimum value" value=-60 required>
            <input id="interval_time_max" type="number" name="interval_time_max" min=-60 max=1500000 placeholder="Maximum value" value=1500000 required><br>
          </p>
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
</form>
<script type="text/javascript">
    $("#select_region").change(
        function(){
            $("#err").hide();
            $("#err").text("");
            var url = "Pages/EDA/ajax_proxy_fire.php";
            url += "?action=country&region=" + $('#select_region').val();

            var selectCountry = $("#select_country").empty();
            selectCountry.append('<option value="null">Select a country</option>');

            $.getJSON(url, function(result){
                $.each(result, function(i, field){
                    selectCountry.append('<option value="'+ i +'">'+field+'</option>');
                });
            });
            var selectSite = $("#select_site").empty();
            selectSite.append('<option value="null">Select a site</option>');
        });

    $("#select_country").change(
        function(){
            $("#err").hide();
            $("#err").text("");
            var url = "Pages/EDA/ajax_proxy_fire.php";
            url += "?action=site&country=" + $('#select_country').val();
            var selectSite = $("#select_site").empty();
            $.getJSON(url, function(result){
                if (result != null) {
                    selectSite.append('<option value="null">Select a site</option>');
                    $.each(result, function(i, field){
                            selectSite.append('<option value="'+ i +'">'+field+'</option>');
                        });
                } else {
                    selectSite.append('<option value="null">No site</option>');
                    $("#err").show();
                    $("#err").text("The country " + $('#select_country option:selected').text() + " has no site");
                }
            });
        });

     function exportData(){
        $('#err').empty();
        $('#err').hide();
        // on vérifie que le champ pays au moins est saisi
        if ($('#select_country').val() === "NULL"){
            $('#err').text("A country must be selected");
            $('#err').show();
        } else {
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

            var url = "Pages/EDA/ajax_proxy_fire.php";


            url += "?action=export";
            url += "&region=" + $('#select_region').val();
            url += "&country=" + $('#select_country').val();
            url += "&site=" + $('#select_site').val();
            url += "&f=" + fields;
            url += "&fs=" + fields_samples;
            url += "&fd=" + fields_dateinfo;
            url += "&tmin=" + $('#interval_time_min').val();
            url += "&tmax=" + $('#interval_time_max').val();
            url += "&fn=" + $('#filename').val();
            window.open(url, '_self');
        }
     }

     $("#buttonClear").click(function(){
        polygonSelected.setMap(null);
        polygonSelected = null;
        $("#buttonClear").removeClass("active");
        $("#buttonClear").addClass("disabled");
        drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
    });

    $("#buttonSelectAll").click(function(){
        if ($(this).text() == "Select all fields"){
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
