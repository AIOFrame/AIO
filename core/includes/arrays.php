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

function is_assoc( $a ): bool {
    //return !array_values($a) === $a;
    return is_array($a) && array_keys($a) !== range(0, count($a) - 1);
}

/**
 * Remove content in HTML Elements
 * @param string $html
 * @param string $start
 * @param string $end
 * @return string
 */
function remove_elements( string $html, string $start, string $end ): string {
    $exist = 1;
    do {
        $si = strpos( $html, $start );
        $ei = strpos( $html, $end )+strlen( $end );
        $html = str_replace( PHP_EOL, '', str_replace( substr( $html, $si, $ei - $si ), '', $html) );
        $exist = strpos( $html, $start ) > 0 ? 1 : 0;
    } while ( $exist > 0 );
    return $html;
}

/**
 * Gets content between a string
 * @param string $string
 * @param string $start
 * @param string $end
 * @return string
 */
function get_string_between( string $string, string $start, string $end ): string {
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

// Restructures an array grouped by a common of key value

/**
 * Groups a multi array by a common key value Ex: groups arrays by user_type
 * @param array $array Data array
 * @param string $key Key to group by Ex: user_type
 * @return array|null
 */
function array_group_by( array $array = [], string $key = '' ): ?array {
    if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key) ) {
        trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
        return null;
    }
    $func = (!is_string($key) && is_callable($key) ? $key : null);
    $_key = $key;

    $grouped = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach ($array as $k => $value) {
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

            $grouped[$key][$k] = $value;
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

function is_json( $string ): bool {
    json_decode( $string );
    return ( json_last_error() == JSON_ERROR_NONE );
}

/**
 * Restructures a multi array with sub key value as parent key
 * @param array $array Array that needs sub key as parent key
 * @param string $key Sub Key Ex: user_id
 * @return array
 */
function array_by_key( array $array = [], string $key = '' ): array {
    $new_array = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $a ){
            $new_array[ $a[ $key ] ] = $a;
        }
    }
    return $new_array;
}

/**
 * Converts multi array from database into simple assoc array with chosen key and value
 * @param array $array Data Array from database
 * @param string $key Key Ex: user_id
 * @param string|array $values Value Ex: user_name
 * @return array
 */
function array_to_assoc( array $array = [], string $key = '', string|array $values = '', string $separator = ' - ' ): array {
    $r = [];
    foreach( $array as $a ) {
        if( isset(  $a[ $key ] ) && !empty( $values ) ) {
            $values = is_array( $values ) ? $values : explode( ',', $values );
            $value = '';
            foreach( $values as $v ) {
                $value .= isset( $a[ $v ] ) ? $a[ $v ] . $separator : $v . $separator;
            }
            $r[ $a[ $key ] ] = rtrim( $value, $separator );
        }
    }
    return $r;
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

/**
 * Loops through multi arrays and replaces in keys
 * @param array $array Data array that needs key replaced
 * @param string $trim The text to be removed from key
 * @param string $replace The text to be replaced with removed text
 * @param bool $json Return JSON or String
 * @return string|array
 */
function replace_in_keys( array $array = [], string $trim = '', string $replace = '', bool $json = false ): string|array {
    $data = $vd = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ) {
            if( !is_array( $v ) ) {
                $f = is_array($trim) && isset($trim[0]) ? $trim[0] : $trim;
                $r = is_array($trim) && isset($trim[1]) ? $trim[1] : $replace;
                $data[str_replace($f, $r, $k)] = $v;
            } else {
                foreach( $v as $a => $b ){
                    $f = is_array($trim) && isset($trim[0]) ? $trim[0] : $trim;
                    $r = is_array($trim) && isset($trim[1]) ? $trim[1] : $replace;
                    $vd[str_replace($f, $r, $a)] = $b;
                }
                $data[$k] = $vd;
            }
        }
    }
    return $json ? json_encode( $data ) : $data;
}

/**
 * Append a string to array keys
 * @param array $array Data array that needs string appended to keys
 * @param string $pre String that will be appended to keys
 * @param bool $json Return JSON or String
 * @return string|array
 */
function pre_keys( array $array = [], string $pre = '', bool $json = false ): string|array {
    $data = [];
    if( is_array( $array ) && !empty( $array ) ){
        foreach( $array as $k => $v ) {
            $data[ $pre . $k ] = $v;
        }
    }
    return $json ? json_encode( $data ) : $data;
}

/**
 * Returns random value of Array
 * @param array $array
 * @return string
 */
function random_of_array( array $array = [] ): string {
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

/**
 * Turns an array into SQL query
 * @param array $array Array with data values Ex: [ 1, 2, 4, 7 ]
 * @param string $column String that loops for each value Ex: user_id
 * @param string $query Logic that appends between loop Ex: OR, AND
 * @param string $key Key from the array that is equal to column
 * @return string
 */
function array_to_query( array $array = [], string $column = '', string $query = 'OR', string $key = '' ): string {
    $q = '';
    if( !empty( $key ) ){
        foreach( $array as $c ){
            $q .= ' '.$column.' = "'.$c[ $key ].'" '.$query.' ';
        }
    } else if( is_array( $array ) && !empty( $array ) ){
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

/**
 * Returns Ordinal of a Number Ex: 2 will return 2nd
 * @param int $number The number that needs to be turned into Ordinal
 * @return string
 */
function ordinal( int $number ): string {
    $ends = array('th','st','nd','rd','th','th','th','th','th','th');
    if ((($number % 100) >= 11) && (($number%100) <= 13)) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }
}