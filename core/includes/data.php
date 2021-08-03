<?php

require_once( ROOTPATH . 'core/modules/world.php' );

class RANGE {

    /**
     * Returns array of 12 Months
     * @param bool $assoc Whether to return indexed months or associative months
     * @return array
     */
    function months( bool $assoc = true ): array {
        return $assoc ? [ '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' ] : [ 'January' => 'January', 'February' => 'February', 'March' => 'March', 'April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December' ];
    }

    /**
     * Returns array of range of years
     * @param int $from From year
     * @param int $to To year
     * @param bool $desc Reverses the order of years
     * @return array
     */
    function years( int $from, int $to, bool $desc = true ): array {
        if( is_numeric( $from ) && is_numeric( $to )) {
            $years = [];
            if( strlen( $from ) > 3 && strlen( $to ) > 3 && $from < $to ) {
                if( $desc ) {
                    for ($x = $from; $x <= $to; $x++) {
                        $years[] = $x;
                    }
                } else {
                    for ($x = $to; $x >= $from; $x--) {
                        $years[] = $x;
                    }
                }
            } else {
                for( $x = date('Y', strtotime('-'.$from.' years')); $x <= date('Y', strtotime('+'.$to.' years')); $x++){
                    $years[] = $x;
                }
            }
            return $years;
        } else {
            return [];
        }
    }

    /**
     * Years from given date till now
     * @param string $date Date to start counting years till now
     * @return false|string
     */
    function years_from_date( string $date ){
        $then = date('Ymd', strtotime( $date ));
        $diff = date('Ymd') - $then;
        return substr($diff, 0, -4);
    }

}

// Returns any given date or current date in format

function easy_date( $date = '', $format = 'd M, Y' ) {
    if( $date == '' ){
        $date = date('Y-m-d H:i:s');
    }
    $date = date_create( $date );
    return date_format( $date, $format );
}

// Return Timezones

function get_timezones(){
    return timezone_identifiers_list();
}



function __fn( $num, $decimals = 2, $locale = 'AE' ) {
    echo _fn( $num, $decimals, $locale );
}

function _fn( $num, $decimals = 2, $locale = 'AE' ) {
    $fmt = new NumberFormatter($locale = 'en_'.$locale, NumberFormatter::CURRENCY);
    $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
    return str_replace( 'AED', '', $fmt->format( $num ) );
}

function nth( $num ){
    $fmt = new NumberFormatter($locale = 'en', 6);
    echo $fmt->format( $num );
}

function _nth( $num ){
    $fmt = new NumberFormatter($locale = 'en', 6);
    return $fmt->format( $num );
}

function pre( $text ) {
    $text = str_replace( '<', '&lt;', $text );
    $text = str_replace( '>', '&gt;', $text );
    echo $text;
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

function get_countries( string $key = 'id', string $value = 'name' ): array {
    $w = new WORLD();
    return $w->countries( $key, $value );
}

function get_currencies( string $key = 'code', string $value = 'name' ): array {
    $w = new WORLD();
    return $w->currencies( $key, $value );
}

function get_calling_codes( string $key = 'calling_code', string $value = 'calling_code' ): array {
    $w = new WORLD();
    return $w->calling_codes( $key, $value );
}