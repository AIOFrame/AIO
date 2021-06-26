<?php

include_once( ROOTPATH . 'core/modules/agent.php' );

/**
 * Get client device name
 * @return string Client device name
 */
function get_user_device(): string {
    $client = new AGENT();
    return $client->get_device();
}

/**
 * Get client device type
 * @return string Client device type
 */
function get_user_device_type(): string {
    $client = new AGENT();
    return $client->get_device_type();
}

/**
 * Get client OS from user agent
 * @return string Client OS
 */
function get_user_os(): string {
    $client = new AGENT();
    return $client->get_os();
}

/**
 * Get client browser
 * @return string Client browser
 */
function get_user_browser(): string {
    $client = new AGENT();
    return $client->get_browser();
}

/**
 * Get client browser version
 * @return string Client browser version
 */
function get_user_browser_version(): string {
    $client = new AGENT();
    return $client->get_browser_version();
}

/**
 * Checks if user device is mobile
 * @return string
 */
function is_mobile(): string {
    $client = new AGENT();
    return $client->is_mobile();
}

/**
 * Checks if user device is tablet
 * @return string
 */
function is_tablet(): string {
    $client = new AGENT();
    return $client->is_tablet();
}

/**
 * Checks if user device is desktop
 * @return string
 */
function is_desktop(): string {
    $client = new AGENT();
    return $client->is_desktop();
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