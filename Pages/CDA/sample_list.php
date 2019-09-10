<?php
/* 
 * fichier Pages/CDA/sample_list.php 
 *  
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
    $current_core_id = null;
    if (isset($_GET['core_id']) && is_numeric($_GET['core_id'])) {
        $current_core_id = $_GET['core_id'];
    }

    if ($current_core_id == null) {
        $total = Sample::countPaleofireObjects();
    } else {
        $total = Sample::countPaleofireObjects(sql_equal(Sample::ID_CORE, $current_core_id));
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
    if ($current_core_id != null) {
        $samples = Sample::getAll($start, $epp, sql_equal(Sample::ID_CORE, $current_core_id));
    } else {
        $samples = Sample::getAll($start, $epp);
    }
    ?>
    <?php
    echo "The database contains " . $total . " samples";
    if ($current_core_id != null) {
        echo " for the core " . Core::getObjectPaleofireFromId($current_core_id)->getName();
    }

    echo Pagination::paginate("index.php?p=CDA/sample_list&gcd_menu=CDA", '&page=', $nbPages, $current);
    ?>

    <div class="tableGeneralInfo" >
        <table >
            <tr>
                <td>
                    Sample name
                </td>
                <td>
                    Default depth
                </td>
                <td>
                    Other depths
                </td>
                                <td>
                    Estimated Age
                </td>
                <td>
                    Type
                </td>
            </tr>
            <?php
            foreach ($samples as $sample) {
                ?>
                <tr>
                    <td>
                        <a href='index.php?p=CDA/sample_view&gcd_menu=CDA&sample_id=<?php echo $sample->getIdValue(); ?>'><?php echo $sample->getName(); ?></a>
                    </td>
                    <td>
                        <?php echo ($sample->_default_depth != null) ? $sample->_default_depth->getName() : ""; ?>
                    </td>
                    <td>
                        <?php 
                        foreach ($sample->getListDepths() as $depth) {
                            echo $depth->getName();
                            echo " (".$depth->getDepthType()->getName().")";
                            echo "<br/>";
                        }
                        ?>
                    </td>
                                        <td>
                        <?php 
                        foreach ($sample->getListEstimatedAge() as $age) {
                            echo $age->getName();
                            echo " ";
                        }
                        ?>
                    </td>
                    <td >
                        <?php
                        $_temp_list = $sample->getListCharcoals();
                        if(empty($_temp_list)){
                            ?>
                        <img src="./pictures/calendar.png"/>
                        <?php
                        }
                        else{
                              ?>
                        <img src="./pictures/Charcoal.png"/>
                        <?php                          
                        }
                        unset($_temp_list);
                        ?>
                    </td>
                    
                </tr>            
                <?php
            }
            ?>
        </table>
    </div>
    <?php
}
?>