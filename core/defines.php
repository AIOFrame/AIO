<?php

// Check if not main app and loads config
// Defines the name of the Application
/* if ( !defined( 'APPNAME' ) ) {
    $anq = "SELECT option_value FROM options WHERE option_name = 'app_name' AND option_scope = '0'";
    if( $an = @mysqli_fetch_assoc( mysqli_query( $conn, $anq ) ) ) {
        define( 'APPNAME', $an['option_value'] );
    } else {
        define( 'APPNAME', 'AutoApp' );
    }
} */

// Defines the loading of Application
!defined( 'UINAME' ) ? define( 'UINAME', !empty( sub_domain() ) ? sub_domain() : get_domain() ) : '';

// Defines the Secret Key for Compression
// !defined( 'EKEY' ) ? define( 'EKEY', 'TheAutoAppIsBest' ) : '';

// Defines the Root Path of the Application
!defined( 'UIPATH' ) ? define( 'UIPATH', "http://$_SERVER[HTTP_HOST]".'/apps/'.UINAME.'/' ) : '';