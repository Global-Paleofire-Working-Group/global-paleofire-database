<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type="text/javascript">
    function displayOrHide(anchor, elt){
        if ($(elt).is(":visible")){
            $(elt).hide();
            $(anchor).html('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>Show');
        } else {
            $(elt).show();
            $(anchor).html('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>Hide');
        }
    }
</script>

<?php
if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){

    echo '<h3>Quality of data</h3>';

    $title = 'Sites without cores';
    $query = "select ID_SITE, GCD_ACCESS_ID, SITE_NAME from t_site where t_site.id_site not in (select id_site from t_core)";
    $nb = displayTable($query, $title, 'sites', "index.php?p=CDA/site_view_proxy_fire&site_id=", "ID_SITE");

    $title = 'Cores without samples';
    $query = "select ID_CORE, CORE_NAME, t_site.ID_SITE, GCD_ACCESS_ID, SITE_NAME from t_core join t_site on t_site.id_site = t_core.id_site where t_core.id_core not in (select id_core from t_sample) order by id_core";
    $nb = displayTable($query, $title, 'cores', "index.php?p=CDA/core_view_proxy_fire&core_id=", "ID_CORE");

    $title = 'Publications without charcoal and sites';
    $query = "select ID_PUB, PUB_CITATION, GCD_ACCESS_ID from t_pub where t_pub.id_pub not in (select id_pub from r_site_is_referenced UNION select ID_PUB from r_has_pub)";
    $nb = displayTable($query, $title, 'publications', "index.php?p=CDA/publication_view&pub_id=", "ID_PUB");

    $title = 'Affiliations whithout cores or contacts';
    $query = "Select * from t_affiliation where t_affiliation.ID_AFFILIATION not in (select id_affiliation from t_core UNION select ID_affiliation from t_contact)";
    $nb = displayTable($query, $title, 'affiliations', "index.php?p=CDA/affiliation_view&affiliation_id=", "ID_AFFILIATION");

    $title = 'Contacts whitout affiliations or charcoal';
    $query = "Select * from t_contact where t_contact.ID_contact not in (select id_contact from t_contact where id_affiliation = 1 UNION select ID_contact from t_charcoal)";
    $nb = displayTable($query, $title, 'contacts', "index.php?p=CDA/contact_view&contact_id=", "ID_CONTACT");

}

function displayTable($query, $title, $elt_name, $link, $id){
    $nb = NULL;

    $res = queryToExecute($query);
    if ($res != NULL){
        $rows = fetchAll($res);
        $nb = count($rows);

        echo '<h4>'.$title.' <small>('.$nb.' '.$elt_name.')</small>';
        if ($nb > 0) echo '<button type="button" class="btn btn-info btn-sm" onclick="displayOrHide(this, table'.$elt_name.');">Show</button>';
        echo '</h4>';
        if(ENVIRONNEMENT == "DEV-LOCAL") echo '<small>'.$query.'</small>';

        if ($rows != NULL){
            echo '<div style="display:none" id="table'.$elt_name.'">
                    <table class="table table-sm">';
            $keys = array_keys($rows[0]);
            echo '<tr><th>'.implode('</th><th>',$keys).'</th><th></th></tr>';
            foreach($rows as $values){
                echo '<tr><td>'.implode('</td><td>', $values).'</td>';
                echo '<td><a class="btn btn-default btn-lg active btn-xs" role="button" href="'.$link.$values[$id].'"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></a></td>';
                echo '</tr>';
            }
            echo '</table></div>';
        }
    }

    return $nb;
}
