<?php
!defined( 'ROOTPATH' ) ? exit() : '';

/**
 * Get domain or sub domain
 * @param string $t
 * @return string
 */
function get_domain( string $t = 'main' ): string {
    $dex = explode( '.', rtrim($_SERVER['HTTP_HOST'],'/') );
    if(count($dex) > 3){
        return $t == 'sub' ? $dex[1] : $dex[1].'.'.$dex[2];
    } else if(count($dex) == 3) {
        if($dex[0] !== 'www' && $dex[0] !== 'http://www' && $dex[0] !== 'https://www'){
            return $t == 'sub' ? $dex[0] : $dex[1].'.'.$dex[2];
        } else {
            return $dex[1];
        }
    } else if(count($dex) == 2) {
        if($dex[0] !== 'www' && $dex[0] !== 'http://www' && $dex[0] !== 'https://www'){
            return $t == 'sub' ? false : $dex[0];
        } else {
            return $dex[0];
        }
    } else if( count($dex) == 1 ){
        return strpos($dex[0], ':') !== false ? substr($dex[0], 0, strpos($dex[0],':')) : $dex[0];
    }
}

/**
 * Get sub domain if
 * @return string
 */
function sub_domain(): string {
    return get_domain('sub');
}

/**
 * Reads App Mapping
 * Will check map.php and set domain to app link
 */
$app = !empty( sub_domain() ) ? sub_domain() : get_domain();
if( file_exists( ROOTPATH . 'map.php' ) ){
    include ROOTPATH . 'map.php';
    if( !empty( $map ) && !empty( $map[ $app ] ) ){ $app = $map[ $app ]; }
    if( !file_exists( ROOTPATH . 'apps/' . $app ) ) {
        if (isset($default) && file_exists(ROOTPATH . 'apps/' . $default)) {
            $app = $default;
        }
    }
}

/**
 * Defines the Application Directory
 * Ex: ecommerce
 */
!defined( 'APPDIR' ) ? define( 'APPDIR', $app ) : '';

/**
 * Defines Application Path
 * Ex: /users/root/www/application/apps/ecommerce/
 */
!defined( 'APPPATH' ) ? define( 'APPPATH', ROOTPATH . 'apps/' . $app . '/' ) : '';

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
require_once ROOTPATH . 'core/includes/config.php';
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
    // !file_exists( APPPATH . 'storage/log' ) ? mkdir( APPPATH . 'storage/log', 0700, 1 ) : '';
    if( !file_exists( $log ) ) {
        $file = fopen( $log, 'w' );
        fwrite( $file, 'THIS EVENTS LOG FILE IS AUTO-GENERATED BY AIO'.PHP_EOL.PHP_EOL );
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
error_log( json_encode( $_POST ) );
/**
 * Load Page
 */
defined( 'PAGELOAD' ) ? require PAGELOAD : '';

/**
 * Loads exit files, that run at the end
 */
require ROOTPATH . 'core/includes/exit.php';