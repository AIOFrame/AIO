<?php

if( !empty( $_SESSION['lang'] ) ){
    global $langs;
    if( file_exists( APPPATH . 'languages/en.php' ) ){
        $langs[] = include( APPPATH . 'languages/en.php' );
    }
    if( file_exists( APPPATH . 'languages/' . $_SESSION['lang'] . '.php' ) ){
        $langs[] = include( APPPATH . 'languages/' . $_SESSION['lang'] . '.php' );
    }
    $langs = array_combine( $langs[0], $langs[1] );
}


function __( $string ) {
    global $langs;
    echo isset( $langs[$string] ) ? $langs[$string] : $string;
}

function _t( $string ) {
    global $langs;
    echo isset( $langs[$string] ) ? $langs[$string] : $string;
}

// Language Translations

function get_translations() {
    if( !empty( $_POST['languages'] ) && is_array( $_POST['languages'] ) ){
        foreach( $_POST['languages'] as $ln ){
            if( file_exists( APPPATH . 'languages/' . $ln . '.php' ) ){
                $langs[] = include( APPPATH . 'languages/' . $ln . '.php' );
            }
        }
        if( !empty( $langs ) ){
            if( !empty( $_POST['method'] ) && $_POST['method'] == 'json' ){
                echo json_encode( $langs );
            } else {
                return include( $langs );
            }
        }
    }
    if( !empty( $_POST['lang'] ) ){
        $ln = isset( $_POST['lang'] ) && !empty( $_POST['lang'] ) ? $_POST['lang'] : 'en';
        if( file_exists( APPPATH . 'languages/' . $ln . '.php' ) ){
            if( !empty( $_POST['method'] ) && $_POST['method'] == 'json' ){
                echo json_encode( include( APPPATH . 'languages/' . $ln . '.php' ) );
            } else {
                return include( APPPATH . 'languages/' . $ln . '.php' );
            }
        } else {
            echo 0;
        }
    }
}

function set_language( $language = '' ) {
    unset($_POST['action']);
    $language = !empty( $language ) ? $language : !empty( $_POST['lang'] ) ? $_POST['lang'] : 'en';
    //elog($language);
    if( !empty( $language ) ){
        $_SESSION['lang'] = $language;
    }
}