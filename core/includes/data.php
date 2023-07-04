<?php

require_once( ROOTPATH . 'core/modules/world.php' );



function get_timezones(){
    return timezone_identifiers_list();
}





// Returns Languages

function get_languages( $lang_keys = [] ): array {
    $w = new WORLD();
    $lang = $w->languages();
    if( !empty( $lang_keys ) ) {
        $final = [];
        foreach( $lang_keys as $l ) {
            $final[$l] = $lang[$l];
        }
        return $final;
    } else {
        return $lang;
    }
}

function get_countries( string $key = 'id', string $value = 'name flag' ): array {
    $w = new WORLD();
    return $w->countries( $key, $value );
}

function get_currencies( string $key = 'code', string $value = 'name' ): array {
    $w = new WORLD();
    return $w->currencies( $key, $value );
}

function get_calling_codes( string $key = 'calling_code', string $value = 'calling_code flag' ): array {
    $w = new WORLD();
    return $w->calling_codes( $key, $value );
}