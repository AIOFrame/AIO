<?php

$app = '';

if( is_array( $_GET ) ) {
    foreach( $_GET as $a => $f ) {
        $app = !is_numeric( $a ) ? $a : '';
        $_GET['family'] = $f;
    }
}

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=31536000");

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
                $weight = is_numeric( $w ) && isset( $weights[ $w ] ) ? $weights[ $w ] : $w;
                $fp = '/' . $fn . '-' . $weight;
                $css_pre = '@font-face{font-family:\'' . $fn . '\';';
                $fonts_css = $fn == 'NotoColorEmoji' ? 'unicode-range: U+1F1E6-1F1FF;' : '';
                if( file_exists( $f . $fp.'.eot' ) ) {
                    $fonts_exist[] = 'eot';
                    $fonts_css .= file_exists($f . $fp . '.eot') ? 'src:url(\'fonts/' . $fn . $fp . '.eot\'),' : '';
                    $fonts_css .= file_exists($f . $fp . '.eot') ? 'url(\'fonts/' . $fn . $fp . '.eot?#iefix\') format(\'embeded-opentype\')' : '';
                }
                if( file_exists( $f . $fp.'.woff2' ) ) {
                    $fonts_exist[] = 'woff2';
                    $fonts_css .= file_exists($f . $fp. '.woff2') ? ',url(\'fonts/'.$fn . $fp . '.woff2\') format(\'woff2\')' : '';
                }
                if( file_exists( $f . $fp.'.woff' ) ) {
                    $fonts_exist[] = 'woff';
                    $fonts_css .= file_exists($f . $fp. '.woff') ? ',url(\'fonts/'.$fn . $fp . '.woff\') format(\'woff\')' : '';
                }
                $css_post = 'font-weight:' . $w . ';font-style:normal;font-display: swap;}';
                if( !empty( $fonts_css ) ) {
                    echo $css_pre.$fonts_css.';'.$css_post;
                }
            }
        }
    }
}

foreach( glob( dirname( __DIR__ ) . '/apps/' . $app . '/assets/fonts/*', GLOB_ONLYDIR ) as $f ){

    $weights = [ 100 => 'Thin', 200 => 'ExtraLight', 300 => 'Light', 400 => 'Regular', 500 => 'Medium', 600 => 'SemiBold', 700 => 'Bold', 800 => 'ExtraBold', 900 => 'Black' ];

    $fe = explode( '/', $f );
    $fn = $fe[ count( $fe ) - 1 ];

    global $fonts;
    foreach( $fonts as $font ){
        if( strtolower( $fn ) == strtolower( $font[0] ) ){
            $ws = !empty( $font[1] ) ? explode( ',', $font[1] ) : [ 400 ];
            foreach( $ws as $w ){
                $weight = is_numeric( $w ) ? $weights[ $w ] : $w;
                $fp = '/' . $fn . '-' . $weight;
                echo '@font-face{font-family:\'' . $fn . '\';';
                if( file_exists( $f . $fp.'.eot' ) ) {
                    echo file_exists($f . $fp . '.eot') ? 'src:url(\'../apps/'.$app.'/assets/fonts/' . $fn . $fp . '.eot\');src:' : 'src:';
                    echo file_exists($f . $fp . '.eot') ? 'url(\'../apps/'.$app.'/assets/fonts/' . $fn . $fp . '.eot?#iefix\') format(\'embeded-opentype\')' : '';
                }
                if( file_exists( $f . $fp.'.woff2' ) ) {
                    echo file_exists($f . $fp. '.woff2') ? ',url(\'../apps/'.$app.'/assets/fonts/'.$fn . $fp . '.woff2\') format(\'woff2\')' : '';
                }
                if( file_exists( $f . $fp.'.woff' ) ) {
                    echo file_exists($f . $fp. '.woff') ? ',url(\'../apps/'.$app.'/assets/fonts/'.$fn . $fp . '.woff\') format(\'woff\')' : '';
                }
                echo ';font-weight:' . $w . ';';
                echo 'font-style:normal;font-display: swap;';
                echo '}';
            }
        }
    }
}