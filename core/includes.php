<?php

if( !defined( 'COREPATH' ) ) { exit(); }

$db = defined('DB_TYPE') ? DB_TYPE : '';

$initials = [
    'assets',                           // Lets you easily add styles and scripts related assets
    'functions',                        // Most commonly used functions - Will be deprecated soon into individual identities
    'crypt',                            // Most powerful encryption functions
    'device',                           // Client device info functions
    'form',                             // Input element rendering functions
    'arrays',                           // Advanced array functions
    'icons',                            // Individual icon renderer - Will be deprecated or improved
    'components',                       // Improves or Includes most of the third-party components
    'translation/strings',              // Translation functions
];

if( defined( 'CONFIG' ) ) {
    array_push(
        $initials,
        'config'                        // Config functions
    );
}

if( defined( 'DB_TYPE' ) ) {
    array_push(
        $initials,
        'connect/'.DB_TYPE,             // Database connection functions
        'structure',                    // Basic database structure tables
        'alerts',                       // Notifications to users
        'data',                         // Common used data - Will be deprecated or improved
        'files',                        // File uploader functions
        'translation/functions',        // Translation functions
        'backup'                        // Database backup / restore functions
    );
}

foreach( $initials as $i ) {
    include_once( dirname( __FILE__ ) . '/includes/'. $i . '.php' );
}

if( defined( 'CONFIG' ) ) {
    // Get functions directory and add to array
    $c = get_config();
    $app_functions_dirs = isset($c['function_dirs']) ? $c['function_dirs'] : ['functions'];

    // Get in config and include pages if user logged in
    if (isset($c['logged_in']) && !empty($c['logged_in']) && user_logged_in()) {
        if (is_assoc($c['logged_in'])) {

        } else if (is_array($c['logged_in'])) {
            foreach ($c['logged_in'] as $file) {
                foreach ($app_functions_dirs as $dir) {
                    if (file_exists(APPPATH . $dir . '/' . $file . '.php')) {
                        include_once(APPPATH . $dir . '/' . $file . '.php');
                    }
                }
            }
        } else {
            include_once(APPPATH . $c['logged_in']);
        }
    }

    // Get out config and include pages if user not logged in
    if (isset($c['logged_out']) && !empty($c['logged_out']) && user_logged_in()) {
        if (is_assoc($c['logged_out'])) {

        } else if (is_array($c['logged_out'])) {
            foreach ($c['logged_out'] as $file) {
                foreach ($app_functions_dirs as $dir) {
                    if (file_exists(APPPATH . $dir . '/' . $file . '.php')) {
                        include_once(APPPATH . $dir . '/' . $file . '.php');
                    }
                }
            }
        } else {
            include_once(APPPATH . $c['logged_out']);
        }
    }

    // Auto Include Functions if in and out config are empty
    if (!isset($c['in']) && !isset($c['out'])) {
        foreach ($app_functions_dirs as $dir) {
            foreach (glob(APPPATH . $dir . '/*.php') as $app_file) {
                include_once($app_file);
            }
        }
    }
}

include_once( dirname( __FILE__ ) . '/includes/ajax.php' );
