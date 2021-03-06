<?php

function E( $string ) {
    echo T( $string );
}

// Returns the translation of string if present, or adds to untranslated
function T( $string ) {

    //$lang = defined(BASELANG) ? BASELANG : 'en';
    global $translated;
    global $untranslated;
    $translated = empty( $translated ) ? [] : $translated;
    $untranslated = empty( $untranslated ) ? [] : $untranslated;

    if( !empty( $_SESSION['lang'] ) && defined( 'BASELANG' ) && $_SESSION['lang'] !== BASELANG ) {

        $aio_translations_file = ROOTPATH . 'core/translations/' . $_SESSION['lang'] . '.php';
        $aio_trans = file_exists( $aio_translations_file ) ? include $aio_translations_file : [];

        $translated = array_merge( $aio_trans, $translated );

        if( !array_key_exists( $string, $translated ) && !array_key_exists( $string, $untranslated ) ) {

            $df = debug_backtrace();
            $file = in_array(count($df), [3, 5, 7, 9, 11, 13]) ? $df[1]['file'] : $df[2]['file'];

            $file = !empty($file) && strpos( $file, 'pages/' ) > 0 ? str_replace(ROOTPATH, '', str_replace('apps/' . APPDIR . '/pages/', '', str_replace('.php', '', str_replace('/index', '', $file)))) : '';

            $untranslated[ $string ] = [ $file ]; // Add string and page to untranslated

        }

        return isset( $translated[$string] ) && $translated[$string] !== '' ? $translated[$string] : $string;

    } else {

        return $string;

    }
}

function __( $string ) {
    return T( $string );
}