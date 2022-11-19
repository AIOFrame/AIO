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

/* Check if AJAX is from same origin
if (array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $origin = $_SERVER['HTTP_ORIGIN'];
} else if (array_key_exists('HTTP_REFERER', $_SERVER)) {
    $origin = $_SERVER['HTTP_REFERER'];
} else {
    $origin = $_SERVER['REMOTE_ADDR'];
}
$url = !empty( $origin ) && $origin . '/' == APPURL; */

// Action is set
$cry = Encrypt::initiate();
$action = isset( $_POST['action'] ) && !empty( $_POST['action'] ) ? $cry->decrypt( $_POST['action'] ) : '';
unset( $_POST['action'] );
$action_is_ajax = !empty( $action ) && str_contains($action, '_ajax');

if( $action_is_ajax ) {
    if( !$ajax ) {
        elog('AJAX request is not Asynchronous!');
        ef('AJAX request is not Asynchronous!');
        exit();
    }
    /* if( !$url ) {
        elog('AJAX request is not from authorized domain '.$origin );
        ef('AJAX request is not from authorized domain '.$origin, 0);
        exit();
    } */
    if( function_exists( $action ) ){
        // If isset hidden array
        if( isset( $_POST['h'] ) ) {
            $e = Encrypt::initiate();
            $hidden = $e->decrypt_array( $_POST['h'] );
            if( !empty( $hidden ) ) {
                foreach( $hidden as $hk => $hv ) {
                    $_POST[$hk] = $hv;
                }
            }
            unset( $_POST['h'] );
        }
        $action( $_POST );
    } else {
        elog('AJAX function not found!');
        ef('Asynchronous function not found');
    }
    die();
}