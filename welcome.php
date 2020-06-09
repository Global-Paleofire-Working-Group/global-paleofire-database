<!DOCTYPE html>
<html lang="en">
<head>
	<title>Global Paleofire Database</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<link rel="icon" href="./images/logo/Logo-Paleofire-Rainbow-Circle.ico" />
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
		body {
			width: 1200px;
			margin-left: auto;
			margin-right: auto;
		}
		/* Remove the navbar's default margin-bottom and rounded borders */
		.navbar {
		  margin-bottom: 0;
		  border-radius: 0;
		}

		#content {
			background-color: #ffffff;
		}

		.link {
			color: #2aabd2;
		}

		/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
		.row.content {height: 450px}

		/* Set gray background color and 100% height */
		.sidenav {
		  padding-top: 20px;
		  background-color: #f1f1f1;
		  height: 100%;
		}

		/* set margin-top to 3 em  */
		.margintop {
			margin-top: 3em;
		}

		/* Set black background color, white text and some padding */
		footer {
			background-color: #555;
			color: white;
			padding: 15px;
			display: flex;
			align-items: center;
		}

		/* On small screens, set height to 'auto' for sidenav and grid */
		@media screen and (max-width: 767px) {
		  .sidenav {
			height: auto;
			padding: 15px;
		  }
		  .row.content {height:auto;}
		}
	</style>
</head>
<body>

<nav class="navbar navbar-inverse">
	<div class="container-fluid">
		<div id="img_paleo" align="center"><a href="http://paleofire.org" title="Paleofire"><img src="./images/paleofire.jpg" alt="Paleofire"></a></div>
	</div>
</nav>
  
<div id="content" class="container">
	<h3>Welcome to the International Paleofire Network (IPN)</h3>
	<p>IPN aims to advance our understanding of the controls and impacts of fire in the Earth system on a wide range of spatial and temporal scales.</p>
	<p>IPN looks to meet the growing needs of interdisciplinary fire scientists and practitioners by developing research projects, in collaboration with stakeholders, that employ diverse data sources, an open-access database (GPD), statistical tools, and state-of-the-art models to address questions about fire-regime variations and their feedbacks on species, ecosystems, and climate.</p>
	<p>The Global Paleofire Database (GPD) is to provide the scientific community with a global paleofire dataset for research, documentation and archiving.</p>

	<div class="row content text-center">
		<div class="col-sm-6 margintop">
			<b>click below to access the International Paleofire Network</b>
			<div id="logo_gpwg" class="margintop">
				<a href="https://ipn.paleofire.org" target="_blank" title="IPN">
					<img src="./images/logo/IPN-Logo-transparent.png" alt="IPN" height="100">
				</a><br />
				<p style="margin-top: 5em">the former GPWG blog is still available here:
					<a href="https://oldgpwg.paleofire.org" target="_blank" title="GPWG">
						<img src="./images/logo/gpwg.png" alt="GPWG" width="100">
					</a>
				</p>
			</div>

		</div>
		<div class="col-sm-6 margintop">
			<b>click below to access the Global Paleofire Database</b>
			<div id="bd_gcd" class="margintop">
				<a href="/index.php" target="_blank" title="GPD">
					<img src="./images/logo/GPD-Logo-transparent.png" alt="GPD" height="100">
				</a>
			</div>
		</div>
	</div>
</div>

<footer class="container">
	<div class="col-sm-12 text-center">
	All Rights Reserved (2015-<?php echo date("Y"); ?>). | Contact: <a id="contact" class="link">contact@paleofire.org</a>
		| Technology support: <a href="https://mshe.univ-fcomte.fr/" class="link">MSHE</a> - <a href="http://www.univ-fcomte.fr/les-services-administratifs/services-informatiques" class="link">DSIN</a> - <a href="https://univ-fcomte.fr/" class="link">Université de Franche-Comté</a>
		| Admin: <a id="contact2" class="link">admin@paleofire.org</a>
	<script type="text/javascript">
		var add = "ma";
		add += "ilto:contact";
		add += "@";
		add += "paleofire.org?";
		add += "Subject=Contact%20from%20main%20page";
		$("#contact").mouseover(function(){
			$(this).attr("href", add);
		});
		var add2 = "ma";
		add2 += "ilto:admin";
		add2 += "@";
		add2 += "paleofire.org?";
		add2 += "Subject=Contact%20from%20main%20page";
		$("#contact2").mouseover(function(){
			$(this).attr("href", add2);
		});
	</script>
	</div>
</footer>

</body>
</html>
