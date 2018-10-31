<?php

global $access;
$access = new ACCESS();

class ACCESS {

    function __construct() {
        ob_start();

        session_name(str_replace(' ','',APPNAME));

        $secure = false;
        $httponly = true;

        //$domain = isset($domain) ? $domain : isset($_SERVER['SERVER_NAME']);
        //$https = isset($secure) ? $secure : isset($_SERVER['HTTPS']);

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
        if ( $uq ) { return [false, 'The User with same username or email address already exist in database']; die(); }

        // Prepares data to insert into users table
        $dt = date('Y-m-d H-i-s');
        $n = !empty( $d['name'] ) ? $d['name'] : ucwords( str_replace( '_', ' ', $user_login ) );

        // Creates new user
        $user_keys = [ 'user_login', 'user_name', 'user_since', 'user_data' ];
        $user_vals = [ $user_login, $n, $dt, serialize($d) ];
        if( !empty( $columns ) && is_assoc( $columns ) ){
            foreach( $columns as $k => $v ){
                $user_keys[] = $k;
                $user_vals[] = $v;
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

    function update_user( $user_login, $password = '', $columns = [], $d = [] ) {

        // Checks if user with same login name or email exists
        $email = !empty($d['email']) ? $d['email'] : $user_login;
        $uq = select( 'users', 'user_id', 'user_login = "' . $user_login . '" OR user_email = "' . $email . '"', 1 );
        if ( is_array( $uq ) ) {
            $uq = $uq['user_id'];
            $n = !empty( $d['name'] ) ? $d['name'] : ucwords( str_replace( '_', ' ', $user_login ) );
            $user_keys = [ 'user_login', 'user_name', 'user_data' ];
            $user_vals = [ $user_login, $n, serialize($d) ];
            if( !empty( $columns ) && is_assoc( $columns ) ){
                foreach( $columns as $k => $v ){
                    $user_keys[] = $k;
                    $user_vals[] = $v;
                }
            }
            $nu = update( 'users', $user_keys, $user_vals, 'user_id = "'.$uq.'"' );
            if( !empty( $password ) && $password !== '' ){
                if ( $nu ) {
                    // Sets the user's access data
                    $um = update( 'access', ['access_pass'], [ password_hash( $password, PASSWORD_BCRYPT, ['cost' => 12] )], 'access_uid = "'.$uq.'"' );
                    return $um ? [true, $nu] : [false, 'A new User could not be created'];
                } else {
                    return [false, 'Issue when creating a new User'];
                }
            } else {
                if( $nu ){
                    return [true, $nu];
                } else {
                    return [false, 'Issue when creating a new User'];
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
                        $_SESSION['user_level'] = !empty( $gp['user_level'] ) ? $gp['user_level'] : '';
                        $_SESSION['user_login'] = preg_replace("/[^a-zA-Z0-9_\-]+/", "", $un);
                        !empty($gu['user_data']) ? $_SESSION['user_data'] = unserialize($gu['user_data']) : '';
                        $_SESSION['login_string'] = hash('sha512', $gp['access_pass'] . $this->get_user_browser());
                        if (!isset($_SESSION)) {
                            session_set_cookie_params(0, '/', str_replace(' ', '_', APPNAME), false, false);
                            @session_regenerate_id(true);
                        }
                        return array(true, $gu);
                    } else {
                        return array(false, 'Password incorrect');
                    }
                } else {
                    return array(false, 'No Password was set for this user');
                }
            }
            else{
                return array(false, 'User status is not active ');
            }
        } else {
            return array(false, 'User not found');
        }
    }

    function get_users() {
        global $conn;
        $us = "SELECT * FROM users ORDER by ID";
        if ($uq = $conn->query($us)) {
            while ($row = $uq->fetch_assoc()) {
                $users[] = ['ID' => $row['ID'], 'user_login' => $row['user_login'], 'user_email' => $row['user_email'], 'user_fullname' => $row['user_fullname'], 'register_date' => $row['register_date'], 'user_pic' => $row['user_pic'], 'user_status' => $row['user_status']];
            }
            return $users;
        }
    }

    function get_user_os() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $os_platform = "Unknown OS Platform";
        $os_array = array(
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
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );
        foreach ($os_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $os_platform = $value;
            }
        }
        return $os_platform;
    }

    public static function get_user_browser() {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $browser = "Unknown Browser";
        $browser_array = array(
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
        );
        foreach ($browser_array as $regex => $value) {
            if (preg_match($regex, $user_agent)) {
                $browser = $value;
            }
        }
        return $browser;
    }

    function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    function generate_password() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 11; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }
}

function get_current_user_id() {
    if (user_logged_in()) {
        return $_SESSION['user_id'];
    }
}

function get_user_level( $u = '' ) {
    if (user_logged_in()) {
        if (!empty($u)) {
            return select('access', false, 'user_id = "' . $u . '"')['user_level'];
        } else {
            return select('access', false, 'user_id = "' . get_current_user_id() . '"')['user_level'];
        }
    } else {
        return false;
    }
}

function login_check() {
    if( !user_logged_in() ){
        header( "Location:" .APPURL. "login" );
        die();
    }
}

function user_logged_in() {
    if (isset($_SESSION['user_login']) && isset($_SESSION['user_id']) && isset($_SESSION['login_string'])) {
        $pw = select('access', '*', 'access_uid = "' . $_SESSION['user_id'] . '"',1)['access_pass'];
        if (hash_equals(hash('sha512', $pw . access::get_user_browser()), $_SESSION['login_string'])) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function ajax_user_logged_in() {
    if (isset($_SESSION['username']) && isset($_SESSION['user_id']) && isset($_SESSION['login_string'])) {
        $pw = select('access', false, 'user_id = "' . $_SESSION['user_id'] . '"')['user_pass'];
        if (hash_equals(hash('sha512', $pw . access::get_user_browser()), $_SESSION['login_string'])) {
            echo 'All Set';
        } else {
            //return false;
            echo 'Hash Error';
        }
    } else {
        echo 'Session not available';
        //return false;
    }
}

function ajax_google_sign() {
    if (!empty($_POST['data'])) {
        $d = $_POST['data'];
        $vd = get_option('google_domain');
        global $access;
        if (!empty($vd)) {
            $gd = explode(',', str_replace(' ', '', $vd));
            if (in_array(str_replace('@', '', strstr($d['email'], '@')), $gd)) {
                echo json_encode($access->user_login($d['email'], $d['ID']));
            } else {
                echo json_encode(array('error', 'Domain not approved', 'Login is restricted only to users from ' . $gd));
            }
        } else {
            echo json_encode($access->user_login($d['email'], $d['ID']));
        }
    }
}

function ajax_reg() {
    if (!empty($_POST['data'])) {
        $d = $_POST['data'];
        if ($d['em']) {
            if ($d['fn']) {
                if ($d['lv'] == '0') {
                    $em = $d['em'];
                    $un = $d['un'];
                    $fn = $d['fn'];
                    $lv = $d['lv'];

                    $options = ['cost' => 12];
                    $enc = password_hash($_POST['password'], PASSWORD_BCRYPT, $options);

                    global $access;
                    //function register_user($un, $ps, $em, $fn, $up, $l, $alert = true)
                    echo json_encode($access->register_user($un, $enc,$em, $fn,'',$lv ,true));

                } else {
                    echo json_encode(array('error'), 'User Role is required');
                }
            } else
                echo json_encode(array('error'), 'Full Name is required');
        } else {
            echo json_encode(array('error'), 'Email Field is required');
        }

    }
}

function ajax_google_reg() {
    if (!empty($_POST['data'])) {
        $d = $_POST['data'];
        $gd = explode(',', str_replace(' ', '', get_option('google_domain')));
        global $access;
        if (!empty($gd)) {
            if (in_array(str_replace('@', '', strstr($d['email'], '@')), $gd)) {
                echo json_encode($access->register_user($d['email'], $d['ID'], $d['email'], ucfirst(substr($d['email'], 0, strpos($d['email'], "@"))), $d['pic'], $d['level'], true));
            } else {
                echo json_encode(array('error', 'Domain not approved', 'Login is restricted only to users from ' . $gd));
            }
        } else {
            echo json_encode($access->register_user($d['email'], $d['ID'], $d['email'], $d['email'], $d['pic'], $d['level'], true));
        }
    }
}

if (isset($_POST['ru_name']) && isset($_POST['ru_pass']) && isset($_POST['ru_email']) && isset($_POST['ru_fn']) && isset($_POST['ru_ln'])) {
    $access->register_user($_POST['ru_name'], $_POST['ru_pass'], $_POST['ru_email'], $_POST['ru_fn'], $_POST['ru_ln']);
}

function logout() {
    $_SESSION = array();
    session_destroy();
    echo 'goodbye';
}

if (isset($_GET['logout'])) {
    if ($_GET['logout'] == 'true') {
        $_SESSION = array();
        session_destroy();
        /* $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(),
            '', 0,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]);
        session_destroy(); */
    }
}