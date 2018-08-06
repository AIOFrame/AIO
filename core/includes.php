<?php

if( !defined( 'COREPATH' ) ) { exit(); }

foreach( glob( dirname( __FILE__ ).'/includes/*.php' ) as $filename ) {
    include_once( $filename );
}

?>