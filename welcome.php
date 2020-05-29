<!DOCTYPE html>
<html lang="en">
<head>
  <title>Global Paleofire Database</title>
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

	/* set margin-top to x em  */
	.margintop {
		margin-top: 3em;
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
		<div id="img_paleo" align="center"><a href="http://paleofire.org" title="Paleofire"><img src="./images/paleofire.jpg" alt="Paleofire"></a></div>
	</div>
</nav>
  
<div class="container-fluid text-center">
	<h3>Welcome to the International Paleofire Network (IPN)</h3>
	<p>IPN aims to advance our understanding of the controls and impacts of fire in the Earth system on a wide range of spatial and temporal scales. IPN looks to meet the growing needs of interdisciplinary fire scientists and practitioners by developing research projects, in collaboration with stakeholders, that employ diverse data sources, an open-access database (GPD), statistical tools, and state-of-the-art models to address questions about fire-regime variations and their feedbacks on species, ecosystems, and climate. The Global Paleofire Database (GPD) is to provide the scientific community with a global paleofire dataset for research, documentation and archiving.</p>

	<div class="row content">
		<div class="col-sm-6 margintop">
			<b>click below to access the International Paleofire Network (2020-…)</b>
			<div id="logo_gpwg" class="margintop">
				<a href="https://ipn.paleofire.org" target="_blank" title="IPN">
					<img src="./images/logo/IPN-Logo-fullred.png" alt="IPN" width="500">
				</a><br />
				<p style="margin-top: 5em">the former GPWG blog is still available here :
					<a href="https://oldgpwg.paleofire.org" target="_blank" title="GPWG">
						<img src="./images/logo/gpwg.png" alt="GPWG" width="100">
					</a>
				</p>
			</div>

		</div>
		<div class="col-sm-6 margintop">
			<b>click below to access the Global Paleofire Database (2013-…)</b>
			<div id="bd_gcd" class="margintop">
				<a href="/index.php" target="_blank" title="GPD">
					<img src="./images/logo/database.png" alt="GPD" width="200">
				</a>
			</div>
		</div>
	</div>
</div>

<footer class="container-fluid text-center">
	All Rights Reserved (2015-<?php echo date("Y"); ?>). | Contact : <a id="contact">paleofire@gmail.com</a> |
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
