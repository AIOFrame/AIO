<?php

if( !defined( 'COREPATH' ) ) { exit(); }

foreach( glob( dirname( __FILE__ ).'/includes/*.php' ) as $file ) {
    include_once( $file );
}