<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once './Models/Site.php';
require_once './Models/Core.php';
require_once './Models/Sample.php';
require_once './Models/Charcoal.php';
require_once './Models/DateInfo.php';
require_once './Models/DateComment.php';
//include_once './Library/DatabaseTable.php';

$query_nb_rows = "SELECT CONCAT(TABLE_NAME,'(', TABLE_SCHEMA, ')') as TABLE_NAME_SCHEMA, TABLE_ROWS FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'gcd_paleofire' OR TABLE_SCHEMA = 'gcd_paleofire_in_progress' order by TABLE_NAME ";
$res = queryToExecute($query_nb_rows);
if ($res != NULL){
    $rows = fetchAll($res);
    echo '<table>';
    foreach ($rows as $values){
        echo '<tr><td>'.$values["TABLE_NAME_SCHEMA"]."</td><td>".$values["TABLE_ROWS"]."</td></tr>";
    }
    echo '</table>';
}



$tables = ["Site", "Core", "Sample", "Charcoal", "DateInfo", "DateComment", "AgeModel", "Age", "EstimatedAge", "Affiliation", "Contact", "Depth", "NoteCore", "Publi"];

foreach($tables as $table){
    echo '<h3>'.$table.'</h3>';
    echo '<h4>Eléments existants dans gcd_paleofire mais supprimés dans gcd_paleofire_in_progress</h4>';
    
    $query = "select * from gcd_paleofire.".$table::TABLE_NAME." where ".$table::ID." NOT IN (select ".$table::TABLE_NAME.".".$table::ID." from gcd_paleofire_in_progress.".$table::TABLE_NAME.")";
    echo '<p>'.$query.'</p>';
    $res = queryToExecute($query);
    if ($res != NULL){
        $values = fetchAll($res);
        echo '<p>'.count($values).' '.$table.'</p>';
        
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
                echo $value[$table::ID].' '.$value[$table::ID_SITE].' '.$value[$table::GCD_ACCESS_ID].'<br/>';
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
    }
    
    echo '<h4>Nouveaux éléments de gcd_paleofire_in_progress</h4>';
    
    $query = "select * from gcd_paleofire_in_progress.".$table::TABLE_NAME." where ".$table::ID." NOT IN (select ".$table::TABLE_NAME.".".$table::ID." from gcd_paleofire.".$table::TABLE_NAME.")";
    echo '<p>'.$query.'</p>';
    $res = queryToExecute($query);
    if ($res != NULL){
        $values = fetchAll($res);
        echo '<p>'.count($values).' '.$table.'</p>';
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
    }
    
}