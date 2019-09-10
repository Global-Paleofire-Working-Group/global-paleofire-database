<?php
/* 
 * fichier Pages/CDA/site_view_list.php 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';

    $sites = Site::getAllIds(); // on récupère tous les Id de site
    arsort( $sites); // tri par id décroissant
    $sitesNames = Site::getAllIdName(); // on récupère tous les noms de site

    $nbSites = count($sites); //nombre de sites dans la base
?>

    <script type="text/javascript">
    $(function(){
       $('#dialog-paleo').on('shown.bs.modal', function (event) {
           console.log('test');
           var button = $(event.relatedTarget);
           var recipient = button.data('whatever');
           var modal = $(this);
           modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');
           if (recipient[0] == 0){
               // suppression d'un site
               modal.find('.modal-body').html('<h3>Confirm the deletion of the following site ?</h3><p>' + recipient[2] + '</p>');
               modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_site&id=" + recipient[1]);
           }
       });
     });
    </script>
    
    <div class="row">
        <div class="col-md-9">
            <h3 class="paleo">Sites <small> <?php echo '('.$nbSites; ?> sites)</small></h3>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite">
        <?php
            foreach($sites as $site){
                // affichage d'un site
                echo '<div class="panel panel-info">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-9">'.$sitesNames[$site->getIdValue()].'</div>'; // nom du site
                echo '<div class="col-md-3">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">';
                if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {                 
                    if ($site->countCore() == 0){
                        if ($site->getPubliReferencedBySite() == NULL){
                            // le site n'a pas de core 
                            // et n'est pas référencé dans des publications 
                            // donc on propose la suppression
                            echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[0,'.$site->getIdValue().',&quot;'.$sitesNames[$site->getIdValue()].'&quot;]">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
                            </a>';
                        }
                    }
                }
                echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_site&id='.$site->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/site_view&site_id='.$site->getIdValue().'">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                        </a>
                    </div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                echo '<dt>GCD code</dt><dd>'.$site->getIdValue().'</dd>';
                if ($site->getSiteCountry() != null){
                    echo '<dt>Country</dt><dd>'.$site->getSiteCountry()->getName().'</dd>';
                }
                echo '</dl>';
                
                echo '</div>';
                echo '</div>';
            }
        ?>
    </div>

    <?php  
}
