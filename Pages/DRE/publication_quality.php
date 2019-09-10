<?php
/*
 *  fichier \Pages\DRE\publication_quality.php
 * 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once'./Pages/Common/PaleofireHtmlUtilities.php';


    
    $listePubli = Publi::getAll();
    //var_dump($listePubli);

    foreach($listePubli as $publi){

        if (isset($publi->_publi_link)){
            echo '<p>';
            echo $publi->getName();
            echo'</br>';
            echo '<a href="http://'.$publi->_publi_link.'">lien</a>';
            echo '</p>';
        }

        // test à mettre en place si jamais des liens commencent à être enregistré

        /* On commence par le fichier qui existe */
        /* 
        $url = 'http://www.google.fr/intl/fr_fr/images/logo.gif'; // logo google.fr
        $essais = get_headers($url, 1);

        echo $url.'<br />';
        if (preg_match("#OK#i", $essais[0])) {
          echo 'Le fichier existe :o)';
        }
        else
        {
          echo 'Le fichier n\'existe pas :o(';
        }
         */
        /* Puis le même script mais avec un fichier qui n'existe pas */
         /*
        $url2 = 'http://www.google.fr/intl/fr_fr/images/logoQuiExistePas.gif'; // logo qui n'existe pas
        $essais2 = get_headers($url2, 1);

        echo '<br /><br />'.$url2.'<br />';
        if (preg_match("#OK#i", $essais2[0])) {
          echo 'Le fichier existe :o)';
        }
        else
        {
          echo 'Le fichier n\'existe pas :o(';
        }
         */
    }
}
