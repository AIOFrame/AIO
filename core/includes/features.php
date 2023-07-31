<?php

/**
 * Loads features from config file if exists
 */
if( file_exists( ROOTPATH . 'apps/' . APPDIR . '/config.php' ) ) {
    $c = include ROOTPATH . 'apps/' . APPDIR . '/config.php';
    if( !empty( $c['features'] ) ) {
        !defined( 'FEATURES' ) ? define( 'FEATURES', $c['features'] ) : '';
    }
}