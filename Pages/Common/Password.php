<?php

/* 
 * fichier Pages/Common/Password.php 
 * 18 mai 2016
 * xli : fonctions sur les mots de passe
 */

/* génère automatiquement un mot de passe de longueur $taille_mdp passée en entrée */
function generate_mdp($taille_mdp)
{
        $pwd = "";
   
        $chaine = "abcdefghjkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789+@!$%?&";
        $long_chaine = strlen($chaine);
       
        for($i =0 ; $i < $taille_mdp; $i++)
        {
            $alea_pos = mt_rand(0,($long_chaine-1));
            $pwd .= $chaine[$alea_pos];
        }
        return $pwd;   
}