<?php
//xli 05/02/16
//fonctions de manipulation de fichier
// $Fich : nom du fichier
// $String : chaîne de caractères à ajouter au fichier


//écrit une chaîne de caractères $String dans un fichier $File
function writeFile($File, $String)
{
//on crée le fichier s'il n'existe pas
    if (file_exists($File)) 
        {    $fp = fopen($File,'a'); }
    else                        
        {    $fp = fopen($File,'w'); }
    fwrite($fp,$String); 
    fclose($fp); 
} 




    

