<?php
!defined( 'ROOTPATH' ) ? exit() : '';



/**
 * Define Vendor Path
 */
if( !defined( 'EXTPATH' ) )
    define( 'EXTPATH', ROOTPATH . 'core/external/' );
if( !defined( 'VENDORPATH' ) )
    define( 'VENDORPATH', ROOTPATH . 'core/external/vendor' );
if( !defined( 'VENDORLOAD' ) )
    define( 'VENDORLOAD', ROOTPATH . 'core/external/vendor/autoload.php' );

/**
 * Loads Basic Necessary Functions
 */
//if( !defined( 'CONFIG' ) ) {
require_once ROOTPATH . 'core/includes/features.php';
//}
//$c = CONFIG;

/**
 * Defines Timezone if set in config
 */
isset( $c['timezone'] ) && !empty( $c['timezone'] ) ? date_default_timezone_set( $c['timezone'] ) : '';

/**
 * Forces SSL if set in config
 */
if( ( isset( $c['ssl'] ) && $c['ssl'] ) && !isset( $_SERVER['HTTPS'] ) ) {
    header('Location: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
}

/**
 * Defines App Name if set in config
 * Ex: Amazing App
 */
isset( $c['name'] ) && !empty( $c['name'] ) && !defined( 'APPNAME' ) ? define( 'APPNAME', $c['name'] ) : '';
!defined( 'APPNAME' ) ? define( 'APPNAME', ucwords( $app ) ) : '';

/**
 * Sets Debug settings if set in config
 */
$debug = isset( $c['debug'] ) && !empty( $c['debug'] ) ? $c['debug'] : false; // Defines if the Application is under development mode
!defined( 'APPDEBUG' ) ? define( 'APPDEBUG', $debug ) : '';

if( APPDEBUG ) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    $log = APPPATH . 'events.log';
    if( !file_exists( $log ) ) {
        $file = fopen( $log, 'w' );
        fwrite( $file, 'LOG BY AIO'.PHP_EOL.PHP_EOL );
        fclose( $file );
    }
    ini_set( 'error_log', $log );
} else {
    error_reporting(0);
}

/**
 * Loads Database connection
 */
require ROOTPATH . 'core/includes/connect/connect.php';

/**
 * Loads includes, which in-turn loads dependencies
 */
require ROOTPATH . 'core/includes.php';

/**
 * Load Page
 */
if( !defined('APPDIR') || !file_exists( ROOTPATH . 'apps/' . APPDIR ) ) {
    /**
     * Load Error page if web app not found
     */
    $error = '00';
    !defined('APPDEBUG') ? define('APPDEBUG',1) : '';
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
    include ROOTPATH . 'core/pages/error.php';
    return;
} else {
    /**
     * Sets AIO page routing
     */
    require ROOTPATH . 'core/routes.php';
}
defined( 'PAGELOAD' ) ? require PAGELOAD : '';

/**
 * Loads exit files, that run at the end
 */
require ROOTPATH . 'core/includes/exit.php';