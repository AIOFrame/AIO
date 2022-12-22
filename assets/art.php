<?php

$art = '';
$ui_dir = __DIR__ . '/styles/aio/art/';
$ux_dir = __DIR__ . '/scripts/aio/art/';

// Fetch type and set cache
if( isset( $_GET['type'] ) && $_GET['type'] == 'ux' ) {
    $type = 'ux';
    $header = 'Content-Type: application/javascript';
} else {
    $type = 'ui';
    $header = 'Content-type: text/css; charset: UTF-8';
}
$v = isset( $_GET['v'] ) && !empty( $_GET['v'] ) ? $_GET['v'] : '31536000';
header( $header );
header( 'Cache-Control: max-age=' . $v );

// Parse art modules
$arts = isset( $_GET['arts'] ) && !empty( $_GET['arts'] ) ? explode( ',', $_GET['arts'] ) : [];

if( $type == 'ui' ) {

    // Get style code
    foreach( $arts as $a ) {
        if( file_exists( $ui_dir . $a . '.min.css' ) ){
            $art .= file_get_contents( $ui_dir . $a . '.min.css' );
        } else if( file_exists( $ui_dir . $a . '.css' ) ) {
            $art .= file_get_contents( $ui_dir . $a . '.css' );
        }
    }

    // Replace First Gradient
    if( isset( $_GET['fc'] ) ){
        $fc = $_GET['fc'];
        $art = strlen( $fc ) <= 6 ? str_replace( '111', $fc, $art ) : str_replace( '#111', $fc, $art );
    }

    // Replace Second Gradient
    if( isset( $_GET['sc'] ) ){
        $sc = $_GET['sc'];
        $art = strlen( $sc ) <= 6 ? str_replace( '222', $sc, $art ) : str_replace( '#222', $sc, $art );
    }

    // Replace necessary strings
    $art = str_replace( '../images', './images', $art );

} else if( $type == 'ux' ) {

    // Get script code
    foreach( $arts as $a ) {
        // Load variables
        include_once( __DIR__ . '/../core/includes/encrypt.php' );
        $cry = Encrypt::initiate();
        if( $a == 'alerts' ) {
            $art .= 'let get_alerts = \''.$cry->encrypt('get_alerts_ajax').'\';';
        }
        // Load script content
        if( file_exists( $ux_dir . $a . '.min.js' ) ){
            $art .= file_get_contents( $ux_dir . $a . '.min.js' );
        } else if( file_exists( $ux_dir . $a . '.js' ) ) {
            $art .= file_get_contents( $ux_dir . $a . '.js' );
        }
    }

}

// Output code

echo $art;