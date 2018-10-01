<?php

// Function to check if site is accessed thru root index
function sc() {
    !defined( 'COREPATH' ) ? exit() : '';
}

sc();

// Function to check sub domain
function is_sub_domain() {
    $dex = explode( '.', $_SERVER['HTTP_HOST'] );
    if(count($dex) > 2) {
        if(count($dex) > 3){
            return true;
        } else if(count($dex) == 3) {
            if($dex[0] !== 'www' && $dex[0] !== 'http://www' && $dex[0] !== 'https://www'){
                return true;
            } else {
                return false;
            }
        }
    } else {
        return false;
    }
}

// Function to get domain or sub domain
function get_domain( $t = 'main' ){
    $dex = explode( '.', rtrim($_SERVER['HTTP_HOST'],'/') );
    //print_r($dex);
    if(count($dex) > 3){
        if( $t == 'sub' ){
            return $dex[1];
        } else {
            return $dex[1].'.'.$dex[2];
        }
    } else if(count($dex) == 3) {
        if($dex[0] !== 'www' && $dex[0] !== 'http://www' && $dex[0] !== 'https://www'){
            if( $t == 'sub' ){
                return $dex[0];
            } else {
                return $dex[1].'.'.$dex[2];
            }
        }
    } else if(count($dex) == 2) {
        if($dex[0] !== 'www' && $dex[0] !== 'http://www' && $dex[0] !== 'https://www'){
            if( $t == 'sub' ){
                return false;
            } else {
                return $dex[0];
            }
        }
    }
}

// if ( !defined( 'MAINAPPURL') ){ define( 'MAINAPPURL', get_domain() ); }
function sub_domain() {
    return get_domain('sub');
}

// Load Application Settings
function app_loader() {
    $app = !empty( sub_domain() ) ? sub_domain() : get_domain();

    if( file_exists( COREPATH . 'apps/' . $app ) && file_exists( COREPATH . 'apps/' . $app . '/config.php' ) ) { // if app exists and config exists

        include( COREPATH . 'apps/' . $app . '/config.php' );// load config and make connection

        if( !is_null( $config['host'] ) && !is_null( $config['user'] ) && !is_null( $config['pass'] ) && !is_null( $config['database'] ) ){

            // Defines the Application Name
            !defined( 'APPNAME' ) && !is_null( $config['name'] ) ? define( 'APPNAME', $config['name'] ) : '';

            // Defines the Application Directory
            !defined( 'APPDIR' ) ? define( 'APPDIR', $app ) : '';

            // Defines the Application Path
            !defined( 'APPPATH' ) ? define( 'APPPATH', COREPATH . 'apps/' . $app ) : '';

            // Defines the Application URI
            !defined( 'APPURI' ) ? define( 'APPURI', APPURL . 'apps/' . $app . '/' ) : '';

            // Defines the Application Encryption Key
            !defined( 'EKEY' ) ? define( 'EKEY', $config['key'] ) : str_replace(' ','_',defined('APPNAME') ? APPNAME : 'SU');
            global $conn;
            $conn = @mysqli_connect( $config['host'], $config['user'], $config['pass'], $config['database'] );
            if ( $conn ) {
                mysqli_query( $conn, 'SET CHARACTER SET utf8' );
                include( COREPATH . 'core/access.php' );
                require( COREPATH . 'core/includes.php' );
            } else {
                die( mysqli_connect_error() );
            }
        } else {
            echo "Config values are empty"; // Redirect to setup with message
        }

    } else { // if app not exists

        // if default exists and config exists

            // load config and make connection

        // if default not exists
    }
}

app_loader();

    // If Sub Domain
    //$sd = get_domain('sub');
    

/*} else { // if not sub domain

    // if default exists and config exists

        // load config and make connection

    // if default not exists
}








// Function to make connection

/*
if( isset( $_GET['a'] ) ) {
    if ( $_GET['a'] == 'install' ) {
        if ( file_exists( COREPATH.'install/index.php' ) ) {
            include( COREPATH.'install/index.php' );
        }
    } else {
        require('connect.php');
    }
} else {
    require('connect.php');
}
*/

