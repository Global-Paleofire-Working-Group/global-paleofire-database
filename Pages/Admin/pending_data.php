<?php
/* 
 * fichier Pages/Admin/validate_pending_data.php 
 * Auteur : CBO 
 */

if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) 
            && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) ) {
    require_once './Models/Site.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';

    $sites = Site::getAllIds(null, null, Site::ID_STATUS."!=1");
    arsort( $sites);
    $sitesNames = Site::getAllIdName();
    $nbSites = count($sites);
    
    $core_list = Core::getCoreWithCharcoalsWaitingForValidation();
    $nbCores = count($core_list);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Charcoal<small> <?php echo '('.$nbCores; ?> cores)</small></h3>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite" style="height:600px">
        <?php
            foreach($core_list as $core){
                // affichage d'un site
                echo '<div class="panel panel-info">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-10">['. $core[CORE::ID].'] '.$core[CORE::NAME].'</div>'; 
                echo '<div class="col-md-2">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view&core_id='.$core[CORE::ID].'">
                            <span class="glyphicon glyphicon-look" aria-hidden="true"></span> View
                        </a>';
                echo '</div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo $core['nb_charcoals'].' charcoals : ';
                $listStatus = explode(',', $core['list_status']);
                foreach($listStatus as $status){
                    if (Status::isDenied($status))
                        echo '<span class="label label-danger">'.Status::getNameFromStaticList($status).'</span>';
                    else if (Status::isWaiting($status))
                        echo '<span class="label label-warning">'.Status::getNameFromStaticList($status).'</span>';
                    else echo Status::getNameFromStaticList($status);
                }
                echo '</div>';
                echo '</div>';
            }
        ?>
    </div>
    <script type="text/javascript">
    $(function(){
       $('#dialog-paleo').on('shown.bs.modal', function (event) {
           var button = $(event.relatedTarget);
           var recipient = button.data('whatever');
           var modal = $(this);
           modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');
           console.log(recipient); 
           if (recipient[0] == 0){
               // suppression d'un site
               modal.find('.modal-body').html('<h3>Confirm the deletion of the following site ?</h3><p>' + recipient[2] + '</p>');
               modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_site&gcd_menu=ADA&id=" + recipient[1]);
           }
       });
     });
    </script>
    <?php  
} else {
    // todo rediriger
}