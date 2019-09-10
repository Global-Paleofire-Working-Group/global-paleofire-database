<?php
/* 
 * fichier \Pages\ADA\del_publication_ajax.php
 * 
 */ 

//if (isset($_SESSION['started'])) {
    require_once '../../Models/Publi.php';
    require_once('../../Library/data_securisation.php');
    require_once '../../Library/PaleofireHtmlTools.php';
    require_once '../../Library/connect_database.php';
    require_once '../../Library/database.php';
    
    connectionBaseInProgress();
    if (isset($_GET['id'])) {
        $id = data_securisation::toBdd($_GET['id']);
        // on vérifie qu'on peut récupérer une publi
        $publi = Publi::getObjectPaleofireFromId($id); 
		$tab = null;
		if($publi !=null){
			$err = $publi->del();
			if ($err == NULL){
			     $tab = Array("result"=>"success", "id" => $publi->getIdValue(), "text" => $publi->getName());
			} else {
				$tab = Array("result"=>"fail");
			}			
		} else {
			$tab = Array("result"=>"fail");
		}
		$publi = null;
        $json = json_encode($tab);
        echo($json);
    }
//}
