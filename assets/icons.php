<?php

header("Content-type: text/css; charset: UTF-8");
header("Cache-Control: max-age=14400");

if( is_array( $_GET ) ) {
    foreach( $_GET as $a => $f ) {
        $app = !is_numeric( $a ) ? $a : '';
    }
}

//print_r( $_SESSION );

$path = str_replace( 'assets', '', glob( __DIR__ )[0] ) . 'apps/' . $app . '/assets/icons/';

$pre = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";
!defined( 'APPURL' ) ? define( 'APPURL', $pre.$_SERVER['HTTP_HOST']."/" ) : '';

foreach( glob( $path . '*.png' ) as $i ){

    $n = str_replace( $path, '', str_replace( '.png', '', $i ) );
    echo  '.'.$n.'{background:url("'.APPURL.'apps/'.$app.'/assets/icons/'.$n.'.png")}';

}
foreach( glob( $path . '*.svg' ) as $i ){

    $n = str_replace( $path, '', str_replace( '.svg', '', $i ) );
    echo  '.'.$n.'{background-image:url("'.APPURL.'apps/'.$app.'/assets/icons/'.$n.'.svg"); }';

}