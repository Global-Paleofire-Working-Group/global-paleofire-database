<?php
 /*
 * fichier Pages/CDA/search.php
 */
require_once './Models/Site.php';
require_once './Models/Core.php';
require_once './Models/Contact.php';
require_once './Models/Affiliation.php';
require_once './Models/Publi.php';

if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::VISITOR) {
    if (isset($_POST['search_data'])) {
        // protection contre l'injection SQL
        $recherche = escapeString($_POST['search_data']);
        if (!empty($recherche)){
            echo '<h2> <span class="label label-info">Search : '.$recherche.'</span></h2>';
            // protection contre l'injection sql
            if (is_numeric($recherche)){
                echo '<h3>Search in identifier : </h3>';
                echo '<div class="list-group">';
                // si la recherche est un entier on peut chercher dans les identifiants
                // SITE
                $name = SITE::getNameFromStaticList($recherche);
                if ($name != NULL) {
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/site_view_proxy_fire&site_id='.$recherche.'" target="_blank">
                            Site : '.$name.' ID : '.$recherche.
                        '</a>';
                }
                // CORE
                $res = Core::getArrayFieldsValueFromWhere(array(CORE::ID, CORE::NAME), CORE::ID." = ".$recherche);
                if ($res != NULL) {
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/core_view_proxy_fire&core_id='.$res[0][CORE::ID].'" target="_blank">
                            Core : '.$res[0][CORE::NAME].' ID : '.$res[0][CORE::ID].
                        '</a>';
                }
                // CONTACT
                $obj = CONTACT::getObjectPaleofireFromId($recherche);
                if ($obj != NULL) {
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/contact_view&contact_id='.$obj->getIdValue().'" target="_blank">
                            Contact : '.$obj->getName().' ID : '.$obj->getIdValue().
                        '</a>';
                }
                // AFFILIATION
                $obj = AFFILIATION::getObjectPaleofireFromId($recherche);
                if ($obj != NULL) {
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/affiliation_view&affiliation_id='.$obj->getIdValue().'" target="_blank">
                            Affiliation : '.$obj->getName().' ID : '.$obj->getIdValue().
                        '</a>';
                }
                // PUBLICATION
                $obj = PUBLI::getObjectPaleofireFromId($recherche);
                if ($obj != NULL) {
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/publication_view&pub_id='.$obj->getIdValue().'" target="_blank">
                            Publication : '.$obj->getName().' ID : '.$obj->getIdValue().
                        '</a>';
                }
                echo '</div>';
            }

            // pour la recherche dans les champs textes
            $recherche_text = "'%".oteAccents($recherche)."%'";
            echo '<h3>Search in text : </h3>';
            // SITE (name)
            $res = SITE::getArrayFieldsValueFromWhere(array(SITE::ID, SITE::NAME), SITE::NAME." LIKE ".$recherche_text);
            if (!empty($res)){
                echo '<h4>'.count($res).' site(s) found</h4>';
                echo '<div class="list-group">';
                foreach($res as $row){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/site_view_proxy_fire&site_id='.$row[SITE::ID].'" target="_blank">
                            ['.$row[SITE::ID].'] '.$row[SITE::NAME].
                        '</a>';
                }
                echo '</div>';
            } else echo '<h4> No site found</h4>';

            // CORE (name)
            $res = Core::getArrayFieldsValueFromWhere(array(CORE::ID, CORE::NAME), CORE::NAME." LIKE ".$recherche_text);
            if (!empty($res)){
                echo '<h4>'.count($res).' core(s) found</h4>';
                echo '<div class="list-group">';
                foreach($res as $row){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/core_view_proxy_fire&core_id='.$row[CORE::ID].'" target="_blank">
                            ['.$row[CORE::ID].'] '.$row[CORE::NAME].
                        '</a>';
                }
                echo '</div>';
            } else echo '<h4> No core found</h4>';

            // si le terme est un nombre, pas de recherche dans les publis car trop de résultats remontent (à cause des dates, des pages, des références)
            if (!is_numeric($recherche)){
                // PUBLICATION (citation)
                $results = PUBLI::getObjectsPaleofireFromWhere(PUBLI::NAME." LIKE ".$recherche_text);
                if (!empty($results)){
                    echo '<h4>'.count($results).' publication(s) found</h4>';
                    echo '<div class="list-group">';
                    foreach($results as $obj){
                        echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/publication_view&pub_id='.$obj->getIdValue().'" target="_blank">
                                ['.$obj->getIdValue().'] '.$obj->getName().'</a>';
                    }
                    echo '</div>';
                } else echo '<h4> No publication found</h4>';
            }

            // CONTACT (lastname, firstname, email)
            $results = CONTACT::getObjectsPaleofireFromWhere(CONTACT::NAME." LIKE ".$recherche_text. " OR ".CONTACT::FIRSTNAME." LIKE ".$recherche_text. " OR " .CONTACT::EMAIL." LIKE ".$recherche_text);
            if (!empty($results)){
                echo '<h4>'.count($results).' contact(s) found</h4>';
                echo '<div class="list-group">';
                foreach($results as $obj){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/contact_view&contact_id='.$obj->getIdValue().'" target="_blank">
                            ['.$obj->getIdValue().'] '.$obj->getName().' '.$obj->getFirstName().
                        '</a>';
                }
                echo '</div>';
            }else echo '<h4> No contact found</h4>';

            // USER (login)
            if ($_SESSION['gcd_user_role'] != WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] != WebAppRoleGCD::SUPERADMINISTRATOR){
                $results = WebAppUserGCD::searchUser($recherche_text);
                if (!empty($results)){
                    echo '<h4>'.count($results).' user(s) found</h4>';
                    echo '<div class="list-group">';
                    foreach($results as $key=>$obj){
                        echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/contact_view&contact_id='.$obj[1].'" target="_blank">
                                ['.$key.'] '.$obj[0].
                            '</a>';
                    }
                    echo '</div>';
                }else echo '<h4> No user found</h4>';
            }

            // COUNTRY (name)
            $results = SITE::getSitesBySearchOnCountry($recherche_text);
            if (!empty($results)){
                echo '<h4>'.count($results).' site(s) found (search in field country)</h4>';
                echo '<div class="list-group">';
                foreach($results as $row){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/site_view_proxy_fire&site_id='.$row[SITE::ID].'" target="_blank">
                            ['.$row[SITE::ID].'] Site : '.$row[SITE::NAME].', Country : '.str_replace($recherche_text, $row[COUNTRY::NAME], '<span class="text-primary font-weight-bold">'.$recherche_text.'</span>').
                        '</a>';
                }
                echo '</div>';
            }//else echo '<h4> No sites found</h4>';

            // REGION (name)
            $results = REGION::getSitesBySearchOnRegion($recherche_text);
            if (!empty($results)){
                echo '<h4>'.count($results).' site(s) found (search in field region)</h4>';
                echo '<div class="list-group">';
                foreach($results as $row){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/site_view_proxy_fire&site_id='.$row[SITE::ID].'" target="_blank">
                            ['.$row[SITE::ID].'] Site : '.$row[SITE::NAME].', Country : '. $row[COUNTRY::NAME] .', Region : '.str_replace($recherche_text, $row[REGION::NAME], '<span class="text-primary font-weight-bold">'.$recherche_text.'</span>').
                        '</a>';
                }
                echo '</div>';
            }//else echo '<h4> No sites found</h4>';

            // AFFILIATION (name)
            $results = Affiliation::getObjectsPaleofireFromWhere(Affiliation::NAME." LIKE ".$recherche_text." OR ".Affiliation::ADDRESS1." LIKE ".$recherche_text
                    ." OR ".Affiliation::ADDRESS2." LIKE ".$recherche_text." OR ".Affiliation::CITY." LIKE ".$recherche_text);
            if (!empty($results)){
                echo '<h4>'.count($results).' affiliation(s) found</h4>';
                echo '<div class="list-group">';
                foreach($results as $obj){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/affiliation_view&affiliation_id='.$obj->getIdValue().'" target="_blank">
                            ['.$obj->getIdValue().'] Affiliation : '.$obj->getName().
                        '</a>';
                }
                echo '</div>';
            }else echo '<h4> No affiliation found</h4>';

            // NOTE (text)
            $results = NoteCore::getObjectsPaleofireFromWhere(NoteCore::NAME." LIKE ".$recherche_text);
            if (!empty($results)){
                echo '<h4>'.count($results).' note(s) found</h4>';
                echo '<div class="list-group">';
                foreach($results as $obj){
                    echo '<a class="list-group-item list-group-item-action" href="index.php?p=CDA/core_view_proxy_fire&core_id='.$obj->getCoreId().'" target="_blank">
                            ['.$obj->getIdValue().'] Note : '.$obj->getName().
                        '</a>';
                }
                echo '</div>';
            }else echo '<h4> No note found</h4>';

        } else {
            echo '<div class="alert alert-info">Please, enter a text in the field "Search"</div>';
        }
    } else {
        echo '<div class="alert alert-info">Please, enter a text in the field "Search"</div>';
    }
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
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
