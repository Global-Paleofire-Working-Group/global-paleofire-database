<?php
/*
 *  fichier \Pages\DRE\data_quality.php
 * 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/DistributionStatistique.php';
    require_once'./Pages/Common/PaleofireHtmlUtilities.php';
    ?>
    <div class="alert alert-info" role="alert">Charts created from a <strong>temporary and non-validated version</strong> of the global charcoal database</div>
    <?php
    
    $tabTableAAfficher = Array(
        "basinSize" => Array("Sites with basin size data", BasinSize::getDataQuality(), "sites for basin size", BasinSize::getRepartitionSites(), "sites by basin size"),
        "biome" => Array("Sites with biome data", BiomeType::getDataQuality(), "sites for biome", BiomeType::getRepartitionSites(), "sites by biome"),
        "catchSize" => Array("Sites with catchment size data", CatchSize::getDataQuality(), "sites for catch size", CatchSize::getRepartitionSites(), "sites by catch size"),
        "flowType" => Array("Sites with flow type data", FlowType::getDataQuality(), "sites for flow type", FlowType::getRepartitionSites(), "sites by flow type"),
        "landDesc" => Array("Sites with land description data", LandsDesc::getDataQuality(), "sites for land description", LandsDesc::getRepartitionSites(), "sites by land description"),
        "typeSite" => Array("Sites with type site data", SiteType::getDataQuality(), "sites for type site", SiteType::getRepartitionSites(), "sites by type site"),
        "coreType" => Array("Core with core type data", CoreType::getDataQuality(), "cores for core type", CoreType::getRepartition(), "cores by core type"),
        "depoContext" => Array("Core with depot context data", DepoContext::getDataQuality(), "cores for depot context", DepoContext::getRepartition(), "cores by depot context")
    );

    foreach($tabTableAAfficher as $key => $elt){
        echo '<div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">'.$elt[0].'</h3>
                </div>
                <div class="panel-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6"><div id="piechart_'.$key.'" style="height: 300px;"></div></div>
                            <div class="col-md-6"><div id="piechart_rep_'.$key.'" style="height: 300px;"></div></div>
                        </div>
                    </div>
                </div>
            </div>';
    }
    ?>
    
    <?php  // script google pour les geochart et les piechart ?>
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    
    <?php // affichage piechart puis geochart?>
    <script type="text/javascript">
        
      google.load("visualization", "1.1", {packages:["corechart"]});
      google.setOnLoadCallback(drawCharts);
      
      // affichage des piecharts
      function drawChart() {
        var data;
        var options;
        <?php 
            foreach($tabTableAAfficher as $key => $elt){
                $listeData = $elt[1];
                echo 'var tab = '.$listeData->tabNbParElt.";\n";
                echo 'data = google.visualization.arrayToDataTable(tab);'."\n";
                echo "if (tab.length == 2 && tab[1][0] === 'undocumented')\n";
                    echo "{\n options = {title : 'Documented ".$elt[2]."', 
                            'slices' : {0: {color: '#dc3912'}}, 'chartArea' : {'width':'100%', 'height':'80%'}};\n";
                    echo " $('#piechart_rep_".$key."').hide();\n }\n";
                    echo "else options = {title: 'Percent of documented ".$elt[2]."', 'chartArea':{'width':'100%', 'height':'80%'}};\n";
                echo "var chart".$key." = new google.visualization.PieChart(document.getElementById('piechart_".$key."'));\n";
                echo "chart".$key.".draw(data, options);\n\n";
            }  
        ?>
        
        $("#tabstats ul li").first().addClass("active");
        $("#tabstats div.tab-content div.tab-pane").first().addClass("active");
      }
      // affiche les diagramme camembert avec la répartition par catégorie
      function drawDistributionChart() {
        var data;
        var options;
        <?php 
            foreach($tabTableAAfficher as $key => $elt){
                $listeData = $elt[3];
                echo 'data = google.visualization.arrayToDataTable('.$listeData->tabNbParElt.');'."\n";
                //echo "options = {'title': 'Number of sites by ".$elt[2]."', 'width':500, 'height':380, 'chartArea':{'width':'80%', 'height':'80%'}};\n";
                echo "options = {'title': 'Percent of ".$elt[4]."', "
                        . "'chartArea':{'width':'100%', 'height':'80%'}, "
                        . "'colors' : ['#ff9900','#109618','#990099','#0099c6','#dd4477','#66aa00','#b82e2e',"
                        . "'#316395','#994499','#22aa99','#aaaa11','#6633cc','#e67300','#8b0707','#651067','#329262','#5574a6','#3b3eac',"
                        . "'#b77322','#16d620','#b91383','#f4359e','#9c5935','#a9c413','#2a778d','#668d1c','#bea413','#0c5922','#743411'] "
                        . "};\n";
                echo "var chart".$key." = new google.visualization.PieChart(document.getElementById('piechart_rep_".$key."'));\n";
                echo "chart".$key.".draw(data, options);\n\n";
            }  
        ?>
       }
      
      // affichage de tous les graphiques
      function drawCharts(){
          drawChart();
          drawDistributionChart();
      }
    </script>
    <?php
}
