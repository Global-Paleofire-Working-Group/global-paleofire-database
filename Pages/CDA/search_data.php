<?php
 /*
 * fichier Pages/CDA/search_data.php
 *  Auteur : XLI mai 2016
 */

$to_be_found = null;
$errors = null;

if (isset($_SESSION['started'])) {
    if (isset($_POST['submitSearch'])&& empty($errors)) {
        if (testPost('search_Data')) {

            $nto_be_found=$_POST['search_Data'];

            $str_sans_accents=oteAccents($nto_be_found);
            $to_be_found="'".$str_sans_accents."'";

            $nbResultats=0; //nb de résultats retournés par la recherche

             echo '<h2> <span class="label label-warning">Searching : '.$nto_be_found.'</span></h2>';



            /* recherche le code GCD dans la table t_site */
            $query = 'SELECT SITE_NAME, ID_SITE FROM t_site WHERE t_site.ID_SITE ='.$nto_be_found.'';
            $result = queryToExecute($query);
            while ($row = fetchAssoc($result)) {
               //$id_site = $row['id_site'];
                $nbResultats++;
                echo "<b>$nbResultats. Site : </b>".$row['SITE_NAME']."<b>, id : </b>".$row['ID_SITE'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
                echo "<br>";
            }


            /* recherche dans la table t_site */
            $query = 'SELECT SITE_NAME, ID_SITE FROM t_site WHERE MATCH(SITE_NAME) AGAINST ('.$to_be_found.')';
            // echo 'query='.$query;
            $result = queryToExecute($query);

            while ($row = fetchAssoc($result)) {
                $nbResultats++;
                echo "<b>$nbResultats. Site name : </b>".$row['SITE_NAME'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
            }


            /* recherche dans la table t_core */
            $query = 'SELECT t_core.CORE_NAME, t_core.ID_SITE, t_site.SITE_NAME FROM t_core, t_site WHERE t_core.ID_SITE=t_site.ID_SITE AND MATCH(CORE_NAME) AGAINST ('.$to_be_found.')';
            $result = queryToExecute($query);
            while ($row = fetchAssoc($result)) {
                $nbResultats++;
                echo "<b>$nbResultats. Core name : </b>".$row['CORE_NAME']."<b>, Site : </b>".$row['SITE_NAME'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
            }


            /* recherche dans la table t_pub */
            $query = 'SELECT t_pub.PUB_CITATION, r_site_is_referenced.ID_SITE,t_site.SITE_NAME  FROM t_pub, r_site_is_referenced, t_site  WHERE t_pub.`ID_PUB`=r_site_is_referenced.ID_PUB AND r_site_is_referenced.ID_SITE=t_site.ID_SITE AND MATCH(PUB_CITATION) AGAINST ('.$to_be_found.')';
            $result = queryToExecute($query);
            while ($row = fetchAssoc($result)) {
               //$id_site = $row['id_site'];
                $nbResultats++;
                echo "<b>$nbResultats. Publication : </b>".$row['PUB_CITATION']."<b><br />Site : </b>".$row['SITE_NAME'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
                echo "<br>";
            }


            /* recherche dans la table t_notes */
            $query = 'SELECT t_notes.WHAT, t_core.ID_SITE, t_site.SITE_NAME FROM t_notes, t_core, t_site WHERE t_notes.ID_CORE=t_core.ID_CORE AND t_core.ID_SITE=t_site.ID_SITE AND MATCH(t_notes.WHAT) AGAINST ('.$to_be_found.')';
            $result = queryToExecute($query);
            while ($row = fetchAssoc($result)) {
               //$id_site = $row['id_site'];
                $nbResultats++;
                echo "<b>$nbResultats. Note : </b>".$row['WHAT']."<b>, Site : </b>".$row['SITE_NAME'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
                echo "<br>";
            }


            /* recherche dans la table t_contact */
            $query = 'SELECT DISTINCT t_contact.LASTNAME, t_contact.FIRSTNAME, t_core.ID_SITE, t_site.SITE_NAME FROM t_contact,t_charcoal, t_sample, t_core, t_site WHERE t_contact.ID_CONTACT=t_charcoal.ID_CONTACT AND t_charcoal.ID_SAMPLE=t_sample.ID_SAMPLE AND t_sample.ID_CORE=t_core.ID_CORE AND t_core.ID_SITE=t_site.ID_SITE AND MATCH(t_contact.LASTNAME) AGAINST ('.$to_be_found.')';
            $result = queryToExecute($query);
            while ($row = fetchAssoc($result)) {
               //$id_site = $row['id_site'];
                $nbResultats++;
                echo "<b>$nbResultats. Contact : </b>".$row['LASTNAME'].' '.$row['FIRSTNAME']."<b>, Site : </b>".$row['SITE_NAME'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
                echo "<br>";
            }


             /* recherche dans la table t_affiliation */
            $query = 'SELECT DISTINCT t_affiliation.AFFILIATION_NAME, t_core.ID_SITE, t_site.SITE_NAME FROM t_affiliation, t_core, t_site WHERE t_affiliation.ID_AFFILIATION=t_core.ID_AFFILIATION AND t_core.ID_SITE=t_site.ID_SITE AND MATCH(AFFILIATION_NAME) AGAINST ('.$to_be_found.')';
            $result = queryToExecute($query);
            while ($row = fetchAssoc($result)) {
               //$id_site = $row['id_site'];
                $nbResultats++;
                echo "<b>$nbResultats. Affiliation : </b>".$row['AFFILIATION_NAME']."<b>, Site : </b>".$row['SITE_NAME'];
                $siteId=$row['ID_SITE'];
                echo '<div class="btn-toolbar" role="toolbar" >
                    <a role="button" class="btn btn-primary btn-xs" href="index.php?p=CDA/site_view_proxy_fire&gcd_menu=CDA&site_id='.$siteId.'" target="_blank">
                        <span class="glyphicon  glyphicon-circle-arrow-right" aria-hidden="true"></span>Go to site
                    </a>
                </div>';
                echo "<br>";
                echo "<br>";
            }



            if ($nbResultats==0) {//aucun résultat correspondant à la recherche n'a été trouvé
                echo '<div class="container">';
                echo '<div class="alert alert-info fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>No result</strong></div>';
                echo '</div>';

            }
        }

        else { //le bouton "Go!" a été appuyé avant que la donnée n'ait été entrée
                 echo '<div class="container">';
                echo '<div class="alert alert-danger fade in">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <strong>Enter a data</strong></div>';
                echo '</div>';
        }


        /*else {
            echo '<div class="alert alert-danger"><strong>Error : </strong></br>'.implode('</br>', $errors)."</div>";
            echo '<div class="btn-toolbar" role="toolbar" style="float:left">
                    <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/search_data&gcd_menu=CDA">
                        <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                        Back
                    </a>
                </div>';

        }*/

    }

   if (!isset($_POST['submitAdd']) && empty($errors)) {
    ?>
     <form action="" class="form_paleofire" name="formsearchData" method="post" id="fsearchData" >

                <fieldset class="cadre">
                    <legend>search data</legend>
                    <p>
                        <div class="form-group">
                            <label id='sD' for="search_Data">data</label>
                            <input type="text" class="form-control" name="search_Data" id="search_Data" maxlength="50"/>
                        </div>
                    </p>
                </fieldset>


                <!-- Boutons du formulaire !-->
                <p class="submit">

                    <?php

                        echo "<input type = 'submit' name = 'submitSearch' value = 'Go!' />";
                    ?>
                </p>
            </form>
            <?php
        }

}


function testPost($post_var) {
        return (isset($_POST[$post_var]))
                    && $_POST[$post_var] != NULL
                    && $_POST[$post_var] != 'NULL'
                    && trim(delete_antiSlash($_POST[$post_var])) != "";
}

// remplace l'ensemble des lettres accentuées d'une chaîne de caractères en caractères non accentués
function oteAccents($str, $encoding='utf-8')   {

    $str = htmlentities($str, ENT_NOQUOTES, $encoding);
    $str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);
    $str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
    $str = preg_replace('#&[^;]+;#', '', $str);
     return $str;
}
