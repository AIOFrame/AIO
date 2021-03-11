<?php

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

class WORLD_DATA {

    function __construct() {
        // TODO: Check if world data table is imported else import
    }

    /**
     * Returns array of countries
     * @param string $value Choose value 'id','name','iso2'-AE,'iso3'-UAE,'phonecode','capital','currency','symbol','currency_name'
     * @param string $key Choose key 'id','name','iso2'-AE,'iso3'-UAE,'phonecode','currency'
     * @param string $country_code
     * @return array
     */
    function countries( string $value = 'name', string $key = 'iso2', $country_code = '' ): array {

        $db = new DB();
        $q = $country_code && $country_code !== '' ? 'iso2 = "'.$country_code.'"' : '';

        if( $key == 'phone' || $key == 'phonecode' )
            $c1 = 'phonecode';
        else if( $key == 'iso3' )
            $c1 = 'iso3';
        else if( $key == 'currency' )
            $c1 = 'currency';
        else
            $c1 = $key;

        if( $value == 'iso2' )
            $c2 = 'iso2';
        else if( $value == 'iso3' )
            $c2 = 'iso3';
        else if( $value == 'phone' || $value == 'phonecode' )
            $c2 = 'phonecode';
        else if( $value == 'capital' )
            $c2 = 'capital';
        else if( $value == 'currency' )
            $c2 = 'currency';
        else if( $value == 'symbol' )
            $c2 = 'symbol';
        else if( $value == 'currency_name' )
            $c2 = 'currency_name';
        else
            $c2 = $value;

        $data = $db->select( 'countries', $c1.','.$c2, $q );
        if( !empty( $data ) && is_array( $data ) ){
            $countries = [];
            foreach( $data as $c ){
                $countries[ $c[$c1] ] = $c[$c2];
            }
            return $countries;
        } else {
            return [];
        }
    }

    /**
     * Returns states of a country
     * @param string $country_iso2 ISO2 code of country. Ex: AE
     * @return array
     */
    function states( string $country_iso2 = 'AE' ): array {
        $db = new DB();
        if( is_numeric( $country_iso2 ) ){
            $id['id'] = $country_iso2;
        } else {
            $id = $db->select( 'countries', 'id', 'iso2 = "'.$country_iso2.'"', 1 );
        }

        $q = isset( $id['id'] ) ? 'country_id = "'.$id['id'].'"' : '';
        if( $q !== '' ) {
            $data = $db->select( 'states', 'name', $q );
            if( is_array( $data ) ){
                $states = [];
                foreach( $data as $d ){
                    $states[] = $d['name'];
                }
                if( isset( $_POST['id'] ) ) { echo json_encode($states); } else { return $states; }
            } else {
                return [];
            }
        } else {
            return [];
        }

    }

    /**
     * Returns cities of a given state and country
     * @param string $state_name Name of the state
     * @param string $country_code ISO2 code of the country Ex: AE
     * @return array
     */
    function cities( string $state_name = '', string $country_code = 'AE' ): array {

        $db = new DB();
        if( $state_name != '' ){
            $id = $db->select( 'states', 'id', 'name = "'.$state_name.'"', 1 );
            $q = isset( $id['id'] ) ? 'state_id = "'.$id['id'].'"' : '';
        } else if( $country_code != '' ){
            $id = $db->select( 'countries', 'id', 'iso2 = "'.$country_code.'"', 1 );
            $q = isset( $id['id'] ) ? 'country_id = "'.$id['id'].'"' : '';
        }

        if( $q !== '' ) {
            $data = $db->select( 'cities', 'name', $q );
            if( is_array( $data ) ){
                $cities = [];
                foreach( $data as $d ){
                    $cities[] = $d['name'];
                }
                return $cities;
            } else {
                return [];
            }
        } else {
            return [];
        }

    }

    /**
     * Returns array of currencies
     * @param string $value Choose value 'id','name','symbol','code'
     * @param string $key Choose key 'id','name','symbol','code'
     * @param bool $country_code ISO2 code of country, Ex: AE if to get currency of UAE
     * @return array
     */
    function currencies( string $value = 'name', string $key = 'code', bool $country_code = false ): array {

        $db = new DB();
        $q = $country_code && $country_code !== '' ? 'iso2 = "'.$country_code.'"' : '';

        if( $key == 'symbol' )
            $c1 = 'currency_symbol';
        else if( $key == 'name' )
            $c1 = 'currency_name';
        else if( $key == 'code' )
            $c1 = 'currency';
        else
            $c1 = $key;

        if( $value == 'symbol' )
            $c2 = 'currency_symbol';
        else if( $value == 'code' )
            $c2 = 'currency';
        else if( $value == 'name' )
            $c2 = 'currency_name';
        else
            $c2 = $value;

        $cdata = $db->select( 'countries', $c1.','.$c2, $q );
        if( !empty( $cdata ) && is_array( $cdata ) ){
            $currencies = [];
            foreach( $cdata as $c ){
                $currencies[ $c[$c1] ] = $c[$c2];
            }
            return array_filter( $currencies );
        } else {
            return [];
        }

        //return $currencies;
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
    $file = COREPATH . 'core/components/data/languages.php';
    $lang = file_exists( $file ) ? include $file : [];
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