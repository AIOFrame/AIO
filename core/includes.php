<?php

if( !defined( 'COREPATH' ) ) { exit(); }

function includes() {

    // Includes First set of Internal files to Include
    $first_internal_includes = [ 'config', 'functions', 'crypt' ];
    if( file_exists( APPPATH . 'config.php' ) ) {
        $data = include(APPPATH . 'config.php');
        $data = !empty( $data ) && is_array( $data ) && isset( $data['data'] ) ? $data['data'] : [];
    }
    $con = isset( $data ) && !empty( $data ) && isset( $data['type'] ) && isset( $data['server'] ) && isset( $data['base'] ) && isset( $data['user'] ) && isset( $data['pass'] );
    if( $con ) {
        $first_internal_includes[] = 'connect/'.$data['type'];
    }
    array_push( $first_internal_includes, 'assets', 'functions', 'device', 'crypt', 'payments', 'alerts', 'arrays', 'data', 'elements', 'files', 'icons', 'language', 'modules', 'spreadsheet', 'user', 'backup', 'log', 'recaptcha' );
    if( $con ) {
        $first_internal_includes[] = 'structure';
    }
    //print_r( $first_internal_includes );
    foreach( $first_internal_includes as $first_internal_file ) {
        include_once( dirname( __FILE__ ) . '/includes/'. $first_internal_file . '.php' );
    }

    // Get functions directory and add to array
    $c = get_config();
    $app_functions_dirs = isset( $c['function_dirs'] ) ? $c['function_dirs'] : [ 'functions' ];

    // Get in config and include pages if user logged in
    if( isset( $c['logged_in'] ) && !empty( $c['logged_in'] ) && user_logged_in() ) {
        if( is_assoc( $c['logged_in'] ) ) {

        } else if( is_array( $c['logged_in'] ) ) {
            foreach( $c['logged_in'] as $file ){
                foreach( $app_functions_dirs as $dir ) {
                    if( file_exists( APPPATH . $dir . '/' . $file . '.php') ) {
                        include_once( APPPATH . $dir . '/' . $file . '.php' );
                    }
                }
            }
        } else {
            include_once( APPPATH . $c['logged_in'] );
        }
    }

    // Get out config and include pages if user not logged in
    if( isset( $c['logged_out'] ) && !empty( $c['logged_out'] ) && user_logged_in() ) {
        if( is_assoc( $c['logged_out'] ) ) {

        } else if( is_array( $c['logged_out'] ) ) {
            foreach( $c['logged_out'] as $file ){
                foreach( $app_functions_dirs as $dir ) {
                    if( file_exists( APPPATH . $dir . '/' . $file . '.php') ) {
                        include_once( APPPATH . $dir . '/' . $file . '.php' );
                    }
                }
            }
        } else {
            include_once( APPPATH . $c['logged_out'] );
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
