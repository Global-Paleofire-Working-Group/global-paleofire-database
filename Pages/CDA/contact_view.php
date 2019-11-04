<?php
/*
 * fichier Pages/CDA/contact_view.php
 *
 */
if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::VISITOR) {
    require_once './Models/Contact.php';
    require_once './Models/Affiliation.php';
    require_once './Models/Core.php';

    $id = null;
    if (isset($_GET['contact_id']) && is_numeric($_GET['contact_id'])) {
        $id = $_GET['contact_id'];
        $contact = Contact::getObjectPaleofireFromId($id);
        if ($contact != NULL) {
            $listUser = WebAppUserGCD::getListUserForAContact($contact->getIdValue());
            $listIsAuthor = Core::getCoresWithCharcoalsFromAuthor($contact->getIdValue());
            $listIsContributor = Core::getCoresWithCharcoalsFromContributor($contact->getIdValue());
            $listAgeModel = AgeModel::getAgeModelFromModeller($contact->getIdValue());
        ?>
            <div class="row">
                <div class="col-md-10">
                    <h3><?php echo $contact->getLastName(). ' '.$contact->getFirstName(); ?> <small> GCD code <?php echo $contact->getIdValue();?></small>
                        <?php
                            if($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
                                $user_list = WebAppUserGCD::getListUserForAContact($id);
                                if($user_list != NULL){
                                    echo '<br><small>User(s):  ';
                                    foreach($user_list as $user){

                                        echo $user->getWebAppUserLogin();
                                    }
                                    echo '</small>';
                                }
                            }
                        ?>
                    </h3>
                </div>
                <div class="col-md-2">
                    <?php if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) { ?>
                    <div class="btn-toolbar" role="toolbar">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_contact&id=<?php echo $contact->getIdValue();?>" style="float:right">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>
                        <?php
                        //deletion is possible only if there no user linked, if the contact is not an author or a contributor or an age modeler
                        if(empty($listUser) && empty($listIsAuthor) && empty($listIsContributor) && empty($listAgeModel)){
                            $dataDialog = '[&quot;'.$contact->getFirstName(). ' '.$contact->getLastName(). '&quot;]';
                            echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="'.$dataDialog.'" style="float:right">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
                              </a>';
                        }
                        ?>
                    </div>

                    <?php } ?>
                </div>
            </div>
            <?php
            echo '<div class="panel panel-info"><div class="panel-body">';
            echo '<dl class="dl-horizontal">';
            if ($contact->getEmail() != null) {
                echo '<dt>Email : </dt><dd>'.$contact->getEmail().'</dd>';
            }

            if ($contact->getAffiliation() != null) {
               $affil = Affiliation::getObjectFromStaticList($contact->getAffiliation());
               echo '<dt>Affiliation : </dt><dd>'.$affil->getName().'</dd>';
            }
            echo '</dl>';
            echo '</div>';
            echo '</div>';
            echo '</div>';
            ?>

            <?php
            if ($listIsContributor != NULL){
                echo '<h4> Has contributed to :</h4>';
                echo '<div class="list-group">';
                foreach($listIsContributor as $obj){
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
                echo '</div>';
            }
            ?>

            <?php
            if ($listIsAuthor != NULL){
                echo '<h4> Is author of :</h4>';
                echo '<div class="list-group">';
                foreach($listIsAuthor as $obj){
                // affichage d'un site
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
                echo '</div>';
            }
        } else {
            echo '<div class="alert alert-danger"><strong>Error</strong> Unknown identifier.</div>';
        }
    } else {
        echo '<div class="alert alert-info"><strong>Error</strong> Unknown identifier.</div>';
    }
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}
?>
<script type="text/javascript">
$(function(){
   $('#dialog-paleo').on('shown.bs.modal', function (event) {
       var button = $(event.relatedTarget);
       var recipient = button.data('whatever');
       var modal = $(this);
       modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');
       // suppression d'un contact
       modal.find('.modal-body').html('<h3>Confirm the deletion of the following contact ?</h3><p><?php echo $contact->getFirstName().' '.$contact->getLastName(); ?></p>');
        // TODO // CBO // 23/10/2017 à reprendre, impossible que ça marche pas de () dans une URL
       modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_contact&gcd_menu=ADA&id=<?php echo $contact->getIdValue(); ?>");

   });
});

function exportMailingList(){
   var url = "Pages/CDA/affiliation_list_ajax.php";
   window.open(url, '_self');
}
</script>
