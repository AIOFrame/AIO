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

function update_option( $name, $value, $user_id = 0, $autoload = false ) {
    if( !empty( $name ) && !empty( $value )){
        $c = select( 'options', '*', 'option_name = "'.$name.'" AND option_scope = "'.$user_id.'"', 1 );
        if( $c ) {
            $u = update( 'options', ['option_value', 'option_scope', 'option_autoload' ], [ $value, $user_id, $autoload ], 'option_name = "'.$name.'"' );
            return $u ? $u : false;
        } else {
            $u = insert( 'options', [ 'option_name', 'option_value', 'option_scope', 'option_autoload' ], [ $name, $value, $user_id, $autoload ] );
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
    global $db;
    if( $id ){
        $o = "SELECT * FROM options WHERE ID = '$opn'";
    } else {
        $o = "SELECT * FROM options WHERE option_name = '$opn'";
    }
    $q = mysqli_query( $db, $o );
    $data = mysqli_fetch_all( $q, MYSQLI_ASSOC );
    return $data;
}

function remove_option( $opn, $id = false ) {
    global $db;
    if( $id ){
        $ro = "DELETE FROM options WHERE ID = '$opn'";
    } else {
        $ro = "DELETE FROM options WHERE option_name = '$opn'";
    }
    if ( mysqli_query( $db, $ro )) {
        return true;
    } else {
        return false;
    }
}

// Inserts data into table Ex: insert( 'users', array( 'name', 'age' ), array( 'ahmed', '38') );
function insert( $table, $names, $values ){
    if( is_array( $names ) && is_array( $values ) ) {
        if( count( $names ) == count( $values ) ){
            global $db;
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
            elog($q);
            //return $q;
            if( mysqli_query( $db, $q ) ) {
                return mysqli_insert_id( $db );
            } else {
                return false;
            }
        } else {
            elog("Mismatch! Number of columns is  ". count($names) . " while number of values is ". count($values));
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

    global $db;
    $q = "UPDATE $table SET ". $logic . " where ". $where;
    elog($q);
    if ( $db->query($q) === TRUE){
    return true;
    } else {
    return false;
    }
}

function update_val( $table, $col,$val, $where = '' ){
    global $db;
    $q = "UPDATE $table SET $col ='".$val."' WHERE ".$where;
    if( mysqli_query( $db, $q ) ) {
        return true;
    } else {
        return false;
    }
}

function total( $table, $cols = '*', $where = '', $limit = 0, $offset = 0 , $group='' ) {
    return select( $table, $cols, $where, $limit, $offset, $group, true );
}

function select( $table, $cols = '*', $where = '', $limit = 0, $offset = 0 , $group = '', $count = false , $order_by = '', $sort = '') {
    global $db;
    $cols = $cols == "" ? "*" : $cols;
    $o = $count ? "SELECT COUNT('". $cols ."') FROM $table " : "SELECT ". $cols ." FROM $table ";
    $o .= !empty( $where ) && $where !== '' ? ' WHERE '.$where : '';
    $o .= !empty( $group ) ?  "GROUP BY ".$group: ""  ;
    $o .= !empty( $order_by ) && $order_by !== '' ? ' ORDER BY '.$order_by : '';
    $o .= !empty( $sort ) && $sort !== '' && !empty( $order_by ) && $order_by !== '' ? ' '.$sort : '';
    $o .= $limit >= 1 ? ' LIMIT '.$limit : '';
    $o .= $offset > 1 ? ' OFFSET '.$offset : '';

    elog($o);

    $q = mysqli_query( $db, $o );

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
    global $db;
    $o = "SELECT * FROM $table1 LEFT JOIN $table2 ON $conditions ";
    $o .= $limit > 1 ? ' LIMIT ' . $limit : '';
    elog($o);

    $q = mysqli_query($db, $o);

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
    global $db;
    if( $where ){
        $o = "SELECT $cols FROM $table WHERE ".$where;
    } else {
        $o = "SELECT $cols  FROM ".$table;
    }
    if( !$array ){
        $o .= " LIMIT 1";
    }

    $q = mysqli_query( $db, $o );

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
    global $db;
    if(is_array($logic)){
        $q = "DELETE FROM $table WHERE $logic[0] = $logic[1]";
    } else {
        $q = "DELETE FROM $table WHERE $logic";
    }

    elog( $q );

    if (mysqli_query( $db, $q )) {
        return true;
    } else {
        return false;
    }
}

function process_data() {
    $a = $_POST;
    if( !empty( $a['target'] ) ){
        $cry = Crypto::initiate();
        $table = $cry->decrypt($a['target']);
        unset($a['target']);

        if( !empty( $a['id'] ) ){
            $id = $a['id'];
            unset($a['id']);
        }

        if( !empty( $a['pre'] ) ){
            $pre = $a['pre'];
            unset($a['pre']);
        } else {
            $pre = '';
        }

        if( !empty( $a['d'] ) ){
            $a[$pre.'_'.$a['d']] = date('Y-m-d');
            unset($a['d']);
        }

        if( !empty( $a['dt'] ) ){
            $a[$pre.'_'.$a['dt']] = date('Y-m-d H:i:s');
            unset($a['dt']);
        }

        if( !empty( $a['by'] ) ){
            $auths = explode(',',str_replace(' ','',$a['by']));
            if( is_array( $auths ) ){
                foreach( $auths as $auth ){
                    $a[$pre.'_'.$auth] = get_current_user_id();
                }
            }
            unset($a['by']);
        }

        if( !empty( $a['h'] ) ){
            $cry = Crypto::initiate();
            $hs = unserialize($cry->decrypt($a['h']));
            if( is_array( $hs ) ){
                foreach( $hs as $k => $v ){
                    $a[$pre.'_'.$k] = $v;
                }
            }
            unset($a['h']);
        }

        $keys = prepare_keys( $a );
        $values = prepare_values( $a );

        if( !empty( $id ) ){
            $query = update( $table, $keys, $values, $pre.'_id = "'.$id.'"' );
        } else {
            $query = insert( $table, $keys, $values );
        }

        if( $query ){
            echo json_encode([1,'Added Successfully']);
        } else {
            echo json_encode([0,'Could not insert data, please try again or contact admin']);
        }
    } else {
        echo json_encode([0,'Database not targeted properly, please contact admin']);
    }
}

function create_table( $table ){
    if( is_array( $table ) ){
        $query = 'CREATE TABLE IF NOT EXISTS '.$table[0].' ('.$table[1].'_id INT(13) AUTO_INCREMENT PRIMARY KEY';
        if( is_array( $table[2] ) ){
            foreach( $table[2] as $col ){
                if( in_array( $col[1], [ 'BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT' ] ) ){
                    $query .= ','.$table[1].'_'.$col[0].' '.$col[1].' '.$col[3];
                } else {
                    $query .= ','.$table[1].'_'.$col[0].' '.$col[1].'('.$col[2].') '.$col[3];
                }
            }
        }
        $query .= ")";
        elog($query);
        if( !empty( $query ) ){
            global $db;
            if( mysqli_query( $db, $query ) ){
                return true;
            } else {
                return false;
            }
        }
    }
}

function create_tables( $tables ) {
    if( is_array( $tables ) ){
        foreach( $tables as $table ){
            create_table( $table );
        }
    }
}