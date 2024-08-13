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
    $o = new OPTIONS();
    $r = $o->current_region_prefix();
    global $options;
    $serving_regions = $options[ $r . 'serving_regions' ] ?? '';
    if( !empty( $serving_regions ) ) {
        $countries = $w->countries( $key, 'iso2' );
        $countries_two = $w->countries( $key, $value );
        $serving_regions = explode( ',', str_replace( ' ', '', $serving_regions ) );
        $final_countries = [];
        foreach( $countries as $k => $v ) {
            in_array( $k, $serving_regions ) ? $final_countries[ $k ] = ( $countries_two[ $v ] ?? '' ) : '';
        }
        return $final_countries;
    } else {
        return $w->countries( $key, $value );
    }
}

function get_all_countries( string $key = 'id', string $value = 'name flag' ): array {
    $w = new WORLD();
    return $w->countries( $key, $value );
}

function get_currencies( string $key = 'code', string $value = 'name' ): array {
    $w = new WORLD();
    return $w->currencies( $key, $value );
}

function get_all_calling_codes( string $key = 'calling_code', string $value = 'calling_code flag' ): array {
    $w = new WORLD();
    return $w->calling_codes( $key, $value );
}

function get_calling_codes( string $key = 'calling_code', string $value = 'calling_code flag' ): array {
    $w = new WORLD();
    $o = new OPTIONS();
    $r = $o->current_region_prefix();
    global $options;
    $serving_codes = $options[ $r . 'serving_phone_codes' ] ?? '';
    $codes = $w->calling_codes( $key, $value );
    if( !empty( $serving_codes ) ) {
        $serving_codes = explode( ',', str_replace( ' ', '', $serving_codes ) );
        $final_codes = [];
        foreach( $codes as $k => $v ) {
            in_array( $k, $serving_codes ) ? $final_codes[ $k ] = $v : '';
        }
        return $final_codes;
    } else {
        return $codes;
    }
}