<?php

$v = isset( $_GET['v'] ) && !empty( $_GET['v'] ) ? $_GET['v'] : '14400';

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=".$v);

if( file_exists( __DIR__ . '/styles/aio/inputs.min.css' ) ){
    $inputs = file_get_contents( __DIR__ . '/styles/aio/inputs.min.css' );
} else {
    $inputs = file_get_contents( __DIR__ . '/styles/aio/inputs.css' );
}

// Replace First Gradient

if( isset( $_GET['fc'] ) ){
    $fc = $_GET['fc'];
    if( strlen( $fc ) <= 6 ){
        $inputs = str_replace( '111', $fc, $inputs );
    } else {
        $inputs = str_replace( '#111', $fc, $inputs );
    }
}

// Replace Second Gradient

if( isset( $_GET['sc'] ) ){
    $sc = $_GET['sc'];
    //$sc = substr( $sc, 0, strpos( $sc, '?'));
    if( strlen( $sc ) <= 6 ){
        $inputs = str_replace( '222', $sc, $inputs );
    } else {
        $inputs = str_replace( '#222', $sc, $inputs );
    }
}

// Replace necessary strings

$inputs = str_replace( '../images', './images', $inputs );

// Output CSS

echo $inputs;