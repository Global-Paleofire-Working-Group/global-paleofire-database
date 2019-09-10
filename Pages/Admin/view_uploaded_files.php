<?php
/* 
 * fichier Pages/Admin/view_uploaded_files.php 
 * affiche la liste des fichiers qui ont été uploadé et sauvegardé 
 */

if (isset($_SESSION['started']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    // répertoire des archives des bases de données
    // géré dans le fichier config.php
    // on veut récupérer l'ensemble des fichiers du répertoire
    $dFiles = glob (REP_CHARCOALS_IMPORT."*.*");
    
    if (empty($dFiles)) {
        echo '<div class="alert alert-warning"><strong>No files</strong></div>';
    } else {    
        $tab = NULL;
        foreach($dFiles as $key => $file) {
            $file_name = basename($file);
            $href = "/Pages/Admin/view_uploaded_files_ajax.php?f=".$file_name;
            $size = (filesize( $file ))/1024;
            //var_dump(filectime($file));
            $tab[filectime($file)]=[$file_name, $href, $size];
        }

        krsort($tab);
        ?>
<h3>List of uploaded charcoal files</h3>
<?php
        
        foreach($tab as $key=>$elt){
            ?>

<div style="float:left;">Uploaded file : <b> <?php echo $elt[0] ?></b>
    <br>Created <?php echo date ("F d Y H:i:s.", $key); ?>
    <br>Size <?php echo $elt[2] ?>Ko
</div>
<div class="btn-toolbar" role="toolbar">
    <a role="button" class="btn btn-primary btn-xs" href='<?php echo $elt[1]; ?>'><span class="glyphicon glyphicon glyphicon-download" aria-hidden="true"></span> Download</a>
</div>
<br />
            <?php
        }   
    }
}   
   