<!DOCTYPE html>
<html lang="en">
<head>
	<title>Paleofire</title>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	<style>
		/* Remove the navbar's default margin-bottom and rounded borders */
		.navbar {
			margin-bottom: 0;
			border-radius: 0;
		}

		/* Set height of the grid so .sidenav can be 100% (adjust as needed) */
		.row.content {height: 450px}

		/* Set gray background color and 100% height */
		.sidenav {
			padding-top: 20px;
			background-color: #f1f1f1;
			height: 100%;
		}

		/* Set black background color, white text and some padding */
		footer {
			background-color: #555;
			color: white;
			padding: 15px;
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
		<div id="img_paleo" align="center"><a href="http://www.paleofire.org" alt=PALEO title="Paleofire"><img src="./images/paleofire.jpg"></a></div>
	</div>
	</div>
</nav>

<div class="container-fluid text-center">
	</br> In the past decade, the Global Paleofire Working Group (GPWG) developed and analysed fire history records using the Global PaleoFire Database (GPFD), which advanced our understanding of the controls and impacts of fire in the Earth system on a wide range of spatial and temporal scales.

	<div class="row content">
		<div class="col-sm-6">
			<br /> <br />
			The GPWG will meet the growing needs of interdisciplinary fire scientists and practitioners by developing research projects, in collaboration with stakeholders, that employ diverse data sources, a new open-access database, statistical tools, and state-of-the-art models to address questions about fire-regime variations and their feedbacks on species, ecosystems, and climate.
			<br /><br /> <b>Click below to access the GPWG</b>
			<div id="logo_gpwg"><a href="http://gpwg.paleofire.org" target="_blank" alt="GPWG" title="GPWG"><img src="./images/logo/gpwg.png"></a></div>

		</div>
		<div class="col-sm-6 ">
			<br /> <br />
			<p>The aim of GPFD is to provide the scientific community with a global paleofire dataset for research and archiving sedimentary records of fire. </p>
			<br />
			<b>Click below to access the GPFD</b>
			<br /> <br />
			<div id="bd_gcd"><a href="/index.php" target="_blank" alt="GPFD" title="GPFD"><img src="./images/logo/database.png"></a></div>
			<br /><br />

		</div>
	</div>
</div>

<footer class="container-fluid text-center">
	All Rights Reserved (2015-<?php echo date("Y"); ?>). |
	Contact : <a id="contact">paleofire@gmail.com</a> |

	</div>
	<script type="text/javascript">
		var add = "ma";
		add += "ilto:paleofire";
		add += "@";
		add += "gmail.com?";
		add += "Subject=Contact%20from%20main%20page";
		$("#contact").mouseover(function(){
			$(this).attr("href", add);
		});
	</script>
</footer>

</body>
</html>