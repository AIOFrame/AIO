<?php

class DB {

    function connect() {

        $connection_string = '';
        switch( DB_TYPE ) {
            case 'mysql':
                $connection_string = "mysql:host=".DB_HOST.";dbname=".DB_BASE.";charset=utf8mb4";
                break;
            case 'mssql':
                $connection_string = "sqlsrv:Server=".DB_HOST.";Database=".DB_BASE;
                break;
        }

        if( !empty( $connection_string ) ) {
            try {
                $c = new PDO( $connection_string, DB_USER, DB_PASS );
                $c->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $c;
            } catch (PDOException $e) {
                elog($e->getMessage());
                return $e->getMessage();
            }
        }
    }

    // TABLE FUNCTIONS

    /**
     * Create Table
     * @param string $name Name of the table
     * @param array $columns [ [ string 'col_name', string 'type', int 'length', bool 'not null', string 'default' ], ... ]
     * @param string $pre String to append to title of column names Ex: user_
     * @param bool $auto_id Automatically creates first column of id Ex: user_id
     * @return array
     */
    function create_table( string $name, array $columns, string $pre = '', bool $auto_id = true ): array {
        $result = [];
        if( is_array( $columns ) && defined('APPCON') && APPCON ){

            // Prerequisites
            $debug = debug_backtrace();
            $db = $this->connect();
            $pre = !empty( $pre ) ? $pre . '_' : '';
            $columns_query = $auto_id ? ( DB_TYPE == 'mssql' ? $pre . 'id INT NOT NULL IDENTITY(1, 1)' : $pre . 'id INT AUTO_INCREMENT PRIMARY KEY' ) : '';

            // Create Table
            if( DB_TYPE == 'mssql' ) {
                $query = 'IF NOT EXISTS (  SELECT [name] FROM sys.tables WHERE [name] = \''.$name.'\' ) CREATE TABLE ' . $name . ' ( ' . $columns_query . ' ) ';
            } else {
                $query = 'CREATE TABLE IF NOT EXISTS ' . $name . ' ( ' . $columns_query . ' ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci';
            }
            elog( $query, 'log', $debug[0]['line'], $debug[0]['file'], $name );

            // Executing Query
            try {
                $execute = $db->query( $query );
                $result = !empty( $execute ) ? [ 1, 'Successfully executed create table query!' ] : [ 0, 'Failed to execute create table query, Please refer log!' ];
            } catch( PDOException $e ) {
                elog( $e, 'error', $debug[1]['line'], $debug[1]['file'], $name );
            }

            // Create Columns
            foreach( $columns as $col ) {
                if ( is_array( $col ) && count( $col ) > 2 ) {
                    $col[2] = !empty( $col[2] ) ? $col[2] : '';
                    $col[4] = isset( $col[4] ) && !empty( $col[4] ) ? ' DEFAULT \'' . $col[4] . '\'' : '';
                    // Changes for MS SQL
                    if( DB_TYPE == 'mssql' ) {
                        $col[1] == 'INT' ? $col[2] = '' : '';
                        $col[1] = $col[1] == 'BOOLEAN' ? 'BIT' : $col[1];
                        //$col[1] = $col[1] == 'VARCHAR' ? 'NVARCHAR' : $col[1];
                    }
                    $this->create_column( $name, $pre.$col[0], $col[1], $col[2], $col[3], $col[4] );
                }
            }
        } else {
            $result = [ 0,'The columns are supposed to be array!' ];
        }
        return $result;
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
                $tables_created[] = $this->create_table( $table[0], $table[1], $table[2], $table[3] );
            }
        } else {
            $tables_created[] = 0;
        }
        return $tables_created;
    }

    /**
     * Automatically saves calling file md5 to not repeat create table requests
     * @param $tables
     * @return array
     */
    function automate_tables( $tables ): array {
        $table_names = '';
        foreach( $tables as $tb ) {
            $table_names .= $tb[0].'_';
        }
        $result = [];
        $db = new DB();
        $trace = debug_backtrace();
        $file_path = isset( $trace[0]['file'] ) ? $trace[0]['file'] : '';
        if( !empty( $file_path ) ) {

            // Get file properties
            $file = str_replace( '/', '_', $file_path ) . '_' . substr($table_names, 0, -1);
            $md5 = md5_file( $file_path );

            // Get database option and verify if md5 is same
            $exist = $db->get_option( $file . '_md5' );
            if( empty( $exist ) || $exist !== $md5 ) {
                $db->update_option( $file . '_md5', $md5 );
                $result = $this->create_tables( $tables );
            }
        }
        return $result;
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
        $null = $null ? 'NOT NULL' : 'NULL';
        $length = !empty($length) ? '('.$length.')' : '';
        //$exist = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
        $query = "ALTER TABLE $table ADD COLUMN $column $type$length $null";
        $query .= !empty($default) ? ' default "'.$default.'"' : '';

        $df = debug_backtrace();
        $exist = $this->table_exists( $table );
        if( $exist ){
            $db = $this->connect();
            try {
                $r = $db->query( $query );
                return 1;
            } catch ( PDOException $e ) {
                elog( json_encode( $e ) . ' - ' . $query, 'column', $df[0]['line'], $df[0]['file'], $table . '-' . $column );
                //elog( $column.' '.mysqli_error($db), 'error', $df[0]['line'], $df[0]['file'], $table . '-' . $column );
                return false;
            }
        } else {
            return 0;
        }
    }

    /**
     * Checks if table exists
     * @param string $table
     * @return bool
     */
    function table_exists( string $table ): bool {
        try {
            $db = $this->connect();
            $r = gettype( $db->exec("SELECT count(*) FROM $table")) == 'integer';
        } catch ( PDOException $e ) {
            elog( json_encode( $e ) );
            $r = 0;
        }
        return $r;
    }

    // DATA FUNCTIONS

    /**
     * Insert data into Table
     * @param string $table Table Name
     * @param array $names Names of columns
     * @param array $values Data to insert
     * @return int
     */
    function insert( string $table, array $names, array $values ): int {
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

                    $query = $db->prepare( $q );
                    try {
                        $query->execute();
                        return $db->lastInsertId();
                    } catch ( PDOException $e ) {
                        elog( $q, 'error', $df[0]['line'], $df[0]['file'], $table );
                        elog( json_encode( $e ), 'error', $df[0]['line'], $df[0]['file'], $table );
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
     * @param string $group Group data rows by column key
     * @param bool $count Only get count of data rows
     * @param string $order_by Order data by
     * @param string $sort
     * @return array
     */
    function select( string $table, string $cols = '*', string $where = '', int $limit = 0, int $offset = 0 , string $group = '', bool $count = false, string $order_by = '', string $sort = '' ): array {
        $db = $this->connect();
        if( $db ) {
            $cols = $cols == "" ? "*" : $cols;
            if ( !is_array( $table ) ) {
                $o = $count ? "SELECT COUNT('" . $cols . "') FROM $table " : "SELECT " . $cols . " FROM $table ";
                $target = $table;
            } else {
                $o = "SELECT " . $cols . " FROM $table[1] $table[0] $table[2] ON $table[1].$table[3] = $table[2].$table[4] ";
                $target = $table[1];
            }
            DB_TYPE == 'mssql' ? $where = str_replace( '"', "'", $where ) : '';
            $o .= !empty($where) && $where !== '' ? ' WHERE ' . $where : '';
            $o .= !empty($group) ? "GROUP BY " . $group : "";
            //$o .= !empty($order_by) && $order_by !== '' ?  $order_by : '';
            $o .= !empty( $sort ) && $sort !== '' && !empty( $order_by ) && $order_by !== '' ? ' ORDER BY ' . $sort . ' ' . $order_by : '';
            $o .= $limit >= 1 ? ( DB_TYPE == 'mssql' ? '' : ' LIMIT ' . $limit ) : '';
            $o .= $offset > 1 ? ( DB_TYPE == 'mssql' ? ' OFFSET ' . $offset . ' ROWS' : ' OFFSET ' . $offset ) : '';

            $df = debug_backtrace();

            elog( $o, 'select', $df[0]['line'], $df[0]['file'], $target );

            if( $db ) {
                try {
                    $q = $db->query( $o );
                } catch ( PDOException $e ) {
                    elog( $e, 'error' );
                    return [];
                }
            }
            $data = [];
            if( $q ) {
                //return $q->fetchAll();
                while ( $row = $q->fetchAll() ) {
                    $data[] = $row;
                }
                if ( $count && !empty($data) ) {
                    return end( $data[0] );
                } else if (!empty($data)) {
                    if ($limit == 1) {
                        return $data[0][0];
                    } else {
                        return $data[0];
                    }
                }
            }
            return $data;
            //elog( $o, 'error', $df[0]['line'], $df[0]['file'], $target );
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
        //$df = !empty($df) && is_array($df) && isset($df[0]['file']) && isset($df[0]['line']) ? '<<'.$df[0]['line'].'>> {'.str_replace(ROOTPATH,'',$df[0]['file']).'}' : '';

        $db = $this->connect();
        $q = "UPDATE $table SET ". $logic . " where ". $where;

        elog( $q, 'update', $df[0]['line'], $df[0]['file'], $table );

        $dq = $db->prepare( $q );
        if ( $dq->execute() && $dq->rowCount() > 0 ){
            return true;
        } else {
            elog( $q, 'error', $df[0]['line'], $df[0]['file'], $table );
            return false;
        }
    }

    /**
     * Delete rows from table
     * @param string $table Table Name
     * @param string $logic Logic ['user_id',12] or 'user_id = 12'
     * @return int
     */
    function delete( string $table, string $logic ): int {
        $db = $this->connect();
        if( is_array( $logic ) ) {
            $q = "DELETE FROM $table WHERE $logic[0] = $logic[1]";
        } else {
            $q = "DELETE FROM $table WHERE $logic";
        }

        $df = debug_backtrace();

        elog( $q, 'delete', $df[0]['line'], $df[0]['file'], $table );

        $del = $db->prepare( $q );

        if( $del->execute() ) {
            $rows = $del->rowCount();
            return $rows > 0 ? $rows : 0;
        } else {
            elog( $q, 'delete', $df[0]['line'], $df[0]['file'], $table );
            return 0;
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
     * Queries PDO statement
     * @param string $query Query
     * @return Exception|false|PDOException|PDOStatement
     */
    function query( string $query ) {
        $db = $this->connect();
        try {
            $e = $db->query( $query );
            return $e->fetchAll();
        } catch ( PDOException $e ) {
            return $db->errorInfo();
        }
    }

    /**
     * Prepares PDO statement
     * @param string $query
     * @return Exception|false|PDOException|PDOStatement
     */
    function prepare( string $query ) {
        $db = $this->connect();
        $result = $db->prepare( $query );
        return $result ? $result : $db->errorInfo();
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
            $c = $this->select( 'options', '*', 'option_name = \''.$name.'\' AND option_scope = \''.$user_id.'\'', 1 );
            if( $c ) {
                return $this->update( 'options', ['option_value', 'option_scope', 'option_load' ], [ $value, $user_id, $autoload ], 'option_name = \''.$name.'\'' );
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
            $q = $key == 'name' ? 'option_name = \''.$name.'\'' : 'option_id = \''.$name.'\'';
            $query = $user_id !== 0 ? $q . ' AND option_scope = \''.$user_id.'\'' : $q;
            $o = $this->select( 'options', 'option_value', $query, 1 );
            $r = $o ? $o['option_value'] : '';
        }
        return $r;
    }

    /**
     * Get multiple options by keys as array
     * @param array $opn Option Names ['theme_color','dark_mode']
     * @param int $user_id User ID optional
     * @param string $key Get option by 'name' or 'id'
     * @return array
     */
    function get_options( array $opn, int $user_id = 0, string $key = 'name' ): array {
        $q = '';
        if( is_array( $opn ) ){
            foreach( $opn as $op ){
                if( $key == 'id' ){
                    $q .= 'option_id = \''.$op.'\' OR ';
                } else {
                    $q .= 'option_name = \''.$op.'\' OR ';
                }
            }
        } else {
            if( $key == 'id' ){
                $q .= 'option_id = \''.$opn.'\' OR ';
            } else {
                $q .= 'option_name = \''.$opn.'\' OR ';
            }
        }
        $q = !empty( $q ) ? substr($q, 0, -3) : $q;
        $query = $user_id ? '('. $q . ') AND option_scope = \''.$user_id.'\'' : $q;
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
            $v = $_POST[$option];
            $v = is_array( $v ) ? json_encode( $v ) : $v;
            $o = $db->update_option( $option, $v, $user ? $_SESSION['user_id'] : 0 );
        }
    }

    // Test and remove
    function save_post_options( $options ){
        $db = new DB();
        $options = is_array( $options ) ? $options : explode( ',', $options );
        foreach( $options as $op ){
            if( is_array( $op ) ){
                $u = isset( $op[1] ) && $op[1] ? 1 : 0;
                //$u = is_array( $u ) ? json_encode( $u ) : $u;
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
        $q .= $key == 'id' ? 'option_name = \''.$opn.'\' ' : 'option_id = \''.$opn.'\' ';
        $query = $user_id !== 0 ? $q . ' AND option_scope = \''.$user_id.'\'' : $q;
        $o = $this->delete( 'options', $query );
        return $o ? true : false;
    }

    /**
     * Removes all options of current user
     * @param int $user_id User ID
     * @return bool
     */
    function remove_user_options( int $user_id ): bool {
        $o = $this->delete( 'options', 'option_scope = \''.$user_id.'\'' );
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
                $db->query( $query ) or die('Problem in executing the SQL query <b>' . $query. '</b>');
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

function process_data_ajax() {
    $a = $_POST;
    if( !empty( $a['t'] ) ){
        $cry = Crypto::initiate();
        $db = new DB();
        $table = $cry->decrypt( $a['t'] );
        unset( $a['t'] );

        if( !empty( $a['id'] ) ){
            $id = $cry->decrypt( $a['id'] );
            unset($a['id']);
        }

        if( !empty( $a['pre'] ) ){
            $pre = $a['pre'];
            unset( $a['pre'] );
        } else {
            $pre = '';
        }

        if( !empty( $a['d'] ) ){
            $a[$pre.$a['d']] = date('Y-m-d');
            unset( $a['d'] );
        }

        if( !empty( $a['dt'] ) ){
            $a[$pre.$a['dt']] = date('Y-m-d H:i:s');
            unset( $a['dt'] );
        }

        if( !empty( $a['by'] ) ){
            $auths = explode(',',str_replace(' ','',$a['by']));
            if( is_array( $auths ) ){
                foreach( $auths as $auth ){
                    $a[$pre.$auth] = get_user_id();
                }
            }
            unset($a['by']);
        }
        if( !empty( $a['h'] ) ){
            $cry = Crypto::initiate();
            $dec = $cry->decrypt( $a['h'] );
            $hs = !empty( $dec ) ? json_decode( $dec, 1 ) : [];
            if( is_array( $hs ) ){
                elog( 'Hidden is array' );
                foreach( $hs as $k => $v ){
                    $a[ $pre.$k ] = $v;
                }
            }
            unset( $a['h'] );
        }

        if( !empty( $a['a'] ) ) {
            $alerts = $cry->decrypt_array( $a['a'] );
            unset( $a['a'] );
        }

        if( !empty( $a['post'] ) ) {
            $post = $cry->decrypt( $a['post'] );
            if( function_exists( $post ) ){
                $post( $_POST );
            }
            unset( $a['post'] );
        }

        $keys = prepare_keys( $a, '', 0 );
        $values = prepare_values( $a, '', 0 );

        $query = !empty( $id ) ? $db->update( $table, $keys, $values, $pre.'id = \''.$id.'\'' ) : $db->insert( $table, $keys, $values );

        if( !empty( $id ) ) {
            $query ? es('Updated Successfully') : ef('Not updated, data sent maybe unchanged / empty');
        } else {
            $query ? es('Added Successfully') : ef('Not stored, please try again or contact support');
        }

        // Send alerts
        if( isset( $alerts ) && is_array( $alerts ) && $query ) {
            foreach( $alerts as $al ) {
                if( !empty( $al->title ) ) {
                    $ac = new ALERTS();
                    $title = isset( $al->title ) && !empty( $al->title ) ? $al->title : 'Alert';
                    $note = isset( $al->note ) && !empty( $al->note ) ? $al->note : '';
                    $type = isset( $al->type ) && !empty( $al->type ) ? $al->type : 'alert';
                    $link = isset( $al->link ) && !empty( $al->link ) ? $al->link : '';
                    $user = isset( $al->user ) && !empty( $al->user ) ? $al->user : get_user_id();
                    $sent_alerts[] = $ac->create( $title, $note, $type, $link, $user );
                }
            }
        }
    } else {
        ef('Database not targeted properly, please contact support');
    }
}

/**
 * Trashes data
 */
function trash_data_ajax() {
    $c = Crypto::initiate();
    $target = ''; //isset( $_POST['target'] ) && !empty( $_POST['target'] ) ? $c->decrypt( $_POST['target'] ) : '';
    $logic = ''; //isset( $_POST['logic'] ) && !empty( $_POST['logic'] ) ? $c->decrypt_array( $_POST['logic'] ) : '';
    if( !empty( $target ) && !empty( $logic ) ){
        $db = new DB();
        $r = $db->delete( $target, $logic[0].' = '.$logic[1] );
        if( $r ){
            ES('Deleted successfully');
        } else {
            EF('Delete failed due to query misinterpret, please contact support');
        }
    } else {
        EF('Delete failed due to query misinterpret, please contact support');
    }
}