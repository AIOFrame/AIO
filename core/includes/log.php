<?php

/**
 * AIO Error Log
 * @param string|array $log Log message
 * @param string $type Log type 'log', 'error'
 * @param string $line The link where the log is logged
 * @param string $file The file path which initiates the log
 * @param string $target Target
 * @author Shaikh <hey@shaikh.dev>
 */
function elog( string|array $log, string $type = 'log', string $line = '', string $file = '', string $target = '' ): void {

    $log = is_array( $log ) ? json_encode( $log ) : $log;
    $data = $log . '<AIO>' . $type;

    $df = debug_backtrace()[0];
    //error_log( json_encode( $df ) );

    $line = empty( $line ) && isset( $df['line'] ) ? $df['line'] : $line;
    $data .= !empty( $line ) ? '<AIO>' . $line : '';

    $file = empty( $file ) && isset( $df['file'] ) ? $df['file'] : $file;
    $data .= !empty( $file ) ? '<AIO>' . $file : '';

    $data .= !empty( $target ) ? '<AIO>' . $target : '';

    // Get dev users
    $debug = 0;
    if( defined( 'CONFIG' ) ) {
        $devs = get_config('dev');
        $devs = !empty($dev) ? explode(',', $devs) : [];
        $debug = isset($_SESSION['user_id']) && is_array($devs) && in_array($_SESSION['user_id'], $devs) ? 1 : 0;
    }

    // Log
    APPDEBUG || $debug ? error_log( $data . PHP_EOL ) : '';

    // OBSOLETE CODE

    //$log = is_object( $log ) ? var_dump( $log ) : $log;

}

function clear_log_ajax(): void {
    unlink( APPPATH . 'events.log' ) ? es('Successfully removed log file!') : ef('Failed to remove log file!');
}