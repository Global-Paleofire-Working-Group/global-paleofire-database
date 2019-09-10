<?php
/* 
 * fichier Pages/CDA/publication_list.php 
 *  
 */
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))) {
    require_once './Models/Publi.php';
    require_once './Models/Site.php';
    require_once './Models/Core.php';

    $publications = Publi::getAll(null, null, null, Publi::NAME);
    $nbPublications = count($publications);
    ?>
    <div class="btn-toolbar" role="toolbar">
        <a role="button" class="btn btn-primary btn-xs" style="float:right" href="index.php?p=ADA/add_publi&gcd_menu=ADA">
            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
            Add a publication
        </a>
    </div>
    <h3 class="paleo">Publications<small> <?php echo '('.$nbPublications; ?> publications)</small></h3>
    <div role="tabpanel" id="tabPubli">
            <?php           
                foreach($publications as $pub){
                    // affichage d'une publication
                    echo '<div class="panel panel-info">';
                    echo '<div class="panel-heading"><div class="row">';
                    
                    echo '<div class="col-md-9">GCD code : '.$pub->getIdValue().'</div>';
                    echo '<div class="col-md-3"><div class="btn-toolbar" role="toolbar" style="float:right">';
                    echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_publi&gcd_menu=ADA&id='.$pub->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                          </a>';
                    echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/publication_view&pub_id='.$pub->getIdValue().'">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                          </a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div></div>';
                    echo '<div class="panel-body">';
                    echo '<dl class="dl-horizontal">';
                    if ($pub->getName() != NULL) echo '<dt>Citation :</dt><dd id="cit'.$pub->getIdValue().'">'.$pub->getName().'</dd>';
                    if (isset($pub->_publi_link)) echo '<dt>Link :</dt><dd>'.$pub->_publi_link.'</dd>';
                    if (isset($pub->_doi)) echo '<dt>DOI :</dt><dd>'.$pub->_doi.'</dd>';
                    echo '</dl>';
                    echo '</div>';
                    echo '</div>';
                }            
        ?>    
	    </div>
<?php
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}
