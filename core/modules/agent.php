<?php

class AGENT {

    function __construct() {
        if( file_exists( ROOTPATH . 'core/external/vendor/autoload.php' ) )
            require ROOTPATH . 'core/external/vendor/autoload.php';
    }

    /**
     * Get client device model
     * @return string|null
     */
    function get_device(): string|null {
        if( isset( $_SESSION['client']['device']['model'] ) && !APPDEBUG ) {
            return $_SESSION['client']['device']['model'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $device = $result->device->model;
            $device = !empty( $device ) ? $device : 'unknown';
            $_SESSION['client']['device']['model'] = $device;
            return $device;
        }
    }

    /**
     * Get client device type
     * @return string|null
     */
    function get_device_type(): string|null {
        if( isset( $_SESSION['client']['device']['type'] ) && !APPDEBUG ) {
            return $_SESSION['client']['device']['type'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $device_type = $result->device->type;
            $device_type = !empty( $device_type ) ? $device_type : $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['client']['device']['type'] = $device_type;
            return $device_type;
        }
    }

    /**
     * Get client browser name
     * @return string|null
     */
    function get_browser(): string|null {
        if( isset( $_SESSION['client']['browser']['name'] ) && !APPDEBUG ) {
            return $_SESSION['client']['browser']['name'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $browser = $result->browser->name;
            $browser = !empty( $browser ) ? $browser : $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['client']['browser']['name'] = $browser;
            return $browser;
        }
    }

    /**
     * Get client browser version
     * @return string|null
     */
    function get_browser_version(): string|null {
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
     * @return string|null
     */
    function get_os(): string|null {
        if( isset( $_SESSION['client']['os'] ) && !APPDEBUG ) {
            return $_SESSION['client']['os'];
        } else {
            $result = new WhichBrowser\Parser(getallheaders());
            $os = $result->os->name;
            if( empty( $os ) ) {
                $agent = $_SERVER['HTTP_USER_AGENT'];
                if(preg_match('/Linux/',$agent)) $os = 'Linux';
                elseif(preg_match('/Win/',$agent)) $os = 'Windows';
                elseif(preg_match('/Mac/',$agent)) $os = 'OS X';
                else $os = 'UnKnown';
            }
            $_SESSION['client']['os'] = $os;
            return $os;
        }
    }

    /**
     * Get client operating system version
     * @return string|null
     */
    function get_os_version(): string|null {
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