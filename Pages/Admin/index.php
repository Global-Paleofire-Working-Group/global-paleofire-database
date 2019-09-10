<?php /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>

<script type="text/javascript">
    function displayOrHide(anchor, elt){
        if ($(elt).is(":visible")){
            $(elt).hide();
            $(anchor).html('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>Show');
        } else {
            $(elt).show();
            $(anchor).html('<span class="glyphicon glyphicon-eye" aria-hidden="true"></span>Hide');
        }
    }
</script>
   
<?php
if (isset($_SESSION['started']) && isset($_SESSION['gcd_user_role']) && ($_SESSION['gcd_user_role'] == WebAppRoleGCD::ADMINISTRATOR || $_SESSION['gcd_user_role'] == WebAppRoleGCD::SUPERADMINISTRATOR)) {
    require_once './Models/Site.php';
    require_once './Models/Core.php';
    require_once './Models/Sample.php';
    require_once './Models/Charcoal.php';
    require_once './Models/DateInfo.php';
    require_once './Models/DateComment.php';
    closeCurrentDBConnexion();
    connectionBaseVersionned();
    //var_dump(getLabelBaseDeDonnées());

    $query_nb_rows = "SELECT TABLE_NAME, TABLE_SCHEMA, TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'gcd_paleofire' OR TABLE_SCHEMA = 'gcd_paleofire_in_progress' order by TABLE_NAME ";
    $res = queryToExecute($query_nb_rows);
    if ($res != NULL){
        $rows = fetchAll($res);
        $displayTable = NULL;
        foreach($rows as $values){
            if (substr($values['TABLE_NAME'],0,3) != "tt_"){

                $query = "SELECT count(*) as nb from gcd_paleofire.".$values['TABLE_NAME'];
                $res = queryToExecute($query);
                $tab = fetchAll($res);
                $displayTable[$values['TABLE_NAME']][0] = $tab[0]['nb'];

                $query = "SELECT count(*) as nb from gcd_paleofire_in_progress.".$values['TABLE_NAME'];
                $res = queryToExecute($query);
                $tab = fetchAll($res);
                $displayTable[$values['TABLE_NAME']][1] = $tab[0]['nb'];

            }
        }
        ?>
    <h3>Comparison of number of lines between GCD V03 (imported from Access) and GCD in progress</h3>
        <table>
            <tr>
                <th>Table</th>
                <th>gcd_paleofire</th>
                <th>gcd_paleofire_in_progress</th>
            </tr>    

        <?php
        foreach ($displayTable as $key=>$values){
            if(key_exists(0, $values) && key_exists(1, $values)){
                if($values[0] > $values[1]) echo '<tr><td>'.$key.'</td><td>'.$values[0].'</td><td class="bg-warning">'.$values[1]."</td></tr>";
                else if ($values[0] < $values[1]) echo '<tr><td>'.$key.'</td><td>'.$values[0].'</td><td class="bg-info">'.$values[1]."</td></tr>";
                else echo '<tr><td>'.$key.'</td><td>'.$values[0]."</td><td>".$values[1]."</td></tr>";
            }
        }?>

        </table>
        <?php
    }

    $tables = ["Site", "Core", "Sample", "Charcoal", "DateInfo", "DateComment", "AgeModel", "EstimatedAge", "Affiliation", "Contact", "Depth", "NoteCore", "Publi"];
    echo '<h3>New and deleted rows in database table <small>('.implode(', ',$tables).')</small></h3>';
    foreach($tables as $table){
        closeCurrentDBConnexion();
        connectionBaseVersionned();
        echo '<h4>'.$table.'</h4>';

        $query = "select * from gcd_paleofire.".$table::TABLE_NAME." where ".$table::ID." NOT IN (select ".$table::TABLE_NAME.".".$table::ID." from gcd_paleofire_in_progress.".$table::TABLE_NAME.")";
        //echo '<p>'.$query.'</p>';
        $res = queryToExecute($query);
        if ($res != NULL){
            $values = fetchAll($res);
            echo '<h5>'.count($values).' row(s) existing in gcd_paleofire but deleted in gcd_paleofire_in_progress ';
            echo '<button type="button" class="btn btn-info btn-sm" onclick="displayOrHide(this, table'.$table.');">Show</button></h5>';
            echo '<div style="display:none" id="table'.$table.'">';

            if($table == "Sample"){
                foreach($values as $value){
                    echo $value[$table::ID].' '.$value[$table::NAME].' '.$value[$table::ID_CORE].'<br/>';
                }
            } else if($table == "Charcoal"){
                foreach($values as $value){
                    echo $value[$table::ID].' '.$value[$table::ID_SAMPLE].'<br/>';
                }
            } else if($table == "Site"){
                foreach($values as $value){
                    echo $value[$table::ID].' '.$value[$table::NAME].' '.$value[$table::GCD_ACCESS_ID].'<br/>';
                }
            } else {
                foreach($values as $value){
                    if (key_exists($table::NAME, $value)){
                        echo $value[$table::ID].' '.$value[$table::NAME];
                    } else {
                        echo $value[$table::ID];
                    }
                    echo '<br/>';
                }
            }
            echo '</div>';
        }



        $query = "select * from gcd_paleofire_in_progress.".$table::TABLE_NAME." where ".$table::ID." NOT IN (select ".$table::TABLE_NAME.".".$table::ID." from gcd_paleofire.".$table::TABLE_NAME.")";
        //echo '<p>'.$query.'</p>';
        $res = queryToExecute($query);
        if ($res != NULL){
            $values = fetchAll($res);
            echo '<h5>'.count($values).' new row(s) in gcd_paleofire_in_progress ';
            echo '<button type="button" class="btn btn-info btn-sm" onclick="displayOrHide(this, table2'.$table.');">Show</button></h5>';
            echo '<div style="display:none" id="table2'.$table.'">';

            if($table == "Sample"){
                foreach($values as $value){
                    echo $value[$table::ID].' '.$value[$table::NAME].' '.$value[$table::ID_CORE].'<br/>';
                }
            } else if($table == "Charcoal"){
                foreach($values as $value){
                    echo $value[$table::ID].' '.$value[$table::ID_SAMPLE].'<br/>';
                }
            } else {
                foreach($values as $value){
                    if (key_exists($table::NAME, $value)){
                        echo $value[$table::ID].' '.$value[$table::NAME].'<br/>';
                    } else {
                        echo $value[$table::ID].'<br/>';
                    }
                }
            }
            echo '</div>';
        }

        closeCurrentDBConnexion();
        connectionBaseInProgress();    
    }
}