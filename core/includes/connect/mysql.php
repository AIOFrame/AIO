<?php

class DB {

    function connect(): mysqli {
        $connect = @mysqli_connect( DB_HOST, DB_USER, DB_PASS, DB_BASE );
        if ( $connect ) {
            mysqli_query($connect, "SET NAMES 'utf8'");
            mysqli_query($connect, 'SET CHARACTER SET utf8');
        } else {
            die( mysqli_connect_error() );
        }
        return $connect;
    }

    // TABLE FUNCTIONS

    /**
     * Create Table
     * @param array $table [ string 'table_name', string 'pre', [ [ string 'col_name', string 'type', int 'length', bool 'not null', string 'default' ] ] ]
     * @return bool
     */
    function create_table( array $table ): bool {
        if( is_array( $table ) && defined('APPCON') && APPCON ){

            // Table Exist Check
            $db = $this->connect();
            $check = "SHOW TABLES LIKE '".$table[0]."'";
            $target = $table[0];

            $exist = mysqli_query( $db, $check );

            if( $exist->num_rows > 0 ) {

                if ( is_array( $table[2] ) ) {
                    foreach ( $table[2] as $col ) {
                        if ( !empty($col[0]) && !empty($col[1]) ) {
                            $col[2] = !empty($col[2]) ? $col[2] : '13';
                            $col[3] = $col[3] == 0 ? 'NULL' : 'NOT NULL';
                            $col[4] = isset( $col[4] ) && !empty($col[4] ) ? $col[4] : '';
                            //if (in_array($col[1], ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT'])) {
                            $this->create_column( $table[0], $table[1].'_'.$col[0], $col[1], $col[2], $col[3], $col[4] );
                            //$query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . ' ' . $col[3];
                            //} else {
                            //create_column( $table[0], $col[0], $col[1], $col[2], $col[3] );
                            //$query .= ',' . $table[1] . '_' . $col[0] . ' ' . $col[1] . '(' . $col[2] . ') ' . $col[3];
                            //}
                        }
                    }
                    return 1;
                } else {
                    return 0;
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
                } else {
                    return 0;
                }
            }
        } else {
            return 0;
        }
    }

    /**
     * Create Tables
     * @param $tables [ [ string 'table_name', string 'pre', [ [ string 'col_name', string 'type', int 'length', bool 'not null', string 'default' ] ] ] ]
     * @return array
     */
    function create_tables( $tables ): array {
        $tables_created = [];
        if( is_array( $tables ) ){
            $query = '';
            foreach( $tables as $table ){
                $tables_created[] = $this->create_table( $table );
            }
        } else {
            $tables_created[] = 0;
        }
        return $tables_created;
    }

    /**
     * Create Column
     * @param string $table Table Name
     * @param string $column Column Name
     * @param string $type Column Type
     * @param string $length Column Length
     * @param bool $null Is Nullable
     * @param string $default Default Value
     * @return bool
     */
    function create_column( string $table, string $column, $type = 'TEXT', $length = '13', $null = true, $default = ''): bool {
        $type == 'BOOLEAN' ? $type = 'TINYINT' : '';
        $length = in_array($type, ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT','DOUBLE']) ? '' : $length;
        $null = $null ? 'NULL' : 'NOT NULL';
        $length = !empty($length) ? '('.$length.')' : '';
        $exist = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
        $query = "ALTER TABLE $table ADD $column $type$length $null";
        $query .= !empty($default) ? ' default "'.$default.'"' : '';

        $df = debug_backtrace();
        $db = $this->connect();
        $e = mysqli_query( $db, $exist );
        if( $e && $e->fetch_assoc()['COUNT(*)'] == 0 ){
            if( mysqli_query( $db, $query ) ){
                return true;
            } else {
                elog( $query, 'column', $df[0]['line'], $df[0]['file'], $table . '-' . $column );
                elog( $column.' '.mysqli_error($db), 'error', $df[0]['line'], $df[0]['file'], $table . '-' . $column );
                return false;
            }
        } else {
            return 0;
        }
    }

    // DATA FUNCTIONS

    /**
     * Insert data into Table
     * @param string $table Table Name
     * @param array $names Names of columns
     * @param array $values Data to insert
     * @return bool
     */
    function insert( string $table, array $names, array $values ): bool {
        $db = $this->connect();
        if( $db ) {
            $names = is_array( $names ) ? $names : explode(',',$names);
            if (is_array($names) && is_array($values)) {
                $df = debug_backtrace();
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

                    elog( $q, 'insert', $df[0]['line'], $df[0]['file'], $table );
                    //return $q;
                    $query = $db ? mysqli_query($db, $q) : '';
                    if ($query) {
                        return mysqli_insert_id($db);
                    } else {
                        elog( mysqli_error($db), 'error', $df[0]['line'], $df[0]['file'], $table );
                        return 0;
                    }
                } else {
                    elog($table . ' has ' . count($names) . ' columns but ' . count($values) . ' values provided', 'error', $df[0]['line'], $df[0]['file'], $table );
                    return 0;
                }
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Select data from Table
     * @param string $table Table Name
     * @param string $cols Columns to get data, Ex: 'user_name,user_dob'
     * @param string $where Where logic, Ex: 'user_id = 12'
     * @param int $limit Limit quantity of rows
     * @param int $offset Offset rows
     * @param string $group Group data rows
     * @param bool $count Only get count of data rows
     * @param string $order_by Order data by
     * @param string $sort
     * @return array
     */
    function select( string $table, $cols = '*', $where = '', $limit = 0, $offset = 0 , $group = '', $count = 0 , $order_by = '', $sort = ''): array {
        $db = $this->connect();
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
                } else {
                    return [];
                }
            } else {
                elog( mysqli_error( $db ), 'error', $df[0]['line'], $df[0]['file'], $target );
                return [];
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
    function update( $table, $cols, $values, $where = '' ): bool {
        $logic  = "";
        $cols = is_string( $cols ) ? explode( ',', $cols ) : $cols;
        $values = is_string( $values ) ? explode( ',', $values ) : $values;
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

        $db = $this->connect();
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

    /**
     * Delete rows from table
     * @param string $table Table Name
     * @param mixed $logic Logic ['user_id',12] or 'user_id = 12'
     * @return int
     */
    function delete( string $table, mixed $logic ): int {
        $db = $this->connect();
        if( is_array( $logic ) ) {
            $q = "DELETE FROM $table WHERE $logic[0] = $logic[1]";
        } else {
            $q = "DELETE FROM $table WHERE $logic";
        }

        $df = debug_backtrace();

        elog( $q, 'delete', $df[0]['line'], $df[0]['file'], $table );

        $del = mysqli_query( $db, $q );

        if( mysqli_error( $db ) ) {
            elog( mysqli_error($db), 'delete', $df[0]['line'], $df[0]['file'], $table );
            return 0;
        } else {
            $rows = mysqli_affected_rows( $db );
            return $rows > 0 ? $rows : 0;
        }
    }

    /**
     * Gets total row count
     * @param string $table Table Name
     * @param string $cols Columns to get data, Ex: 'user_name,user_dob'
     * @param string $where Where logic, Ex: 'user_id = 12'
     * @param int $limit Limit quantity of rows
     * @param int $offset Offset rows
     * @param string $group Group data rows
     * @return array
     */
    function total( string $table, $cols = '*', $where = '', $limit = 0, $offset = 0 , $group='' ) {
        return $this->select( $table, $cols, $where, $limit, $offset, $group, true );
    }

    /**
     * Runs custom MySQL Query
     * @param string $query Query
     * @return mixed
     */
    function query( string $query ): mixed {
        $db = $this->connect();
        $e = mysqli_query( $db, $query );
        return $e;
    }

    // AIO AJAX DATA FUNCTIONS

    // APP OPTIONS FUNCTIONS

    /**
     * Store setting by key and value
     * @param string $name Option Name / Key
     * @param string $value Option Value
     * @param int $user_id User ID to store for specific user
     * @return mixed
     */
    function add_option( string $name, string $value, $user_id = 0 ): mixed {
        if( !empty( $name ) && !empty( $value )){
            return $this->insert( 'options', [ 'option_name', 'option_value', 'option_scope' ], [ $name, $value, $user_id ] );
        } else {
            return false;
        }
    }

    /**
     * Update setting by key and value
     * @param string $name Option Name / Key
     * @param string $value Option Value
     * @param int $user_id User ID to store for specific user
     * @param int $autoload Auto load values to session
     * @return bool
     */
    function update_option( string $name, string $value, int $user_id = 0, int $autoload = 0 ): bool {
        if( $name !== '' && $value !== '' ){
            $c = $this->select( 'options', '*', 'option_name = "'.$name.'" AND option_scope = "'.$user_id.'"', 1 );
            if( $c ) {
                return $this->update( 'options', ['option_value', 'option_scope', 'option_load' ], [ $value, $user_id, $autoload ], 'option_name = "'.$name.'"' );
            } else {
                return $this->insert( 'options', [ 'option_name', 'option_value', 'option_scope', 'option_load' ], [ $name, $value, $user_id, $autoload ] );
            }
        } else {
            return false;
        }
    }

    /**
     * Update options by array param or $_POST
     * @param array $array Array of options [['key'=>'val'],['key2'=>'val2']]
     * @return bool
     */
    function update_options( array $array = [] ): bool {
        $r = false;
        $array = !empty( $array ) && isset( $_POST ) ? $_POST : [];
        foreach( $array as $key => $value ){
            // If value has optional parameters
            if( is_array( $value ) ) {
                // Unique for current logged in user
                if ( array_key_exists('unique', $value) ) {
                    $r = $this->update_option($key, $value['unique'], get_current_user_id());
                } else
                // Encrypt value
                if ( array_key_exists('encrypt', $value) ) {
                    $cry = Crypto::initiate();
                    $r = $this->update_option($key, $cry->encrypt( $value['encrypt'] ) );
                } else
                // Encrypt value + Unique for current logged in user
                if ( array_key_exists('encrypt,unique', $value) ) {
                    $cry = Crypto::initiate();
                    $r = $this->update_option($key, $cry->encrypt( $value['encrypt'] ), get_current_user_id());
                } else {
                    $r = $this->update_option( $key, serialize( $value ) );
                }
            } else {
                $r = $this->update_option( $key, $value );
            }
        }
        return $r;
    }

    /**
     * Get option value by key or ID
     * @param string $name Name or ID
     * @param int $user_id User ID optional
     * @param string $key Get option by 'name' or 'id'
     * @return string
     */
    function get_option( string $name, int $user_id = 0, string $key = 'name' ): string {
        $db = $this->connect();
        $r = '';
        if( $db ) {
            $q = $key == 'name' ? 'option_name = "'.$name.'"' : 'option_id = "'.$name.'"';
            $query = $user_id !== 0 ? $q . ' AND option_scope = "'.$user_id.'"' : $q;
            $o = $this->select( 'options', 'option_value', $query, 1 );
            $r = $o ? $o['option_value'] : '';
        }
        return $r;
    }

    /**
     * Get multiple options by keys as array
     * @param mixed $opn Option Names ['theme_color','dark_mode']
     * @param int $user_id User ID optional
     * @param string $key Get option by 'name' or 'id'
     * @return mixed
     */
    function get_options( mixed $opn, int $user_id = 0, string $key = 'name' ): mixed {
        $q = '';
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
        $query = $user_id ? '('. $q . ') AND option_scope = "'.$user_id.'"' : $q;
        $o = $this->select( 'options', 'option_name, option_value', $query );
        $d = [];
        if( is_array( $o ) ){
            foreach( $o as $k => $v ){
                $d[$v['option_name']] = $v['option_value'];
            }
        }
        return !empty( $d ) && count( $d ) == 1 ? $o[0] : $d;
    }

    // Test and remove
    function save_post_option( $option, $user = false ){
        $db = new DB();
        if( isset( $_POST[$option] ) ){
            $o = $db->update_option( $option, $_POST[$option], $user ? $_SESSION['user_id'] : 0 );
        }
    }

    // Test and remove
    function save_post_options( $options ){
        $db = new DB();
        $options = is_array( $options ) ? $options : explode( ',', $options );
        foreach( $options as $op ){
            if( is_array( $op ) ){
                $u = isset( $op[1] ) && $op[1] ? 1 : 0;
                $db->save_post_option( $op[0], $u );
            } else {
                $db->save_post_option( $op );
            }
        }
    }

    /**
     * Remove option
     * @param string $opn Option Name
     * @param int $user_id User ID optional
     * @param string $key Remove option by 'name' or 'id'
     * @return bool
     */
    function remove_option( string $opn, int $user_id = 0, $key = 'name' ): bool {
        $q = '';
        $q .= $key == 'id' ? 'option_name = "'.$opn.'" ' : 'option_id = "'.$opn.'" ';
        $query = $user_id !== 0 ? $q . ' AND option_scope = "'.$user_id.'"' : $q;
        $o = $this->delete( 'options', $query );
        return $o ? true : false;
    }

    /**
     * Removes all options of current user
     * @param int $user_id User ID
     * @return bool
     */
    function remove_user_options( int $user_id ): bool {
        $o = $this->delete( 'options', 'option_scope = "'.$user_id.'"' );
        return $o ? true : false;
    }

    // IMPORT / EXPORT

    /**
     * Import MySQL File
     * @param string $filename MySQL Filename
     */
    function import( string $filename ) {
        $query = '';
        $script = file( $filename );
        $df = debug_backtrace();
        $db = $this->connect();
        foreach( $script as $l ) {

            $startWith = substr( trim( $l ), 0 ,2 );
            $endWith = substr( trim( $l ), -1 ,1 );

            if( empty( $l ) || $startWith == '--' || $startWith == '/*' || $startWith == '//' ) {
                continue;
            }

            $query = $query . $l;
            if ($endWith == ';') {
                mysqli_query( $db, $query ) or die('Problem in executing the SQL query <b>' . $query. '</b>');
                $query= '';
            }
        }
    }
}

/**
 * Prepares an array of only keys from a given array or post
 * @param array|string $array
 * @param string $pre
 * @param bool $remove_empty
 * @return array
 */
function prepare_keys( $array = '', $pre = '', bool $remove_empty = true ): array {
    $keys = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ){
            if( $remove_empty ){
                if($v !== '' ){
                    $keys[] = $pre.$k;
                }
            } else {
                $keys[] = $pre.$k;
            }
        }
    }
    return $keys;
}

/**
 * Prepares an array of only values from a given array or post
 * @param array|string $array
 * @param string $pre
 * @param bool $remove_empty
 * @return array
 */
function prepare_values( $array = '', $pre = '', bool $remove_empty = true ): array {
    $values = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ){
            $v = is_array( $v ) ? serialize( $v ) : $v;
            if( $remove_empty ){
                if( $v !== '' ){
                    $values[] = $pre.$v;
                }
            } else {
                $values[] = $pre.$v;
            }
        }
    }
    return $values;
}

function process_data() {
    $a = $_POST;
    if( !empty( $a['target'] ) ){
        $cry = Crypto::initiate();
        $db = new DB();
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

        $keys = prepare_keys( $a, '', 0 );
        $values = prepare_values( $a, '', 0 );

        $query = !empty( $id ) ? $db->update( $table, $keys, $values, $pre.'_id = "'.$id.'"' ) : $query = $db->insert( $table, $keys, $values );

        if( !empty( $id ) ) {
            $query ? es('Updated Successfully') : ef('Not updated, data sent maybe unchanged / empty');
        } else {
            $query ? es('Added Successfully') : ef('Not stored, please try again or contact support');
        }
    } else {
        ef('Database not targeted properly, please contact support');
    }
}

function trash_data() {
    unset($_POST['action']);
    $cry = Crypto::initiate();
    $d = $cry->decrypt( $_POST['query'] );
    $db = new DB();
    if( $d = explode('|',$d) ){
        $t = $db->delete( $d[0], $d[1].' = '.$d[2] );
        if( $t ){
            ES('Data deleted successfully');
        } else {
            EF('Delete data failed due to query misinterpret, please contact support');
        }
    } else {
        EF('Delete data failed due to query misinterpret, please contact support');
    }
}