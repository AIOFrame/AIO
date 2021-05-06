<?php

include_once( ROOTPATH . 'core/components/agent.php' );

/**
 * Get client device from user agent
 * @return string Client device name
 */
function get_device(): string {
    $client = new AGENT();
    return $client->get_device();
}

/**
 * Checks if user device is using mobile
 * @return string
 */
function is_mobile(): string {
    $client = new AGENT();
    return $client->is_mobile();
}

/**
 * If client is using iOS device
 * @return boolean
 * @author Shaikh <hey@shaikh.dev>
 */
function is_ios() {
    $client = new AGENT();
    return $client->get_device();
    //$d = get_device();
    //return ( $d == 'iPad' || $d == 'iPhone' || $d == 'iPod' ) ? 1 : 0;
}