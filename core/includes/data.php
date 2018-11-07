<?php

// THIS FILE PROVIDES COMMON DATA LIKE MONTHS, COUNTRIES, TIMEZONES AS ARRAY

// Returns Months

function months( $assoc = 1 ) {
    if( $assoc ){
        return [ '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' ];
    } else {
        return [ 'January' => 'January', 'February' => 'February', 'March' => 'March', 'April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December' ];
    }
}

// Returns Years Range by years or year count

function years( $from, $to, $desc = 1 ) {
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
    }
}

// Return years from given date

function years_from_date( $date ){
    $then = date('Ymd', strtotime( $date ));
    $diff = date('Ymd') - $then;
    return substr($diff, 0, -4);
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

// Returns Countries or Country

function get_countries( $country = "" ){
    $countries = include( COREPATH . 'core/components/data/countries.php');
    if ($country == '' || !isset($country)) {
        return $countries;
    } else {
        if( is_numeric( $country ) ){
            $ckeys = array_keys( $countries );
            if( isset( $countries[$ckeys[$country]] ) && !empty($countries[$ckeys[$country]]) )
                echo $countries[$ckeys[$country]];
        } else {
            $position =  array_key_exists($country, $countries) ;
            if ($position)
                return $countries[$country];
        }
    }
}

// Returns Currencies

function get_currencies( $type = 'codes' ){
    if( file_exists( COREPATH . 'core/components/data/currencies/' . $type . '.php' ) ){
        return include( COREPATH . 'core/components/data/currencies/' . $type . '.php' );
    }
}