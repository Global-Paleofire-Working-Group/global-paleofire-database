<?php
/*
 *  fichier Pages/Admin/UpdateWeb.php 
 *  
 */

// Code lu seulement si cette page a été inclue dans l'index
if (isset ($_SESSION['started'])) {

	echo "<h1>MAINTENANCE</h1>
	Upgrade Web site pages
	<br /><br />\n";
	chdir ("/var/www");
	echo ("<center>");
	//$rev = exec ('/usr/bin/svn checkout svn://chrono-serveur.univ-fcomte.fr/paleofire --username svn --password 6deap 2>&1', $array);
	$rev = exec ('/usr/bin/svn checkout svn://chrono-db1.univ-fcomte.fr/paleofire --username svn --password 6deap 2>&1', $array);
	$count = count ($array);
	for ($i = 0; $i < $count; $i++) {
		echo ("$array[$i]<br />");
	}
	echo ("</center>");
}