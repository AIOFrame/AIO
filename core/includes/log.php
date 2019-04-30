<?php

function log_viewer() {



}

function clear_log_viewer() {

    $file = isset( $_POST['file'] ) ? $_POST['file'] : ini_get('error_log');

    if( $file = fopen( $file, 'w' ) ) {

        $clear = fwrite( $file, '' );

        if( $clear == '' ) {

            echo json_encode([1, 'Log Cleared!']);

        } else {

            echo json_encode([0, 'Log could not be cleared']);

        }

        fclose( $file );

    }

}