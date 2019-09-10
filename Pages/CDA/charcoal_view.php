<?php
/* 
 * fichier Pages/CDA/charcoal_view.php 
 *  
 */ 

if (isset($_SESSION['started'])) {
    require './Models/Site.php';
    require_once './Models/Sample.php';
    require './Models/Age.php';
    require_once './Models/AgeUnits.php';

    require './Models/DataBaseVersion.php';

    $array_emails = array();
    ?>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js" type="text/javascript"></script>

    <ul class="nav">
        <li><a href="index.php?p=CDA/index&gcd_menu=CDA">Site</a></li>
        <li><a href="index.php?p=CDA/core&gcd_menu=CDA">Core</a></li>
        <li><a href="index.php?p=CDA/sample_list&gcd_menu=CDA">Sample</a></li>
    </ul>

    <?php
    $current_sample_id = null;
    if (isset($_GET['sample_id']) && is_numeric($_GET['sample_id'])) {
        $current_sample_id = $_GET['sample_id'];
    }

    if ($current_sample_id != null) {
        $sample = Sample::getObjectPaleofireFromId($current_sample_id);
        //$core = Core::getObjectPaleofireFromId($sample->_sample_core_id);
        $core_site_id = Core::getFieldValueFromWhere(Core::ID_SITE, sql_equal(Core::ID, $sample->_sample_core_id));
        $core_id = Core::getFieldValueFromWhere(Core::ID, sql_equal(Core::ID, $sample->_sample_core_id));
        $site_name = Site::getFieldValueFromWhere(Site::NAME, sql_equal(Site::ID, $core_site_id));


        $core_name = Core::getFieldValueFromWhere(Core::NAME, sql_equal(Core::ID, $sample->_sample_core_id));

        $is_charcoal_sample = false;
        ?>
        <div class="menuline"> </div>
        <table id="tableviewobject">
            <thead>            
                <tr>
                    <td class="subtitle">Sample</td>
                    <td><?php echo $sample->getName(); ?></td>
                    <td colspan="3"></td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="subtitle">Core</td>
                    <td><a href='index.php?p=CDA/core_view&gcd_menu=CDA&core_id=<?php echo $core_id; ?>'><?php echo $core_name; ?></a></td>
                    <td class="subtitle">Site</td>
                    <td><?php echo $site_name; ?></td>
                    <td> </td>
                </tr>
                <tr>
                    <td colspan="5">
                        <div class="desc">

                            <span class="subtitle">Default depth :</span>
                            <?php
                            //echo $sample->_default_depth->getName();
                            //echo " (" . $sample->_default_depth->getDepthType()->getName() . ")";
                            echo ": " . EstimatedAge::getFieldValueFromWhere(EstimatedAge::NAME, sql_equal(EstimatedAge::ID, $sample->_default_depth));
                            echo "<br/>";
                            ?>
                            <div class="menuline"> </div><br/>
                            <div id="map_sample"></div>
                            <script>
        <?php
        $lat = Core::getFieldValueFromWhere(Core::LATITUDE, sql_equal(Core::ID, $sample->_sample_core_id));
        ;
        $long = Core::getFieldValueFromWhere(Core::LONGITUDE, sql_equal(Core::ID, $sample->_sample_core_id));
        ?>
                                var gcdIcon = L.icon({
                                    iconUrl: './images/marker-icon-red.png',
                                    iconSize: [15, 23], // size of the icon
                                });

                                var map_sample;
                                /* initialisation de la fonction initMap */
                                function initMap() {
                                    // paramétrage de la carte
                                    map_sample = new L.Map('map_sample');
                                    // création des "tiles" avec open street map
                                    var osmUrl = 'http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
                                    var osmAttrib = 'Map data de OpenStreetMap';
                                    var osm = new L.TileLayer(osmUrl, {minZoom: 1, maxZoom: 10, attribution: osmAttrib});
                                    // on centre sur la France
                                    map_sample.setView(new L.LatLng(<?php echo $lat; ?>, <?php echo $long; ?>), 9);
                                    map_sample.addLayer(osm);
                                }
                                /* on va procéder à l'initialisation de la carte */
                                initMap();
                                var marker = L.marker([<?php echo $lat; ?>, <?php echo $long; ?>], {icon: gcdIcon});
                                map_sample.addLayer(marker);
                                marker.bindPopup("<b><?php echo $site_name; ?><br/>Lat : <?php echo $lat; ?>, Long : <?php echo $long; ?></b>");
                            </script>
                        </div>
                    </td>
                </tr>
                <?php
                $list_charcoals = $sample->getListCharcoals();
                if (!empty($list_charcoals)) {
                    ?>
                    <tr>
                        <td colspan="5" class="subtitle">
                            <div class="menuline"> </div>
                            List of Charcoal</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="desc">
                                <div>
                                    <table class="charcoal">


                                        <?php
                                        foreach ($list_charcoals as $charcoal) {
                                            $is_charcoal_sample = true;
                                            ?>
                                            <tr class="charcoal_title">
                                                <td>
                                                    Pref Units
                                                </td>
                                                <td>
                                                    Method
                                                </td>
                                                <td>
                                                    Size
                                                </td>
                                                <td>
                                                    Status
                                                </td>
                                                <td>
                                                    Data source
                                                </td>
                                            </tr>                
                                            <tr>
                                                <td>
                                                    <?php echo CharcoalUnits::getFieldValueFromWhere(CharcoalUnits::NAME, sql_equal(CharcoalUnits::ID, $charcoal->_charcoal_charcoal_units_id)); ?>
                                                </td>
                                                <td>
                                                    <?php echo CharcoalMethod::getFieldValueFromWhere(CharcoalMethod::NAME, sql_equal(CharcoalMethod::ID, $charcoal->_charcoal_method_id)); ?>
                                                </td>
                                                <td>
                                                    <?php echo CharcoalSize::getFieldValueFromWhere(CharcoalSize::NAME, sql_equal(CharcoalSize::ID, $charcoal->_charcoal_size_id)); ?>
                                                </td>
                                                <td>
                                                    <?php echo Status::getFieldValueFromWhere(Status::NAME, sql_equal(Status::ID, $charcoal->_charcoal_status_id)); ?>
                                                </td>
                                                <td>
                                                    <?php echo DataSource::getFieldValueFromWhere(DataSource::NAME, sql_equal(DataSource::ID, $charcoal->_charcoal_datasource_id)); ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Charcoal Quantities
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    foreach ($charcoal->getListCharcoalQuantities() as $charcoal_quantity) {
                                                        echo $charcoal_quantity->_charcoal_quantity_value;
                                                        echo " (" . CharcoalUnits::getFieldValueFromWhere(CharcoalUnits::NAME, sql_equal(CharcoalUnits::ID, $charcoal_quantity->_charcoal_unit_id)) . ")";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Author
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    echo Contact::getFieldValueFromWhere(Contact::NAME, sql_equal(Contact::ID, $charcoal->_charcoal_contact_id));
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Contributors
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    foreach ($charcoal->getListContributors() as $contrib) {
                                                        echo $contrib->getLastName();
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Publications
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    foreach ($charcoal->getListPublications() as $publi) {
                                                        echo $publi->getName();
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    GCD Database
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    echo DataBaseVersion::getFieldValueFromWhere(DataBaseVersion::NAME, sql_equal(DataBaseVersion::ID, $charcoal->_charcoal_database_id));
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                    </table>
                                </div>
                            </div> 
                        </td>
                    </tr>
                    <?php
                }
                unset($list_charcoals);
                ?>
                <?php
                if (!$is_charcoal_sample) {
                    ?>
                    <tr>
                        <td colspan="5" class="subtitle">
                            <div class="menuline"> </div>
                            Age model</td>
                    </tr>
                    <tr>
                        <td colspan="5">
                            <div class="desc">
                                <div>
                                    <table class="age_model">
                                        <?php
                                        if ($sample->_sample_age_model == null) {
                                            echo "No age model";
                                        } else {
                                            ?>
                                            <tr class="age_model_title">
                                                <td>
                                                    Age model Version
                                                </td>
                                                <td>
                                                    Method
                                                </td>
                                                <td>
                                                    Core
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <?php echo $sample->_sample_age_model->getName(); ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if (isset($sample->_sample_age_model->_age_model_method)) {
                                                        echo $sample->_sample_age_model->_age_model_method->getName() . "(" . $sample->_sample_age_model->_age_model_method->_age_model_gcd_code . ")";
                                                    } else {
                                                        echo "Error : No Age Model method";
                                                    }
                                                    ?>
                                                </td>

                                                <td><a href='index.php?p=CDA/core_view&gcd_menu=CDA&core_id=<?php echo $core_id; ?>'><?php echo $core_name; ?></a></td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Notes
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    foreach ($sample->_sample_age_model->_age_model_notes as $age_model_note) {
                                                        echo isset($age_model_note->_site_note_who) ? "Who : " . $age_model_note->_site_note_who : "Who : null";
                                                        echo isset($age_model_note) ? ", What : " . $age_model_note->getName() : ", What : null";
                                                        echo isset($age_model_note->_site_note_date) ? ", Date : " . $age_model_note->_site_note_date : ", Date : null";
                                                        echo "</br>";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Estimated Age
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    $_list_estimated_age = EstimatedAge::getObjectsPaleofireFromWhere(sql_equal(EstimatedAge::ID_SAMPLE, $sample->getIdValue()));
                                                    foreach ($_list_estimated_age as $est_age) {
                                                        echo $est_age->_est_age . "(Depth : ";
                                                        echo isset($est_age->_depth->_depth_value) ? $est_age->_depth->_depth_value . ")" : "no depth)";
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="subtitle">
                                                    Has age
                                                </td>
                                                <td colspan="4">
                                                    <?php
                                                    echo "</br>";
                                                    echo "<div>";
                                                    echo "<table border='1'>";
                                                    echo "<tr><td>Value</td><td>+</td><td>-</td><td>Units</td><td>Calibration method</td><td>Date</td></tr>";
                                                    $result_ages = $sample->getQueryAges();
                                                    while ($values = fetchAssoc($result_ages)) {
                                                        echo "<tr>";
                                                        echo "<td>" . $values["AGE_VALUE"] . "</td>";
                                                        //echo "<td>" . $values["AGE_POSITIVE_ERROR"] . "</td>";
                                                        //echo "<td>" . $values["AGE_NEGATIVE_ERROR"] . "</td>";
                                                        echo isset($values["ID_AGE_UNITS"]) ? "<td>" . AgeUnits::getFieldValueFromWhere(AgeUnits::NAME, sql_equal(AgeUnits::ID, $values["ID_AGE_UNITS"])) : "<td>null";
                                                        echo "</td>";
                                                        echo isset($values["ID_CALIBRATION_METHOD"]) ? "<td> " . CalibrationMethod::getFieldValueFromWhere(CalibrationMethod::NAME, sql_equal(CalibrationMethod::ID, $values["ID_CALIBRATION_METHOD"])) : "<td> null";
                                                        echo "</td>";
                                                        echo isset($values["ID_DATE_INFO"]) ? "<td>" . DateInfo::getFieldValueFromWhere(DateInfo::NAME, sql_equal(DateInfo::ID, $values["ID_DATE_INFO"])) : "<td>null";
                                                        echo "</td>";
                                                        echo "</tr>";
                                                    }
                                                    freeResult($result_ages);
                                                    echo "</table>";
                                                    echo "</div>";
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>

                                    </table>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5"></td>
                </tr>          
            </tfoot>
        </table>
        <?php
        unset($sample);
        unset($site_name);
    }
}