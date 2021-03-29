<?php

class ACCESS {

    function create_session() {

        // Initiate Session
        ob_start();
        session_name( str_replace( ' ', '', APPNAME ) );

        $secure = isset( $_SERVER['HTTPS'] );
        session_set_cookie_params( 14400, '/', APPURL, $secure, 1 );

        @session_start() or die();

        $now = time();

        $_SESSION['time'] = $now;

        $agent = new CLIENT();
        $useragent_hash = hash('sha256', $agent->get_device() );
        $ip = $_SERVER['REMOTE_ADDR'];

        if( !isset( $_SESSION['canary'] ) ) {
            session_regenerate_id( true );
            $_SESSION['canary'] = [
                'birth' => time(),
                'ip' => $ip,
                'useragent_hash' => $useragent_hash
            ];
        }

        if( $_SESSION['canary']['birth'] < $now - 300 ) {
            session_regenerate_id( true );
            $_SESSION['canary']['birth'] = $now;
        }

        // If user is logged in, log out user if IP or Useragent is changed (this is intentional, I know users behind load-balancers etc will have issues)
        if( isset( $_SESSION['username'] ) && ( $_SESSION['canary']['ip'] !== $ip OR $_SESSION['canary']['useragent_hash'] !== $useragent_hash )) {
            // Destroy cookie
            setcookie( session_name(), "", time() - 3600, '/', APPURL, $secure, true );

            // Destroy session
            session_unset();
            session_destroy();

            // Redirect (avoid loop by checcking ip_browser_changed)
            /* if( ! isset($_GET['ip_browser_changed']))
            {
                header('Location: '.URL.'login/?ip_browser_changed');
                exit('IP Address or Browser has been changed, please login again!');
            } */
        }
        ob_end_clean();
    }

    /**
     * Registers a user for AIO
     * @param string $login User Login
     * @param string $pass User Password
     * @param array $columns User meta to be stored in independent columns, columns must exist
     * @param array $data User meta to be stored in user_data column
     * @param array $perms User Permissions, Custom array with permission name key and boolean value
     * @return array
     */
    function register( string $login, string $pass, array $columns = [], array $data = [], array $perms = [] ): array {
        // User login restrictions
        $login = strtolower( $login );
        if( strlen( $login ) <= 7 ) {
            return [ 0, T('User login must be at least 8 characters in length!') ];
        }
        if( preg_match( '/[^a-z\d ]/i', $login ) ) {
            return [ 0, T('User login cannot contain special characters!') ];
        }

        $db = new DB();
        $email = !empty( $data['email'] ) ? $data['email'] : '';
        $name = !empty( $data['name'] ) ? $data['name'] : ucwords( str_replace( '_', ' ', $login ) );
        $pic = !empty( $data['pic'] ) ? $data['pic'] : '';
        $perms = empty( $perms ) && !empty( $data['perms'] ) ? $data['perms'] : $perms;
        $perms = empty( $perms ) && !empty( $data['permissions'] ) ? $data['permissions'] : $perms;

        // Checks if user with same login name or email exists
        $exist = $db->select( 'users', 'user_id', 'user_login = "' . $login . '" OR user_email = "' . $email . '"' );
        if( $exist ){ return [ 0, T('The User with same username or email address already exist in database') ]; }

        // Prepares data to insert into users table
        $dt = date('Y-m-d H-i-s');
        $keys = [ 'user_login', 'user_name', 'user_email', 'user_pic', 'user_since', 'user_data', 'user_perms' ];
        $values = [ $login, $name, $email, $pic, $dt, serialize( $data ), serialize( $perms ) ];

        // Parsing columns
        if( !empty( $columns ) && is_assoc( $columns ) ) {
            foreach( $columns as $k => $v ) {
                if( !isset( $user_keys[$k] ) ) {
                    $keys[] = $k;
                    $values[] = $v;
                }
            }
        }

        // Add user to users table
        $add_user = $db->insert( 'users', $keys, $values );
        if( $add_user ) {
            // Sets the user's access data
            $access = $db->insert( 'access',
                ['access_uid', 'access_pass', 'access_status' ],
                [ $add_user, password_hash( $pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ), 1 ]
            );
            if( $access ) {
                return [ $add_user, T('Successfully registered user!') ];
            } else {
                $db->delete( 'users', 'user_id = \'$add_user\'' );
                return [ 0, T('Failed to create new user!') ];
            }
        } else {
            return [ 0, T('Failed to create new user!') ];
        }
    }

    /**
     * Updates user password or data
     * @param string $login
     * @param string $pass
     * @param array $data
     * @param array $perms
     * @return array
     */
    function update( string $login, string $pass = '', array $data = [], array $perms = [] ): array {

        $db = new DB();
        $user = $db->select( 'users', 'user_id', 'user_login = \''.$login.'\'', 1 );

        return $user;

    }
}

/* function login_check() {
    if( !user_logged_in() ){
        header( "Location:" .APPURL. "login" );
        die();
    }
}

function user_logged_in() {
    // TODO: Change define to global vars
    if( !defined('LOGGED_IN') ){
        if ( isset($_SESSION['user_login']) && isset($_SESSION['user_id']) && isset($_SESSION['login_string']) ) {

            // Check if encryption matches access
            $pw = select('access', '*', 'access_uid = "' . $_SESSION['user_id'] . '"',1)['access_pass'];
            $hash_check = hash_equals(hash('sha512', $pw . access::get_user_browser()), $_SESSION['login_string']);

            // Check if session is stored online
            $session_check = 0; global $access;
            $ss = select('sessions', '', 'ss_uid = "'.$_SESSION['user_id'].'"');
            if( is_array( $ss ) ){
                foreach( $ss as $s ){
                    if( $s['ss_ip'] == $access->get_user_ip() && $s['ss_os'] == $access->get_user_os() && $s['ss_client'] == $access::get_user_browser() && $s['ss_status'] == 1 ){
                        $session_check = true;
                    }
                }
            }
            define('LOGGED_IN',1);
            return $hash_check && $session_check ? 1 : 0;
        } else {
            return 0;
        }
    } elseif( defined('LOGGED_IN') && LOGGED_IN == 1 ) {
        return 1;
    } else {
        return 0;
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

function register_user() {
    $c = get_config();
    $feats = is_array($c) && isset($c['access']) ? $c['access'] : [];

    if( in_array( 'register', $feats ) ) {
        $d = $_POST;
        $pass = isset( $_POST['pass'] ) && !empty( $_POST['pass'] ) ? $_POST['pass'] : ( isset( $_POST['password'] ) && !empty( $_POST['password'] ) ? $_POST['password'] : '' );
        $data = isset( $_POST['data'] ) && !empty( $_POST['data'] ) ? $_POST['data'] : '';
        if( !empty( $d['login'] ) && !empty( $pass ) ) {

            // Encrypt Password
            $pass = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);

            // Process user columns
            $fields = ['email','role','eid','phone','pic'];
            $columns = [];
            foreach( $fields as $f ) {
                $columns[ 'user_' . $f ] = isset( $d[$f] ) && !empty( $d[$f] ) ? $d[$f] : '';
            }

            // Add user
            global $access;
            $add_user = $access->register_user( $d['login'], $pass, $columns, $data );
            is_array($add_user) && $add_user[0] == 1 ? es('Successfully added user!') : ef('Failed to add user!');
        } else {
            ef('Login and Pass Fields are required!');
        }
    } else {
        ef('Registrations are not permitted in config, please contact developer');
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
                ef('Domain not approved - Login is restricted only to users from ' . $gd);
            }
        } else {
            echo json_encode($access->register_user($d['email'], $d['ID'], $d['email'], $d['email'], $d['pic'], $d['level'], true));
        }
    }
}

// TODO: Add config to check if registrations are open, then process the following
if (isset($_POST['ru_name']) && isset($_POST['ru_pass']) && isset($_POST['ru_email']) && isset($_POST['ru_fn']) && isset($_POST['ru_ln'])) {
    $access->register_user($_POST['ru_name'], $_POST['ru_pass'], $_POST['ru_email'], $_POST['ru_fn'], $_POST['ru_ln']);
}

function logout() {
    $cry = Crypto::initiate();
    $id = !empty($_POST['session_id']) ? $cry->decrypt($_POST['session_id']) : $_SESSION['id'];
    echo $id;
    $ss = delete( 'sessions', 'ss_id = "'.$id.'"' );
    if( empty($_POST['session_id']) ){
        $_SESSION = array();
        session_destroy();
        echo 1;
    } else {
        echo 0;
    }
    //echo 1;
}

if( isset( $_GET['logout'] ) ) {
    if( $_GET['logout'] == 'true' ) {
        logout();
        /* $_SESSION = array();
        $params = session_get_cookie_params();
        setcookie(session_name(),
            '', 0,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]);
        session_destroy();
    }
}

function logout_all() {
    $ss = delete( 'sessions', 'ss_uid = "'.$_SESSION['user_id'].'"' );
    $_SESSION = array();
    session_destroy();
    echo 1;
}

function get_os() {
    return ACCESS::get_user_os();
}

/**
 * Set autoload user options as session

if( user_logged_in() ) {
    $db = new DB();
    $options = $db->select('options', 'option_name,option_value', 'option_scope = "' . $_SESSION['user_id'] . '" AND option_load = 1');
    if (is_array($options)) {
        foreach ($options as $opt) {
            $_SESSION[$opt['option_name']] = $opt['option_value'];
        }
    }
}

function dev_sessions( $logged_in = true ) {
    if( APPDEBUG && $logged_in ) {
        $_SESSION['username'] = 'Developer Name';
        $_SESSION['user_name'] = 'Developer Name';
        $_SESSION['user_pic'] = 'Developer Name';
        $_SESSION['user_id'] = '856';
        $_SESSION['user_login'] = 'developer';
        if (!isset($_SESSION)) {
            session_set_cookie_params(0, '/', str_replace(' ', '_', APPNAME), false, false);
            @session_regenerate_id(true);
        }
    }
}
*/