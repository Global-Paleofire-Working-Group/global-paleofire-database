<?php
/*
 * fichier Pages/CDA/core_view_list.php
 * Auteur : XLI
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Country.php';
    require_once './Models/Status.php';

    $query = "SELECT c.ID_CORE, c.CORE_NAME, s.ID_SITE, s.SITE_NAME FROM t_core as c, t_site as s WHERE c.ID_SITE = s.ID_SITE ORDER BY `s`.`ID_SITE` DESC " ;
    $cores = queryToExecute($query, "SELECT CORE");

    $nbCores=getNumRows($cores); //nombre de cores dans la base

?>

    <div class="row">
        <div class="col-md-9">
            <h3>Cores <small> <?php echo '('.$nbCores; ?> cores)</small></h3>
        </div>
    </div>

    <div role="tabpanel" id="tabSite">
        <?php
        if ($cores->num_rows > 0) {
            while($row = $cores->fetch_assoc()) {
                echo '<div class="panel panel-info">';
                echo '<div class="panel-heading"><div class="row">';
                echo '<div class="col-md-9">'.$row["CORE_NAME"].'</div>';
                $core_id=$row["ID_CORE"];
                echo '<div class="col-md-3">';
                echo '<div class="btn-toolbar" role="toolbar" style="float:right">';
                if (isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR)||($_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
                    $core = new Core();
                    $core->setIdValue($row["ID_CORE"]);
                    if ($core->countSamples() == 0){
                        if (NoteCore::countPaleofireObjects(NoteCore::ID_CORE." = ".$row["ID_CORE"]) == 0){
                            // la carotte n'a pas de sample et pas de note
                            // donc on propose la suppression
                            echo '<a role="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#dialog-paleo" data-whatever="[0,'.$row["ID_CORE"].',&quot;'.$row["CORE_NAME"].'&quot;]">
                                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span> Delete
                            </a>';
                        }
                    }
                }

                echo '<a role="button" class="btn btn-default btn-xs" href="index.php?p=ADA/edit_core&gcd_menu=ADA&id='.$core_id.'">
                                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span> Edit
                            </a>
                            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=ADA&core_id='.$core_id.'">
                                <span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> View
                            </a>
                        </div>';
                echo '</div>';
                echo '</div></div>';
                echo '<div class="panel-body">';
                echo '<dl class="dl-horizontal">';
                echo  '<dt>GCD Code (Core)</dt><dd>'.$core_id.'</dd>';
                echo '<dt>Site</dt><dd>'.$row["SITE_NAME"].'</dd>';
                $site_id=$row["ID_SITE"];
                echo '<dt>GCD Code (Site)</dt><dd>'.$site_id.'</dd>';

                $query2 = "SELECT c.ID_COUNTRY, c.COUNTRY_NAME FROM tr_country as c, t_site as s WHERE s.ID_COUNTRY=c.ID_COUNTRY AND s.ID_SITE =".$site_id;
                $country = queryToExecute($query2, "SELECT COUNTRY_CORE");

                if ($country->num_rows > 0) {
                    while($row2 = $country->fetch_assoc()) {
                        $country_name=$row2["COUNTRY_NAME"];
                        echo '<dt>Country</dt><dd>'.$country_name.'</dd>';
                    }
                }

                echo '</dl>';

                echo '</div>';
                echo '</div>';

            }
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
               // suppression d'un core
               modal.find('.modal-body').html('<h3>Confirm the deletion of the following core ?</h3><p>' + recipient[2] + '</p>');
               modal.find('#dialog-btn-yes').attr('href', "index.php?p=ADA/del_core&id=" + recipient[1]);
           }
       });
     });
    </script>
    <?php
}
