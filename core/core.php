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
        } else {
            return $dex[1];
        }
    } else if(count($dex) == 2) {
        if($dex[0] !== 'www' && $dex[0] !== 'http://www' && $dex[0] !== 'https://www'){
            if( $t == 'sub' ){
                return false;
            } else {
                return $dex[0];
            }
        } else {
            return $dex[0];
        }
    } else if( count($dex) == 1 ){
        return strpos($dex[0], ':') !== false ? substr($dex[0], 0, strpos($dex[0],':')) : $dex[0];
    }
}

function sub_domain() {
    return get_domain('sub');
}

// Load Application Settings
function app_loader() {

    // Set Locale

    $app = !empty( sub_domain() ) ? sub_domain() : get_domain();
    if( file_exists( COREPATH . 'map.php' ) ){ // Checking if app mapping in defined // !file_exists( COREPATH . 'apps/' . $app ) &&
        include( COREPATH . 'map.php' );
        if( !empty( $map ) && !empty( $map[ $app ] ) ){ $app = $map[ $app ]; }
        if( !file_exists( COREPATH . 'apps/' . $app ) ) {
            if (isset($default) && file_exists(COREPATH . 'apps/' . $default)) {
                $app = $default;
            }
        }
    }
    if( !empty( $app ) && file_exists( COREPATH . 'apps/' . $app ) ) { // if app exists and config exists

        if( file_exists( COREPATH . 'apps/' . $app . '/config.php' ) ) { // If app has config file
            $c = include( COREPATH . 'apps/' . $app . '/config.php' );// load config
        };

        !defined( 'APPDIR' ) ? define( 'APPDIR', $app ) : ''; // Defines the Application Directory Ex: ecommerce

        !defined( 'APPPATH' ) ? define( 'APPPATH', COREPATH . 'apps/' . $app . '/' ) : ''; // Defines Application Path Ex: /users/root/www/application/apps/ecommerce/

        !defined( 'APPURI' ) ? define( 'APPURI', APPURL . 'apps/' . $app . '/' ) : ''; // Defined Application URI Ex: https://ecommerce.mainapp.com/apps/ecommerce

        //$c = !empty( $config ) ? $config : '';
        isset( $c['timezone'] ) && !empty( $c['timezone'] ) ? date_default_timezone_set( $c['timezone'] ) : ''; // Defines Timezone

        isset( $c['ssl'] ) && $c['ssl'] && !isset( $_SERVER['HTTPS'] ) ? header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) : ''; // Forces SSL

        $appname = isset( $c['name'] ) && !empty( $c['name'] ) ? $c['name'] : ucwords( $app ); // Defines the Application Name Ex: Amazing App
        !defined( 'APPNAME' ) ? define( 'APPNAME', $appname ) : '';

        $debug = isset( $c['debug'] ) && !empty( $c['debug'] ) ? $c['debug'] : false; // Defines if the Application is under development mode
        !defined( 'APPDEBUG' ) ? define( 'APPDEBUG', $debug ) : '';
        if( APPDEBUG ) {
            error_reporting(E_ALL);
            $log_dir = APPPATH . 'storage/log';
            $elog = $log_dir . '/error_log.log';
            if( !file_exists( $log_dir )){
                mkdir( $log_dir, 0700 );
            }
            if( !file_exists( $elog )) {
                $file = fopen($elog, 'w');
                fwrite($file, '');
                fclose($file);
            }
            ini_set( 'error_log', APPPATH . 'storage/log/error_log.log' );
        } else {
            error_reporting(0);
        }

        $ekey = isset( $c['key'] ) && !empty( $c['key'] ) ? $c['key'] : str_replace(' ','_',defined('APPNAME') ); // Defines the Application Encryption Key
        !defined( 'EKEY' ) ? define( 'EKEY', $ekey ) : '';

        if( isset( $c['host'] ) && !empty( $c['host'] ) && isset( $c['user'] ) && !empty( $c['user'] ) && isset( $c['pass'] ) && !empty( $c['pass'] ) && isset( $c['database'] ) && !empty( $c['database'] ) ){

            //header("Content-Type: text/html; charset=UTF-8");

            global $db;
            $db = @mysqli_connect( $c['host'], $c['user'], $c['pass'], $c['database'] );
            if ( $db ) {
                mysqli_query( $db, "SET NAMES 'utf8'");
                mysqli_query( $db, 'SET CHARACTER SET utf8' );
                !defined( 'APPCON' ) ? define( 'APPCON', true ) : '';
            } else {
                die( mysqli_connect_error() );
            }

        }
        require( COREPATH . 'core/access.php' ); // Loads authorization files
        require( COREPATH . 'core/includes/routes.php' ); // Loads Routes PHP File
        require( COREPATH . 'core/includes.php' ); // Loads all PHP files from Includes
        defined( 'PAGELOAD' ) ? include( PAGELOAD ) : '';
        require( COREPATH . 'core/includes/exit.php' ); // Loads closing PHP file
    } else {
        $error = '00';
        include( COREPATH . 'core/pages/error.php');
    }
}

app_loader();