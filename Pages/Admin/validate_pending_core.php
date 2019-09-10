<?php
/* 
 * fichier Pages/Admin/validate_pending_site.php 
 * Auteur : XLI 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Core.php';
    require_once './Models/Site.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';

    $cores = Core::getAll(null, null, Core::ID_STATUS."!=1", Core::ID_STATUS);
    $nbCores = count($cores);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Pending Cores <small> <?php echo '('.$nbCores; ?> cores)</small></h3>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite" style="height:600px">
        <?php
            foreach($cores as $core){
                
                echo '<div class="panel panel-warning">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-10">'.$core->getName().'</div>';
                echo '<div class="col-md-2">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_pending_core&gcd_menu=ADA&id='.$core->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>';
                echo '</div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                echo '<dt>Site</dt><dd>'.$core->getSite()->getName().'</dd>';
                $status = Status::getObjectFromStaticList($core->getStatusID());
                echo '<dt>Status</dt><dd>'.$status->getName().'</dd>';
                echo '</dl>';
                
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
}