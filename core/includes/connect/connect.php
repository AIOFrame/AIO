<?php

if( defined( 'CONFIG' ) && !empty( CONFIG ) ) {

    // Parse App CONFIG
    //$c = json_decode( CONFIG, 1 );
    $c = CONFIG;
    $d = !empty( $c['data'] ) && is_array( $c['data'] ) ? $c['data'] : [];
    $type = $host = $base = $user = $pass = '';

    // Assign Database Type
    $type = $d['type'] ?? $type;

    // Assign Database Host
    $host = $d['server'] ?? ( $d['host'] ?? ( $d['url'] ?? $host ) );

    // Assign Database Name
    $base = $d['database'] ?? ( $d['name'] ?? ( $d['base'] ?? ( $d['db'] ?? ( $d['d'] ?? $base ) ) ) );

    // Assign Database Username
    $user = $d['username'] ?? ( $d['user'] ?? ( $d['u'] ?? $user ) );

    // Assign Database Password
    $pass = $d['password'] ?? ( $d['pass'] ?? ( $d['p'] ?? $pass ) );

    // Assign Port
    $port = $d['port'] ?? 3306;

    // Connect to Database
    if( !empty( $type ) && !empty( $host ) && !empty( $base ) ) {

        // Define Database Config
        !defined( 'DB_TYPE' ) ? define( 'DB_TYPE', $type ) : '';
        !defined( 'DB_HOST' ) ? define( 'DB_HOST', $host ) : '';
        !defined( 'DB_BASE' ) ? define( 'DB_BASE', $base ) : '';
        !defined( 'DB_USER' ) ? define( 'DB_USER', $user ) : '';
        !defined( 'DB_PASS' ) ? define( 'DB_PASS', $pass ) : '';
        !defined( 'DB_PORT' ) ? define( 'DB_PORT', $port ) : '';

        global $db;

        // Setup Connection String
        $connection_string = '';
        switch( $type ) {
            case 'mysql':
                $connection_string = "mysql:host=$host;port=$port;dbname=$base;charset=utf8mb4";
                break;
            case 'mssql':
                //$connection_string = "sqlsrv:Server=($host);Database=$base";
                $connection_string = "sqlsrv:Server=".$host.";Database=".$base;
                //$connection_string = "odbc: Driver = {SQL Server}; Server=$host; null; null";
                break;
            // TODO: Add additional database types
        }

        // Connect to Database
        if( !empty( $connection_string ) ) {
            try {
                $connection = new PDO($connection_string, $user, $pass);
                $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                !defined( 'APPCON' ) ? define( 'APPCON', 1 ) : '';
                !defined( 'DB_TYPE' ) ? define( 'DB_TYPE', 'mysql' ) : '';
            } catch(PDOException $e) {
                echo 'Connecting to database failed: '.$e->getMessage();
                elog( $e->getMessage() );
            }
        } else {
            elog( 'Database type is not set! Please check database config!!' );
        }

    }

}