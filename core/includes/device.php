<?php

class CLIENT {

    function __construct() {
        if( file_exists( COREPATH . 'core/components/ext/whichbrowser/autoload.php' ) )
            require COREPATH . 'core/components/ext/whichbrowser/autoload.php';
    }

    /**
     * Get client device model
     * @return string
     */
    function get_device(): string {
        if( isset( $_SESSION['client']['device']['model'] ) && !APPDEBUG ) {
            return $_SESSION['client']['device']['model'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $_SESSION['client']['device']['model'] = $result->device->model;
            return $result->device->model;
        }
    }

    /**
     * Get client device type
     * @return string
     */
    function get_device_type(): string {
        if( isset( $_SESSION['client']['device']['type'] ) && !APPDEBUG ) {
            return $_SESSION['client']['device']['type'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $_SESSION['client']['device']['type'] = $result->device->type;
            return $result->device->type;
        }
    }

    /**
     * Get client browser name
     * @return string
     */
    function get_browser(): string {
        if( isset( $_SESSION['client']['browser']['name'] ) && !APPDEBUG ) {
            return $_SESSION['client']['browser']['name'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $_SESSION['client']['browser']['name'] = $result->browser->name;
            return $result->browser->name;
        }
    }

    /**
     * Get client browser version
     * @return string
     */
    function get_browser_version(): string {
        if( isset( $_SESSION['client']['browser']['version']['value'] ) && !APPDEBUG ) {
            return $_SESSION['client']['browser']['version']['value'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $_SESSION['client']['browser']['version']['value'] = $result->browser->version->value;
            return $result->browser->version->value;
        }
    }

    /**
     * Get client operating system information
     * @return string
     */
    function get_os(): string {
        if( isset( $_SESSION['client']['os'] ) && !APPDEBUG ) {
            return $_SESSION['client']['os'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $_SESSION['client']['os'] = $result->os->name;
            return $result->os->name;
        }
    }

    /**
     * Get client operating system version
     * @return string
     */
    function get_os_version(): string {
        if( isset( $_SESSION['client']['os']['version']['value'] ) && !APPDEBUG ) {
            return $_SESSION['client']['os']['version']['value'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $_SESSION['client']['os']['version']['value'] = $result->os->version->value;
            return $result->os->version->value;
        }
    }

    /**
     * Checks if client is using mobile
     * @return bool
     */
    function is_mobile(): bool {
        $type = $this->get_device_type();
        return $type == 'mobile';
    }

    /**
     * Checks if client is using tablet
     * @return bool
     */
    function is_tablet(): bool {
        $type = $this->get_device_type();
        return $type == 'tablet';
    }

    /**
     * Checks if client is using desktop
     * @return bool
     */
    function is_desktop(): bool {
        $type = $this->get_device_type();
        return $type == 'desktop';
    }

}

/**
 * Get client device from user agent
 * @return string Client device name
 */
function get_device(): string {
    $client = new CLIENT();
    return $client->get_device();
}

/**
 * Checks if user device is using mobile
 * @return string
 */
function is_mobile(): string {
    $client = new CLIENT();
    return $client->is_mobile();
}

/**
 * If client is using iOS device
 * @return boolean
 * @author Shaikh <hey@shaikh.dev>
 */
function is_ios() {
    $client = new CLIENT();
    return $client->get_device();
    //$d = get_device();
    //return ( $d == 'iPad' || $d == 'iPhone' || $d == 'iPod' ) ? 1 : 0;
}