<?php

/*
 * fichier Pages/Common/dwn_file.php 
 *
 * xli : forcer le téléchargement d'un fichier en fonction de son type
 */
function dwnl_File($File_Downloaded, $pathFile){
    ob_start();
    switch(strrchr(basename($File_Downloaded), ".")) {
            case ".gz": $type = "application/x-gzip"; break;
            case ".tgz": $type = "application/x-gzip"; break;
            case ".zip": $type = "application/zip"; break;
            case ".pdf": $type = "application/pdf"; break;
            case ".txt": $type = "text/plain"; break;
            case ".htm": $type = "text/html"; break;
            case ".html": $type = "text/html"; break;
            case ".png": $type = "image/png"; break;
            case ".gif": $type = "image/gif"; break;
            case ".jpg": $type = "image/jpeg"; break;
            default: $type = "application/octet-stream"; break;
    }
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0, public');
    header("Pragma: no-cache");
    header("Expires: 0");
    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment;filename='.$File_Downloaded);
    header("Content-Length: ".filesize($pathFile . $File_Downloaded));
    header("Content-Transfer-Encoding: $type\n"); 
    $errReadFile=readfile($pathFile . $File_Downloaded);
    if ($errReadFile === FALSE) {
        var_dump("err=".$errReadFile);
    }
    else{
        var_dump("ok : ".$errReadFile);
    }
    ob_end_flush(); 
}