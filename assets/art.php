<?php

$art = '';
$l = __DIR__ . '/styles/aio/art/';

// Fetch and set cache
$v = isset( $_GET['v'] ) && !empty( $_GET['v'] ) ? $_GET['v'] : '14400';
header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=".$v);

// Auto fetch existing art styles
$art_styles = glob( $l . '*.css' );
$styles = [];
foreach( $styles as $af ) {
    $arts[] = str_replace( '.css', '', str_replace( $l, '', $af ) );
}

// Parse art modules
$arts = isset( $_GET['arts'] ) && !empty( $_GET['arts'] ) ? explode( ',', $_GET['arts'] ) : [];
foreach( $arts as $a ) {
    if( in_array( $a . '.min', $arts ) && file_exists( $l . $a . '.min.css' ) ){
        $art .= file_get_contents( $l . $a . '.min.css' );
    } else if( in_array( $a, $arts ) && file_exists( $l . $a . '.css' ) ) {
        $art .= file_get_contents( $l . $a . '.css' );
    }
}

// Replace First Gradient
if( isset( $_GET['fc'] ) ){
    $fc = $_GET['fc'];
    if( strlen( $fc ) <= 6 ){
        $art = str_replace( '111', $fc, $art );
    } else {
        $art = str_replace( '#111', $fc, $art );
    }
}

// Replace Second Gradient
if( isset( $_GET['sc'] ) ){
    $sc = $_GET['sc'];
    //$sc = substr( $sc, 0, strpos( $sc, '?'));
    if( strlen( $sc ) <= 6 ){
        $art = str_replace( '222', $sc, $art );
    } else {
        $art = str_replace( '#222', $sc, $art );
    }
}

// Replace necessary strings

$art = str_replace( '../images', './images', $art );

// Output CSS

echo $art;