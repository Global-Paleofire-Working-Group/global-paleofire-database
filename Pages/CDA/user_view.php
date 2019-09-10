<?php
/* 
 * fichier Pages/CDA/user_view.php 
 *  
 */ 

if (isset($_SESSION['started'])) {
    if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    
    require './Models/Contact.php';

    connectionBaseWebapp();
    $users = WebAppUserGCD::getAll();
    sort($users);//tri par ordre alphabétique
    $nbUsers = count($users);
?>

    <div class="row">
        <div class="col-md-9">
            <h3>Users<small> <?php echo '('.$nbUsers; ?> users)</small></h3>
        </div>
        <div class="col-md-3">
            <div class="btn-toolbar" role="toolbar">
                <a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_edit_user&gcd_menu=ADA" style="float:right">
                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
                    Add a new user
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
            $RolesList = WebAppRoleGCD::getStaticList();
            connectionBaseInProgress();
            foreach($users as $user){
                
                $contact = NULL;
                if ($user->_webAppContact_id != NULL){
                    $contact = Contact::getObjectPaleofireFromId($user->_webAppContact_id);
                }

                echo '<div class="panel panel-info">';
                
                if ($contact != null) {
                    echo '<div class="panel-heading">'.$user->getWebAppUserLogin().' ('.$contact->getFirstName().' '.$contact->getLastName().')';
                } else {
                    echo '<div class="panel-heading">'.$user->getWebAppUserLogin();
                }
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">';
                echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/add_edit_user&id='.$user->getWebAppUserID().'">
                            <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit user
                        </a>';
                if($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
                    echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[&quot;Login : '.$user->getWebAppUserLogin().'<br>Role : '.$RolesList[$user->getWebAppUserRole()][WebAppRoleGCD::NAMEFIELD].'<br>GCD ID : '.$user->getWebAppUserID().'&quot;,&quot;'.$user->getWebAppUserID().'&quot;]">
                        <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete user
                    </a>';
                }
                
                // on tente de récupérer le contact si il en existe un
                if ($contact != null) {
                    echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_contact&id='.$contact->getIdValue().'">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit contact
                            </a>';
                    echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/contact_view&contact_id='.$contact->getIdValue().'">
                                <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View contact
                            </a>';
                }
                
                echo '</div>';
                echo '</div>';

                if($contact != null){
                    echo '<dl class="dl-horizontal" style="margin-bottom:0px">';
                    
                    if ($contact->getEmail() != null) {
                        echo '<dt>Email :</dt>';
                        echo '<dd>'.$contact->getEmail().'</dd>';
                    }
                    else echo '<dd></dd>';
                    echo '</dl>';
                }

                echo '<dl class="dl-horizontal" style="margin-bottom:0px">';
                echo '<dt>Login :</dt><dd>'.$user->getWebAppUserLogin().'</dd>';
                if ($user->getWebAppUserRole() != null) {
                    echo '<dt>Role :</dt>';
                    echo '<dd>'.$RolesList[$user->getWebAppUserRole()][WebAppRoleGCD::NAMEFIELD].'</dd>';
                }
                echo '</dl>';
                echo '</div>';
            }
            echo '</ul>';
        ?>
    </div>
    <script type="text/javascript">
    $(function(){
       $('#dialog-paleo').on('shown.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var recipient = button.data('whatever');
            var modal = $(this);
            modal.find('.modal-title').html('<p class="text-danger"><span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span> Deletion<p>');
            
            // suppression d'un user
            modal.find('.modal-body').html('<h3>Confirm the deletion of the following user ?</h3><p>' + recipient[0] + '</p><p><small>If a contact is binded to this user, the contact will not be deleted.<small></p>');
            modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_user&id=" + recipient[1]);
        });
    });

    function exportMailingList(){
       var url = "Pages/CDA/user_view_ajax.php";
       window.open(url, '_self');
    }
    </script>
    <?php  
    }
}