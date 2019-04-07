<?php

// Stores untranslated strings to database

if( isset( $_SESSION['lang'] ) && $_SESSION['lang'] !== 'en' && APPDEBUG ){

    global $translation_strings;

    //skel( $translation_strings );

    $db_translation_strings = get_option('translation_strings');

    $euw = !empty( $db_translation_strings ) ? unserialize( $db_translation_strings ) : [];

    $euw = is_array( $euw ) && is_array( $translation_strings ) ? array_unique( array_merge( $euw, $translation_strings ) ) : array_unique( $euw );

    update_option( 'translation_strings', serialize( array_filter( $euw ) ) );

}