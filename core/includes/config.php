<?php

/**
 * Gets config setting value
 * @param string $name App config setting name
 */
function get_config(string $name = '' ) {
    $v = '';
    if( defined('APPPATH') && file_exists( APPPATH . 'config.php' ) ) {
        $c = include( APPPATH . 'config.php' );
        if( is_array( $c ) && isset( $c[ $name ] ) ){
            $v = $c[ $name ];
        } else if ( is_array( $c ) && $name == '' ) {
            $v = $c;
        }
    }
    return $v;
}

/**
 * Stores config setting
 * @param string $name Config setting name
 * @param string $value Config setting value
 */
function set_config(string $name, string $value) {
    // TODO: Fix auto generated config issues
    $c = [];
    if( file_exists( APPPATH . 'config.php' ) ) {
        $c = include( APPPATH . 'config.php' );
    };
    $c[ $name ] = $value;
    $config_php = fopen( APPPATH . '/config.php', 'w' );
    $config_text = '<?php'.PHP_EOL.'return ['.PHP_EOL.'/* This file is auto generated! */'.PHP_EOL;
    foreach( $c as $k => $v ) {
        if( !is_array( $v ) ) {
            $config_text .= is_numeric( $v ) ? '    \'' . $k . '\' => ' . $v . ',' . PHP_EOL : '    \'' . $k . '\' => \'' . $v . '\',' . PHP_EOL;
        } else {
            $config_text .= '    \'' . $k . '\' => [ ';
            foreach( $v as $sk => $sv ) {
                if( is_array( $sv ) ) {
                    if( is_assoc( $sv ) ) {
                        $config_text .= '\'' . $sk . '\' => [ \'';
                        foreach( $sv as $tk => $tv ) {
                            $tv = is_array( $tv ) ? '[]' : $tv;
                            $config_text .= '\'' . $tk . '\' => \'' . $tv . '\', ';
                        }
                        $config_text .= '\' ],' . PHP_EOL;
                    } else {
                        $config_text .= '\'' . $sk . '\' => [ \'' . implode( '\', \'', $sv ) . '\' ],' . PHP_EOL;
                    }
                } else {
                    $config_text .= '\'' . $sk . '\' => \'' . $sv . '\', ';
                }
            }
            $config_text .= ' ],' . PHP_EOL;
        }
    }
    $config_text .= '];';
    fwrite( $config_php, $config_text );
}