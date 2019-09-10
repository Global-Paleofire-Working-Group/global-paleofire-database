<?php
/*
 *  fichier \Pages\DRE\chrono_distribution.php
 * 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
        require_once './Models/DistributionStatistique.php';
    require_once'./Pages/Common/PaleofireHtmlUtilities.php';
    
    $objplus50000 = DistributionStatistique::getRepartitionSitesChronologique(null, 50000);
    $obj50000a20000 = DistributionStatistique::getRepartitionSitesChronologique(50000, 20000);
    $obj20000a10000 = DistributionStatistique::getRepartitionSitesChronologique(20000, 10000);
    $obj10000a5000 = DistributionStatistique::getRepartitionSitesChronologique(10000, 5000);
    $obj5000a1000 = DistributionStatistique::getRepartitionSitesChronologique(5000, 1000);
    $obj1000a200 = DistributionStatistique::getRepartitionSitesChronologique(1000 , 200);
    $obj200a0 = DistributionStatistique::getRepartitionSitesChronologique(200, 0);
    
    $nbEchelleMax = max(Array($objplus50000->nbMaxSiteParElt, $obj50000a20000->nbMaxSiteParElt, $obj20000a10000->nbMaxSiteParElt, $obj10000a5000->nbMaxSiteParElt, $obj5000a1000->nbMaxSiteParElt, $obj1000a200->nbMaxSiteParElt, $obj200a0->nbMaxSiteParElt));
    
    $tabPhaseAAfficher = Array(
        "50000" => Array("older than 50000", $objplus50000, "#003D7A"),
        "20000" => Array("from 50000 to 20000", $obj50000a20000, "#0052A3"),
        "10000" => Array("from 20000 to 10000", $obj20000a10000, "#0066CC"),
        "5000" => Array("from 10000 to 5000", $obj10000a5000, "#3385D6"),
        "1000" => Array("from 5000 to 1000", $obj5000a1000, "#66A3E0"),
        "200" => Array("from 1000 to 200", $obj1000a200, "#99C2EB"),
        "1" => Array("from 200 to 0", $obj200a0, "#CCE0F5"),
    );
    ?>
    
    <?php  // script pour les geochar et les corechart?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    
    <?php //script d'affichage des cartes avec statistiques et du diagramme en colonne  ?>     
    <script type="text/javascript">
      google.load("visualization", "1", {packages:["corechart", "geochart"]});
      google.setOnLoadCallback(drawGoogleVisualization);
      
      function drawCoreChart() {
        var data = google.visualization.arrayToDataTable([
            ["Periode", "Number of site", { role: "style" } ],
            <?php 
                foreach( $tabPhaseAAfficher as $key=>$elt){
                    echo '["'. $elt[0] . '", '. $elt[1]->nbSites .', "' . $elt[2] . '"],';
                }
            ?>
        ]);
        var chart = new google.visualization.ColumnChart(document.getElementById("core_chart"));
        chart.draw(data);
      }
      
      var options = {
            colorAxis: {minValue: 0, maxValue:<?php echo $nbEchelleMax; ?>,  colors: ['#F6CECE', '#8A0808']}
      };
      var data = [];
      var chart = [];
      var chartIsDrawn = [];

      function drawRegionsMap() {
        <?php 
            foreach($tabPhaseAAfficher as $key=>$elt){
                echo "data[".$key."] = google.visualization.arrayToDataTable(".$elt[1]->tabNbParElt.");\n";
                echo "chart[".$key."] = new google.visualization.GeoChart(document.getElementById('regions_div_".$key."'));\n";
            }
        ?>
            displayPeriode('50000');
      }
      
     function displayPeriode($elt){
          $(".panel_chrono").hide();
          $("#panel_"+$elt).show();
          if (chartIsDrawn.indexOf($elt) == -1){
              chart[parseInt($elt)].draw(data[$elt], options);
              chartIsDrawn.push($elt);
          }
      }
      
      function drawGoogleVisualization(){
        drawCoreChart();  
        drawRegionsMap();
      }
    </script>
  <div class="alert alert-info" role="alert">Charts created from a <strong>temporary and non-validated version</strong> of the global charcoal database</div>
  <div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Chronological distribution of sites</h3>
    </div>
    <div class="panel-body">
        <div id="core_chart"></div>
    </div>
 </div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title">Spatial distribution of sites by periode</h3>
    </div>
    <div class="panel-body">
        <div class="progress">
            <?php 
                foreach($tabPhaseAAfficher as $key=>$elt){
                    echo '<a href="javascript:displayPeriode(\''.$key.'\');">';
                    echo '<div class="progress-bar" style="width: 14.28%;background-color: '.$elt[2].'">';
                    echo $elt[0];
                    echo '  </div>
                          </a>';
                }
            ?>
        </div>
        <?php
            foreach($tabPhaseAAfficher as $key => $elt){
                echo '<div class="panel_chrono" id="panel_'.$key.'" style="display:none">
                        <div id="regions_div_'.$key.'" style="height: 300px;"></div>
                      </div>'."\n";
            }
        ?>
    </div>
</div>
 <?php   }