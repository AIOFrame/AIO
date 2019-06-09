<?php

if( file_exists( APPPATH . 'config.php' ) ) {
    $data = include(APPPATH . 'config.php');
    $data = !empty( $data ) && is_array( $data ) && isset( $data['data'] ) ? $data['data'] : [];
}
if( isset( $data ) && !empty( $data ) && isset( $data['type'] ) && isset( $data['server'] ) && isset( $data['base'] ) && isset( $data['user'] ) && isset( $data['pass'] ) ) {

    global $db;

    if( $data['type'] == 'mysql' ) {

        $db = @mysqli_connect( $data['server'], $data['user'], $data['pass'], $data['base'] );
        if ( $db ) {
            mysqli_query( $db, "SET NAMES 'utf8'");
            mysqli_query( $db, 'SET CHARACTER SET utf8' );
            !defined( 'APPCON' ) ? define( 'APPCON', true ) : '';
        } else {
            die( mysqli_connect_error() );
        }

    }

}