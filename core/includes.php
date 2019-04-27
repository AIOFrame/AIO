<?php

if( !defined( 'COREPATH' ) ) { exit(); }

function includes() {

    // Includes First set of Internal files to Include

    $first_internal_includes = [ 'functions', 'crypt', 'options', 'payments', 'structure', 'alerts', 'arrays', 'data', 'elements', 'files', 'icons', 'language', 'modules', 'options', 'spreadsheet', 'user', 'backup', 'log' ];

    foreach( $first_internal_includes as $first_internal_file ) {
        include_once( dirname( __FILE__ ) . '/includes/'. $first_internal_file . '.php' );
    }

    $app_functions_dirs = [ 'functions' ];
    foreach( $app_functions_dirs as $dir ) {
        foreach( glob( APPPATH . $dir . '/*.php') as $app_file) {
            include_once( $app_file );
        }
    }

    $second_internal_includes = [ 'ajax' ];

    foreach( $second_internal_includes as $second_internal_file ) {
        include_once( dirname( __FILE__ ) . '/includes/'. $second_internal_file  . '.php' );
    }

}

includes();
