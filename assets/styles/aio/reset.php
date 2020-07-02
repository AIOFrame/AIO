<?php

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=14400");

if( file_exists( __DIR__ . '/reset.min.css' ) ){
    $reset = file_get_contents( __DIR__ . '/reset.min.css' );
} else {
    $reset = file_get_contents( __DIR__ . '/reset.css' );
}

if( isset( $_GET['font'] ) && !empty( $_GET['font'] ) ){
    $fs = explode(',',str_replace('$',' ',$_GET['font']));
    foreach($fs as $f) {
        $font .= '"'.$f.'",';
    }
    $reset = str_replace( 'font-family: sans-serif', 'font-family:'.$font.'sans-serif', $reset );
    $reset = str_replace( 'font-family:sans-serif', 'font-family:'.$font.'sans-serif', $reset );
}

echo $reset;