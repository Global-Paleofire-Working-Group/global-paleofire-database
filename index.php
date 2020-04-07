<?php

/*
 *  fichier /index.php 
 */
require './config.php';

if ($_SERVER['REQUEST_URI'] == "/"){
	include ("welcome.php");
	exit();
}

if(MAINTENANCE == TRUE){
	header("Location: http://" . $_SERVER["HTTP_HOST"] . "/maintenance.php");
	exit();
}
session_start();

require_once(REP_LIB."scripts.php");
include_once(REP_LIB."connect_database.php");
include_once(REP_LIB."database.php");
require_once (REP_MODELS."/user/WebAppRoleGCD.php");
require_once (REP_MODELS."/user/WebAppPermGCD.php");
require_once (REP_MODELS."/user/WebAppUserGCD.php");
require_once (REP_LIB."/data_securisation.php");

// si on est en production
// et si on est administrateur ou contributeur ou sur la page de login
// et si on est pas en https
// on force le cryptage des pages en https
if (ENVIRONNEMENT == "SERVEUR-PROD"){
	if ((isset($_SESSION['gcd_user_role']) &&
			($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR)) ||
		$_SERVER["REQUEST_URI"] == "/index.php?p=login") {
		if(empty($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on")
		{
			header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
			exit();
		}
	}
}
$path = $_SERVER["DOCUMENT_ROOT"] . GLOBAL_RACINE . 'Library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

$_SESSION['started'] = 1;
$path = './Library';
set_include_path(get_include_path() . PATH_SEPARATOR . $path);



/*try{*/
// Page à inclure dans le cadre de l'index
if (isset($_GET['p'])) {
	$page = $_GET['p'];
	// On récupère le nom de la page pour désactiver les onglets sur les pages générales comme l'accueil, la messagerie ou l'aPropos
	if (substr_count($page, "/") > 0) {
		// Si c'est un chemin, on récupère le mot de : égal au premier slash
		$finPage = substr($page, strrpos($page, "="), strrpos($page, "/"));
	} else {
		// Si c'est simplement une page (pas de chemin), on découpe à partir de égal
		$finPage = substr($page, strrpos($page, "="));
	}

	if (($finPage == "Messagerie") || ($finPage == "home") || ($finPage == "aPropos") || ($finPage == "contact") || ($finPage == "conditionsUtilisation") || $finPage == "collaborateurs" || $finPage == "login") {
		$_SESSION['current_gcd_menu'] = "";
	}

	if ($finPage == "EDA" &&
		(isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::SUPERADMINISTRATOR && $_SESSION['gcd_user_role'] != WebAppRoleGCD::ADMINISTRATOR && $_SESSION['gcd_user_role'] != WebAppRoleGCD::CONTRIBUTOR)){
		$page = "home";
	}
	if ($finPage == "ADA" &&
		(isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::SUPERADMINISTRATOR && $_SESSION['gcd_user_role'] != WebAppRoleGCD::ADMINISTRATOR && $_SESSION['gcd_user_role'] != WebAppRoleGCD::CONTRIBUTOR)){
		$page = "home";
	}

	$page_undefined = false;
} else {
	$page_undefined = true;
}

if (!isset($_SESSION['gcd_user_role'])) {
	$_SESSION['gcd_user_role'] = WebAppRoleGCD::VISITOR;
}

if ($page_undefined || preg_match("/\./", $page)) {
	$page = "home";
}

// RM plus de securite
if (!preg_match('/^[a-zA-Z0-9\/_]*$/', $page) || (strpos($page, 'old_') !== false)) {// die('Secu');
	$page = "404";
}


// On teste l'existence de la page
if (!file_exists('./Pages/' . $page . '.php')) {
	$page = "404";
}

$footer = "&nbsp;";
?>
	<!DOCTYPE HTML>
	<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

		<meta property="og:title" content="Global Paleofire Database" />
		<meta property="og:site_name" content="Global Paleofire Database" />
		<meta property="og:author" content="Global Paleofire Database" />
		<meta property="og:language" content="en" />

		<title>Global Paleofire Database</title>


		<link href="css/paleofire.css" rel="stylesheet" type="text/css" />
		<link href="Library/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
		<link href="css/form_paleofire.css" rel="stylesheet" type="text/css" />
		<link rel="shortcut icon" href="Images/Ico.gif" />
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" />
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.5.1/dist/leaflet.css" />
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>

		<script type="text/javascript" src="Library/jquery-3.2.1.min.js"></script>
		<script type="text/javascript" src="Library/bootstrap/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="Library/paleofire.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	</head>
	<body>
	<?php
	if (ENVIRONNEMENT != "SERVEUR-PROD"){
		echo '<span class="badge badge-info">Environnement : '.ENVIRONNEMENT.'  Server : '.$_SERVER['SERVER_NAME'].'</span>';
	}
	?>
	<div class="container" id="inner">
		<div class="container" style="top:100px">
			<div class="row">
				<div id="header">
					<h1><a href="index.php">Global PaleoFire Database</a></h1>
					<div id="logo_gpwg"><a href="http://gpwg.paleofire.org" alt="GPWG" title="GPWG"><img src="./images/logo/gpwg_petit.png"></a></div>
					<div id="splash"></div>
				</div>
			</div>
			<div class="row">
				<nav class="navbar navbar-default" id="bar">
					<!-- Brand and toggle get grouped for better mobile display -->
					<div class="navbar-header">
						<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
							<span class="sr-only">Toggle navigation</span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
							<span class="icon-bar"></span>
						</button>
					</div>

					<!-- Collect the nav links, forms, and other content for toggling -->
					<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
						<ul class="nav navbar-nav">
							<li><a href="index.php">Home</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">View data<span class="caret"></span></a>
								<ul class="dropdown-menu" role="menu">
									<?php /*if (($_SESSION['gcd_user_role'] != WebAppRoleGCD::ADMINISTRATOR) && ($_SESSION['gcd_user_role'] != WebAppRoleGCD::SUPERADMINISTRATOR)){
                                            connectionBaseVersionned();
                                        }*/
									?>
									<li><a href="index.php?p=CDA/index&gcd_menu=CDA">All cores (and sites)</a></li>
									<li class="divider"></li>
									<li><a href="index.php?p=DRE/spatial_distribution&gcd_menu=DRE">Charts of spatial distribution</a></li>
									<li><a href="index.php?p=DRE/chrono_distribution&gcd_menu=DRE">Charts of chronological distribution</a></li>
									<li><a href="index.php?p=DRE/data_quality&gcd_menu=DRE">Charts of data quality</a></li>
								</ul>
							</li>
							<?php
							if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
								if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR){
									connectionBaseVersionned();
								}

								echo '<li class="dropdown">
                                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Export<span class="caret"></span></a>
                                                <ul class="dropdown-menu" role="menu">
                                                    <li><a href="index.php?p=EDA/export_charcoals&gcd_menu=EDA">Export from list</a></li>
                                                    <li><a href="index.php?p=EDA/export_charcoals_from_map&gcd_menu=EDA">Export from map</a></li>
                                                    <li><a href="index.php?p=EDA/export_databases&gcd_menu=EDA">Export full databases</a></li>
                                                    <li><a href="index.php?p=exportceed">Central East European Database : CEE-GCD-2020_Feurdean_et_al_2020</a></li>
                                                </ul>
                                            </li>';
							}
							?>
							<?php
							if ((isset($_SESSION['gcd_user_role'])) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){

								if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR){
									connectionBaseInProgress();
								}

								echo '<li class="dropdown">
																	   <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Add data<span class="caret"></span></a>
																	   <ul class="dropdown-menu" role="menu">
																		   <li><a href="index.php?p=ADA/add_contact&gcd_menu=ADA">Add a new contact</a></li>
																		   <li><a href="index.php?p=ADA/add_affiliation&gcd_menu=ADA">Add a new affiliation</a></li>
																		   <li><a href="index.php?p=ADA/add_publi&gcd_menu=ADA">Add a new publication</a></li>
																		   <li class="divider"></li>
																		   <li><a href="index.php?p=ADA/add_site&gcd_menu=ADA">Add a new site</a></li>
																		   <li><a href="index.php?p=ADA/add_core&gcd_menu=ADA">Add a new core</a></li>
																		   <li><a href="index.php?p=ADA/add_note_core&gcd_menu=ADA">Add a new note about a core</a></li>
																		   <li class="divider"></li>
																		   <li><a href="index.php?p=ADA/add_charcoals&gcd_menu=ADA">Add new charcoal data</a></li>
																		   <li><a href="index.php?p=ADA/add_age_model&gcd_menu=ADA">Add a new age model</a></li>
																		   <li><a href="index.php?p=ADA/add_date_info&gcd_menu=ADA">Add a new date info</a></li>
																		   <li><a href="index.php?p=ADA/add_note_age_model&gcd_menu=ADA">Add a new note about an age model</a></li>
																	   </ul>
																   </li>';
								// }
							}
							?>
							<?php
							if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::CONTRIBUTOR ||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
								// on récupère l'ID du user
								$user_id = $_SESSION['gcd_user_id'];
								// on récupère l'ID du contact
								connectionBaseWebapp();
								$contact_id = WebAppUserGCD::getContactId($user_id);
								connectionBaseInProgress(); //xli 4/4/16


								echo '<li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Account data<span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                          <li><a href ="index.php?p=ADA/edit_contact&gcd_menu=ADA&id='.data_securisation::tohtml($contact_id).'">Edit contact data</a></li>
                                          <li><a href="index.php?p=ADA/add_edit_user&gcd_menu=ADA&id='.data_securisation::tohtml($user_id).'">Edit login or password</a></li>
                                        </ul>
                                      </li>';
							}
							?>

							<?php
							if (isset($_SESSION['gcd_user_role']) && (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR))){
								echo '<li class="dropdown">  
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Edit Data<span class="caret"></span></a>
                                    <ul class="dropdown-menu" role="menu">
                                            <li><a href="index.php?p=CDA/site_view_list&gcd_menu=CDA">Sites</a></li>
                                            <li><a href="index.php?p=CDA/core_view_list&gcd_menu=CDA">Cores</a></li>
                                            <li><a href="index.php?p=CDA/affiliation_list&gcd_menu=CDA">Affiliations</a></li>
                                            <li><a href="index.php?p=CDA/contact_list&gcd_menu=CDA">Contacts</a></li>
                                            <li><a href="index.php?p=CDA/user_view&gcd_menu=CDA">Users</a></li>
                                            <li><a href="index.php?p=CDA/publication_list&gcd_menu=CDA">Publications</a></li>
                                            <li class="divider"></li>';
								/*<li class="dropdown-submenu" >
									<a class="val_pend_data" href="#">Validate pending data <span class="caret"></span></a>
									<ul class="dropdown-menu">
										<li><a href="index.php?p=Admin/validate_pending_contact&gcd_menu=ADA">Contact</a></li>
										<li><a href="index.php?p=Admin/validate_pending_affiliation&gcd_menu=ADA">Affiliation</a></li>
										<li><a href="index.php?p=Admin/validate_pending_publi&gcd_menu=ADA">Publication</a></li>
										<li class="divider"></li>
										<li><a href="index.php?p=Admin/validate_pending_site&gcd_menu=ADA">Site</a></li>
										<li><a href="index.php?p=Admin/validate_pending_core&gcd_menu=ADA">Core</a></li>
										<li><a href="index.php?p=Admin/validate_pending_sample&gcd_menu=ADA">Sample</a></li>
										<li class="divider"></li>
										<li><a href="index.php?p=Admin/validate_pending_charcoal&gcd_menu=ADA">Charcoal</a></li>
										<li><a href="index.php?p=Admin/validate_pending_agemodel&gcd_menu=ADA">Age Model</a></li>
									</ul>
								</li>';*/

								if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
									/*
									echo '<li class="divider"></li>
									<li class="dropdown-header">for SUPERADMINISTRATOR only</li>
									<li><a href="index.php?p=Admin/validate_DB&gcd_menu=ADA">Validate database</a></li>';

									 */
								}
								echo '                                    
                                    </ul>
                                    </li>';
							}
							?>
							<?php if (($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR) || ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){?>
								<li class="dropdown">
									<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Admin<span class="caret"></span></a>
									<ul class="dropdown-menu" role="menu">
										<li><a href="index.php?p=Admin/pending_data">Pending data</a></li>
										<li class="divider"></li>
										<li><a href="index.php?p=Admin/index">Comparison between GCD V03 (imported from Access) and GCD in progress</a></li>
										<li><a href="index.php?p=Admin/db_quality">Database quality</a></li>
										<li class="divider"></li>
										<li><a href="Pages/Admin/rexport.php">Export for R package</a></li>
										<?php if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){?>
											<li class="divider"></li>
											<li><a href="index.php?p=Admin/view_uploaded_files">List of uploaded charcoal files</a></li>
										<?php } ?>
									</ul>
								</li>
							<?php }
							if (isset($_SESSION['gcd_user_name'])) {?>
								<li><a href="index.php?p=help" target="_blank">Help</a></li>
							<?php } ?>
						</ul>
						<?php if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] != WebAppRoleGCD::VISITOR)) {?>
							<form name="formSearch" action="index.php?p=CDA/search" method="post" id="formSearch" class="navbar-text form-inline my-2 my-lg-0" style="margin-top:10px;margin-bottom:0px">
								<input class="form-control mr-sm-2" id="search_data" name="search_data" type="search" placeholder="Search" aria-label="Search">
								<button class="btn btn-outline-success my-2 my-sm-0" type="submit"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
							</form>
						<?php } ?>
						<p class="navbar-text navbar-right">
							<?php
							if (isset($_SESSION['gcd_user_name'])) {
								echo "User : " . $_SESSION['gcd_user_name'];
								echo '<a href="index.php?p=logout" class="btn btn-default btn-lg btn-xs" role="button">logout</a>';
							}
							else {
								echo '<a href="index.php?p=login" class="btn btn-default btn-lg btn-xs" role="button">login</a>';
							}
							?>
						</p>

					</div><!-- /.navbar-collapse -->
				</nav>
			</div><!-- /.container-fluid -->
			<div id="contenu" class="row">
				<?php include ("Pages/$page.php"); ?>
			</div>
			<div id="footer" class="row">
				All Rights Reserved (2015-<?php echo date("Y"); ?>). |
				Contact : <a id="contact">paleofire@gmail.com</a> |
				<a href="index.php?p=terms_of_use">Terms of use</a> |
				<!--	                <a href="index.php?p=contributors">Contributors</a> -->
			</div>
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

		<script>
			// permet d'ouvrir le sous-menu de "Validate pending data"
			$(document).ready(function(){
				$('.dropdown-submenu a.val_pend_data').on("click", function(e){
					$(this).next('ul').toggle();
					e.stopPropagation();
					e.preventDefault();
				});
			});
		</script>

	</div> <!--  div container  -->
	<div class="modal fade" id="dialog-simple" tabindex="-1" role="dialog" aria-labelledby="dialogLabelError">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="dialog-title"></h4>
				</div>
				<div class="modal-body" id="dialog-text"></div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="dialog-paleo" tabindex="-1" role="dialog" aria-labelledby="dialogLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="dialogLabel"></h4>
				</div>
				<div class="modal-body">
				</div>
				<div class="modal-footer">
					<a role="button" class="btn btn-primary" id="dialog-btn-yes">Yes</a>
					<button type="button" class="btn btn-default" data-dismiss="modal">No</button>
				</div>
			</div>
		</div>
	</div>
	</body>
	</html>

<?php

/*} catch(Exception $e){
    var_dump("Error" .$e->getFile(). $e->getLine() . $e->getMessage() );
//logError("Error" .$e->getFile(). $e->getLine() . $e->getMessage() );
}*/

