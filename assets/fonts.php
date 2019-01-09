<?php

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=14400");

global $fonts;
$fonts = [];
if( isset( $_GET['family'] ) && !empty( $_GET['family'] ) ){
    $fd = explode( '|', $_GET['family'] );
    foreach( $fd as $fc ){
        $fs = explode( ':', $fc );
        $fonts[] = [ $fs[0], $fs[1] ];
    }
} else { die(); }

$pre = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";

!defined( 'APPURL' ) ? define( 'APPURL', $pre.$_SERVER['HTTP_HOST']."/" ) : '';

foreach( glob( dirname( __FILE__ ) . '/fonts/*', GLOB_ONLYDIR ) as $f ){

    $weights = [ 100 => 'Thin', 200 => 'ExtraLight', 300 => 'Light', 400 => 'Regular', 500 => 'Medium', 600 => 'SemiBold', 700 => 'Bold', 800 => 'ExtraBold', 900 => 'Black' ];

    $fe = explode( '/', $f );
    $fn = $fe[ count( $fe ) - 1 ];

    global $fonts;
    foreach( $fonts as $font ){
        if( strtolower( $fn ) == strtolower( $font[0] ) ){
            $ws = !empty( $font[1] ) ? explode( ',', $font[1] ) : [ 400 ];
            foreach( $ws as $w ){
                $fp = '/' . $fn . '-' . $weights[ $w ];
                if( file_exists( $f . $fp.'.eot' ) ) {
                    echo '@font-face{font-family:\'' . $fn . '\';';
                    echo file_exists($f . $fp. '.eot' ) ? 'src:url(\'fonts/'.$fn . $fp . '.eot\');src:' : 'src:';
                    echo file_exists($f . $fp. '.eot') ? 'url(\'fonts/'.$fn . $fp . '.eot?#iefix\') format(\'embeded-opentype\')' : '';
                    echo file_exists($f . $fp. '.woff2') ? ',url(\'fonts/'.$fn . $fp . '.woff2\') format(\'woff2\')' : '';
                    echo file_exists($f . $fp. '.woff') ? ',url(\'fonts/'.$fn . $fp . '.woff\') format(\'woff\')' : '';
                    echo ';font-weight:' . $w . ';';
                    echo 'font-style:normal';
                    echo '}';
                }
            }
        }
    }
}