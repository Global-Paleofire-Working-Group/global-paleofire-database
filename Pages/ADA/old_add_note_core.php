<?php

if (isset($_SESSION['started'])) {
    require_once './Models/NoteCore.php';
    require_once './Models/Site.php';
    require_once './Library/PaleofireHtmlTools.php';

    $max_length_str = 50;
    $max_length_text = 300;

    connectionBaseInProgress();

    if (!isset($success_form)) {
        $success_form = "";
    }

    $obj = null;
    if (isset($_GET['id_core'])) {
        $param_id_core = $_GET['id_core'];
        $param_core = Core::getObjectPaleofireFromId($param_id_core);
        $param_id_site = null;
        if (isset($param_core)) {
            $param_id_site = $param_core->getSiteId();
        } else {
            $param_id_core = null;
        }
        $obj = new NoteCore();
        $obj->setCoreId($param_id_core);
    } else if (isset($_GET['id'])){
        $obj = NoteCore::getObjectPaleofireFromId($_GET['id']);
        //if ($obj == null) // todo redirection
    } else {
        $obj = new NoteCore();
    }

    if (isset($_POST['submitAdd'])) {
        $error_form = array();
        $success_form = false;

        $errors = null;

        //affectation avec les données postées dans le formulaire
        if (testPost(NoteCore::NAME)) {
            if (strlen($_POST[NoteCore::NAME]) < $max_length_text){
                if ($_POST[NoteCore::NAME] != ""){
                    $obj->setNameValue(utf8_decode($_POST[NoteCore::NAME]));
                } else {
                    $errors[] = "What must be filled";
                }
            } else {
                $errors[] = "What must be less than ". $max_length_text . " characters";
            }
        } else {
            $errors[] = "What must be filled";
        }

        if (testPost(NoteCore::ID_CORE)) {
            $obj->setCoreId($_POST[NoteCore::ID_CORE]);
        } else {
            $errors[] = "A core must be selected";
        }

        // on récupère le contributeur (personne connecté actuellement)
        $user_id = $_SESSION['gcd_user_id'];
        $contact_contributeur = WebAppUserGCD::getContactId($user_id);
        if ($contact_contributeur != null){
            $obj->setCoreNoteWho($contact_contributeur);
        } else {
            $errors[] = "Error your user account is not linked to a contact";
        }

        // la date du jour sera récupéré juste avant la création de l'objet en base
        //$obj->setCoreNoteDate(date("Y-m-d"));
        if (empty($errors)) {
            // on tente d'enregistrer la note
            $errors = $obj->save();
        }

        if (empty($errors)){
            echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
        } else {
            echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>'.implode('</br>', $errors)."</div>";
        }
        echo '<div class="btn-toolbar" role="toolbar" align="left">
            <a role="button" class="btn btn-default btn-xs" href="index.php?p=CDA/core_view_proxy_fire&gcd_menu=CDA&core_id='.$obj->getCoreId().'">
                <span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
                Go back to core page
            </a>
        </div>';
    }

    if (!isset($_POST['submitAdd']) || !empty($errors)) {
        // si on arrive sur la page la première fois
        // ou si le formulaire a été soumis mais que des erreurs empêchent l'enregistement
        // le formulaire est affiché
    ?>

    <?php
        if (isset($_GET['id'])) {
           echo '<h1>Editing note</h1>';
        } else {
           echo '<h1>Add a new note about a core</h1>';
        }
    ?>
            <!-- Formulaire de saisie d'une note-->
            <form action="" class="form_paleofire" name="formAdd" method="post" id="formAdd" >
                <!-- Cadre pour la note-->
                <fieldset class="cadre">
                    <legend>Note</legend>
                    <p class="site_name">
                        <label for="name_site">Site name*</label>
                        <select id="<?php echo SITE::ID ?>" name="<?php echo SITE::ID ?>">
                            <option value="">Select a site</option>
                        <?php
                            $listeSiteEtCore = CORE::getAllCoreBySite();
                            foreach($listeSiteEtCore as $id_site => $site){
                                echo '<option value = "' . $id_site . '">' . $site[0] . '</option>';
                            }
                        ?>
                        </select>
                    </p>
                    <p class="core_name">
                        <label for="name_core">Core name*</label>
                        <select id="<?php echo CORE::ID ?>" name="<?php echo CORE::ID ?>">
                        </select>
                    </p>
                    <p>
                        <label for="what">What*</label>
                        <textarea name="<?php echo NoteCore::NAME; ?>" id="what" maxlength="300"/><?php if (isset($obj)) echo $obj->getName(); ?></textarea>
                    </p>
                </fieldset>

                <!-- Boutons du formulaire !-->
                <p class="submit">
                    <?php
                        if (isset($obj) == null){
                            echo "<input type = 'submit' name = 'submitAdd' value = 'Add' />";
                        } else {
                            echo "<input type = 'submit' name = 'submitAdd' value = 'Save' />";
                        }

                        /*
                         * <input type = 'button' name = 'cancelAdd' onclick=\"redirection('index.php?p=".$redirection."')\" value = 'Cancel' />
                         */
                    ?>
                </p>
            </form>
            <?php
        }
    }

    function testPost($post_var) {
        return (isset($_POST[$post_var]))
                    && $_POST[$post_var] != NULL
                    && $_POST[$post_var] != 'NULL'
                    && trim(delete_antiSlash($_POST[$post_var])) != "";
    }
    ?>
<script type="text/javascript">
<?php
    echo 'var tabCore = '. json_encode($listeSiteEtCore).';';
?>
$('#ID_SITE').change(
        function(){
            $('#ID_CORE').empty();
            var id_site = $(this).val();
            var tab = tabCore[id_site][1];
            for(var key in tab){
                $('#ID_CORE').append('<option value="'+ key +'">'+tab[key]+'</option>');
            }
        });

<?php
    if(isset($param_id_site)){
        echo '$("#ID_SITE").val("'.$param_id_site.'");';
        echo '$("#ID_SITE").change();';
        echo '$("#ID_CORE").val("'.$param_id_core.'");';
    }

    if (isset($obj) && $obj->getCoreId() != null){
        $param_core = Core::getObjectPaleofireFromId($obj->getCoreId());
        $param_id_site = $param_core->getSiteID();
        echo '$("#ID_SITE").val("'.$param_id_site.'");';
        echo '$("#ID_SITE").change();';
        echo '$("#ID_CORE").val("'.$obj->getCoreId().'");';
    }
?>
</script>
<?php
