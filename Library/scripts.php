<?php
        
/*
 * Cette fonction prend en parametre un message et affiche ce message dans le fichie de log d'erreur
 * correspondant au mois courant avec la date, l'heure et l'adresse IP de l'utilisateur
 */
function logError ($chaine) {
	$nomFichCourant = "LogError" . date('Y_m') . ".txt";
	// Si le fichier n'existe pas il sera cree automatiquement
        $fd = fopen (REP_LOG . $nomFichCourant, 'a');
	if ($fd == TRUE) {
		// Date et heure
		$debChaine = date ("d-m-Y H:i:s");
		// Adresse IP
		$debChaine .= " " . $_SERVER['REMOTE_ADDR'] . " ";
		// Url de la page
		$debChaine .= "(" . $_SERVER["REQUEST_URI"] . ")" . $chaine . "\n";
		// On ecrit dans le fichier
		fputs ($fd, $debChaine);
		fclose ($fd);
	}
	else {
		echo "\t<script type='text/javascript'>alert ('Une erreur est survenue lors de l\'archivage des erreurs. Merci de prevenir l\'administrateur');</script>\n";
	}
}

/*
 * Cette fonction prend en parametre un message et affiche ce message dans le fichier de log d'erreur
 * correspondant au mois courant avec la date, l'heure et l'adresse IP de l'utilisateur
 */
function logAction ($chaine) {
	$chemin = $_SERVER["DOCUMENT_ROOT"] . GLOBAL_RACINE . "Logs/Actions/";
	$nomFichCourant = "LogAction" . date('Y_m') . ".txt";
	// Si le fichier n'existe pas il sera crée automatiquement
	if ($fd = fopen ($chemin . $nomFichCourant, 'a')) {
		// Date et heure
		$debChaine = date ("d-m-Y H:i:s");
		// Adresse IP
		$debChaine .= " " . $_SERVER['REMOTE_ADDR'] . " ";
		// Url de la page
		$debChaine .= $chaine . "\n";
		// On écrit dans le fichier
		fputs ($fd, $debChaine);
		fclose ($fd);
	}
	else {
		echo "\t<script type='text/javascript'>alert('Une erreur est survenue lors de l\'archivage des actions. Merci de prevenir l\'administrateur');</script>\n";
	}
}

// Supprime les accents d'une chaine (é è ë ê ç à â î ï û ü ù ô ö ò)
function strip_iso_accents ($chaine) {
	$code_html = array ("é", "è", "ë", "ê", "ç", "à", "â", "î", "ï", "û", "ü", "ù", "ô", "ö", "ò");
	$code_affichage = array ("e", "e", "e", "e", "c", "a", "a", "i", "i", "u", "u", "u", "o", "o", "o");

	$chaine = str_replace ($code_html, $code_affichage, $chaine);
	return $chaine;
}

// Décode les accents d'une chaine (é è ë ê ç à â î ï û ü ù ô ö ò)
function decode_accents ($chaine) {
	$code_html = array ("&eacute;", "&egrave", "&euml;", "&ecirc;", "&ccedil;", "&agrave;", "&acirc;", "&icirc;", "&iuml;", "&ucirc;", "&uuml;", "&ugrave;", "&ocirc;", "&ouml;", "&ograve;");
	$code_affichage = array ("é", "è", "ë", "ê", "ç", "à", "â", "î", "ï", "û", "ü", "ù", "ô", "ö", "ò");

	$chaine = str_replace ($code_html, $code_affichage, $chaine);
	return $chaine;
}

function insecable ($chaine) {
	return str_replace (" ", "&nbsp;", $chaine);
}

function delete_antiSlash ($text) {
	return str_replace("\\", "", $text);
}

function implode_r($glue,$arr){
        $ret_str = "";
		$i=0;
        foreach($arr as $a){
			if($i>0){
				$ret_str .= (is_array($a)) ? implode_r($glue,$a) :  $glue.$a;
			}
			else{
				$ret_str .= (is_array($a)) ? implode_r($glue,$a) :  $a;
			}
			$i++;
        }
        return $ret_str;
}

// Verifie qu'une chaine est alphanumerique
function alphanumerique($input) {
    // return (ereg ("^[[:alnum:]]+$", $input));
    return (preg_match("@^[[:alnum:]]+$@", $input));
}

// Affiche Date et Heure du jour
function dateToday() {
    return date("d/m/Y") . " " . date("H:i");
}


function addValueToEachValuesInArray($value, $array_p){
    $new_array = array();
    foreach ($array_p as $value_array){
       $new_array[] =  $value."_".$value_array;
    }
    return $new_array;
}
?>