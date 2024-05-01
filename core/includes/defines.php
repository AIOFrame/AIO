<?php

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
//print_r( APPDIR );

/**
 * Defines Application Path
 * Ex: /users/root/www/application/apps/ecommerce/
 */
!defined( 'APPPATH' ) ? define( 'APPPATH', ROOTPATH . 'apps/' . $app . '/' ) : '';
//print_r( APPPATH );

/**
 * Get domain or sub domain
 * @param string $t
 * @return string
 */
function get_domain( string $t = 'main' ): string {
    $dex = explode( '.', rtrim($_SERVER['HTTP_HOST'],'/') );
    if( count($dex) > 3 ){
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
    } else {
        return '';
    }
}

/**
 * Get subdomain if is
 * @return string
 */
function sub_domain(): string {
    return get_domain('sub');
}

/**
 * Defines the core directory path of AIO Framework
 * Ex: /home/user/public_html/
 */
!defined( 'COREPATH' ) ? define( 'COREPATH', ROOTPATH . 'core/' ) : '';
//print_r( COREPATH );

/**
 * Defines the includes directory path of AIO Framework
 * Ex: /home/user/public_html/
 */
!defined( 'INCPATH' ) ? define( 'INCPATH', COREPATH . 'includes/' ) : '';
//print_r( INCPATH );

$pre = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";

// Load App Config
require_once ROOTPATH . 'core/includes/config.php';
//echo '<pre>';
//print_r( CONFIG );
//echo '</pre>';

/**
 * Defines the URL of the Main Application
 * Ex: www.example.com
 */
$url = CONFIG['appurl'] ?? $_SERVER['HTTP_HOST'];
$url = str_replace( 'http://', '', str_replace( 'https://', '', $url ) );
!defined( 'APPURL' ) ? define( 'APPURL', $pre . $url . "/" ) : '';
//print_r( APPURL );