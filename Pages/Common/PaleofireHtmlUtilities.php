<?php
/* 
 * fichier \Pages\Common\PaleofireHtmlUtilities.php
 * 
 */

class PaleofireHtmlUtilities {

    static function insertHTMLSelect($id_select, $array_id_value, $entity_name, $page_reference, $current_id_object=null) {

        echo "<select id = 'select_".$id_select."' name = '".$entity_name."' onchange=\"if(this.value!=0){location.href='index.php?p=".$page_reference."&amp;".$entity_name."='+this.value;}\" >
		\n<option value='0'>Select ".$entity_name."</option>
		\n<option value='ALL'>ALL</option>";
        foreach ($array_id_value as $id_object => $value_object) {
            if ($id_object == $current_id_object) {
                echo "\n<option value = '" . $id_object . "' selected='selected'>" . $value_object . "</option>";
            } else {
                echo "\n<option value = '" . $id_object . "' >" . $value_object . "</option>";
            }
        }
        echo "</select>";
    }

}
