<?php

if( !defined( 'ROOTPATH' ) ) { exit(); }

$db = defined('DB_TYPE') ? DB_TYPE : '';

$initials = [
    'log',                              // Logs issues to error log file
    'assets',                           // Lets you easily add styles and scripts related assets
    'encrypt',                          // Most powerful encryption functions
    'device',                           // Client device info functions
    'form',                             // Input element rendering functions
    'arrays',                           // Advanced array functions
    //'icons',                            // Individual icon renderer - Will be deprecated or improved // Deprecated
    'content',                          // Logs issues to error log file
    'dependencies',                     // Improves or Includes most of the third-party dependencies
    'curl',                             // CURL Functions
    'translation/strings',              // Translation functions
];

if( defined( 'DB_TYPE' ) ) {
    array_push(
        $initials,
        'connect/queries',              // Database query functions
        //'access',                       // User access functions like login, register
        'structure',                    // Basic database structure tables
        //'alerts',                       // Notifications to users
        //'data',                         // Common used data - Will be deprecated or improved
        //'files',                         // File uploader functions
        //'translation/functions',        // Translation functions
        'components',                   // AIO Components
        //'backup',                       // Database backup / restore functions
        //'options'                       // Pre Coded Options
    );
}

foreach( $initials as $i ) {
    include_once( dirname( __FILE__ ) . '/includes/'. $i . '.php' );
}

if( defined( 'CONFIG' ) ) {
    // Get functions directory and add to array
    $c = get_config();
    $app_functions_dirs = $c['function_dirs'] ?? ['functions'];
    $features = $c['features'] ?? [];
    $path = ROOTPATH . 'core/includes/';

    // Common used data - Will be deprecated or improved
    if( isset( $features['data'] ) ) {
        include_once( dirname( __FILE__ ) . '/includes/data.php' );
    }

    if( defined( 'DB_TYPE' ) ) {

        // Create User tables if featured
        if( in_array( 'users', $features ) || in_array( 'auth', $features ) || in_array( 'authentication', $features ) ) {
            include_once( $path . 'access.php' );
            include_once( $path . 'alerts.php' );
            include_once( $path . 'options.php' );
        }

        // Translation functions
        if( in_array( 'translations', $features ) || in_array( 'languages', $features ) ) {
            include_once( $path . 'translation/functions.php' );
        }

        // Portal functions
        if( in_array( 'portal', $features ) ) {
            include_once( $path . 'portal.php' );
        }

        // Create File uploader tables if featured
        if( in_array( 'storage', $features ) || in_array( 'uploads', $features ) ) {
            include_once( $path . 'files.php' );
        }

        // User Options
        if( in_array( 'options', $features ) ) {
            include_once( $path . 'options.php' );
        }

        // Backup Functions
        if( in_array( 'backup', $features ) ) {
            include_once( $path . 'backup.php' );
        }

    }

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

    // Auto load modules
    if( isset( $c['modules'] ) && is_array( $c['modules'] ) ) {
        foreach( $c['modules'] as $m ) {
            get_module( $m );
        }
    }
} else {
    foreach( glob( APPPATH . '/functions/*.php' ) as $fun ) {
        include_once( $fun );
    }
}

include_once( dirname( __FILE__ ) . '/includes/ajax.php' );
