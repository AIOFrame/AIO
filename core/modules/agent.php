<?php

class AGENT {

    function __construct() {
        if( file_exists( VENDORLOAD ) )
            require VENDORLOAD;
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
            $browser = !empty( $browser ) ? $browser : $this::legacy_browser();
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

    /**
     * Returns Client Info
     * @param $info
     * @return string
     */
    function legacy_client( $info ): string {
        $browserInfo = array('user_agent'=>'','browser'=>'','browser_version'=>'','os_platform'=>'','pattern'=>'', 'device'=>'');

        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $bname = 'Unknown';
        $ub = 'Unknown';
        $version = "";
        $platform = 'Unknown';

        $deviceType='Desktop';

        if(preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$u_agent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($u_agent,0,4))){

            $deviceType='Mobile';

        }

        if($_SERVER['HTTP_USER_AGENT'] == 'Mozilla/5.0(iPad; U; CPU iPhone OS 3_2 like Mac OS X; en-us) AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B314 Safari/531.21.10') {
            $deviceType='Tablet';
        }

        if(stristr($_SERVER['HTTP_USER_AGENT'], 'Mozilla/5.0(iPad;')) {
            $deviceType='Tablet';
        }

        //$detect = new Mobile_Detect();

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';

        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';

        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }

        // Next get the name of the user agent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
        {
            $bname = 'IE';
            $ub = "MSIE";

        } else if(preg_match('/Firefox/i',$u_agent))
        {
            $bname = 'Mozilla Firefox';
            $ub = "Firefox";

        } else if(preg_match('/Chrome/i',$u_agent) && (!preg_match('/Opera/i',$u_agent) && !preg_match('/OPR/i',$u_agent)))
        {
            $bname = 'Chrome';
            $ub = "Chrome";

        } else if(preg_match('/Safari/i',$u_agent) && (!preg_match('/Opera/i',$u_agent) && !preg_match('/OPR/i',$u_agent)))
        {
            $bname = 'Safari';
            $ub = "Safari";

        } else if(preg_match('/Opera/i',$u_agent) || preg_match('/OPR/i',$u_agent))
        {
            $bname = 'Opera';
            $ub = "Opera";

        } else if(preg_match('/Netscape/i',$u_agent))
        {
            $bname = 'Netscape';
            $ub = "Netscape";

        } else if((isset($u_agent) && (strpos($u_agent, 'Trident') !== false || strpos($u_agent, 'MSIE') !== false)))
        {
            $bname = 'Internet Explorer';
            $ub = 'Internet Explorer';
        }


        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) . ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';

        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }

        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];

            } else {
                $version= @$matches['version'][1];
            }

        } else {
            $version= $matches['version'][0];
        }

        // check if we have a number
        if ($version==null || $version=="") {$version="?";}

        switch( $info ) {
            case 'user_agent':
                return $u_agent;
            case 'browser':
                return $bname;
            case 'browser_version';
                return $version;
            case 'os':
                return $platform;
            case 'device':
                return $deviceType;
        }
    }
    
    function legacy_user_agent():string {
        return $this::legacy_client('user_agent');
    }
    
    function legacy_browser():string {
        return $this::legacy_client('browser');
    }

    function legacy_browser_version():string {
        return $this::legacy_client('browser_version');
    }

    function legacy_os():string {
        return $this::legacy_client('os');
    }

    function legacy_device():string {
        return $this::legacy_client('device');
    }

}