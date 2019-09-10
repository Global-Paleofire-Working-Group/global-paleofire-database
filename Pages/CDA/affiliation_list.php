<?php
/* 
 * fichier Pages/CDA/affiliation_list.php 
 *  
 */ 

if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && $_SESSION['gcd_user_role'] != WebAppRoleGCD::VISITOR) {
    require './Models/Affiliation.php';
    require './Models/Country.php';
    require './Models/Contact.php';

    $affiliations = Affiliation::getAll(null, null, null, Affiliation::NAME);
    $nbAffiliations = count($affiliations);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Affiliations<small> <?php echo '('.$nbAffiliations; ?> affiliations)</small></h3>
        </div>
        <div class="col-md-3">
            <div class="btn-toolbar" role="toolbar" align="right">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_affiliation&gcd_menu=ADA">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    Add an affiliation
                </a>
            </div>
        </div>
    </div>
    
    <div role="tabpanel" id="tabSite">
        <?php
            foreach($affiliations as $affiliation){
                // affichage de l'affiliation
                echo '<div class="panel panel-info">';
                echo '<div class="panel-heading">'.$affiliation->getName();
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_affiliation&id='.$affiliation->getIdValue().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                        </a>';
                echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/affiliation_view&affiliation_id='.$affiliation->getIdValue().'">
                            <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                        </a>';
                // si l'affiliation n'a pas de contact on propose la suppression
                // sinon la suppression est impossible
                if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)){
                    $tabContacts = $affiliation->getContacts();
                    if ($tabContacts == null || count($tabContacts) <= 0){
                        $dataDialog = '[&quot;0&quot;,&quot;'.$affiliation->getIdValue().'&quot;,&quot;'.$affiliation->getName().'&quot;]';
                        echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="'.$dataDialog.'">
                                    <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
                                </a>';
                    }
                }
                echo '</div>';
                echo '</div>';
                echo '<div class="panel-body">';
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
                
                // affichage des contacts
               /* echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                        <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_contact&gcd_menu=ADA&id_affiliation='.$affiliation->getIdValue().'">
                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add a contact
                        </a>
                    </div>';*/
                if ($tabContacts != null || count($tabContacts) > 0){
                    echo '<a class="btn btn-info" role="button" data-toggle="collapse" href="#collapseContacts'.$affiliation->getIDValue().'" aria-expanded="false" aria-controls="collapseContacts">Show contacts ('.count($tabContacts).')</a>';
                    echo '<div style="margin-top:5px" class="collapse" id="collapseContacts'.$affiliation->getIDValue().'">';
                    echo '<ul class="list-group">';
                    foreach($tabContacts as $contact){
                        echo '<li class="list-group-item">';
                        echo '<h4 class="list-group-item-heading">'.$contact->getFirstName().' '.$contact->getLastName();
                        echo '<div class="btn-toolbar" role="toolbar" style="float:right">
                            <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_contact&gcd_menu=ADA&id='.$contact->getIdValue().'">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit this contact
                            </a>';/*
                            $dataDialog = '[&quot;1&quot;,&quot;'.$contact->getIdValue().'&quot;,&quot;'.$contact->getFirstName().' '.$contact->getLastName().'&quot;]';
                            echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="'.$dataDialog.'">
                                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
                                    </a>';*/
                        echo '</div>';
                        echo '</h4>';
                        
                        echo '<dl style="margin-bottom:0px">';
                        /*if ($contact->getPhone() != null) echo '<dd>'.$contact->getPhone().'</dd>';*/
                        if ($contact->getEmail() != null) echo '<dd>'.$contact->getEmail().'</dd>';
                        echo '</dl>';
                        
                        //echo '</p></a>';
                        echo '</li>';
                        
                        // todo modifier 
                        // supprimer
                    }
                    echo '</ul>';
                    echo '</div>';
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
           if (recipient[0] == 0){
               // suppression d'une affiliation
               modal.find('.modal-body').html('<h3>Confirm the deletion of the following affiliation ?</h3><p>' + recipient[2] + '</p>');
               modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_affiliation&gcd_menu=ADA&id=" + recipient[1]);
           }
       });
     });
    </script>

    <?php  
}