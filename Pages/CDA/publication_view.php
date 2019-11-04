<?php
/*
 * fichier Pages/CDA/publication_list.php
 *
 */
if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::VISITOR) {
    require_once './Models/Publi.php';
    require_once './Models/Site.php';

    $id = null;
    if (isset($_GET['pub_id']) && is_numeric($_GET['pub_id'])) {
        $id = $_GET['pub_id'];
        $pub = Publi::getObjectPaleofireFromId($id);

        if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) {
?>
    <div class="btn-toolbar" role="toolbar">
        <a role="button" style="float:right" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_publi&id=<?php echo $pub->getIdValue();?>">
            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
        </a>
<?php
            // only superadministrator can delete a publication
            if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
                // deletion is possible if the publication is not bind to a site or charcoal
                if(empty(Site::getSitesReferencedByPublication($pub->getIdValue()))){
                    if(empty(Core::getCoresWithCharcoalsReferencedByPublication($pub->getIdValue()))){
?>
        <a role="button" style="float:right" class="btn btn-default btn-xs" data-toggle="modal" data-whatever="[&quot;'.$pub->getIdValue().'&quot;]" data-target="#dialog-paleo">
            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
        </a>
<?php
                    }
                }
            }
?>
    </div>
<?php
        }
?>

    <h3 class="paleo">Publication<small> GCD code <?php echo $pub->getIdValue();?></small></h3>
    <div role="tabpanel" id="tabPubli">
        <div class="panel panel-info">
            <div class="panel-body">
                <dl class="dl-horizontal">
                    <?php if ($pub->getName() != NULL) echo '<dt>Citation :</dt><dd id="cit'.$pub->getIdValue().'">'.$pub->getName().'</dd>'; ?>
                    <dt>Link :</dt> <?php if (isset($pub->_publi_link)) echo '<dd>'.$pub->_publi_link.'</dd>'; ?>
                    <dt>DOI :</dt> <?php if (isset($pub->_doi)) echo '<dd>'.$pub->_doi.'</dd>'; ?>
                </dl>
            </div>
        </div>
    </div>

    <h4> Site(s) referenced by publication :</h4>
    <div class="list-group">
<?php
        $obj_list = Site::getSitesReferencedByPublication($pub->getIdValue());
        if ($obj_list != NULL){
            foreach($obj_list as $obj){
                // affichage d'un site
?>
        <a href="index.php?p=CDA/site_view_proxy_fire&site_id=<?php echo $obj->getIdValue(); ?>" class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"><?php echo $obj->getName(); ?></h5>
                <small>
                    <dl class="dl-horizontal">
                        <dt>GCD Code :</dt><dd><?php echo $obj->getIdValue(); ?></dd>
                        <?php if ($obj->getSiteCountry() != null){
                            echo '<dt>Country : </dt><dd>'.$obj->getSiteCountry()->getName().'</dd>';
                        }?>
                    </dl>
                </small>
            </div>
        </a>
<?php
            }
        }

        echo '<h4> Charcoal(s) referenced by publication : (link on core view)</h4>';
        $obj_list = Core::getCoresWithCharcoalsReferencedByPublication($pub->getIdValue());
        if ($obj_list != NULL){
            foreach($obj_list as $obj){
                // affichage d'un core
?>
        <a href="index.php?p=CDA/core_view_proxy_fire&core_id=<?php echo $obj->getIdValue(); ?>" class="list-group-item list-group-item-action flex-column align-items-start">
            <div class="d-flex w-100 justify-content-between">
                <h5 class="mb-1"><?php echo $obj->getName(); ?></h5>
                <small>
                    <dl class="dl-horizontal">
                        <dt>GCD Code :</dt><dd><?php echo $obj->getIdValue(); ?></dd>
                    </dl>
                </small>
            </div>
        </a>
            <?php
            }
        }
    } else {
        echo '<div class="alert alert-info"><strong>Error</strong>Unknown identifier.</div>';
    }
    ?>
    </div>
<script type="text/javascript">
$(function(){
   $('#dialog-paleo').on('shown.bs.modal', function (event) {
       var modal = $(this);
       modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');
       // deletion of a publication
       modal.find('.modal-body').html('<h3>Confirm the deletion of the following publication ?</h3><p><?php echo $pub->getName(); ?></p>');
       modal.find('#dialog-btn-yes').attr('href', 'index.php?p=ADA/del_publi&id=<?php echo $pub->getIdValue(); ?>');

   });
});
</script>
<?php
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}
