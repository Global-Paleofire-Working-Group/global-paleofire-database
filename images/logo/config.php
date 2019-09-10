<?php

define('REP_LIB', $_SERVER["DOCUMENT_ROOT"] . "/Library/");
define('REP_MODELS', $_SERVER["DOCUMENT_ROOT"] . "/Models/");
define('REP_HTML', $_SERVER["DOCUMENT_ROOT"] . "/views/");
define('REP_PAGES', $_SERVER["DOCUMENT_ROOT"] . "/Pages/");
define("GLOBAL_RACINE","/");
        
define("GLOBAL_RACINE_PALEOFIRE", "/");

define("PRODUCTION", true);
define("MAINTENANCE", false);

require_once REP_LIB . 'scripts.php';

?>
