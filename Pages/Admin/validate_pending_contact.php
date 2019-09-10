<?php
/* 
 * fichier Pages/Admin/validate_pending_contact.php 
 * Auteur : XLI 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Contact.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';
    require_once './Models/Affiliation.php';

    $contacts = Contact::getAll(null, null, Contact::ID_STATUS."!=1", Contact::ID_STATUS);
    $nbContacts = count($contacts);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Pending Contacts <small> <?php echo '('.$nbContacts; ?> contacts)</small></h3>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite" style="height:600px">
        <?php
            foreach($contacts as $contact){
                // affichage d'une publication
                echo '<div class="panel panel-warning">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-10">'.$contact->getName().'</div>';
                echo '<div class="col-md-2">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_pending_contact&gcd_menu=ADA&id='.$contact->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>';
                echo '</div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                echo '<dt>Last Name</dt><dd>'.$contact->_lastname.'</dd>';
                echo '<dt>First Name</dt><dd>'.$contact->_firstname.'</dd>';
                echo '<dt>Email</dt><dd>'.$contact->_email.'</dd>';
                echo '<dt>Phone Number</dt><dd>'.$contact->_phone_number.'</dd>';
                $affiliation = Affiliation::getObjectFromStaticList($contact->getAffiliation());
                echo '<dt>Affiliation</dt><dd>'.$affiliation->getName().'</dd>';
                echo 'sfdsfdd='.$contact->getStatusId();
                $status = Status::getObjectFromStaticList($contact->getStatusId());
                echo '<dt>Status</dt><dd>'.$status->getName().'</dd>';
                echo '</dl>';              
                echo '</div>';
                echo '</div>';
            }
        ?>
    </div>
    <?php  
}