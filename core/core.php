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

function sub_domain() {
    return get_domain('sub');
}

// Load Application Settings
function app_loader() {
    $app = !empty( sub_domain() ) ? sub_domain() : get_domain();

    if( file_exists( COREPATH . 'map.php' ) ){ // Checking if app mapping in defined // !file_exists( COREPATH . 'apps/' . $app ) &&
        include( COREPATH . 'map.php' );
        if( !empty( $map ) && !empty( $map[ $app ] ) ){ $app = $map[ $app ]; }
    }

    if( file_exists( COREPATH . 'apps/' . $app ) ) { // if app exists and config exists

        if( file_exists( COREPATH . 'apps/' . $app . '/config.php' ) ) { // If app has config file
            include( COREPATH . 'apps/' . $app . '/config.php' );// load config
        }

        !defined( 'APPDIR' ) ? define( 'APPDIR', $app ) : ''; // Defines the Application Directory Ex: ecommerce

        !defined( 'APPPATH' ) ? define( 'APPPATH', COREPATH . 'apps/' . $app . '/' ) : ''; // Defines Application Path Ex: /users/root/www/application/apps/ecommerce/

        !defined( 'APPURI' ) ? define( 'APPURI', APPURL . 'apps/' . $app . '/' ) : ''; // Defined Application URI Ex: https://ecommerce.mainapp.com/apps/ecommerce

        $c = !empty( $config ) ? $config : '';

        if( !empty( $c['host'] ) && !is_null( $c['user'] ) && !is_null( $c['pass'] ) && !is_null( $c['database'] ) ){

            $appname = !empty( $c['name'] ) ? $c['name'] : ucwords( $app );
            $ekey = !empty( $c['key'] ) ? $c['key'] : str_replace(' ','_',defined('APPNAME'));
            $debug = !empty( $c['debug'] ) ? $c['debug'] ? true : false : false;

            !defined( 'APPNAME' ) ? define( 'APPNAME', $appname ) : ''; // Defines the Application Name Ex: Amazing App

            !defined( 'EKEY' ) ? define( 'EKEY', $ekey ) : ''; // Defines the Application Encryption Key

            !defined( 'APPDEBUG' ) ? define( 'APPDEBUG', $debug ) : ''; // Defines if the Application is under development mode

            global $db;
            $db = @mysqli_connect( $c['host'], $c['user'], $c['pass'], $c['database'] );
            if ( $db ) {
                mysqli_query( $db, 'SET CHARACTER SET utf8' );
                include( COREPATH . 'core/access.php' );
            } else {
                die( mysqli_connect_error() );
            }

            if( !APPDEBUG ){
                error_reporting(0);
            } else {
                error_reporting(E_ALL);
            }

        }

        $appname = !empty( $appname ) ? $appname : ucwords( $app );
        !defined( 'APPNAME' ) ? define( 'APPNAME', $appname ) : ''; // Defines the Application Name Ex: Amazing App

        $ekey = !empty( $ekey ) ? $ekey : str_replace(' ','_',defined('APPNAME'));
        !defined( 'EKEY' ) ? define( 'EKEY', $ekey ) : ''; // Defines the Application Encryption Key

        !defined( 'APPDEBUG' ) ? define( 'APPDEBUG', false ) : '';

        require( COREPATH . 'core/includes.php' );
    } else {
        $error = '00';
        include( COREPATH . 'core/pages/error.php');
    }
}

app_loader();