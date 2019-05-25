<?php

if( !defined( 'COREPATH' ) ) { exit(); }

function includes() {

    // Includes First set of Internal files to Include

    $first_internal_includes = [ 'functions', 'crypt', 'options', 'payments', 'structure', 'alerts', 'arrays', 'data', 'elements', 'files', 'icons', 'language', 'modules', 'options', 'spreadsheet', 'user', 'backup', 'log' ];

    foreach( $first_internal_includes as $first_internal_file ) {
        include_once( dirname( __FILE__ ) . '/includes/'. $first_internal_file . '.php' );
    }

    // Get functions directory and add to array
    $c = get_config();
    $app_functions_dirs = isset( $c['function_dirs'] ) ? $c['function_dirs'] : [ 'functions' ];

    // Get in config and include pages if user logged in
    if( isset( $c['in'] ) && !empty( $c['in'] ) && user_logged_in() ) {
        if( is_assoc( $c['in'] ) ) {

        } else if( is_array( $c['in'] ) ) {
            foreach( $c['in'] as $file ){
                foreach( $app_functions_dirs as $dir ) {
                    if( file_exists( APPPATH . $dir . '/' . $file . '.php') ) {
                        include_once( APPPATH . $dir . '/' . $file . '.php' );
                    }
                }
            }
        } else {
            include_once( APPPATH . $c['in'] );
        }
    }

    // Get out config and include pages if user not logged in
    if( isset( $c['out'] ) && !empty( $c['out'] ) && user_logged_in() ) {
        if( is_assoc( $c['out'] ) ) {

        } else if( is_array( $c['out'] ) ) {
            foreach( $c['out'] as $file ){
                foreach( $app_functions_dirs as $dir ) {
                    if( file_exists( APPPATH . $dir . '/' . $file . '.php') ) {
                        include_once( APPPATH . $dir . '/' . $file . '.php' );
                    }
                }
            }
        } else {
            include_once( APPPATH . $c['out'] );
        }
    }

    // Auto Include Functions if in and out config are empty
    if( !isset( $c['in'] ) && !isset( $c['out'] ) ) {
        foreach( $app_functions_dirs as $dir ) {
            foreach( glob( APPPATH . $dir . '/*.php') as $app_file) {
                include_once( $app_file );
            }
        }
    }

    $second_internal_includes = [ 'ajax' ];

    foreach( $second_internal_includes as $second_internal_file ) {
        include_once( dirname( __FILE__ ) . '/includes/'. $second_internal_file  . '.php' );
    }

}

includes();
