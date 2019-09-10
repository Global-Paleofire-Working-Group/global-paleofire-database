<?php
/* 
 * fichier Pages/CDA/affiliation_view.php 
 *  
 */ 

if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::VISITOR) {
    require './Models/Affiliation.php';
    require './Models/Country.php';
    require './Models/Contact.php';

    $id = null;
    if (isset($_GET['affiliation_id']) && is_numeric($_GET['affiliation_id'])) {
        $id = $_GET['affiliation_id'];
        $affiliation = Affiliation::getObjectPaleofireFromId($id);  
?>

<?php if ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR) { ?>
<div class="btn-toolbar" role="toolbar" align="right">
    <a role="button" class="btn btn-default btn-xs" style="float:right" href="index.php?p=ADA/edit_affiliation&id=<?php $affiliation->getIdValue();?>">
        <span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
        Edit
    </a>
    <?php 
    // si l'affiliation n'a pas de contact on propose la suppression
    // sinon la suppression est impossible
    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
        $tabContacts = $affiliation->getContacts();
        if ($tabContacts == null || count($tabContacts) <= 0){
            ?>
            <a role="button" class="btn btn-default btn-xs" style="float:right" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;delaffiliation&quot;]">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
            </a>
            <?php
        }
    }
    ?>
</div>
<?php } ?>
<h3 class="paleo"><?php echo $affiliation->getName(); ?> <small>GCD code<?php echo $affiliation->getIdValue(); ?></small></h3>
    
<div role="tabpanel" id="tabSite">
    <?php
    // affichage de l'affiliation
    echo '<div class="panel panel-info"><div class="panel-body">';
    echo '<dl>';
    echo '<dd>'.$affiliation->getAddress1().'</dd>';
    echo '<dd>'.$affiliation->getAddress2().'</dd>';
    echo '<dd>'.$affiliation->getCity().'</dd>';
    echo '<dd>'.$affiliation->getStateProv().'</dd>';
    echo '<dd>'.$affiliation->getStateProvCode().'</dd>';
    if ($affiliation->getCountryID() != null) {
        $country = Country::getObjectFromStaticList($affiliation->getCountryID());
        echo '<dd>'.$country->getName().'</dd>';
    }
    echo '</dl>';
    echo '</div></div>';

    // affichage des contacts
    $tabContacts = $affiliation->getContacts();
    if ($tabContacts != null || count($tabContacts) > 0){
        echo '<h4>Contacts from '.$affiliation->getName().'</h4>';
        echo '<ul class="list-group">';
        foreach($tabContacts as $contact){
            echo '<li class="list-group-item">';
            echo '<h4 class="list-group-item-heading">'.$contact->getFirstName().' '.$contact->getLastName();
            echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/contact_view&contact_id='.$contact->getIdValue().'">
                    <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                </a>';
            echo '</div>';
            echo '</h4>';

            echo '<dl style="margin-bottom:0px">';
            if ($contact->getEmail() != null) echo '<dd>'.$contact->getEmail().'</dd>';
            echo '</dl>';

            echo '</li>';
        }
        echo '</ul>';
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
        if (recipient[0] == "delaffiliation"){
            // suppression d'une affiliation
            modal.find('.modal-body').html('<h3>Confirm the deletion of the following affiliation ?</h3><p><?php echo $affiliation->getName(); ?></p>');
            modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_affiliation&gcd_menu=ADA&id=<?php echo $affiliation->getIdValue(); ?>");
        }
    });
});
</script>
<?php
    } else {
        echo '<div class="alert alert-info"><strong>Error</strong>Unknown identifier.</div>';
    }
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}
?>