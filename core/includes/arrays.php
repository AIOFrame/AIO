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
    return array_keys( $a ) !== range(0, count( $a ) - 1);
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
    if (func_num_args() > 2) {
        $args = func_get_args();

        foreach ($grouped as $key => $value) {
            $params = array_merge([ $value ], array_slice($args, 2, func_num_args()));
            $grouped[$key] = call_user_func_array('array_group_by', $params);
        }
    }
    return $grouped;
}

// Restructures an array with key value as key

function array_by_key( $array, $key ){
    if( is_array( $array ) && !empty( $array ) ){
        $new_array = [];
        foreach( $array as $a ){
            $new_array[ $a[ $key ] ] = $a;
        }
        return $new_array;
    }
}

// Prepares an array of only keys from a given array or post

function prepare_keys( $array = '', $pre = '' ) {
    $keys = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    foreach( $array as $k => $v ){
        if( $v !== '' ){
            $keys[] = $pre.$k;
        }
    }
    return $keys;
}

// Prepares an array of only values from a given array or post

function prepare_values( $array = '' ) {
    $values = [];
    $array = is_array( $array ) ? $array : $_POST;
    unset( $array['action'] );
    foreach( $array as $k => $v ){
        if( $v !== '' ){
            $values[] = $v;
        }
    }
    return $values;
}

// Finds and Replaces in Array Keys

function replace_in_keys( $array = [], $trim = '', $json = false ){
    $data = [];
    foreach( $array as $k => $v ) {
        $f = is_array( $trim ) && isset( $trim[0] ) ? $trim[0] : $trim;
        $r = is_array( $trim ) && isset( $trim[1] ) ? $trim[1] : '';
        elog($f);
        elog($r);
        $data[ str_replace( $f, $r, $k) ] = $v;
    }
    return $json ? json_encode( $data ) : $data;
}

// Attached pre string to Array Keys

function pre_keys( $array = [], $trim = '', $json = false ){
    $data = [];
    foreach( $array as $k => $v ) {
        $data[ $trim . $k ] = $v;
    }
    return $json ? json_encode( $data ) : $data;
}