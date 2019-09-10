<?php
/*
 * fichier Pages/ADA/add_charcoal.php 
 * 
 */

if (isset($_SESSION['started'])) {
    require_once './Models/Site.php';
    require_once './Models/Sample.php';
    require_once './Library/PaleofireHtmlTools.php';
    require_once './Models/DataBaseVersion.php';
    require_once './Models/user/WebAppUserGCD.php';

    if (!isset($success_form)) {
        $success_form = "";
    }
    if (!isset($error_form)) {
        $error_form = array();
    }
    
    $charcoal = NULL;
    $sample = NULL;
    $error = [];
    
    //var_dump($_POST);
    
    $id = $_GET['id'];
    if (!isset($id)) {
        $sample = new Sample();
        $charcoal = new Charcoal();
    } else {
        // on vérifie que l'id est numérique
        if(is_numeric($id)){
            $charcoal = Charcoal::getObjectPaleofireFromId($id);
            if ($charcoal != null){
                $sample = Sample::getObjectPaleofireFromId($charcoal->_sample_id);
                if ($sample == null) $error[] = "Unable to load sample data";
            } else $errors[] = "Unable to load charchoal data";
        } else $errors[] = "Unable to load charchoal data";
    }

    if (empty($errors)) {
        if (isset($_POST['submitAdd'])) {
            $depth_up = NULL;
            $depth_down = NULL;
            $id_database = NULL;
            $authors = NULL;
            $publis = NULL;
            
            // test des différents champs
            // DATA SOURCE (donnée non obligatoire)
            if (isset($_POST['ID_DATA_SOURCE'])){
                $id_data_source = $_POST['ID_DATA_SOURCE'];
                if(!is_numeric($id_data_source) || !DataSource::idExistsInDatabase($id_data_source)) 
                    $errors[] = "Data souce value is out of range";
            } else $id_data_source = NULL;

            // METHOD (donnée non obligatoire)
            if (isset($_POST['ID_CHARCOAL_METHOD'])){
                $id_method = $_POST['ID_CHARCOAL_METHOD'];
                if(!is_numeric($id_method) || !CharcoalMethod::idExistsInDatabase($id_method)) 
                    $errors[] = "Method value is out of range";
            } else $id_method = NULL;

            // UNITS
            if (isset($_POST['ID_CHARCOAL_UNITS'])){
                $id_units = $_POST['ID_CHARCOAL_UNITS'];
                if(!is_numeric($id_units) || !CharcoalUnits::idExistsInDatabase($id_units)) 
                    $errors[] = "Units value is out of range";
            } else $errors[] = "Units must be selected";

            // DEPTH UP
            if (isset($_POST['depth_up'])){
                $depth_up = $_POST['depth_up'];
                if(!is_numeric($depth_up)) 
                    $errors[] = "Depth up must be numeric";
                else if(((float)$depth_up) < 0) 
                    $errors[] = "Depth up must be positive";
            } else $errors[] = "Depth up must be filled";

            // DEPTH DOWN
            if (isset($_POST['depth_down'])){
                $depth_down = $_POST['depth_down'];
                if(!is_numeric($depth_down)) 
                    $errors[] = "Depth down must be numeric";
                else if(((float)$depth_down) < 0) 
                    $errors[] = "Depth down must be positive";
            } else $errors[] = "Depth down must be filled";

            // AGE UP
            if (isset($_POST['age_up'])){
                $age_up = $_POST['age_up'];
                if(!is_numeric($age_up)) 
                    $errors[] = "Age up must be numeric";
            } else $errors[] = "Age up must be filled";

            // AGE DOWN
            if (isset($_POST['age_down'])){
                $age_down = $_POST['age_down'];
                if(!is_numeric($age_down)) 
                    $errors[] = "Age down must be numeric";
            } else $errors[] = "Age down must be filled";

            // TODO // vérifier que do > up pour depth
            
            // QUANTITY
            if (isset($_POST['quantity'])){
                $quantity = $_POST['quantity'];
                if(!is_numeric($quantity)) 
                    $errors[] = "Age down must be numeric";
                else if(((float)$quantity) < 0) 
                    $errors[] = "Quantity must be positive";
            } else $errors[] = "Quantity must be filled";

            // SIZE
            if (isset($_POST['ID_CHARCOAL_SIZE'])){
                $id_charcoal_size = $_POST['ID_CHARCOAL_SIZE'];
                if(!is_numeric($id_charcoal_size) || !CharcoalSize::idExistsInDatabase($id_charcoal_size)) 
                    $errors[] = "Volume identifier is out of range";
                if($id_charcoal_size == "40"){
                    if(isset($_POST['CHARCOAL_SIZE_VALUE'])){
                        $charcoal_size_value = $_POST['CHARCOAL_SIZE_VALUE'];
                        if(!is_numeric($charcoal_size_value)) $errors[] = "Unit of volume value is out of range";
                    } else {
                        $errors[] = "Unit of volume must be filled";
                    }
                }
            } else $errors[] = "Volume must be selected";
            
            // STATUS
            if($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
                if (isset($_POST['ID_STATUS'])){
                    $id_status = $_POST['ID_STATUS'];
                    if (!is_numeric($id_status) || !Status::idExistsInDatabase($id_status)){
                        $errors[] = "Status identifier is out of range";
                    }
                } else {
                    $errors[] = "Status must be selected";
                }
            } else {
                $id_status = $charcoal->_charcoal_status_id;
            }
            
            //author
            if(isset($_POST["ID_CONTACT"]) && is_array($_POST["ID_CONTACT"])){
                foreach($_POST["ID_CONTACT"] as $author_id){
                    if(is_numeric($author_id)){
                        if (CONTACT::getNameFromStaticList($author_id)==NULL){
                            $errors[] = "Unknown author";
                        } else {
                            $authors = $_POST["ID_CONTACT"];
                        }
                    }
                }
            }
            
            //publis
            if(isset($_POST["ID_PUB"]) && is_array($_POST["ID_PUB"])){
                foreach($_POST["ID_PUB"] as $pub_id){
                    if(is_numeric($pub_id)){
                        if(Publi::idExistsInDatabase($pub_id) == NULL){
                            $errors[] = "Unknow publication";
                        } else {
                            $publis = $_POST["ID_PUB"];
                        }
                    }
                }
            }

            if (empty($errors)){
                // on prépare l'enregistrement en base de données
                $depth_up = NULL;
                $depth_down = NULL;
                $depth_middle = NULL;
                $age_up = NULL;
                $age_down = NULL;
                $age_middle = NULL;

                if ($sample->getListEstimatedAge() != NULL){
                    // on récupère les ages estimés et les depths liées
                    foreach($sample->getListEstimatedAge() as $age){
                        if($age->_depth != null){
                            switch($age->_depth->getDepthType()->getIdValue()){
                                case DepthType::ID_DEPTH_TOP: 
                                    //$depth_up = $age->_depth;
                                    //$age_up = $age->_est_age;
                                    $age_up = $age;
                                    break;
                                case DepthType::ID_DEPTH_BOTTOM: 
                                    //$depth_down = $age->_depth; 
                                    //$age_down = $age->_est_age;
                                    $age_down = $age;
                                    break;
                                case DepthType::ID_DEPTH_MIDDLE:
                                    //$depth_middle = $age->_depth;
                                    $age_middle = $age;
                            }
                        }
                    }
                }
                
                /*if($depth_up != NULL){
                    $depth_up->_depth_value = $_POST['depth_up'];
                    echo 'test';
                    $depth_up->setDepthType(DepthType::DEPTH_TOP);
                } else {
                    $depth_up = new Depth($_POST['depth_up'], DepthType::DEPTH_TOP);
                    $depth_up->setDepthType(DepthType::DEPTH_TOP);
                    $sample->addDepth($depth_up);
                } */         
                /*if($depth_down != NULL){
                    $depth_down->_depth_value = $_POST['depth_down'];
                    $depth_down->setDepthType(DepthType::DEPTH_BOTTOM);
                } else {
                    $depth_down = new Depth($_POST['depth_down'], DepthType::DEPTH_DOWN);
                    $depth_down->setDepthType(DepthType::DEPTH_BOTTOM);
                    $sample->addDepth($depth_down);
                }*/
                // calcul de la middle depth
                /*if($depth_middle != NULL){
                    $depth_middle->_depth_value = ($_POST['depth_up'] + $_POST['depth_down'])/2;
                    $depth_middle->setDepthType(DepthType::DEPTH_MIDDLE);
                } else {
                    $depth_middle = new Depth(($_POST['depth_up'] + $_POST['depth_down'])/2, DepthType::DEPTH_MIDDLE);
                    $sample->addDepth($depth_middle);
                }*/
                
                
                if($age_up != NULL){
                    $age_up->_est_age = $_POST['age_up'];
                    $age_up->_depth->_depth_value = $_POST['depth_up'];
    
                } else {
                    $age_up = new EstimatedAge($_POST['age_up']);
                    $age_up->_depth = new Depth($_POST['depth_up'], DepthType::DEPTH_TOP);
                    $age_up->_age_model = $sample->_sample_age_model;
                    $sample->addEstimatedAge($age_up);
                }
                $age_up->_age_model = $sample->_sample_age_model;
                
                if($age_down != NULL){
                    $age_down->_est_age = $_POST['age_down'];
                    $age_down->_depth->_depth_value = $_POST['depth_down'];
                } else {
                    $age_down = new EstimatedAge($_POST['age_down']);
                    $age_down->_depth = new Depth($_POST['depth_down'], DepthType::DEPTH_DOWN);
                    $age_down->_age_model = $sample->_sample_age_model;
                    $sample->addEstimatedAge($age_down);
                }
                $age_down->_age_model = $sample->_sample_age_model;
                
                if($age_middle != NULL){
                    $age_middle->_est_age = ($_POST['age_up'] + $_POST['age_down'])/2;
                    $age_middle->_depth->_depth_value = ($_POST['depth_up'] + $_POST['depth_down'])/2;
                } else {
                    $age_middle = new EstimatedAge(($_POST['age_up'] + $_POST['age_down'])/2);
                    $age_middle->_depth = new Depth(($_POST['depth_up'] + $_POST['depth_down'])/2, DepthType::DEPTH_MIDDLE);
                    $age_middle->_age_model = $sample->_sample_age_model;
                    $sample->addEstimatedAge($age_middle);
                }
                $sample->_default_depth = $age_middle->_depth;
                $age_middle->_age_model = $sample->_sample_age_model;
                
                // affecte les données au charcoal
                $charcoal->_charcoal_datasource_id = $id_data_source;
                $charcoal->_charcoal_method_id = $id_method;
                $charcoal->_charcoal_size_id = $id_charcoal_size;
                $charcoal->_charcoal_size_value = $charcoal_size_value;
                $charcoal->_charcoal_status_id = $id_status;
                $charcoal->_charcoal_database_id = $id_database;
                $charcoal->_list_charcoal_quantities = NULL;
                $charcoal->addCharcoalQuantity($quantity, $id_units);
                
                $charcoal->_list_authors = $authors;
                $charcoal->_list_publications = $publis;

                // on commence la transaction en base de données
                beginTransaction();
                // enregistrement du SAMPLE
                $errors = $sample->save();
                if(empty($errors)){
                    // enregistrement du CHARCOAL
                    $errors = $charcoal->save();
                }                
                // si il n'y a pas d'erreur on termine la transaction
                if (empty($errors)) {
                    commit();
                } else {
                    rollBack();
                }

            }
            
            if (empty($errors)) {
                //ajout des infos dans le fichier log qui sera envoyé par mail aux administrateurs
                writeChangeLog("add_charcoal", "id_charcoal : ".$charcoal->getIdValue().", sample : ".$sample->getIdValue()." core :".$sample->_sample_core_id);
                echo '<div class="alert alert-success"><strong>Success !</strong> Thanks for your contribution.</div>';
            } else {
                echo '<div class="alert alert-danger"><strong>Error recording !</strong></br>' . implode('</br>', $errors) . "</div>";
            }
        }
    } else {
        echo '<div class="alert alert-danger"><strong>Error !</strong></br>' . implode('</br>', $errors) . "</div>";
    }
    
    if (!(isset($_POST['submitAdd'])) || !empty($errors)){
    ?>

    <h1>Edit charcoal</h1>     
    <!-- Formulaire de modification d'un charcoal -->
    <form action="" method="post" enctype="multipart/form-data" class="form_paleofire" name="formAdd" id="formAdd">
        <fieldset class="cadre">
            <legend>Charcoal description</legend>
            <p class="data_source form-group">
                <label for="name">Sample name</label>
                <?php echo $sample->getName(); ?>
            </p>
            <p class="data_source"><label for="data_source">Data source</label>
                <?php
                    $selectedId = (isset($charcoal))?$charcoal->_charcoal_datasource_id:null;
                    selectHTML(Charcoal::ID_DATA_SOURCE, 'addCharcoals_data_source', 'DataSource', intval($selectedId)); ?>
            </p>
            <p class="charcoal_method"><label for="charcoal_method">Method</label>
                <?php $selectedId = (isset($charcoal))?$charcoal->_charcoal_method_id:null; 
                    selectHTML(Charcoal::ID_CHARCOAL_METHOD, 'addCharcoals_method', 'CharcoalMethod', intval($selectedId)); ?>
            </p>
            <p class="charcoal_database"><label for="charcoal_database">Database</label>
                <?php if (isset($charcoal)) echo DataBaseVersion::getNameFromStaticList($charcoal->_charcoal_database_id); ?>
            </p>
        </fieldset>

        <?php
        if ($sample != NULL)
        {
            $depth_up = NULL;
            $depth_down = NULL;
            $depth_up_value = NULL;
            $depth_down_value = NULL;
            $age_up = NULL;
            $age_down = NULL;
            
            // on récupère les ages estimés et les depths liées
            foreach($sample->getListEstimatedAge() as $age){
                if($age->_depth != null){
                    switch($age->_depth->getDepthType()->getIdValue()){
                        case DepthType::ID_DEPTH_TOP: 
                            $depth_up = $age->_depth;
                            $depth_up_value = $age->_depth->_depth_value;
                            $age_up = $age->_est_age;
                            break;
                        case DepthType::ID_DEPTH_BOTTOM: 
                            $depth_down = $age->_depth; 
                            $depth_down_value = $age->_depth->_depth_value;
                            $age_down = $age->_est_age;
                            break;
                    }
                }
            }
        }
        ?>
        <fieldset class="cadre">
            <legend>Depths</legend>
            <p>
                <label for="depth_up">Depth up* (cm)</label>
                <input type="text" name="depth_up" id="depth_up" value="<?php echo $depth_up_value; ?>"/>
            </p>
            <p>
                <label for="depth_down">Depth down* (cm)</label>
                <input type="text" name="depth_down" id="depth_down" value="<?php echo $depth_down_value; ?>"/>
            </p>
        </fieldset>
        <fieldset class="cadre">
            <legend>Ages</legend>
            <p>
                <label for="age_up">Age up* (Cal BP)</label>
                <input type="text" name="age_up" id="age_up" value="<?php echo $age_up; ?>"/>
            </p>
            <p>
                <label for="age_up">Age down* (Cal BP)</label>
                <input type="text" name="age_down" id="age_down" value="<?php echo $age_down; ?>"/>
            </p>
        </fieldset>
        <?php
        if ($charcoal != NULL){
            $quantity_value = NULL;
            $quantity_unit = NULL;
            // on récupère la liste des quantités 
            // (pour l'instant une seule quantité possible, voir pour évolution sur liste de plusieurs enregistrements)
            $list = $charcoal->getListCharcoalQuantities();
            if (($list != NULL) && (count($list) > 0)){
                $quantity_value = $list[0]->_charcoal_quantity_value;
                $quantity_unit = $list[0]->_charcoal_unit_id;
            }
        }
        ?>
        <fieldset class="cadre">
            <legend>Charcoal quantity</legend>
            <p class="quantity">
                <label for="quantity">Quantity*</label>
                <input type="text" name="quantity" id="quantity" value="<?php echo $quantity_value; ?>"/>
            </p>
            <?php if (isset($charcoal) && $charcoal->_charcoal_size_id == "40"){ ?>
                <p class="charchoal_size"><label for="size">Unit of volume*</label>
                    <input type="text" name="<?php echo Charcoal::CHARCOAL_SIZE_VALUE;?>" id="<?php echo Charcoal::CHARCOAL_SIZE_VALUE;?>" value="<?php echo $charcoal->_charcoal_size_value; ?>"/>
                    <input type="hidden" name="<?php echo Charcoal::ID_CHARCOAL_SIZE;?>" id="<?php echo Charcoal::ID_CHARCOAL_SIZE;?>" value="40"/>
                    <?php echo CharcoalSize::getNameFromStaticList($charcoal->_charcoal_size_id); ?>
                </p>
            <?php } else { ?> 
                <p class="charchoal_size"><label for="size">Volume*</label>
                    <?php $selectedId = (isset($charcoal))?$charcoal->_charcoal_size_id:null;
                        selectHTML(Charcoal::ID_CHARCOAL_SIZE, 'addSize', 'CharcoalSize', intval($selectedId)); ?>
                </p>
            <?php } ?>
            
            <p class="charcoal_units"><label for="charcoal_units">Units*</label>
                <?php selectHTML(Charcoal::ID_PREF_CHARCOAL_UNITS, 'addCharcoals_units', 'CharcoalUnits', intval($quantity_unit)); ?>
            </p>
        </fieldset>
        <?php
        if($charcoal != NULL){
            $list_id_publications = array();
            if(!empty($charcoal->getListPublications())){
                foreach($charcoal->getListPublications() as $obj){
                    $list_id_publications[] = $obj->getIdValue();
                }
            }
            $list_id_authors = array();
            if(!empty($charcoal->getListAuthors())){
                foreach($charcoal->getListAuthors() as $obj){
                    $list_id_authors[] = $obj->getIdValue();
                }            
            }
        }
        ?>
        <fieldset class="cadre">
            <legend>Publication</legend>
            <p class="pub"><label for="pub">Publication</label>
                <?php selectMultipleOptionsHTML(Publi::ID . '[]', 'addCharcoals_publi', 'Publi', $list_id_publications, 5, true); ?>
            </p>
            <p class="author" style="width:470px"><label for="author">Author</label>
                <?php selectMultipleOptionsHTML(Contact::ID . '[]', 'addCharcoals_author', 'Contact', $list_id_authors, 5, true); ?>
            </p>           
        </fieldset>
        <?php
        if($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR){
        ?>
        <fieldset class="cadre">
            <legend>Status*</legend>
            <p>
                <label for="addCharcoal_status">Status</label>
                <?php
                $selectedId = (isset($charcoal))?$charcoal->_charcoal_status_id:null;
                selectHTML(Charcoal::ID_STATUS, 'addCharcoal_status', 'Status', intval($selectedId));
                ?>
            </p>  
        </fieldset>
        <?php } ?>
        <!-- Boutons du formulaire !-->
        <p class="submit">
            <?php
                if ($id == null){
                    echo "<input type = 'submit' name = 'submitAdd' value = 'Add' />";
                } else {
                    echo "<input type = 'submit' name = 'submitAdd' value = 'Save' />";
                }
                $redirection="CDA/core_view_list&gcd_menu=CDA";
            ?>
            <input type='button' name='cancel' onclick="redirection('index.php?p=CDA/core_view&core_id=<?php $sample->_sample_core_id?>')" value = 'Cancel' />
        </p>
    </form>
    <?php
    }
}
?>