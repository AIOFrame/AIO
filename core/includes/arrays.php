<?php

// Skeleton for Arrays and Objects

function skel( $s ){
    if( !empty( $s ) ){
        echo '<pre style="font-size:15px">';
        print_r( $s );
        echo '</pre>';
    } else {
        echo 'Its Empty';
    }
}

// Skeleton for Arrays and Objects In Error Log

function skell( $s ){
    if( !empty( $s ) ){
        error_log(print_r($s, true));
    } else {
        error_log( 'Its Empty');
    }
}

// Checks if array is associated array

function is_assoc( $a ) {
    return is_array( $a ) ? array_keys( $a ) !== range(0, count( $a ) - 1) : false;
}

// Restructures an array grouped by a common of key value

function array_group_by( $array, $key ) {
    if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
        trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
        return null;
    }
    $func = (!is_string($key) && is_callable($key) ? $key : null);
    $_key = $key;

    $grouped = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach ($array as $value) {
            $key = null;

            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && isset($value->{$_key})) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            }

            if ($key === null) {
                continue;
            }

            $grouped[$key][] = $value;
        }
    }
    if (func_num_args() > 2) {
        $args = func_get_args();

        foreach ($grouped as $key => $value) {
            $params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $params);
        }
    }
    return $grouped;
}

// Create multiple where statement from Array

function create_where_from_array( $where, $array, $type = 'OR' ) {
    $s = '';
    if( !empty( $where ) && !empty( $array ) && is_array( $array ) ) {
        foreach( $array as $a => $k ) {
            $s .= $where.' = "'.$k.'" '.$type.' ';
        }
    }
    return rtrim( $s, ' OR ');
}

// Check if string is json

function is_json( $string ) {
    json_decode( $string );
    return ( json_last_error() == JSON_ERROR_NONE );
}

// Restructures an array with key value as key

function array_by_key( $array, $key ){
    $new_array = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $a ){
            $new_array[ $a[ $key ] ] = $a;
        }
    }
    return $new_array;
}

// TODO: Returns Multi array of only two sub arrays values into associative key and value

function array_into_assoc( $array = [] ) {

    if( !empty( $array ) ){

        $data = [];

        foreach( $array as $a ){

            if( count( $a ) == 2 && is_array( $a ) ){

                $data[$a[0]] = [];

                foreach( $a as $k => $v ){

                    $sub_data[$v] = '';

                }

            }

        }

    }

}

// Prepares an array of only keys from a given array or post

function prepare_keys( $array = '', $pre = '', $remove_empty = 1 ) {
    $keys = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ){
            if( $remove_empty ){
                if($v !== '' ){
                    $keys[] = $pre.$k;
                }
            } else {
                $keys[] = $pre.$k;
            }
        }
    }
    return $keys;
}

// Prepares an array of only values from a given array or post

function prepare_values( $array = '', $pre = '', $remove_empty = 1 ) {
    $values = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ){
            if( $remove_empty ){
                if( $v !== '' ){
                    $values[] = $pre.$v;
                }
            } else {
                $values[] = $pre.$v;
            }
        }
    }
    return $values;
}

// Prepares an array of values of particular key from a given array or post

function values_by_key( $array = [], $key = '' ){
    $values = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $a ){
            if( $a[$key] !== '' ){
                $values[] = $a[$key];
            }
        }
    }
    return $values;
}

// Finds and Replaces in Array Keys
// TODO: Add string that should be replaced with

function replace_in_keys( $array = [], $trim = '', $json = false ){
    $data = $vd = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ) {
            if( !is_array( $v ) ) {
                $f = is_array($trim) && isset($trim[0]) ? $trim[0] : $trim;
                $r = is_array($trim) && isset($trim[1]) ? $trim[1] : '';
                $data[str_replace($f, $r, $k)] = $v;
            } else {
                foreach( $v as $a => $b ){
                    $f = is_array($trim) && isset($trim[0]) ? $trim[0] : $trim;
                    $r = is_array($trim) && isset($trim[1]) ? $trim[1] : '';
                    $vd[str_replace($f, $r, $a)] = $b;
                }
                $data[$k] = $vd;
            }
        }
    }
    return $json ? json_encode( $data ) : $data;
}

// Attach pre string to Array Keys

function pre_keys( $array = [], $trim = '', $json = false ){
    $data = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ) {
            $data[ $trim . $k ] = $v;
        }
    }
    return $json ? json_encode( $data ) : $data;
}

// Returns random of Array

function random_of_array( $array = [] ){
    return $array[ rand( 0, count( $array ) - 1 ) ];
}

//

function array_sub_values( $arrays = [] ) {
    $data = [];
    if( is_array( $arrays ) && !empty( $arrays ) ) {
        foreach ($arrays as $array) {
            foreach ($array as $k => $v) {
                $data[] = $v;
            }
        }
    }
    return $data;
}

function array_to_query( $array = [], $column = '', $query = 'OR' ) {
    $q = '';
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $c ){
            $q .= ' '.$column.' = "'.$c.'" '.$query.' ';
        }
    }
    return !empty( $q ) ? substr($q, 0, -3) : '';
}

function restructure_arrays( $array = [], $structure = [] ){

    if( !empty( $array ) && !empty( $structure ) ){
        $new = [];
        foreach( $array as $as ){
            if( is_array( $as ) ) {
                $new_sub = [];
                $x = 0;
                foreach ( $structure as $new_key ){
                    $new_sub[$new_key] = isset( $as[$new_key] ) ? $as[$new_key] : '';
                    $x++;
                }
                $new[] = $new_sub;
            }
        }
        return $new;
    }

}

function ordinal( $number ) {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}