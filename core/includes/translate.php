<?php

global $translated;
global $untranslated;
$translated = empty( $translated ) ? [] : $translated;
$untranslated = empty( $untranslated ) ? [] : $untranslated;
session_start();
if( !empty( $_SESSION['lang'] ) && $_SESSION['lang'] !== 'en' ) {
    $aio_translations_file = ROOTPATH . 'core/i18n/' . $_SESSION['lang'] . '.php';
    $aio_trans = file_exists( $aio_translations_file ) ? include $aio_translations_file : [];
    //skel( $aio_trans );
    $translated = array_merge( $aio_trans, $translated );
}
//skel( $translated );

/**
 * Echo the translation of string if present, or adds to untranslated
 * @param string $string
 * @return void
 */
function E( string $string ): void {
    echo T( $string );
}

/**
 * Returns the translation of string if present, or adds to untranslated
 * @param string $string String
 * @return string
 */
function T( string $string ): string {
    global $translated;
    global $untranslated;
    if( !array_key_exists( $string, $translated ) && !array_key_exists( $string, $untranslated ) && defined( 'PAGEPATH') ) {
        $untranslated[ $string ] = PAGEPATH;
    }
    return isset( $translated[$string] ) && $translated[$string] !== '' ? $translated[$string] : $string;
}