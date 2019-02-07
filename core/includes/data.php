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

// Returns Countries or Country $key = 'code' || 'name' || 'symbol'

function countries( $value = 'name', $key = 'iso2', $country_code = '' ){

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

    $data = select( 'countries', $c1.','.$c2, $q );

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

// Returns States of a Country

function states( $country_code = 'AE' ) {

    if( isset( $_POST['id'] ) ){

        $country_code = $_POST['id'];

    }

    if( is_numeric( $country_code ) ){

        $id['id'] = $country_code;

    } else {

        $id = select( 'countries', 'id', 'iso2 = "'.$country_code.'"', 1 );

    }

    $q = isset( $id['id'] ) ? 'country_id = "'.$id['id'].'"' : '';

    if( $q != '' ) {

        $data = select( 'states', 'name', $q );

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

// Returns Cities of a State or Country

function cities( $state_name = '', $country_code = 'AE' ) {

    if( $state_name != '' ){

        $id = select( 'states', 'id', 'name = "'.$state_name.'"', 1 );

        $q = isset( $id['id'] ) ? 'state_id = "'.$id['id'].'"' : '';

    } else if( $country_code != '' ){

        $id = select( 'countries', 'id', 'iso2 = "'.$country_code.'"', 1 );

        $q = isset( $id['id'] ) ? 'country_id = "'.$id['id'].'"' : '';

    }

    if( $q != '' ) {

        $data = select( 'cities', 'name', $q );

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

// Returns Currency or Currencies. $key = 'code' || 'name' || 'symbol'

function currencies( $value = 'code', $key = 'currency', $country_code = false ){

    $q = $country_code && $country_code !== '' ? 'iso2 = "'.$country_code.'"' : '';

    if( $key == 'symbol' )
        $c1 = 'currency_symbol';
    else if( $key == 'name' )
        $c1 = 'currency_name';
    else
        $c1 = $key;

    if( $value == 'symbol' )
        $c2 = 'currency_symbol';
    else if( $value == 'code' )
        $c2 = 'currency';
    else
        $c2 = $value;

    $cdata = select( 'countries', $c1.','.$c2, $q );

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

// Returns Languages

function get_languages() {
    if( file_exists( COREPATH . 'core/components/data/' . 'languages' . '.php' ) ){
        return include( COREPATH . 'core/components/data/' . 'languages' . '.php' );
    }
}