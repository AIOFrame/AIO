<?php

if( isset( $_POST['action'] ) && !empty( $_POST['action'] ) ){
    if( function_exists( $_POST['action'] ) ){
        $process_ajax = $_POST['action'];
        $process_ajax( $_POST );
    } else {
        echo json_encode([0,'Asynchronous function not found']);
    }
    die();
}

function es( $string ) {
    echo json_encode( [ 1, T( $string ) ] );
}

function ef( $string ) {
    echo json_encode( [ 0, T( $string ) ] );
}