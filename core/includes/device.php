<?php

/**
 * Get client device from user agent
 * @return string Client device name
 */
function get_device(): string {
    if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'iPod' ) )
        return 'iPod';
    elseif ( stripos( $_SERVER['HTTP_USER_AGENT'] , 'iPhone' ) )
        return 'iPhone';
    elseif( stripos( $_SERVER['HTTP_USER_AGENT'] , 'iPad' ) )
        return 'iPad';
    elseif( stripos( $_SERVER['HTTP_USER_AGENT'] , 'Android' ) )
        return 'Android';
    else if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'webOS' ) )
        return 'webOS';
    else if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'macintosh' ) )
        return 'Mac';
    else
        return '';
}

/**
 * Checks if user device is desktop
 * @return bool
 */
function is_desktop(): bool {
    if( !is_mobile() && !is_tablet() ){
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if user device is iPad
 * @return bool
 */
function is_tablet(): bool{
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
        return false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false ) {
        return false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) {
        return true;
    } else {
        return false;
    }
}

/**
 * Checks if user device is Mobile
 * @return bool|string
 */
function is_mobile() {
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
        return 'true';
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false ) {
        return true;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) {
        return false;
    } else {
        return false;
    }
}

/**
 * If client is using iOS device
 * @return boolean
 * @author Shaikh <hey@shaikh.dev>
 */
function is_ios() {
    $d = get_device();
    return ( $d == 'iPad' || $d == 'iPhone' || $d == 'iPod' ) ? 1 : 0;
}