<?php

// elog( json_encode( $_POST ) );
// elog( json_encode( $_SERVER ) );

if( session_status() === PHP_SESSION_NONE ) {
    session_start();
}

function es( string $string = '', bool $translate = true ): void {
    $string = $translate ? T( $string ) : $string;
    echo json_encode( [ 1, $string ] );
}

function ef( string $string = '', bool $translate = true ): void {
    $string = $translate ? T( $string ) : $string;
    echo json_encode( [ 0, $string ] );
}

// Check if request is AJAX
$ajax = !empty( $_SERVER['HTTP_X_REQUESTED_WITH'] ) && strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest';

// Check if AJAX is from same URL
$url = !empty( $_SERVER['HTTP_ORIGIN'] ) && $_SERVER['HTTP_ORIGIN'] . '/' == APPURL;

// Action is set
$cry = Encrypt::initiate();
$action = isset( $_POST['action'] ) && !empty( $_POST['action'] ) ? $cry->decrypt( $_POST['action'] ) : '';
unset( $_POST['action'] );
$action_is_ajax = !empty( $action ) && strpos( $action, '_ajax' ) !== false;

if( $action_is_ajax ) {
    if( !$ajax ) {
        elog('AJAX request is not Asynchronous!');
        ef('AJAX request is not Asynchronous!');
        exit();
    }
    if( !$url ) {
        elog('AJAX request is not from authorized domain!');
        ef('AJAX request is not from authorized domain!');
        exit();
    }
    if( function_exists( $action ) ){
        $action( $_POST );
    } else {
        elog('AJAX function not found!');
        ef('Asynchronous function not found');
    }
    die();
}