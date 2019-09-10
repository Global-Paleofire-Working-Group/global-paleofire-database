<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Sample.php';
    require './Library/Pagination.php';
    $array_emails = array();
    ?>
    <!--<ul class="nav">
        <li><a href="index.php?p=CDA/index&gcd_menu=CDA">Site</a></li>
        <li><a href="index.php?p=CDA/core&gcd_menu=CDA">Core</a></li>
        <li><a href="index.php?p=CDA/sample_list&gcd_menu=CDA">Sample</a></li>
    </ul>-->

    <?php
    $current_site_id = null;
    if (isset($_GET['site_id']) && is_numeric($_GET['site_id'])) {
        $current_site_id = $_GET['site_id'];
    }

    if ($current_site_id == null) {
        $total = Core::countPaleofireObjects();
    } else {
        $total = Core::countPaleofireObjects(sql_equal(Core::ID_SITE, $current_site_id));
    }

    $epp = 20; // nombre d'entrées à afficher par page (entries per page)
    $nbPages = ceil($total / $epp); // calcul du nombre de pages $nbPages (on arrondit à l'entier supérieur avec la fonction ceil())
    // Récupération du numéro de la page courante depuis l'URL avec la méthode GET
    // S'il s'agit d'un nombre on traite, sinon on garde la valeur par défaut : 1
    $current = 1;
    if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $page = intval($_GET['page']);
        if ($page >= 1 && $page <= $nbPages) {
            // cas normal
            $current = $page;
        } else if ($page < 1) {
            // cas où le numéro de page est inférieure 1 : on affecte 1 à la page courante
            $current = 1;
        } else {
            //cas où le numéro de page est supérieur au nombre total de pages : on affecte le numéro de la dernière page à la page courante
            $current = $nbPages;
        }
    }
    // $start est la valeur de départ du LIMIT dans notre requête SQL (dépend de la page courante)
    $start = ($current * $epp - $epp);
    if ($current_site_id != null) {
        $cores = Core::getAllIds($start, $epp, sql_equal(Core::ID_SITE, $current_site_id));
    } else {
        $cores = Core::getAllIds($start, $epp);
    }
    ?>
    <?php
    echo "The database contains " . $total . " cores";
    if ($current_site_id != null) {
        echo " for the site " . Site::getObjectPaleofireFromId($current_site_id)->getName();
    }

    echo Pagination::paginate("index.php?p=CDA/core&gcd_menu=CDA", '&page=', $nbPages, $current);
    ?>

    <div class="tableGeneralInfo" >
        <table >
            <tr>
                <td>
                    Core name
                </td>
                <td>
                    Coring date
                </td>
                <td>
                    Elevation
                </td>
                <td>
                    Water depth
                </td>
                <td>
                    Core's samples
                </td>
            </tr>
    <?php
    $cores = CORE::getCoreForPage($start, $epp);
    foreach ($cores as $core) {
        $id = $core['id'];
        ?>
                <tr>
                    <td>
                        <a href="http://localhost/Paleofire/index.php?p=CDA/core_view&gcd_menu=CDA&core_id=<?php echo $id; ?>"><?php echo $core['core_name']; ?></a>
                    </td>
                    <td>
        <?php echo $core['coring_date']; ?>
                    </td>
                    <td>
        <?php echo $core['elevation']; ?>
                    </td>
                    <td>
        <?php echo $core['water_depth']; ?>
                    </td>
                    <td>
                        <a href="index.php?p=CDA/sample_list&gcd_menu=CDA&core_id=<?php echo $id; ?>">core's samples </a><?php echo " (" . $core['nb_samples'] . ")" ?>
                    </td>
                </tr>            
        <?php
    }
    ?>
        </table>
    </div>
    <?php
}
