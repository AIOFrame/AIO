<?php

// Stores untranslated strings to database
$l = isset( $_SESSION['lang'] ) ? $_SESSION['lang'] : '';
if( defined( 'BASELANG' ) && $l !== BASELANG ){

    global $untranslated;

    if( is_array( $untranslated ) ) {

        foreach( $untranslated as $u => $p ){

            //skel( $ut );
            insert('translations', ['t_base', 't_page'], [ $u, $p ]);

        }

    }

    //skel( $translation_strings );

    /*$db_translation_strings = get_option('translation_strings');

    $euw = !empty( $db_translation_strings ) ? unserialize( $db_translation_strings ) : [];

    $euw = is_array( $euw ) && is_array( $translation_strings ) ? array_unique( array_merge( $euw, $translation_strings ) ) : array_unique( $euw );

    is_array( $euw ) && !empty( $euw ) ? update_option( 'translation_strings', serialize( array_filter( $euw ) ) ) : '';*/

}