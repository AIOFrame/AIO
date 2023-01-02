<?php

// Load Default Options
global $options;
if( defined( 'DB_TYPE' ) && empty( $options ) ) {
    $db = new DB();
    $option_set = $db->select( 'options', '', 'option_scope = \'0\' AND option_load = \'1\'' );
    if( !empty( $option_set ) ) {
        foreach( $option_set as $os ) {
            $options[ $os['option_name'] ] = $os['option_value'];
        }
    }
}
if( isset( $options['app_name'] ) && !empty( $options['app_name'] ) ) {
    if( !defined( 'APP_NAME' ) )
        define( 'APP_NAME', $options['app_name'] );
}

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

    function log( string|array $log, string $type = 'log', string $line = '', string $file = '', string $target = '' ): void {
        $log = is_array( $log ) ? json_encode( $log ) : $log;

        $db = new DB();

        $data = [
            'dt' => date('Y-m-d H:i:s'),
            'type' => $type,
            'table' => $target,
            'data' => $log,
            'url' => $file,
            'line' => $line,
            'uid' => get_user_id(),
            'name' => get_user_name(),
            'client' => get_user_browser(),
            'device' => get_user_device(),
            'os' => get_user_os()
        ];

        $names = implode( ',', prepare_keys( $data, 'log_' ) );
        $values = '\'' . implode( "', '", prepare_values( $data ) ) . '\'';

        $q = "INSERT INTO log ($names) VALUES ( $values )";
        $query = $db->prepare( $q );
        try {
            $query->execute();
        } catch ( PDOException $e ) {
            $df = debug_backtrace();
            elog( $q, 'error', $df[0]['line'], $df[0]['file'], 'log' );
            elog( json_encode( $e ), 'error', $df[0]['line'], $df[0]['file'], 'log' );
        }

        //$db->insert( 'log', prepare_keys( $data, 'log_' ), prepare_values( $data ) );
    }

    // TABLE FUNCTIONS

    /**
     * Create Table
     * @param string $name Name of the table
     * @param array $columns [ [ string 'col_name', string 'type', int 'length', bool 'not null', string 'default' ], ... ]
     * @param string $pre String to append to title of column names Ex: user (without underscore)
     * @param bool $auto_id Automatically creates first column of id Ex: user_id
     * @return array
     */
    function create_table( string $name, array $columns, string $pre = '', bool $auto_id = true ): array {
        $result = [];
        if( defined('APPCON') && APPCON ){

            // Prerequisites
            $debug = debug_backtrace();
            $db = $this->connect();
            $pre = !empty( $pre ) ? $pre . '_' : '';
            $columns_query = $auto_id ? ( DB_TYPE == 'mssql' ? $pre . 'id INT NOT NULL IDENTITY(1, 1)' : $pre . 'id INT AUTO_INCREMENT PRIMARY KEY' ) : '';

            // Create Table if it doesn't exist
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
        $file_path = $trace[0]['file'] ?? '';
        if( !empty( $file_path ) ) {

            // Get file properties & options
            $file = str_replace( '/', '_', str_replace( '.php', '', $file_path ) );
            $md5 = md5_file( $file_path );
            global $options;
            $exist = $options[ $file . '_md5' ] ?? '';

            // Verify if file is changed
            if( empty( $exist ) || $exist !== $md5 ) {
                $db->update_option( $file . '_md5', $md5, 0, 1 );
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
     * @param int|string $length Column Length
     * @param bool $null Is Nullable
     * @param string $default Default Value
     */
    function create_column( string $table, string $column, string $type = 'TEXT', int|string $length = '13', bool $null = true, string $default = '' ): void {

        // Get file properties & options
        $trace = debug_backtrace();
        $file_path = $trace[0]['file'] ?? '';
        $file = str_replace( '/', '_', str_replace( '.php', '', $file_path ) );
        $md5 = md5_file( $file_path );
        global $options;
        $current_md5 = $options[ $file . '_md5' ] ?? '';

        //$exist = "SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table' AND COLUMN_NAME = '$column'";
        elog('HERE!');
        if( empty( $current_md5 ) || $current_md5 !== $md5 ) {
            $type == 'BOOLEAN' ? $type = 'TINYINT' : '';
            $length = in_array($type, ['BOOLEAN', 'DATETIME', 'DATE', 'TIME', 'TINYTEXT', 'DOUBLE']) ? '' : $length;
            $null = $null ? 'NOT NULL' : 'NULL';
            $length = !empty($length) ? '(' . $length . ')' : '';
            $exist = $this->query( "SHOW COLUMNS FROM $table LIKE '$column';" );
            if( !$exist ) {
                $query = "ALTER TABLE $table ADD COLUMN $column $type$length $null";
            } else {
                $query = "ALTER TABLE $table MODIFY COLUMN $column $type$length $null";
            }
            $query .= !empty($default) ? ' default "' . $default . '"' : '';
        }

        $df = debug_backtrace();
        $table_exist = $this->table_exists($table);
        if( $table_exist ) {
            $db = $this->connect();
            try {
                $r = $db->query($query);
            } catch (PDOException $e) {
                elog(json_encode($e) . ' - ' . $query, 'column', $df[0]['line'], $df[0]['file'], $table . '-' . $column);
            }
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
     * @param string|array $table Table name or names with join [ 'customers', [ 'addresses', 'address_customer', 'customer_id' ] ]
     * @param string $cols Columns to get data, Ex: 'user_name,user_dob'
     * @param string $where Where logic, Ex: 'user_id = 12'
     * @param int $limit Limit quantity of rows
     * @param int $offset Offset rows
     * @param string $group Group data rows by column key
     * @param bool $count Only get count of data rows
     * @param string $order_by Order data by ASC or DESC
     * @param string $sort Sort Order by Column Name
     * @return array
     */
    function select( string|array $table, string $cols = '*', string $where = '', int $limit = 0, int $offset = 0 , string $group = '', bool $count = false, string $order_by = '', string $sort = '' ): array {
        $db = $this->connect();
        if( $db ) {
            $cols = $cols == "" ? "*" : $cols;
            if ( !is_array( $table ) ) {
                $query = $count ? "SELECT COUNT('" . $cols . "') FROM $table " : "SELECT " . $cols . " FROM $table ";
                $target = $table;
            } else {
                //skel( $table );
                $target = $table[0];
                unset( $table[0] );
                $query = "SELECT " . $cols . " FROM " . $target . " ";
                foreach( $table as $t ) {
                    $query .= "JOIN " . $t[0] . " ON " . $t[0] . "." . $t[1] . " = " . $target . "." . $t[2] . " ";
                }
            }
            DB_TYPE == 'mssql' ? $where = str_replace( '"', "'", $where ) : '';
            $query .= !empty($where) && $where !== '' ? ' WHERE ' . $where : '';
            $query .= !empty($group) ? "GROUP BY " . $group : "";
            //$o .= !empty($order_by) && $order_by !== '' ?  $order_by : '';
            $query .= !empty( $sort ) && $sort !== '' && !empty( $order_by ) && $order_by !== '' ? ' ORDER BY ' . $sort . ' ' . $order_by : '';
            $query .= $limit >= 1 ? ( DB_TYPE == 'mssql' ? '' : ' LIMIT ' . $limit ) : '';
            $query .= $offset > 1 ? ( DB_TYPE == 'mssql' ? ' OFFSET ' . $offset . ' ROWS' : ' OFFSET ' . $offset ) : '';

            $df = debug_backtrace();

            elog( $query, 'select', $df[0]['line'], $df[0]['file'], $target );

            try {
                $q = $db->query( $query );
            } catch ( PDOException $e ) {
                elog( $e, 'error' );
                return [];
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

    function count( $table, $where = '' ) {
        $db = $this->connect();
        $q = "SELECT COUNT(*) FROM $table";
        $q .= !empty( $where ) ? " WHERE $where" : '';

        $data = $db->query( $q );
        $count = $data->fetchColumn();
        if ( $count ){
            return $count;
        } else {
            $df = debug_backtrace();
            elog( $q, 'error', $df[0]['line'], $df[0]['file'], $table );
            return 0;
        }
    }

    /***
     * This function converts arrays of columns and their values into string that gets updated on MYSQL.
     * @param $table
     * @param $cols
     * @param $values
     * @param string $where
     * @return array
     */
    function update( $table, $cols, $values, string $where = '' ): array {
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
        try {
            $did = $dq->execute() && $dq->rowCount() > 0;
            return [ 1, $did ];
        } catch ( PDOException $e ) {
            elog( $q, 'error', $df[0]['line'], $df[0]['file'], $table );
            elog( $db->errorInfo(), 'error', $df[0]['line'], $df[0]['file'], $table );
            return [ 0, $db->errorInfo() ];
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
    function total( string $table, string $cols = '*', string $where = '', int $limit = 0, int $offset = 0, string $group='' ): array {
        return $this->select( $table, $cols, $where, $limit, $offset, $group, true );
    }

    /**
     * Queries PDO statement
     * @param string $query Query
     */
    function query( string $query ): array {
        $db = $this->connect();
        try {
            $e = $db->query( $query );
            return $e->fetchAll();
        } catch ( PDOException $e ) {
            elog( $db->errorInfo() );
            return [ 0, $e ];
        }
    }

    /**
     * Fetches the total of data in column of a table
     * @param string $table Name of the table
     * @param string $column Name of the column to sum the data
     * @return int|float
     */
    function sum( string $table = '', string $column = '' ): int|float {
        $db = $this->connect();
        $query = $db->query('SELECT SUM('.$column.') '.$column.' FROM '.$table);
        $data = $query->fetchAll();
        //skel( $data );
        return is_array( $data ) && isset( $data[0][0] ) ? $data[0][0] : 0;
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
     * @param string|array $value Option Value
     * @param int $user_id User ID to store for specific user
     * @return mixed
     */
    function add_option( string $name, string|array $value, int $user_id = 0 ): mixed {
        if( !empty( $name ) && !empty( $value )){
            return $this->insert( 'options', [ 'option_name', 'option_value', 'option_scope' ], [ $name, $value, $user_id ] );
        } else {
            return false;
        }
    }

    /**
     * Update setting by key and value
     * @param string $name Option Name / Key
     * @param string|array $value Option Value
     * @param int $user_id User ID to store for specific user
     * @param int $autoload Auto load values to session
     * @return bool
     */
    function update_option( string $name, string|array $value, int $user_id = 0, int $autoload = 0 ): bool {
        if( $name !== '' ){
            $exist = $this->select( 'options', '*', 'option_name = \''.$name.'\' AND option_scope = \''.$user_id.'\'', 1 );
            if( $exist ) {
                $c = $this->update('options', ['option_value', 'option_scope', 'option_load'], [$value, $user_id, $autoload], 'option_name = \'' . $name . '\'');
            } else {
                $c = $this->insert( 'options', [ 'option_name', 'option_value', 'option_scope', 'option_load' ], [ $name, $value, $user_id, $autoload ] );
            }
            return is_array( $c ) ? json_encode( $c ) : $c;
        } else {
            return 0;
        }
    }

    /**
     * Update options by array param or $_POST
     * @param array $array Array of options [['key'=>'val'],['key2'=>'val2']]
     * @param string|array $encrypt Array or String (separated by comma) of key of options whose value is to be encrypted
     * @param string|array $unique Array or String (separated by comma) of key of options whose value is to be specific for that logged-in user
     * @param string|array $autoload Array or String (separated by comma) of key of options whose value is to be loaded in global $options
     * @return bool
     */
    function update_options( array $array = [], string|array $encrypt = '', string|array $unique = '', string|array $autoload = '' ): bool {
        $r = [];
        $array = !empty( $array ) && isset( $_POST ) ? $_POST : [];
        $encrypt = !is_array( $encrypt ) && !empty( $encrypt ) ? explode( ',', $encrypt ) : $encrypt;
        $unique = !is_array( $unique ) && !empty( $unique ) ? explode( ',', $unique ) : $unique;
        $autoload = !is_array( $autoload ) && !empty( $autoload ) ? explode( ',', $autoload ) : $autoload;
        foreach( $array as $key => $value ){
            // If value has optional parameters
            if( is_array( $value ) ) {
                // Unique for current logged in user
                if ( array_key_exists('unique', $value) ) {
                    $r[] = $this->update_option($key, $value['unique'], get_user_id());
                } else
                    // Encrypt value
                    if ( array_key_exists('encrypt', $value) ) {
                        $cry = Encrypt::initiate();
                        $r[] = $this->update_option($key, $cry->encrypt( $value['encrypt'] ) );
                    } else
                        // Encrypt value + Unique for current logged in user
                        if ( array_key_exists('encrypt,unique', $value) ) {
                            $cry = Encrypt::initiate();
                            $r[] = $this->update_option($key, $cry->encrypt( $value['encrypt'] ), get_user_id());
                        } else {
                            $r[] = $this->update_option( $key, serialize( $value ) );
                        }
            } else {
                $c = Encrypt::initiate();
                if( !empty( $encrypt ) && in_array( $key, $encrypt ) ) {
                    $value = is_array($value) ? $c->encrypt_array($value) : $c->encrypt($value);
                }
                $user = !empty( $unique ) && in_array( $key, $unique ) ? get_user_id() : 0;
                /* elog( 'Autoload:' );
                elog( $autoload );
                elog( 'Unique:' );
                elog( $unique );
                elog( 'Key:' );
                elog( $key ); */
                $load = !empty( $autoload ) && in_array( $key, $autoload ) ? 1 : 0;
                $r[] = $this->update_option( $key, $value, $user, $load );
            }
        }
        $r = array_unique( $r );
        elog( $r );
        return ( count( $r ) == 1 && $r[0] == '' ) || empty( $r ) ? 0 : 1;
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
            $df = debug_backtrace();
            elog( $query, 'get_option', $df[0]['line'], $df[0]['file'], $name );
        }
        return $r;
    }

    /**
     * Get multiple options by keys as array
     * @param array|string $options Option Names ['theme_color','dark_mode']
     * @param int $user_id User ID optional
     * @param string $key Get option by 'name' or 'id'
     * @return array
     */
    function get_options( array|string $options, int $user_id = 0, string $key = 'name' ): array {
        $q = '';
        if( !empty( $options ) ){
            if( is_assoc($options) ) {
                foreach( $options as $opk => $opv ){
                    if( $key == 'id' ){
                        $q .= 'option_id = \''.$opk.'\' OR ';
                    } else {
                        $q .= 'option_name = \''.$opk.'\' OR ';
                    }
                }
            } else {
                foreach( $options as $op ){
                    if( $key == 'id' ){
                        $q .= 'option_id = \''.$op.'\' OR ';
                    } else {
                        $q .= 'option_name = \''.$op.'\' OR ';
                    }
                }
            }
        } else {
            if( $key == 'id' ){
                $q .= 'option_id = \''.$options.'\' OR ';
            } else {
                $q .= 'option_name = \''.$options.'\' OR ';
            }
        }
        $q = !empty( $q ) ? substr($q, 0, -3) : $q;
        $query = $user_id ? '('. $q . ') AND option_scope = \''.$user_id.'\'' : $q;
        $o = $this->select( 'options', 'option_name, option_value', $query );
        $d = [];
        if( !empty( $o ) ){
            foreach( $o as $k => $v ){
                $d[$v['option_name']] = $v['option_value'];
            }
        }
        return $d; //!empty( $d ) && count( $d ) == 1 ? $o[0] : $d;
    }

    // Test and remove
    function save_post_option( $option, $user = false ):void {
        $db = new DB();
        if( isset( $_POST[$option] ) ){
            $v = $_POST[$option];
            $v = is_array( $v ) ? json_encode( $v ) : $v;
            $o = $db->update_option( $option, $v, $user ? $_SESSION['user_id'] : 0 );
        }
    }

    /**
     * Saves POST data as options
     * @param string|array $options
     * @return void
     */
    function save_post_options( string|array $options ): void {
        $options = is_array( $options ) ? $options : explode( ',', $options );
        elog( $options );
        foreach( $options as $op ){
            if( is_array( $op ) ){
                $u = isset( $op[1] ) && $op[1] ? 1 : 0;
                $u = is_array( $u ) ? json_encode( $u ) : $u;
                $this->save_post_option( $op[0], $u );
            } else if ( isset( $_POST[ $op ] ) && !empty( $_POST[ $op ] ) ) {
                elog( $op );
                $v = $_POST[ $op ];
                $v = is_array( $v ) ? json_encode( $v ) : $v;
                $r = $this->select( 'options', 'option_value', 'option_name = \''.$op.'\'', 1 );
                if( $r ) {
                    $this->update('options', ['option_value'], [$v], 'option_name = \'' . $op . '\'');
                } else {
                    $this->insert( 'options', [ 'option_name', 'option_value' ], [ $op, $v ] );
                }
                //return $c;
                //$db->save_post_option( $op );
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

    /**
     * Turns a string of IDs into a query statement Ex: 1, 4 into user_id = 1 OR user_id = 4
     * @param string $string IDs string Ex: 1, 2, 4
     * @param string $column Columns to query thru Ex: company_id
     * @return string
     */
    function ids_string_to_query( string $string, string $column ): string {
        $ids = explode( ',', str_replace( ' ', '', $string ) );
        $query = '';
        if( !empty( $ids ) && is_array( $ids ) ) {
            foreach( $ids as $id ) {
                $query .= $column.' = \''.$id.'\' OR ';
            }
        }
        return rtrim( $query, ' OR ' );
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
function prepare_keys( array|string $array = '', string $pre = '', bool $remove_empty = true ): array {
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
    //elog( $keys );
    return $keys;
}

/**
 * Prepares an array of only values from a given array or post
 * @param array|string $array
 * @param string $pre
 * @param bool $remove_empty
 * @return array
 */
function prepare_values( array|string $array = '', string $pre = '', bool $remove_empty = true ): array {
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
    //elog( $values );
    return $values;
}

function process_data_ajax(): void {
    $a = $_POST;
    elog( $a );
    if( !empty( $a['t'] ) ){
        $cry = Encrypt::initiate();
        $db = new DB();
        $table = $cry->decrypt( $a['t'] );
        unset( $a['t'] );

        if( isset( $a[ $a['pre'].'id'] ) ){
            $id = is_numeric( $a[ $a['pre'].'id'] ) ? $a[ $a['pre'].'id'] : $cry->decrypt( $a[$a['pre'].'id'] );
            //elog( $id );
            unset($a[$a['pre'].'id']);
        } else if( isset( $a[ 'id' ] ) ) {
            $id = is_numeric( $a[ 'id' ] ) ? $a[ 'id' ] : $cry->decrypt( $a[ 'id' ] );
            unset( $a['id'] );
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
            $cry = Encrypt::initiate();
            $dec = $cry->decrypt( $a['h'] );
            $hs = !empty( $dec ) ? json_decode( $dec, 1 ) : [];
            if( is_array( $hs ) ){
                elog( 'Hidden is array' );
                foreach( $hs as $k => $v ){
                    $k == 'id' ? $id = $v : $a[ $pre.$k ] = $v;
                }
            }
            unset( $a['h'] );
        }

        if( !empty( $a['alerts'] ) ) {
            $alerts = $cry->decrypt_array( $a['alerts'] );
            elog($alerts);
            unset( $a['alerts'] );
        }

        if( !empty( $a['emails'] ) ) {
            $emails = $cry->decrypt_array( $a['emails'] );
            unset( $a['emails'] );
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
            $ac = new ALERTS();
            foreach( $alerts as $al ) {
                elog($al);
                if( !empty( $al->title ) ) {
                    $title = $al->title;
                    $note = isset( $al->note ) && !empty( $al->note ) ? $al->note : '';
                    $type = isset( $al->type ) && !empty( $al->type ) ? $al->type : 'alert';
                    $link = isset( $al->link ) && !empty( $al->link ) ? $al->link : '';
                    $user = isset( $al->user ) && !empty( $al->user ) ? $al->user : '';
                    $sent_alerts[] = $ac->create( $title, $note, $type, $link, $user );
                }
            }
        }

        // Send Emails
        if( isset( $emails ) && is_array( $emails ) && $query ) {
            elog( $emails );
            elog('Isset Emails :)');
            $mailer = new MAIL();
            foreach( $emails as $e ) {
                $to = isset( $e['field'] ) ? $a[ $e['field'] ] : $e['to'];
                elog( 'Each Email' );
                if( !empty( $to ) ) {
                    $mailer->send( $e['to'], $e['subject'], $e['content'] );
                }
            }
        }
        die();
    } else {
        ef('Database not targeted properly, please contact support');
    }
}

function process_options_ajax(): void {
    unset( $_POST['t'] );
    unset( $_POST['pre'] );
    $p = $_POST;
    elog( $p );
    if( !empty( $p ) ) {
        $db = new DB();
        $encrypt = $p['encrypt'] ?? [];
        $unique = $p['unique'] ?? [];
        $autoload = $p['autoload'] ?? [];
        $result = $db->update_options( $p, $encrypt, $unique, $autoload );
        $result ? es('Successfully updated!') : ef('No new data is updated!');
    }
}

function update_data_ajax(): void {
    $c = Encrypt::initiate();
    $p = $_POST;
    elog( $p );
    $target = isset( $p['target'] ) && !empty( $p['target'] ) ? $c->decrypt( $p['target'] ) : '';
    $keys = isset( $p['keys'] ) && !empty( $p['keys'] ) ? $c->decrypt_array( $p['keys'] ) : '';
    $values = isset( $p['values'] ) && !empty( $p['values'] ) ? $c->decrypt_array( $p['values'] ) : '';
    $logic = isset( $p['logic'] ) && !empty( $p['logic'] ) ? $c->decrypt( $p['logic'] ) : '';
    if( !empty( $target ) && is_array( $keys ) && is_array( $values ) && !empty( $logic ) ) {
        $db = new DB();
        $r = $db->update( $target, $keys, $values, $logic );
        $r ? es('Successfully Updated!') : ef('Update failed due to query misinterpret, please contact support');
    } else {
        ef('Update failed due to query misinterpret, please contact developer');
    }
}

/**
 * Trashes data
 */
function trash_data_ajax(): void {
    $c = Encrypt::initiate();
    $p = $_POST;
    $target = isset( $p['target'] ) && !empty( $p['target'] ) ? $c->decrypt( $p['target'] ) : '';
    $logic = isset( $p['logic'] ) && !empty( $p['logic'] ) ? $c->decrypt( $p['logic'] ) : '';
    if( !empty( $target ) && !empty( $logic ) ){
        $db = new DB();
        $r = $db->delete( $target, $logic );
        $r ? es('Deleted successfully') : ef('Delete failed due to query misinterpret, please contact support');
    } else {
        ef('Delete failed due to query misinterpret, please contact developer');
    }
}
