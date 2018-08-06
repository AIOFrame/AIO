<?php
// These functions are to be deleted later if will not used !

// Inserts data into table Ex: insert( 'users', array( 'name' => 'ahmed', 'age' => '38' ) );
/*function sinsert( $table, $assoc_array ) {
    global $conn;
    $ks = $vs = "";
    if( is_array( $assoc_array ) ) {
        foreach( $assoc_array as $k => $v) {
            $ks .= $k.',';
            if( is_array( $v ) ){
                $nv = str_replace('"',"'",json_encode($v));
                $vs .= '"'.$nv.'",';
            } else {
                $vs .= '"'.$v.'",';
            }
        }
    }
    $q = "INSERT INTO $table ( substr( $ks, 0, -1 ) ) VALUES ( substr( $vs, 0, -1 ) )";
    return $q;
    if( mysqli_query( $conn, $q ) ) {
        return mysqli_insert_id( $conn );
    } else {
        return false;
    }
}*/


function update_ondupkey($table, $names, $values)
{
    if (is_array($names) && is_array($values)) {
        if (count($names) == count($values)) {

            $names_arry = $names;
            $values_arry = $values;

            $update_stmnt = '';
            global $conn;
            $names = implode(',', $names);
            $fv = "'";
            foreach ($values as $value) {
                if (is_array($value)) {
                    $fv .= implode(',', $value) . "','";
                } else {
                    $fv .= $value . "','";
                }
            }
            $fv = substr($fv, 0, -2);
            foreach ($names_arry as $index => $n) {
                $update_stmnt .=  $n . '="' . $values_arry[$index] . '",';
            }

            $update_stmnt = substr($update_stmnt, 0, -1);

            $q = "INSERT INTO $table ($names) VALUES ($fv) ON DUPLICATE KEY UPDATE  $update_stmnt";
            if ($result=mysqli_query($conn, $q)) {
                return $result;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

//function insert_withfk($table, $names, $values, $pk, $pkv, $t2, $fk){
//    // If there is a fk in the table then update data
//
//    // Else if there is no fk
//    // Insert into main table
//    // Update fk in the other table
//
//    $rec = select($table, array(), "$pk = $pkv AND $fk != NULL");
//    if ($rec) {
//        $result = update($t2, $names, $values);
//        echo json_encode(array('Success', 'Successful', 'You have inserted New Data Successfully'));
//    } else {
//        $id = insert($table, $names, $values);
//        $result = insert($t2, $pk, $id);
//        if ($result)
//            echo json_encode(array('Success', 'Successful', 'You have inserted New Data Successfully'));
//    }
//
//
//}

// Ajax Functions
function ajax_update_ondupkey(){

    // Consider Primary key !! not user_id
    if (!empty ($_POST['data'])) {
        $d = $_POST['data'];
        if (isset($d['dpk']) && isset($d['check']) && isset($d['columns']) && isset($d['values']) && isset($d['dpk']) && isset($d['dpkv'])) {
            array_push($d['columns'], $d['dpk']);
            array_push($d['values'], $d['dpkv']);
            $result = update_ondupkey($d['check'], $d['columns'], $d['values']);
            if($result== 1){
                echo json_encode(array('success', 'Successful Update', 'You have Successfully Updated Information'));
            }
        }
    }

}

// This Ajax Call Function is to call all functions in ajax without explicitly expose their names in front end
// And with no need to create more than one version of the same function.
//
function axcll()
{
    if (!empty ($_POST['data'])) {
        $data = $_POST['data'];

        $func = 'func_' . $_POST['data']['name'];
        call_user_func_array($func, array($data));

    }
}