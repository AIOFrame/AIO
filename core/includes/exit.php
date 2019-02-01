<?php

// Stores untranslated strings to database

global $untranslated_words;

if( isset( $_SESSION['lang'] ) && $_SESSION['lang'] !== 'en' ){

    $existing_untranslated_words = get_option('untranslated_' . $_SESSION['lang']);

    $euw = !empty( $existing_untranslated_words ) ? unserialize( $existing_untranslated_words ) : [];

    $euw = is_array( $euw ) && $untranslated_words ? array_unique( array_merge( $euw, $untranslated_words ) ) : array_unique( $euw );

    update_option('untranslated_' . $_SESSION['lang'], serialize( $euw ) );

}