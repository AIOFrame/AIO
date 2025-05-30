<?php

if( !defined( 'ROOTPATH' ) ) { exit(); }

//$db = defined('DB_TYPE') ? DB_TYPE : '';

$base_load = [
    'log',                              // Logs issues to error log file
    'assets',                           // Lets you easily add styles and scripts related assets
    'encrypt',                          // Most powerful encryption functions
    'device',                           // Client device info functions
    'form',                             // Input element rendering functions
    'arrays',                           // Advanced array functions
    //'icons',                            // Individual icon renderer - Will be deprecated or improved // Deprecated
    'code',                             // Provides easy to use html snippets
    'dependencies',                     // Improves or Includes most of the third-party dependencies
    'curl',                             // CURL Functions
    'translate',                        // Translation functions
];

foreach( $base_load as $bl ) {
    include_once( dirname( __FILE__ ) . '/includes/'. $bl . '.php' );
}

//$e = Encrypt::initiate();
//$action = isset( $_POST['action'] ) && !empty( $_POST['action'] ) ? $e->decrypt( $_POST['action'] ) : '';
//$_POST['action'] = $action;

//if( !empty( $action ) && str_contains( $action, '_base_ajax' ) ) {
//    include_once( dirname( __FILE__ ) . '/includes/ajax.php' );
//}

if( defined( 'DB_TYPE' ) ) {
    include_once( dirname( __FILE__ ) . '/includes/connect/queries.php' );
    if( !empty( $action ) && str_contains( $action, '_base_ajax' ) ) {
        include_once( dirname( __FILE__ ) . '/includes/ajax.php' );
    }
    include_once( dirname( __FILE__ ) . '/includes/structure.php' );
//    array_push(
//        $initials,
//        'connect/queries',              // Database query functions
//        //'access',                       // User access functions like login, register
//        'structure',                    // Basic database structure tables
//        //'alerts',                       // Notifications to users
//        //'data',                         // Common used data - Will be deprecated or improved
//        //'files',                         // File uploader functions
//        //'translation/functions',        // Translation functions
//        //'components',                   // AIO Components
//        //'backup',                       // Database backup / restore functions
//        //'options'                       // Pre Coded Options
//    );
}

if( defined( 'CONFIG' ) ) {
    // Get functions directory and add to array
    $c = get_config();
    $app_functions_dirs = $c['function_dirs'] ?? ['functions'];
    $features = $c['features'] ?? [];
    $path = ROOTPATH . 'core/includes/';
    $m_path = ROOTPATH . 'core/modules/';

    // Common used data - Will be deprecated or improved
    //if( in_array( 'data', $features ) || in_array( 'portal', $features ) ) {
    if( in_array_any( [ 'data', 'portal' ], $features ) ) {
        include_once( dirname( __FILE__ ) . '/includes/data.php' );
    }

    if( defined( 'DB_TYPE' ) ) {

        // Create User tables if featured
        //if( in_array( 'users', $features ) || in_array( 'auth', $features ) || in_array( 'authentication', $features ) ) {
        if( in_array_any( [ 'users', 'auth', 'authentication' ], $features ) ) {
            include_once( $path . 'access.php' );
            include_once( $path . 'alerts.php' );
            include_once( $path . 'options.php' );
            include_once( $path . 'perms.php' );

            // Mobile REST Features
            if( in_array( 'mobile', $features ) ) {
                include_once( $path . 'mobile.php' );
            }
        }

        // Option Features
        if( in_array( 'options', $features ) ) {
            include_once( $path . 'options.php' );
        }

        // Content Management System Features
        //if( in_array( 'cms', $features ) || in_array( 'pages', $features ) ) {
        if( in_array_any( [ 'cms', 'pages' ], $features ) ) {
            include_once( $path . 'cms.php' );
        }

        // Chat Features
        if( in_array( 'chat', $features ) ) {
            include_once( $path . 'chat.php' );
        }

        // Alerts System
        //if( in_array( 'alert', $features ) || in_array( 'alerts', $features ) || in_array( 'notifications', $features ) ) {
        if( in_array_any( [ 'alert', 'alerts', 'notifications' ], $features ) ) {
            include_once( $path . 'alerts.php' );
        }

        // Translation functions
        //if( in_array( 'translations', $features ) || in_array( 'languages', $features ) || in_array( 'lang', $features ) ) {
        if( in_array_any( [ 'translations', 'translation', 'translate', 'languages', 'language', 'lang', 'i18n' ], $features ) ) {
            include_once( $path . 'i18n.php' );
        }

        // Portal functions
        if( in_array_any( [ 'portal', 'admin', 'backend' ], $features ) ) {
            include_once( $m_path . 'icons.php' );
            include_once( $path . 'portal.php' );
        }

        // Create File uploader tables if featured
        //if( in_array( 'storage', $features ) || in_array( 'uploads', $features ) || in_array( 'upload', $features ) ) {
        if( in_array_any( [ 'storage', 'uploads', 'upload' ], $features ) ) {
            include_once( $path . 'files.php' );
        }

        // Regional Limitations Feature
        //if( in_array( 'region', $features ) || in_array( 'regions', $features ) ) {
        if( in_array_any( [ 'region', 'regions' ], $features ) ) {
            include_once( $path . 'region.php' );
        }

        // Customer Relationship Manager
        if( in_array( 'crm', $features ) ) {
            include_once( $path . 'crm.php' );
        }

        // Employee Management System
        if( in_array( 'ems', $features ) ) {
            include_once( $path . 'ems.php' );
        }

        // Accounting System
        //if( in_array( 'accounts', $features ) || in_array( 'accounting', $features ) ) {
        if( in_array_any( [ 'accounts', 'accounting' ], $features ) ) {
            include_once( $path . 'accounts.php' );
        }

        // Project Management System
        if( in_array_any( [ 'pms', 'pm', 'projects', 'project' ], $features ) ) {
            include_once( $path . 'projects.php' );
        }

        // E-Commerce System
        //if( in_array( 'ecom', $features ) || in_array( 'commerce', $features ) || in_array( 'ecommerce', $features ) || in_array( 'store', $features ) ) {
        if( in_array_any( [ 'ecom', 'commerce', 'ecommerce', 'store' ], $features ) ) {
            include_once( $path . 'ecommerce.php' );
        }

        // Backup Functions
        //if( in_array( 'backup', $features ) ) {
            include_once( $path . 'backup.php' );
        //}

        if( in_array_any( [ 'ecom', 'commerce', 'ecommerce', 'store', 'pms', 'pm', 'projects', 'project', 'accounts', 'accounting', 'ems', 'crm' ], $features ) ) {
            include_once( $path . 'clients.php' );
        }

    }

    // Load functions for signed-in user
    if ( isset($c['logged_in']) && !empty($c['logged_in']) && user_logged_in() ) {
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

    // Load functions for signed out user
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