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

/**
 * Get sub domain if
 * @return string
 */
function sub_domain(): string {
    return get_domain('sub');
}

/**
 * AIO Error Log
 * @param string $log Log message
 * @param string $type Log type 'log', 'error'
 * @param string $line The link where the log is logged
 * @param string $file The file path which initiates the log
 * @param string $target Target
 * @author Shaikh <hey@shaikh.dev>
 */
function elog( string $log, string $type = 'log', string $line = '', string $file = '', string $target = '' ){
    $log = is_array( $log ) ? json_encode( $log ) : $log;
    $log = is_object( $log ) ? var_dump( $log ) : $log;

    $data = $log . '<AIO>' . $type;
    $data .= $line !== '' ? '<AIO>' . $line : '';
    $data .= $file !== '' ? '<AIO>' . $file : '';
    $data .= $target !== '' ? '<AIO>' . $target : '';

    // Get dev users
    $debug = 0;
    if( defined( 'CONFIG' ) ) {
        $devs = get_config('dev');
        $devs = !empty($dev) ? explode(',', $devs) : [];
        $debug = isset($_SESSION['user_id']) && is_array($devs) && in_array($_SESSION['user_id'], $devs) ? 1 : 0;
    }

    // Log
    APPDEBUG || $debug ? error_log( $data . PHP_EOL ) : '';
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
 * Defines Application URI
 * Ex: https://ecommerce.mainapp.com/apps/ecommerce
 */
!defined( 'APPURI' ) ? define( 'APPURI', APPURL . 'apps/' . $app . '/' ) : '';

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
 * Loads config file if exists
 */
if( file_exists( ROOTPATH . 'apps/' . $app . '/config.php' ) ) {
    $c = include ROOTPATH . 'apps/' . $app . '/config.php';
    !defined( 'CONFIG' ) ? define( 'CONFIG', json_encode( $c ) ) : '';
}

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
    $log = APPPATH . 'storage/log/error_log.log';
    !file_exists( APPPATH . 'storage/log' ) ? mkdir( APPPATH . 'storage/log', 0700, 1 ) : '';
    if( !file_exists( $log ) ) {
        $file = fopen($log, 'w');
        fwrite($file, '');
        fclose($file);
    }
    ini_set( 'error_log', $log );
} else {
    error_reporting(0);
}

/**
 * Loads Basic Necessary Functions
 */
if( defined( 'CONFIG' ) ) {
    require_once ROOTPATH . 'core/includes/config.php';
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

/**
 * Load Page
 */
defined( 'PAGELOAD' ) ? require PAGELOAD : '';

/**
 * Loads exit files, that run at the end
 */
require ROOTPATH . 'core/includes/exit.php';