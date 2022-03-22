<?php

include_once( ROOTPATH . 'core/modules/agent.php' );

/**
 * Get client device name
 * @return string|null Client device name
 */
function get_user_device(): string|null {
    $client = new AGENT();
    return $client->get_device();
}

/**
 * Get client device type
 * @return string|null Client device type
 */
function get_user_device_type(): string|null {
    $client = new AGENT();
    return $client->get_device_type();
}

/**
 * Get client OS from user agent
 * @return string|null Client OS
 */
function get_user_os(): string|null {
    $client = new AGENT();
    return $client->get_os();
}

/**
 * Get client browser
 * @return string|null Client browser
 */
function get_user_browser(): string|null {
    $client = new AGENT();
    return $client->get_browser();
}

/**
 * Get client browser version
 * @return string|null Client browser version
 */
function get_user_browser_version(): string|null {
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
 * @return string|null
 */
function is_tablet(): string|null {
    $client = new AGENT();
    return $client->is_tablet();
}

/**
 * Checks if user device is desktop
 * @return string|null
 */
function is_desktop(): string|null {
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