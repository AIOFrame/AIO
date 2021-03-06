<?php

// Stores untranslated strings to database
$l = isset( $_SESSION['lang'] ) ? $_SESSION['lang'] : 'en';
if( defined( 'BASELANG' ) && $l !== BASELANG ){

    global $untranslated;
    $db = new DB();

    if( is_array( $untranslated ) ) {

        foreach( $untranslated as $u => $p ){

            $t = $db->select( 'translations', 't_base', 't_base = "'.$u.'"' );
            if( !$t ) {
                $db->insert('translations', ['t_base', 't_page'], [$u, $p]);
            }

        }

    }

    //skel( $translation_strings );

    /*$db_translation_strings = get_option('translation_strings');

    $euw = !empty( $db_translation_strings ) ? unserialize( $db_translation_strings ) : [];

    $euw = is_array( $euw ) && is_array( $translation_strings ) ? array_unique( array_merge( $euw, $translation_strings ) ) : array_unique( $euw );

    is_array( $euw ) && !empty( $euw ) ? update_option( 'translation_strings', serialize( array_filter( $euw ) ) ) : '';*/

}