<?php

class ACCESS_OLD {

    function __construct() {

        // Initiate Session
        ob_start();
        session_name(str_replace(' ','',APPNAME));

        $secure = defined( 'CONFIG' ) && isset( CONFIG[''] );
        $httponly = true;
        //$domain = isset($domain) ? $domain : isset($_SERVER['SERVER_NAME']);
        //$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

        // Check if session cannot be set
        if (ini_set('session.use_only_cookies', 1) === FALSE) {
            header("Location: ../404");
            echo 'Could not initiate a safe session';
            exit();
        }

        $cookieParams = session_get_cookie_params();

        if ($cookieParams["lifetime"] == 0) {
            $cookieParams["lifetime"] = 28000;
        }
        //$lifetime=60 * 60 * 24 * 365;

        session_start();
        //session_regenerate_id(true);
    }

    function register_user( $user_login, $password, $columns = [], $d = []) {

        // Checks if user with same login name or email exists
        $email = !empty($d['email']) ? $d['email'] : $user_login;
        $uq = select( 'users', 'user_id', 'user_login = "' . $user_login . '" OR user_email = "' . $email . '"' );
        if ( $uq ) { return [false, '']; die(); }

        // Prepares data to insert into users table
        $dt = date('Y-m-d H-i-s');
        $n = !empty( $columns['name'] ) ? $columns['name'] : ucwords( str_replace( '_', ' ', $user_login ) );
        if( !empty( $columns['name'] ) ) {
            unset( $columns['name'] );
        }
        elog( $d );

        // Creates new user
        $user_keys = [ 'user_login', 'user_name', 'user_since', 'user_data' ];
        $user_vals = [ $user_login, $n, $dt, serialize($d) ];
        if( !empty( $columns ) && is_assoc( $columns ) ){
            foreach( $columns as $k => $v ){
                if( !isset( $user_keys[$k] ) ) {
                    $user_keys[] = $k;
                    $user_vals[] = $v;
                }
            }
        }
        $nu = insert( 'users', $user_keys, $user_vals );
        if ( $nu ) {
            // Sets the user's access data
            $um = insert( 'access', ['access_uid', 'access_pass', 'access_status' ], [$nu, password_hash( $password, PASSWORD_BCRYPT, ['cost' => 12] ), 1 ] );
            if( $um ) {
                return [true, $nu];
            } else {
                delete( 'users', 'user_id = "'.$nu.'"' );
                return [false, 'A new User could not be created'];
            }
        } else {
            return [false, 'Issue when creating a new User'];
        }
    }

    function update_user( $user_login, $password = '', $columns = [], $d = [], $logout = false ) {

        // Checks if user with same login name or email exists
        $email = !empty($d['email']) ? $d['email'] : $user_login;

        if( !is_numeric( $user_login ) ) {
            $uq = select( 'users', 'user_id', 'user_login = "' . $user_login . '" OR user_email = "' . $email . '"', 1 );
        } else {
            $uq['user_id'] = $user_login;
        }

        if ( is_array( $uq ) ) {

            $uq = $uq['user_id'];
            $n = !empty($d['name']) ? $d['name'] : ucwords(str_replace('_', ' ', $user_login));
            if( !is_numeric( $user_login ) ) {
                $user_keys = ['user_login', 'user_name', 'user_data'];
                $user_vals = [$user_login, $n, serialize($d)];
                if (!empty($columns) && is_assoc($columns)) {
                    foreach ($columns as $k => $v) {
                        $user_keys[] = $k;
                        $user_vals[] = $v;
                    }
                }
                $nu = update( 'users', $user_keys, $user_vals, 'user_id = "'.$uq.'"' );
            } else {
                $nu = 1;
            }
            if( !empty( $password ) && $password !== '' ){
                if ( $nu ) {
                    // Sets the user's access data
                    $um = update( 'access', ['access_pass'], [ password_hash( $password, PASSWORD_BCRYPT, ['cost' => 12] )], 'access_uid = "'.$uq.'"' );
                    $um && $logout ? delete( 'sessions', 'ss_uid = "'.$uq.'"' ) : '';
                    return $um ? [true, $nu] : [0, 'Update Successful'];
                } else {
                    return [1, 'Failed to update'];
                }
            } else {
                if( $nu ){
                    return [1, $nu];
                } else {
                    return [0, 'Failed to update'];
                }
            }
        } else {
            return [false, 'The User with same username or email address does not exist in database']; die();
        }
    }

    function user_login($un, $ps) {
        //$gu = select('users', false, 'user_login = "' . $un . '" OR user_email = "' . $un . '"');
        $gu = select('users', '*', 'user_login = "' . $un . '" OR user_email = "' . $un . '"', 1);
        if ($gu) {
            $uid = $gu['user_id'];
            $gp = select('access', '*', "access_uid = $uid" , 1);
            //error_log(print_r($gp, true));
            if($gp['access_status'] === '1') {
                if ($gp) {
                    if (password_verify($ps, $gp['access_pass'])) {
                        $_SESSION['user_id'] = preg_replace("/[^0-9]+/", "", $uid);
                        /*$_SESSION['user_level'] = !empty( $gu['user_level'] ) ? $gu['user_level'] : '';
                        $_SESSION['user_name'] = !empty( $gu['user_name'] ) ? $gu['user_name'] : '';
                        $_SESSION['user_pic'] = !empty( $gu['user_pic'] ) ? $gu['user_pic'] : '';
                        $_SESSION['user_login'] = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $un);*/
                        $_SESSION = $gu;
                        !empty($gu['user_data']) ? $_SESSION['user_data'] = unserialize($gu['user_data']) : '';
                        $_SESSION['login_string'] = hash('sha512', $gp['access_pass'] . $this->get_user_browser());
                        $ssid = insert( 'sessions', ['ss_uid','ss_time','ss_ip','ss_os','ss_client','ss_status'],[$uid,date("Y-m-d H:i:s"),$this->get_user_ip(),$this->get_user_os(),self::get_user_browser(),1] );
                        $_SESSION['id'] = $ssid;
                        if (!isset($_SESSION)) {
                            session_set_cookie_params(0, '/', str_replace(' ', '_', APPNAME), false, false);
                            @session_regenerate_id(true);
                        }
                        return [true, $gu];
                    } else {
                        return [0, 'Password incorrect'];
                    }
                } else {
                    return [0, 'No Password was set for this user'];
                }
            }
            else{
                return [0, 'User status is not active'];
            }
        } else {
            return [0, 'User not found'];
        }
    }

    function reset_password( $u, $url ) {
        $user = select( 'users', 'user_id,user_email', 'user_login = "'.$u.'" OR user_email = "'.$u.'"', 1 );
        //skell( $user );
        if( $user ) {
            // Generate Code
            $code = self::generate_password(6);
            // Store in user_reset_pass
            $up = update( 'users', ['user_reset_pass'], [$code], 'user_id = "'.$user['user_id'].'"' );
            if( $up ) {
                // Encrypt Code and Username
                $cry = Crypto::initiate();
                $enc = $cry->encrypt($user['user_id'] . '|' . $code);
                // Send Email
                email($user['user_email'], T('Password Reset Email from ' . APPNAME), 'You requested for password reset, <a href="' . $url . '/?r=' . $enc . '">Please click here to reset password.</a>', 'server@email.com');
                return [1, T('Password reset link has been emailed')];
            }
        } else {
            return [0, T('User / Email not found to reset password')];
        }
    }

    function update_password( $code, $password ) {
        // Url to decode Code and Username
        $cry = Crypto::initiate();
        $code = $cry->decrypt( $code );
        $c = explode( '|', $code );

        if( is_array( $c ) ) {
            // If username and code match, let set new password
            $user = select('users', 'user_login,user_reset_pass', 'user_id = "' . $c[0] . '"', 1);
            if( $user['user_reset_pass'] === $c[1] ) {
                // Update User Password
                $update = $this->update_user( $user['user_login'], $password, [ 'user_reset_pass' => NULL ] );
                if( $update ) {
                    return [1, T('Your password has been updated successfully')];
                } else {
                    return [0, T('Your password was not updated, please contact support!')];
                }
            } else {
                return [0, T('Your reset link doesn\'t match our records for this user! Please try the process again or contact support!')];
            }
        } else {
            return [0, T('Your reset link seems to be corrupted, please try resetting again or contact support!')];
        }
    }

    function get_users() {
        $us = select( 'users', '', '', '', '', '', '', 'ID' );
        $users = [];
        if( is_array( $us ) ) {
            foreach($us as $u) {
                $users[] = ['ID' => $u['ID'], 'user_login' => $u['user_login'], 'user_email' => $u['user_email'], 'user_fullname' => $u['user_fullname'], 'register_date' => $u['register_date'], 'user_pic' => $u['user_pic'], 'user_status' => $u['user_status']];
            }
        }
        return $users;
    }

    public static function get_user_os() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS Platform";
        $os_array = [
            '/windows nt 10/i' => 'Windows 10',
            '/windows nt 6.3/i' => 'Windows 8.1',
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iOS',
            '/ipod/i' => 'iOS',
            '/ipad/i' => 'PadOS',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'webOS'
        ];
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }

    public static function get_user_device() {
        return $_SERVER["HTTP_USER_AGENT"];
    }

    public static function get_user_browser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = "Unknown Browser";
        $browser_array = [
            '/msie/i' => 'Internet Explorer',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/chrome/i' => 'Chrome',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/netscape/i' => 'Netscape',
            '/maxthon/i' => 'Maxthon',
            '/konqueror/i' => 'Konqueror',
            '/mobile/i' => 'Handheld Browser'
        ];
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }
        return $browser;
    }

    public static function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip == '::1' ? '127.0.0.1' : $ip;
    }

    public static function generate_password( $chars = 11 ) {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < $chars; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}

function set_login() {
    $uns = ['username','user','uid','first','id','name','un'];
    $pss = ['password','pass','secure','key','gateway','passy','ps','as'];
    $uname = $pass = '';
    foreach( $uns as $un ){
        if( !empty( $_POST[$un] ) && $uname == '' ){
            $uname = $_POST[$un];
        }
    }
    foreach( $pss as $ps ){
        if( !empty( $_POST[$ps] ) && $pass == '' ){
            $pass = $_POST[$ps];
        }
    }
    unset($_POST['action']);
    $uname = $uname == '' && !empty( $POST[0] ) ? $_POST[0] : $uname;
    $pass = $pass == '' && !empty( $POST[1] ) ? $_POST[1] : $pass;
    if( $uname !== '' && $pass !== '' ){
        global $access;
        $r = $access->user_login( $uname, $pass );
        echo $r[0] ? json_encode([1,$r[1]]) : json_encode([0,$r[1]]);
    } else {
        echo json_encode([0,'Fields seem to empty']);
    }
}

function reset_pass( $u = '', $url = '' ) {
    if( !user_logged_in() ) {
        if ( is_array( $u ) ) {
            unset( $_POST['action'] );
            $u = $_POST['u'];
            $url = $_POST['url'];
        }
        if ( $u !== '' ) {
            global $access;
            echo json_encode($access->reset_password( $u, $url ));
        }
    }
}
