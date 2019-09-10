<?php
/* 
 * fichier Pages/Admin/validate_pending_publi.php 
 * Auteur : XLI 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Publi.php';
    require_once './Models/Status.php';

    $publications = Publi::getAll(null, null, Publi::ID_STATUS."!=1", Publi::ID_STATUS);
    $nbPublications = count($publications);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Pending Publications <small> <?php echo '('.$nbPublications; ?> publications)</small></h3>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite" style="height:600px">
        <?php
            foreach($publications as $pub){
                // affichage d'une publication
                echo '<div class="panel panel-warning">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-10">'.$pub->getName().'</div>';
                echo '<div class="col-md-2">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_pending_publi&gcd_menu=ADA&id='.$pub->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>';
                echo '</div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                echo '<dt>DOI</dt><dd>'.$pub->_doi.'</dd>';
                echo '<dt>Link</dt><dd>'.$pub->_publi_link.'</dd>';
                $status = Status::getObjectFromStaticList($pub->getStatusID());
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
               // suppression d'une publication
               modal.find('.modal-body').html('<h3>Confirm the deletion of the following publication ?</h3><p>' + recipient[2] + '</p>');
               modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_publi&gcd_menu=ADA&id=" + recipient[1]);
           }
       });
     });
    </script>
    <?php  
}