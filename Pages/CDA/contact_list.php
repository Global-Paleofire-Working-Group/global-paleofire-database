<?php
/*  
 * fichier Pages/CDA/contact_list.php 
 *  
 */ 
if (isset($_SESSION['started']) && (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR))) {
    require_once './Models/Contact.php';
    require_once './Models/Affiliation.php';
    require_once './Models/AgeModel.php';
    require_once './Models/Core.php';

    $contacts = Contact::getAll(null, null, null, Contact::LASTNAME);
    $nbContacts = count($contacts);
?>
<form  action=""  class="form_paleofire" name="formDel" method="post" id="formListContact" >
    <div class="row">
        <div class="col-md-9">
            <h3>Contacts<small> <?php echo '('.$nbContacts; ?> contacts)</small></h3>
        </div>
        <div class="col-md-3">
            <div class="btn-toolbar" role="toolbar">
                <a role="button" class="btn btn-primary btn-xs" href="index.php?p=ADA/add_contact&gcd_menu=ADA" style="float:right">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    Add a contact
                </a>
                <a role="button" class="btn btn-default btn-xs" href="javascript:exportMailingList()" style="float:right">
                    <span class="glyphicon glyphicon-save-file" aria-hidden="true"></span>
                    Mailing list
                </a>       
            </div>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite">
        <?php           
            foreach($contacts as $contact){
                // affichage du contact
                echo '<div class="panel panel-info">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-8">'.$contact->getLastName(). ' '.$contact->getFirstName().'</div>';                    
                echo '<div class="col-md-4">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_contact&id='.$contact->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>';
                echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/contact_view&contact_id='.$contact->getIdValue().'">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                        </a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                if ($contact->getEmail() != null) {
                    echo '<dd>'.'<b>Email:</b>'.$contact->getEmail().'</dd>';
                }
                /* if ($contact->getPhone() != null) {
                    echo '<dd>'.'<b>phone&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;:&nbsp; </b>'.$contact-> getPhone().'</dd>';
                }*/
                if ($contact->getAffiliation() != null) {
                   $affil = Affiliation::getObjectFromStaticList($contact->getAffiliation());
                   if ($affil != NULL){
                        echo '<dd>'.'<b>Affiliation:</b>'.$affil->getName().'</dd>';
                   }             
                }                
                echo '</dl>';                       
                echo '</div>'; 
                echo '</div>';  
          }

        ?>
    </div>
    </form>
    <script type="text/javascript">
    function exportMailingList(){
       var url = "Pages/CDA/affiliation_list_ajax.php";
       window.open(url, '_self');
    }
    </script>
<?php  
} else {
    echo '<div class="alert alert-info"><strong>Access denied</strong> You have to log in to access this page.</div>';
}