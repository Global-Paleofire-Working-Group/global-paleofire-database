<?php

function selectHTML($select_name, $select_id, $class_name, $id_selected, $where_clause = null,$page_ref = null, $entity_name = null) {
    if ($page_ref == null || $entity_name == null) {
        ?>
        <select name = '<?php echo $select_name; ?>' id = '<?php echo $select_id; ?>' >
        <?php
    } else {
        ?>
                <select name = '<?php echo $select_name; ?>' id = '<?php echo $select_id; ?>' 
                onchange="location.href = 'index.php?p=<?php echo $page_ref; ?>&<?php echo $entity_name; ?>=' + this[this.selectedIndex].value;">
                    <?php
                }
                ?>
            <option value = 'NULL'>Select</option>
            <?php

            $result_all_objects = $class_name::getAllIdName($where_clause);
            foreach ($result_all_objects as $id_object => $value_basinSize) {
                if ($id_object === $id_selected) {
                    $select = " selected ";
                } else {
                    $select = "";
                }
                echo "<option value='" . $id_object . "'  $select>" . htmlspecialchars($value_basinSize) . "</option>";
            }
            unset($result_all_objects);
            ?>
        </select>
        <?php
    }
    
    function selectMultipleHTML($select_name, $select_id, $class_name, $id_selected, $where_clause = null, $multiple = null, $size = null) {
        ($size != null && is_numeric($size)) ? $size = ' size="'.$size.'" ' : "";
        ($multiple != null && $multiple == true) ? $multiple = ' multiple="multiple" ' : "";
        ?>
        <select style='width:100%' name = '<?php echo $select_name; ?>' id = '<?php echo $select_id; ?>' <?php echo $size.$multiple; ?>>
            <!-- <option value = 'NULL'>Select</option> -->
            <?php

            $result_all_objects = $class_name::getAllIdName($where_clause);
            foreach ($result_all_objects as $id_object => $value_basinSize) {
                if ($id_object === $id_selected) {
                    $select = " selected ";
                } else {
                    $select = "";
                }
                echo "<option value='" . $id_object . "'  $select>" . $value_basinSize . "</option>";
            }
            unset($result_all_objects);
            ?>
        </select>
        <?php
    }
    
    function selectMultipleOptionsHTML($select_name, $select_id, $class_name, $ids_selected, $multiple = null, $size = null) {
        ($size != null && is_numeric($size)) ? $size = ' size="'.$size.'" ' : "";
        ($multiple != null && $multiple == true) ? $multiple = ' multiple="multiple" ' : "";
        ?>
        <select style='width:100%' name = '<?php echo $select_name; ?>' id = '<?php echo $select_id; ?>' <?php echo $size.$multiple; ?>>
            <?php
            var_dump($ids_selected);
            $result_all_objects = $class_name::getAllIdName();
            foreach ($result_all_objects as $id_object => $value_basinSize) {
                if (array_search($id_object, $ids_selected) === FALSE){
                    $select = "";
                } else {
                    $select = " selected ";
                }
                echo "<option value='" . $id_object . "'  $select>" . $value_basinSize . "</option>";
            }
            unset($result_all_objects);
            ?>
        </select>
        <?php
    }
    
    
    function getTabJavascriptHTML($select_name, $select_id, $class_name, $id_selected) {
        $result_all_objects = $class_name::getAllIdName(null);
        $tabstr = "[";
        foreach ($result_all_objects as $id_object => $value_basinSize) {
            $order   = array("\r\n", "\n", "\r");
            $replace = '<br />';

            $tabstr.= "{id:\"" . $id_object . "\",label:\"". str_replace($order, $replace, addslashes($value_basinSize)) . "\"},";
        }
        $tabtrim = rtrim($tabstr, ',');
        return $tabtrim . "]";
    }
    