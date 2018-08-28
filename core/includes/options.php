<?php

function update_options() {
    //$postdata = file_get_contents("php://input");
    foreach( $_POST as $key => $value ){
        if(is_array($value)) {
            if (array_key_exists('unique', $value)) {
                $r = update_option($key, $value['unique'], get_current_user_id());
            } else if (array_key_exists('encrypt', $value)) {
                $r = update_option($key, aa_encrypt($value['encrypt']) );
            } else if (array_key_exists('encrypt,unique', $value)) {
                $r = update_option($key, aa_encrypt($value['encrypt']), get_current_user_id());
            } else if (array_key_exists('no', $value)) {
                // Do nothing
            } else {
                $r = update_option( $key, serialize( $value ) );
            }
        } else {
            $r = update_option( $key, $value );
        }
        $r;
    };
}

function update_option( $name, $value, $user_id = 0 ) {
    if( !empty( $name ) && !empty( $value )){
        $c = select( 'options', '*', 'option_name = "'.$name.'" AND option_scope = "'.$user_id.'"', 1 );
        if( $c ) {
            $u = update( 'options', ['option_value', 'option_scope' ], [ $value, $user_id ], 'option_name = "'.$name.'"' );
            return $u ? $u : false;
        } else {
            $u = insert( 'options', [ 'option_name', 'option_value', 'option_scope'], [ $name, $value, $user_id ] );
            return $u ? $u : false;
        }
    }
}

function add_option( $name, $value, $user_id = 0 ) {
    if( !empty( $name ) && !empty( $value )){
        $o = insert( 'options', [ 'option_name', 'option_value', 'option_scope' ], [ $name, $value, $user_id ] );
        return $o ? $o : false;
    }
}

function get_option( $name, $user_id = '' ) {
    $query = !empty( $option_id ) ? 'option_name = "'.$name.'" AND option_scope = "'.$user_id.'"' : 'option_name = "'.$name.'"';
    $o = select( 'options', 'option_value', $query, 1 );
    return $o ? $o['option_value'] : false;
}


function get_options( $opn, $id = false ) {
    global $conn;
    if( $id ){
        $o = "SELECT * FROM options WHERE ID = '$opn'";
    } else {
        $o = "SELECT * FROM options WHERE option_name = '$opn'";
    }
    $q = mysqli_query( $conn, $o );
    $data = mysqli_fetch_all( $q, MYSQLI_ASSOC );
    return $data;
}

function remove_option( $opn, $id = false ) {
    global $conn;
    if( $id ){
        $ro = "DELETE FROM options WHERE ID = '$opn'";
    } else {
        $ro = "DELETE FROM options WHERE option_name = '$opn'";
    }
    if ( mysqli_query( $conn, $ro )) {
        return true;
    } else {
        return false;
    }
}

// Inserts data into table Ex: insert( 'users', array( 'name', 'age' ), array( 'ahmed', '38') );
function insert( $table, $names, $values ){
    if( is_array( $names ) && is_array( $values ) ) {
        if( count( $names ) == count( $values ) ){
            global $conn;
            $names = implode( ',', $names );
            $fv = "'";
            foreach( $values as $value ){
                if( is_array( $value ) ){
                    $fv .= implode(',',$value)."','";
                } else {
                    $fv .= $value."','";
                }
            }
            $fv = substr($fv,0, -2);
            $q = "INSERT INTO $table ($names) VALUES ($fv)";
            error_log($q);
            //return $q;
            if( mysqli_query( $conn, $q ) ) {
                return mysqli_insert_id( $conn );
            } else {
                return false;
            }
        } else {
            error_log("Mismatch! Number of columns is  ". count($names) . " while number of values is ". count($values));
            return false;
        }
    }
}

/***
 * This function converts arrays of columns and their values into string that gets updated on MYSQL.
 * @param $table
 * @param $cols
 * @param $values
 * @param string $where
 * @return bool
 */
function update( $table, $cols, $values, $where = '' ){
    $logic  = "";
    if( is_string( $cols ) ){
        $cols = explode( ',', $cols );
        $values = explode( ',', $values );
    }
    if ( count( $cols ) == count( $values ) ) {
        foreach( $cols as $i => $col ){
            if( is_array( $values[$i] ))
                $logic .= $col . " = '" . serialize( $values[$i] ) . "',";
            else
                $logic .= $col . " = '" . $values[$i] . "', ";
        }
    }
    $logic = substr( $logic, 0, -2 );

    global $conn;
    $q = "UPDATE $table SET ". $logic . " where ". $where;
    error_log($q);
    if ( $conn->query($q) === TRUE){
    return true;
    } else {
    return false;
    }
}

function update_val( $table, $col,$val, $where = '' ){
    global $conn;
    $q = "UPDATE $table SET $col ='".$val."' WHERE ".$where;
    if( mysqli_query( $conn, $q ) ) {
        return true;
    } else {
        return false;
    }
}

function total( $table, $cols = '*', $where = '', $limit = 0, $offset = 0 , $group='' ) {
    return select( $table, $cols, $where, $limit, $offset, $group, true );
}

function select( $table, $cols = '*', $where = '', $limit = 0, $offset = 0 , $group = '', $count = false , $order_by = '', $sort = '') {
    global $conn;
    $cols = $cols == "" ? "*" : $cols;
    $o = $count ? "SELECT COUNT('". $cols ."') FROM $table " : "SELECT ". $cols ." FROM $table ";
    $o .= !empty( $where ) && $where !== '' ? ' WHERE '.$where : '';
    $o .= !empty( $group ) ?  "GROUP BY ".$group: ""  ;
    $o .= !empty( $order_by ) && $order_by !== '' ? ' ORDER BY '.$order_by : '';
    $o .= !empty( $sort ) && $sort !== '' && !empty( $order_by ) && $order_by !== '' ? ' '.$sort : '';
    $o .= $limit >= 1 ? ' LIMIT '.$limit : '';
    $o .= $offset > 1 ? ' OFFSET '.$offset : '';

    error_log($o);
//    error_log(str_replace(ROOTPATH.'apps/','',debug_backtrace()[0]['file']).' In line ' . debug_backtrace()[0]['line']);
//    error_log(' ');

    $q = mysqli_query( $conn, $o );

    if( $q ){
        $data = [];
        while ($row = $q->fetch_assoc()) {
            $data[] = $row;
        }
        if( $count && !empty( $data ) ){
            return end( $data[0] );
        } else if( !empty( $data ) ){
            if( $limit == 1 ){
                return $data[0];
            } else {
                return $data;
            }
        }
    }
}

function left_join($table1, $table2, $conditions, $limit){
    global $conn;
    $o = "SELECT * FROM $table1 LEFT JOIN $table2 ON $conditions ";
    $o .= $limit > 1 ? ' LIMIT ' . $limit : '';
    error_log($o);

    $q = mysqli_query($conn, $o);

    if ($q) {
        $data = [];
        while ($row = $q->fetch_assoc()) {
            $data[] = $row;
        }
        if (!empty($data)) {
            if ($limit == 1) {
                return $data[0];
            } else {
                return $data;
            }
        }
    }

}

function select_val( $table,$cols, $array = false, $where = '' ) {
    global $conn;
    if( $where ){
        $o = "SELECT $cols FROM $table WHERE ".$where;
    } else {
        $o = "SELECT $cols  FROM ".$table;
    }
    if( !$array ){
        $o .= " LIMIT 1";
    }

    $q = mysqli_query( $conn, $o );

    if( $q ){
        $data = [];
        while ($row = $q->fetch_assoc()) {
            $data[] = $row;
        }
        if(!empty($data)){
            if( $array ){
                return $data;
            } else {
                return $data[0];
            }
        }
    }
}

function delete( $table, $logic ){
    global $conn;
    if(is_array($logic)){
        $q = "DELETE FROM $table WHERE $logic[0] = $logic[1]";
    } else {
        $q = "DELETE FROM $table WHERE $logic";
    }

    if (mysqli_query( $conn, $q )) {
        return true;
    } else {
        return false;
    }
}

function get_events(){
    $user_id = $_POST['user_id'];
    $room_id = $_POST['room_id'];

    $yesterday =   Date("D d-M-Y", Time() -  86400);
    $where = 'event_room ='. $room_id. " AND event_start_date > ".$yesterday;
    $meeting_events = select('events','',$where,'','','');
    if($meeting_events)
        echo json_encode($meeting_events);
    else
        echo "failed";
}

function create_table($table, $query){
    if (empty($result)) {
        global $conn;
        $app = !empty(sub_domain()) ? sub_domain() : get_domain();

        include(COREPATH . 'apps/' . $app . '/config.php');// load config and make connection
        $db = $config['database'];

        if ($conn->query($query) === TRUE) {

        }
    }

}