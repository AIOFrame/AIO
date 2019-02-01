<?php

if( !defined( 'COREPATH' ) ) { exit(); }

foreach( glob( dirname( __FILE__ ).'/includes/*.php' ) as $file ) {
    if( $file !== dirname( __FILE__ ) . '/includes/exit.php' ){
        include_once( $file );
    }
}