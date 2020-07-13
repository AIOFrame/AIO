<?php

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=14400");

if( file_exists( __DIR__ . '/styles/aio/elements.min.css' ) ){
    $art = file_get_contents( __DIR__ . '/styles/aio/elements.min.css' );
} else {
    $art = file_get_contents( __DIR__ . '/styles/aio/elements.css' );
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