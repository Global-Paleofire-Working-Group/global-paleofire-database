<?php
/* 
 * fichier Pages/Admin/edit_consult_DB.php
 * Auteur : XLI 
 * Pour les administrateurs : comportement du bouton de changement de version de la base de données vn ou vn+1
 */

if (isset($_SESSION['started'])) {
       
     include_once(REP_LIB."/connect_database.php");

     if (!isset($_POST['submitMDB'])) {
         echo '
		<form id="conn" method="post" action="">
			<p><input type="submit" id="submitMDB" name="submitMDB" value="Change Mode" /></p>
		</form>';
     }
     if (isset($_POST['submitMDB'])) {
	
        if ($_SESSION['mode_edit_db']===0) {       
            $_SESSION['mode_edit_db']=1; 
            connectionBaseInProgress();            
        }
        else if($_SESSION['mode_edit_db']===1) {
            $_SESSION['mode_edit_db']=0; 
            connectionBaseVersionned();
        } 
        echo "<script> document.location.href='index.php';</script>";       
     }

  }
