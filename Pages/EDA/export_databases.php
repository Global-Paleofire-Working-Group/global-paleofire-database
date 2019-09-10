<?php
/* 
 * fichier Pages/EDA/export_databases.php 
 * permet le téléchargement des bases de données présentes dans le répertoire Old_DB 
 * ATTENTION : le nom des fichiers de bases de données ne doit pas comporter d'espaces 
 */

if (isset($_SESSION['started'])) {
    // répertoire des archives des bases de données
    // géré dans le fichier config.php
    // on veut récupérer l'ensemble des fichiers du répertoire
    $dFiles = glob (REP_OLD_BD."*.*");
    
    if (empty($dFiles)) {
        echo '<div class="alert alert-warning"><strong>No database to export.</strong></div>';
    } else {    
        foreach($dFiles as $key => $file) {
            $file_name = basename($file);
            $href = str_replace($_SERVER["DOCUMENT_ROOT"], "", $file);
            $size = (filesize( $file ))/1024;
            
            echo("Database : <b>".$file_name."</b>, size=".$size." Ko".'<br />');
            echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href='.$href.'><span class="glyphicon glyphicon glyphicon-download" aria-hidden="true"></span> Download</a></div> ';
            echo '<br />';
            echo '<br />';
        }   
    }
}   
   