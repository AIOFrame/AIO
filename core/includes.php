<?php

if( !defined( 'COREPATH' ) ) { exit(); }

function includes() {

    $includes_order = [ 'functions', 'crypt', 'options', 'structure', 'alerts', 'arrays', 'data', 'elements', 'files', 'icons', 'language', 'modules', 'options', 'ajax', 'spreadsheet' ];

    foreach( $includes_order as $file ) {
        include_once( dirname( __FILE__ ) . '/includes/'. $file . '.php' );
    }

}

includes();
