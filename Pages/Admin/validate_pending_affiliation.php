<?php
/* 
 * fichier Pages/Admin/validate_pending_affiliation.php 
 * Auteur : XLI 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Affiliation.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';

    $affiliations = Affiliation::getAll(null, null, Affiliation::ID_STATUS."!=1", Affiliation::ID_STATUS);
    $nbAffiliations = count($affiliations);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Pending Affiliations <small> <?php echo '('.$nbAffiliations; ?> affiliations)</small></h3>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite" style="height:600px">
        <?php
            foreach($affiliations as $affil){
                // affichage d'une publication
                echo '<div class="panel panel-warning">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-10">'.$affil->getName().'</div>';
                echo '<div class="col-md-2">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_pending_affiliation&gcd_menu=ADA&id='.$affil->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>';
                echo '</div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                echo '<dt>Address</dt><dd>'.$affil->_affiliation_address1.'</dd>';
                echo '<dt>Address 2</dt><dd>'.$affil->_affiliation_address2.'</dd>';
                echo '<dt>City</dt><dd>'.$affil->_affiliation_city.'</dd>';
                echo '<dt>State</dt><dd>'.$affil->_affiliation_state_prov.'</dd>';
                echo '<dt>State code</dt><dd>'.$affil->_affiliation_state_prov_code.'</dd>';
                $country = Country::getObjectFromStaticList($affil->getCountryId());
                echo '<dt>Country</dt><dd>'.$country->getName().'</dd>';
                $status = Status::getObjectFromStaticList($affil->getStatusId());
                echo '<dt>Status</dt><dd>'.$status->getName().'</dd>';
                echo '</dl>';              
                echo '</div>';
                echo '</div>';
            }
        ?>
    </div>
    <?php  
}