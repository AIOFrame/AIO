<?php
!defined( 'ROOTPATH' ) ? exit() : '';



/**
 * Define Vendor Path
 */
!defined( 'EXTPATH' ) ? define( 'EXTPATH', ROOTPATH . 'core/external/' ) : '';
!defined( 'VENDORPATH' ) ? define( 'VENDORPATH', ROOTPATH . 'core/external/vendor' ) : '';
!defined( 'VENDORLOAD' ) ? define( 'VENDORLOAD', ROOTPATH . 'core/external/vendor/autoload.php' ) : '';

/**
 * Load Error page if web app not found
 */
if( empty( $app ) || !file_exists( ROOTPATH . 'apps/' . $app ) ) {
    $error = '00';
    define('APPDEBUG',1);
    include ROOTPATH . 'core/pages/error.php';
    return;
}

/**
 * Loads Basic Necessary Functions
 */
//if( !defined( 'CONFIG' ) ) {
require_once ROOTPATH . 'core/includes/features.php';
//}
//$c = CONFIG;
//skel( $c );

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
 * Sets AIO page routing
 */
require ROOTPATH . 'core/routes.php';

/**
 * Loads includes, which in-turn loads dependencies
 */
require ROOTPATH . 'core/includes.php';
//error_log( json_encode( $_POST ) );
/**
 * Load Page
 */
defined( 'PAGELOAD' ) ? require PAGELOAD : '';

/**
 * Loads exit files, that run at the end
 */
require ROOTPATH . 'core/includes/exit.php';