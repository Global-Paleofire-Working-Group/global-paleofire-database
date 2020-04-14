<?php

/*
 * 
 *  fichier /maintenance.php 
 */

require './config.php';
require_once(REP_LIB."scripts.php");
include_once(REP_LIB."connect_database.php");
include_once(REP_LIB."database.php");
require_once (REP_MODELS."/user/WebAppRoleGCD.php");
require_once (REP_MODELS."/user/WebAppPermGCD.php");
require_once (REP_MODELS."/user/WebAppUserGCD.php");
require_once (REP_LIB."/data_securisation.php");



?>
    <!DOCTYPE HTML>
    <html>
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />        

            <meta property="og:title" content="Global Charcoal Database" />
            <meta property="og:site_name" content="Global Charcoal Database" />
            <meta property="og:author" content="Global Charcoal Database" />
            <meta property="og:language" content="en" />

            <title>Global Paleofire Database</title>

            <link href="css/paleofire.css" rel="stylesheet" type="text/css" />
            <link href="css/bootstrap.css" rel="stylesheet" type="text/css" />
            <link href="css/form_paleofire.css" rel="stylesheet" type="text/css" />
            <link rel="shortcut icon" href="Images/Ico.gif" />

            <script type="text/javascript" src="Library/jquery-3.2.1.min.js"></script>
            <script type="text/javascript" src="Library/bootstrap.min.js"></script>
            <script type="text/javascript" src="Library/paleofire.js"></script>
        </head>
        <body>
          <div class="container" id="inner">
            <div class="container-fluid" style="top:100px">
                        <div class="row">
                            <div id="header">
                                <h1><a href="index.php">Global Paleofire Database</a></h1>
                                <div id="logo_gpwg"><a href="http://gpwg.paleofire.org" alt="GPWG" title="GPWG"><img src="./images/logo/gpwg_petit.png"></a></div>
                                <div id="splash"></div>
                            </div>
                        </div>
                        
            </div>
        <h2><b><font color="red">THE GPD IS UNDER MAINTENANCE - Back to normal planned on Friday, April 10th 2020 17h</font></b></h2>
        <h3>Welcome to the Global Paleofire Database</h3>
        <div>
            <p>The aim of the <b>Global Paleofire Database (GPD)</b> is to provide the scientific community with a global paleofire dataset for research and archiving sedimentary records of fire. The GCD is managed by the <b>Global Paleofire Working Group – <a href="http://gpwg.paleofire.org">GPWG</a></b>.
	        <p>The GPD is funded and supported by: the <a href="http://www.pages-igbp.org"><b>PAGES</b></a> initiative and the project OREAS by Région Bourgogne - Franche-Comté in France via the <b><a href="http://chrono-environnement.univ-fcomte.fr">Chrono-environnement laboratory</a></b>.
            <p>The science emerging from the <b>GPWG</b> is mainly:</p>
            <p>- the creation of a <b>public-access database</b> and an international research community with multiple-authored papers describing observed spatiotemporal changes in fire at global and regional scales (e.g. time series and maps).</p>
            <p>- <b>Global and regional syntheses</b> which enable the examination of broad-scale patterns in paleofire activity, creating a framework for exploring the <b>linkages among fire, Human, climate and vegetation</b> at centennial-to-multi-millennial time scales and allowing for <b>evaluation of fire model simulations</b> at regional to global scales.</p>                    
        </div>

        <div id="footer">
            All Rights Reserved (2015-<?php echo date("Y"); ?>). |
            Contact : <a id="contact">paleofire@gmail.com</a> |
            <?php //<a href="index.php?p=collaborateurs">Collaborators</a> |?> <a href="index.php?p=terms_of_use">Terms of use</a>
        </div>
              
        <script type="text/javascript">
        var add = "ma";
        add += "ilto:paleofire";
        add += "@";
        add += "gmail.com?";
        add += "Subject=Contact%20from%20term%20of%20use%20page";
        $("#contact").mouseover(function(){
            $(this).attr("href", add);
        });
        </script>

    </body>
</html>  
    
<?php


