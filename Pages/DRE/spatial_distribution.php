<?php
/*
 *  fichier \Pages\DRE\spatial_distribution.php
 * 
 */

/* XLI 02/03/16 modification des graphiques par ajout de légendes et d'une séparation afin de ne pas les confondre*/ 

if (isset($_SESSION['started'])) {
    
    require_once './Models/Site.php';
    require_once './Models/DistributionStatistique.php';
    require_once'./Pages/Common/PaleofireHtmlUtilities.php';
     

    $objSites = Site::getNbSitesByCountry();
    $objRegion = Region::getRepartitionSites();
    $nbSites = Site::countPaleofireObjects();
?>

<div class="alert alert-info" role="alert">Charts created from a <strong>temporary and non-validated version</strong> of the global charcoal database</div>
<div class="panel panel-default">
    <div class="panel-heading">
        <h3 class="panel-title"><center>Spatial distribution of sites (<?php echo $nbSites ?> sites)</center></h3>
       
    </div>
    <div class="panel-body" } >
        
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6 center" style="border-right:  thin dotted grey"><b><center>Number of sites by country</center></b><div id="regions_div" style="height: 300px;"></div> </div>
                <div class="col-md-6 center" ><b><center>Number of sites by region</center></b><div id="piechart_region" style="height: 300px;"></div> </div>
            </div>
        </div>
    </div>
</div>

<?php  // script google pour les geochart et les piechart ?>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
    
<?php // affichage piechart puis geochart?>
<script type="text/javascript">

  google.load("visualization", "1", {packages:["corechart", "geochart"]});
  google.setOnLoadCallback(drawCharts);

  // affichage des piecharts
  function drawChart() {
    var data;
    var options;

    // affichage du graphique du nb site par régions
    data = google.visualization.arrayToDataTable(<?php echo $objRegion->tabNbParElt?>);
    options = {'chartArea':{'width':'100%', 'height':'80%'}};
    var chart_region = new google.visualization.PieChart(document.getElementById('piechart_region'));
    chart_region.draw(data, options);
  }

  // affichage geochart
  function drawRegionsMap() {
    var data = google.visualization.arrayToDataTable(<?php echo $objSites->tab; ?>);
    var options = { colorAxis: {minValue: 0,  colors: ['#F6CECE', '#8A0808']}};
    var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));
    chart.draw(data, options);
  }

  // affichage de tous les graphiques
  function drawCharts(){
      drawRegionsMap();
      drawChart();
  }
</script>
<?php
}