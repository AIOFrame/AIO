<?php

class AGENT {

    function __construct() {
        if( file_exists( ROOTPATH . 'core/external/vendor/autoload.php' ) )
            require ROOTPATH . 'core/external/vendor/autoload.php';
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