<?php

$c = APPPATH . 'config.php';

if( file_exists( $c ) ) {
    $data = include( $c );
    $data = !empty( $data ) && is_array( $data ) && isset( $data['data'] ) ? $data['data'] : [];
}
if( isset( $data ) && !empty( $data ) && isset( $data['type'] ) && isset( $data['server'] ) && isset( $data['base'] ) && isset( $data['user'] ) && isset( $data['pass'] ) ) {

    global $db;

    switch( $data['type'] ) {
        case 'mysql':
            $db = @mysqli_connect( $data['server'], $data['user'], $data['pass'], $data['base'] );
            if ( $db ) {
                mysqli_query( $db, "SET NAMES 'utf8'");
                mysqli_query( $db, 'SET CHARACTER SET utf8' );
                !defined( 'APPCON' ) ? define( 'APPCON', true ) : '';
            } else {
                die( mysqli_connect_error() );
            }
        break;
    }

}