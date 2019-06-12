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

function update_option( $name, $value, $user_id = 0, $autoload = 0 ) {
    if( $name !== '' && $value !== '' ){
        $c = select( 'options', '*', 'option_name = "'.$name.'" AND option_scope = "'.$user_id.'"', 1 );
        if( $c ) {
            $u = update( 'options', ['option_value', 'option_scope', 'option_load' ], [ $value, $user_id, $autoload ], 'option_name = "'.$name.'"' );
            return $u ? $u : false;
        } else {
            $u = insert( 'options', [ 'option_name', 'option_value', 'option_scope', 'option_load' ], [ $name, $value, $user_id, $autoload ] );
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

function get_option( $name, $user = false, $key = 'name' ) {
    global $db;
    if( $db ) {
        $q = $key == 'id' ? 'option_id = "'.$name.'"' : 'option_name = "'.$name.'"';
        $query = $user == 1 ? $q . ' AND option_scope = "'.$_SESSION['user_id'].'"' : $q;
        $o = select( 'options', 'option_value', $query, 1 );
        return $o ? $o['option_value'] : false;
    }
}


function get_options( $opn, $user = false, $key = 'name' ) {
    global $db; $q = '';
    if( is_array( $opn ) ){
        foreach( $opn as $op ){
            if( $key == 'id' ){
                $q .= 'option_id = "'.$op.'" OR ';
            } else {
                $q .= 'option_name = "'.$op.'" OR ';
            }
        }
    } else {
        if( $key == 'id' ){
            $q .= 'option_id = "'.$opn.'" OR ';
        } else {
            $q .= 'option_name = "'.$opn.'" OR ';
        }
    }
    $q = !empty( $q ) ? substr($q, 0, -3) : $q;
    $query = $user ? $q . ' AND option_scope = "'.$_SESSION['user_id'].'"' : $q;
    $o = select( 'options', 'option_name, option_value', $query );
    $d = [];
    if( is_array( $o ) ){
        foreach( $o as $k => $v ){
            $d[$v['option_name']] = $v['option_value'];
        }
    }
    return !empty( $d ) && count( $d ) == 1 ? $o[0] : $d;
}

function remove_option( $opn, $user = false, $key = 'name' ) {
    $q = '';
    if( $key == 'id' ){
        $q .= 'option_id = "'.$opn.'" ';
    } else {
        $q .= 'option_name = "'.$opn.'" ';
    }
    $query = $user ? $q . ' AND option_scope = "'.$_SESSION['user_id'].'"' : $q;
    $o = delete( 'options', $query );
    return $o ? true : false;
}

function remove_user_options( $user_id ) {
    $o = delete( 'options', 'option_scope = "'.$user_id.'"' );
    return $o ? true : false;
}

function save_post_option( $option, $user = false ){
    if( isset( $_POST[$option] ) ){
        $o = update_option( $option, $_POST[$option], $user ? $_SESSION['user_id'] : 0 );
    }
}

function save_post_options( $options ){
    if( is_array( $options ) ){
        foreach( $options as $op ){
            if( is_array( $op ) ){
                $u = isset( $op[1] ) && $op[1] ? 1 : 0;
                save_post_option( $op[0], $u );
            } else {
                save_post_option( $op );
            }
        }
    }
}

// Inserts data into table Ex: insert( 'users', array( 'name', 'age' ), array( 'ahmed', '38') );
function insert( $table, $names, $values ){
    global $db;
    if( $db ) {
        if (is_array($names) && is_array($values)) {
            if (count($names) == count($values)) {
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
                $q = "INSERT INTO $table ($names) VALUES ($fv)";

                $df = debug_backtrace();

                elog( $q, 'insert', $df[0]['line'], $df[0]['file'], $table );
                //return $q;
                $query = $db ? mysqli_query($db, $q) : '';
                if ($query) {
                    return mysqli_insert_id($db);
                } else {
                    elog( mysqli_error($db), 'error', $df[0]['line'], $df[0]['file'], $table );
                    return false;
                }
            } else {
                elog($table . ' has ' . count($names) . ' columns but ' . count($values) . ' values provided', 'error', $df[0]['line'], $df[0]['file'], $table );
                return false;
            }
        }
    } else {
        return [];
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

    $df = debug_backtrace();
    //$df = !empty($df) && is_array($df) && isset($df[0]['file']) && isset($df[0]['line']) ? '<<'.$df[0]['line'].'>> {'.str_replace(COREPATH,'',$df[0]['file']).'}' : '';

    global $db;
    $q = "UPDATE $table SET ". $logic . " where ". $where;

    elog( $q, 'update', $df[0]['line'], $df[0]['file'], $table );

    $dq = $db->query($q);
    if ( $dq === TRUE && $db->affected_rows > 0 ){
        return true;
    } else {
        elog( mysqli_error( $db ), 'error', $df[0]['line'], $df[0]['file'], $table );
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
    if( $db ) {
        $cols = $cols == "" ? "*" : $cols;
        if (!is_array($table)) {
            $o = $count ? "SELECT COUNT('" . $cols . "') FROM $table " : "SELECT " . $cols . " FROM $table ";
            $target = $table;
        } else {
            $o = "SELECT " . $cols . " FROM $table[1] $table[0] $table[2] ON $table[1].$table[3] = $table[2].$table[4] ";
            $target = $table[1];
        }
        $o .= !empty($where) && $where !== '' ? ' WHERE ' . $where : '';
        $o .= !empty($group) ? "GROUP BY " . $group : "";
        $o .= !empty($order_by) && $order_by !== '' ? ' ORDER BY ' . $order_by : '';
        $o .= !empty($sort) && $sort !== '' && !empty($order_by) && $order_by !== '' ? ' ' . $sort : '';
        $o .= $limit >= 1 ? ' LIMIT ' . $limit : '';
        $o .= $offset > 1 ? ' OFFSET ' . $offset : '';

        $df = debug_backtrace();

        elog( $o, 'select', $df[0]['line'], $df[0]['file'], $target );

        $q = $db ? mysqli_query($db, $o) : '';

        if ($q) {
            $data = [];
            while ($row = $q->fetch_assoc()) {
                $data[] = $row;
            }
            if ($count && !empty($data)) {
                return end($data[0]);
            } else if (!empty($data)) {
                if ($limit == 1) {
                    return $data[0];
                } else {
                    return $data;
                }
            }
        } else {
            elog( mysqli_error( $db ), 'error', $df[0]['line'], $df[0]['file'], $target );
        }
    } else {
        return [];
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

    $df = debug_backtrace();

    elog( $q, 'delete', $df[0]['line'], $df[0]['file'], $table );

    $del = mysqli_query( $db, $q );

    if( mysqli_error( $db ) ) {
        elog( mysqli_error($db), 'delete', $df[0]['line'], $df[0]['file'], $table );
    } else {
        $rows = mysqli_affected_rows( $db );
        return $rows > 0 ? $rows : 0;
    }
}

function process_data() {
    $a = $_POST;
    if( !empty( $a['target'] ) ){
        $cry = Crypto::initiate();
        $table = $cry->decrypt($a['target']);
        unset($a['target']);

        if( !empty( $a['id'] ) ){
            $id = $cry->decrypt( $a['id'] );
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

        if( !empty( $id ) ) {
            if ($query) {
                echo json_encode([1, T('Updated Successfully')]);
            } else {
                echo json_encode([0, T('not updated, maybe data is empty')]);
            }
        } else {
            if( $query ){
                echo json_encode([1, T('Added Successfully')]);
            } else {
                echo json_encode([0, T('not stored, please try again or contact support')]);
            }
        }
    } else {
        echo json_encode([0,T('Database not targeted properly, please contact support')]);
    }
}

function trash_data() {
    unset($_POST['action']);
    $cry = Crypto::initiate();
    $d = $cry->decrypt( $_POST['query'] );
    if( $d = explode('|',$d) ){
        $t = delete( $d[0], $d[1].' = '.$d[2] );
        if( $t ){
            echo json_encode([1,T('Data deleted successfully')]);
        } else {
            echo json_encode([0,T('Delete data failed due to query misinterpret, please contact support')]);
        }
    } else {
        echo json_encode([0,T('Delete data failed due to query misinterpret, please contact support')]);
    }
}

function create_table( $table ){
    if( is_array( $table ) && defined('APPCON') && APPCON ){

        // Table Exist Check
        global $db;
        $check = "SHOW TABLES LIKE '".$table[0]."'";
        $target = $table[0];

        $exist = mysqli_query( $db, $check );

        if( $exist->num_rows > 0 ) {

            if ( is_array( $table[2] ) ) {
                foreach ( $table[2] as $col ) {
                    if ( !empty($col[0]) && !empty($col[1]) ) {
                        $col[2] = !empty($col[2]) ? $col[2] : '13';
                        $col[3] = $col[3] == 0 ? 'NULL' : 'NOT NULL';
                        //if (in_array($col[1], ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT'])) {
                        create_column( $table[0], $table[1].'_'.$col[0], $col[1], $col[2], $col[3] );
                        //$query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . ' ' . $col[3];
                        //} else {
                        //create_column( $table[0], $col[0], $col[1], $col[2], $col[3] );
                        //$query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . '(' . $col[2] . ') ' . $col[3];
                        //}
                    }
                }
            }

        } else {

            $target = $table[0];

            $query = 'CREATE TABLE IF NOT EXISTS ' . $table[0] . ' (' . $table[1] . '_id INT(13) AUTO_INCREMENT PRIMARY KEY';
            if (is_array($table[2])) {
                foreach ($table[2] as $col) {
                    if (!empty($col[0]) && !empty($col[1])) {
                        $col[2] = !empty($col[2]) ? $col[2] : '13';
                        $col[3] = $col[3] == 0 ? 'NULL' : 'NOT NULL';
                        if (in_array($col[1], ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT', 'DOUBLE'])) {
                            $query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . ' ' . $col[3];
                        } else {
                            $query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . '(' . $col[2] . ') ' . $col[3];
                        }
                    }
                }
            }
            $query .= ") DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";

            $df = debug_backtrace();

            //elog('|TABLE| ' . $query . ' ' . $df . PHP_EOL . PHP_EOL);
            elog( $query, 'log', $df[0]['line'], $df[0]['file'], $target );

            if (!empty($query)) {
                if (mysqli_query($db, $query) == 1) {
                    return true;
                } else {
                    elog( mysqli_error($db), 'error', $df[0]['line'], $df[0]['file'], $target );
                    return false;
                }
            }
        }
    }
}

function create_tables( $tables ) {
    if( is_array( $tables ) ){
        $query = '';
        foreach( $tables as $table ){
            create_table( $table );
            /* $query .= 'CREATE TABLE IF NOT EXISTS '.$table[0].' ('.$table[1].'_id INT(13) AUTO_INCREMENT PRIMARY KEY';
            if( is_array( $table[2] ) ){
                foreach( $table[2] as $col ){
                    if( !empty( $col[0] && !empty( $col[1] ) ) ) {
                        if (in_array($col[1], ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT'])) {
                            $query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . ' ' . $col[3];
                        } else {
                            $query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . '(' . $col[2] . ') ' . $col[3];
                        }
                    }
                }
            }
            $query .= ');'; */
        }
    }
}

function create_column( $table, $column, $type = 'TEXT', $length = '13', $null = true, $default = '' ){
    $type == 'BOOLEAN' ? $type = 'TINYINT' : '';
    $length = in_array($type, ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT']) ? '' : $length;
    $null = $null ? 'NULL' : 'NOT NULL';
    $length = !empty($length) ? '('.$length.')' : '';
    $exist = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
    $query = "ALTER TABLE $table ADD $column $type$length $null";
    $query .= !empty($default) ? ' default "'.$default.'"' : '';

    $df = debug_backtrace();
    elog( $query, 'column', $df[0]['line'], $df[0]['file'], $table . '-' . $column );

    global $db;
    $e = mysqli_query( $db, $exist );
    if( $e && $e->fetch_assoc()['COUNT(*)'] == 0 ){
        if( mysqli_query( $db, $query ) ){
            return true;
        } else {
            elog( $column.' '.mysqli_error($db), 'error', $df[0]['line'], $df[0]['file'], $table . '-' . $column );
            return false;
        }
    }
}

function query( $query ) {

    global $db;
    $e = mysqli_query( $db, $query );

    return $e;

}

function export_tables( $tables = [] ) {

}

function import_tables( $file ) {

    if( file_exists( $file ) ){

        $q = '';

        $lines = file( $file );

        global $db;

        $df = debug_backtrace();
        //$df = !empty($df) && is_array($df) && isset($df[0]['file']) && isset($df[0]['line']) ? '<<'.$df[0]['line'].'>> {'.str_replace(COREPATH,'',$df[0]['file']).'}' : '';

        //elog('|SELECT| '.$o.' '.$df.PHP_EOL.PHP_EOL);

        $q = $db ? mysqli_query($db, $o) : '';

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
        } else {
            elog('|ERROR| '.mysqli_error($db).' '.$df);
        }

    }

}

function log_man() {

}