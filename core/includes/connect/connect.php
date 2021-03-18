<?php

if( defined( 'CONFIG' ) && !empty( CONFIG ) ) {

    // Parse App CONFIG
    $c = json_decode( CONFIG, 1 );
    $d = !empty( $c['data'] ) && is_array( $c['data'] ) ? $c['data'] : [];
    $type = $host = $base = $user = $pass = '';

    // Assign Database Type
    $type = isset( $d['type'] ) ? $d['type'] : $type;

    // Assign Database Host
    $host = isset( $d['server'] ) ? $d['server'] : $host;
    $host = isset( $d['host'] ) ? $d['host'] : $host;
    $host = isset( $d['url'] ) ? $d['url'] : $host;

    // Assign Database Name
    $base = isset( $d['database'] ) ? $d['database'] : $base;
    $base = isset( $d['name'] ) ? $d['name'] : $base;
    $base = isset( $d['base'] ) ? $d['base'] : $base;
    $base = isset( $d['db'] ) ? $d['db'] : $base;
    $base = isset( $d['d'] ) ? $d['d'] : $base;

    // Assign Database Username
    $user = isset( $d['username'] ) ? $d['username'] : $user;
    $user = isset( $d['user'] ) ? $d['user'] : $user;
    $user = isset( $d['u'] ) ? $d['u'] : $user;

    // Assign Database Password
    $pass = isset( $d['password'] ) ? $d['password'] : $pass;
    $pass = isset( $d['pass'] ) ? $d['pass'] : $pass;
    $pass = isset( $d['p'] ) ? $d['p'] : $pass;

    // Connect to Database
    if( !empty( $type ) && !empty( $host ) && !empty( $base ) && !empty( $user ) && !empty( $pass ) ) {

        // Define Database Config
        !defined( 'DB_HOST' ) ? define( 'DB_HOST', $host ) : '';
        !defined( 'DB_BASE' ) ? define( 'DB_BASE', $base ) : '';
        !defined( 'DB_USER' ) ? define( 'DB_USER', $user ) : '';
        !defined( 'DB_PASS' ) ? define( 'DB_PASS', $pass ) : '';

        global $db;

        // Setup Connection String
        $connection_string = '';
        switch( $type ) {
            case 'mysql':
                $connection_string = "mysql:host=$host;dbname=$base;charset=utf8mb4";
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