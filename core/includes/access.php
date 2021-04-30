<?php
if( session_status() === PHP_SESSION_NONE ) {
    session_start();
}
/*
// Verify existing sessions
if( isset( $_SESSION['user'] ) && isset( $_SESSION['db_session'] ) ) {
    $db = new DB();
    $id = $cry->decrypt( $_SESSION['db_session'] );
    $session = $db->select( 'sessions', 'session_code', 'session_id = \''.$id.'\'' );
    skel( $session );
    if( empty( $session ) || $session['session_code'] !== session_id() ) {
        $a = new ACCESS();
        //$a->clear_current_sessions();
    }
} else {
    $a = new ACCESS();
    //$a->clear_current_sessions();
} */
//session_start();
// Reload User Sessions
if( user_logged_in() ) {
    //skel( 'logged in' );
} else {
    //skel( 'logged out' );
}

class ACCESS {

    function __construct() {
        ob_start();
        $now = time();
        $agent = new CLIENT();
        $device = $agent->get_device();
        $device_type = $agent->get_device_type();
        $os = $agent->get_os();
        $browser = $agent->get_browser();
        if( session_status() === PHP_SESSION_NONE ) {
            session_name(str_replace(' ', '', APPNAME));
            $secure = isset($_SERVER['HTTPS']);
            session_set_cookie_params(14400, '/', APPURL, $secure, 1);
            session_start();
        }

        if (!isset($_SESSION['agent'])) {
            session_regenerate_id(true);
            $_SESSION['agent'] = [
                'birth' => $now,
                'device' => $device,
                'device_type' => $device_type,
                'os' => $os,
                'browser' => $browser,
            ];
        }
        if( $_SESSION['agent']['birth'] < $now - 300 ) {
            session_regenerate_id( true );
            $_SESSION['agent']['birth'] = $now;
        }

        // Destroy session if user device is changed
        if( isset( $_SESSION['agent'] ) && is_array( $_SESSION['agent'] ) ) {
            $device_check = $_SESSION['agent']['device'] == $device;
            $device_type_check = $_SESSION['agent']['device_type'] == $device_type;
            $os_check = $_SESSION['agent']['os'] == $os;
            $browser_check = $_SESSION['agent']['browser'] == $browser;
            if( !$device_check || !$device_type_check || !$os_check || !$browser_check ) {
                setcookie( session_name(), "", time() - 3600, '/', APPURL, $secure, true );
                session_unset();
                session_destroy();
            }
        }
        ob_end_clean();
    }


    function clear_local_sessions() {
        setcookie( session_name(), "", time() - 3600, '/', APPURL, '', true );
        session_unset();
        session_destroy();
    }

    function clear_live_sessions() {
        $db = new DB();
        $cry = Crypto::initiate();
        $session_id = isset( $_SESSION['db_session'] ) ? $cry->decrypt( $_SESSION['db_session'] ) : '';
        if( !empty( $session_id ) && is_numeric( $session_id ) ) {
            $db->delete( 'sessions', 'session_id = \''.$session_id.'\'' );
        }
    }

    function clear_current_sessions() {
        $this->clear_live_sessions();
        $this->clear_local_sessions();
    }

    /**
     * Clears the current login session of the current logged in user
     * @return array
     */
    function clear_session(): array {
        if( !empty( $_SESSION['user']['id'] ) && is_numeric( $_SESSION['user']['id'] ) ) {
            $db = new DB();
            $deleted = $db->delete( 'sessions', 'ss_uid = \''.$_SESSION['user_id'].'\'' );
            return $deleted ? [ 1, T('Successfully cleared all sessions!') ] : [ 0, T('Failed to clear sessions!') ];
        } else {
            return [];
        }
    }

    /**
     * Clears all login session logs of current logged in user
     * @return array
     */
    function clear_all_sessions(): array {
        if( !empty( $_SESSION['user_id'] ) && is_numeric( $_SESSION['user_id'] ) ) {
            $db = new DB();
            $deleted = $db->delete( 'sessions', 'ss_uid = \''.$_SESSION['user_id'].'\'' );
            return $deleted ? [ 1, T('Successfully cleared all sessions!') ] : [ 0, T('Failed to clear sessions!') ];
        } else {
            return [];
        }
    }

    /**
     * Registers a user for AIO
     * @param string $login User Login
     * @param string $pass User Password
     * @param string $email User Email
     * @param string $name User Full Name
     * @param string $picture User Picture
     * @param array $columns User meta to be stored in independent columns, columns must exist
     * @param array $data User meta to be stored in user_data column
     * @param array $access User Permissions, Custom array with permission name key and boolean value
     * @param string $status User status, 1 for active and 0 for inactive, default 1
     * @return array
     */
    function register( string $login, string $pass, string $email = '', string $name = '', string $picture = '', array $columns = [], array $data = [], array $access = [], string $status = '1' ) : array {
        // User login restrictions
        $login = strtolower( $login );
        if( strlen( $login ) <= 6 ) {
            return [ 0, T('User login must be at least 8 characters in length!') ];
        }
        if( strlen( $pass ) <= 8 ) {
            return [ 0, T('User password must be at least 8 characters in length!') ];
        }
        if( preg_match( '/[^a-z\d ]/i', $login ) ) {
            return [ 0, T('User login cannot contain special characters!') ];
        }

        $db = new DB();
        if( empty( $name ) )
            $name = !empty( $data['name'] ) ? $data['name'] : ucwords( str_replace( '_', ' ', $login ) );

        // Checks if user with same login name or email exists
        $user_query = 'user_login = "' . $login . '"';
        $user_query .= !empty( $email ) ? ' OR user_email = "' . $email . '"' : '';
        $exist = $db->select( 'users', 'user_id', $user_query );
        if( $exist ){ return [ 0, T('The User with same username or email address already exist in database') ]; }

        // Prepares data to insert into users table
        $keys = [ 'user_login', 'user_email', 'user_name', 'user_picture', 'user_data', 'user_access', 'user_since', 'user_status' ];
        $values = [ $login, $email, $name, $picture, json_encode( $data ), json_encode( $access ), date('Y-m-d H-i-s'), $status ];

        // Parsing columns
        if( !empty( $columns ) && is_assoc( $columns ) ) {
            foreach( $columns as $k => $v ) {
                elog( $v );
                if( !isset( $keys[ $k ] ) ) {
                    $keys[] = 'user_'.$k;
                    $values[] = $v;
                }
            }
        }
        // Add user to users table
        $add_user = $db->insert( 'users', $keys, $values );
        if( $add_user ) {
            elog( $add_user );
            // Sets the user's access data
            $access = $db->insert( 'access',
                ['access_uid', 'access_pass' ],
                [ $add_user, password_hash( $pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ) ]
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
     * Verifies and logs in user!
     * @param string $login
     * @param string $pass
     * @return array
     */
    function login( string $login, string $pass ): array {
        $db = new DB();

        // Checks if user with same login name or email exists
        $user = $db->select( 'users', '', '(user_login = \'' . $login . '\' OR user_email = \'' . $login . '\') AND user_status = \'1\'', 1 );
        if( empty( $user ) || empty( $user['user_id'] ) ){
            return [ 0, T('The login or email address does not exist or disabled!') ];
        }

        // Fetch stored and encrypted password
        $hash = $db->select( 'access', 'access_pass', 'access_uid = \''.$user['user_id'].'\'', 1 );
        if( empty( $hash ) || empty( $hash['access_pass'] ) ){
            return [ 0, T('User account disabled!') ];
        }

        // Verify password
        if( password_verify( $pass, $hash['access_pass'] ) ) {
            $db->update( 'access', [ 'access_recent' ], [ date('Y-m-d H-i-s') ], 'access_uid = \''.$user['user_id'].'\'' );
            // Set database sessions
            $agent = new CLIENT();
            $cry = Crypto::initiate();
            $session_data = [
                'uid' => $user['user_id'],
                'time' => date('Y-m-d H-i-s'),
                'code' => session_id(),
                'os' => $agent->get_os(),
                'client' => $agent->get_browser(),
                'device' => $agent->get_device_type(),
                'status' => 1
            ];
            $session = $db->insert( 'sessions', prepare_keys( $session_data, 'session_' ), prepare_values( $session_data ) );
            if( $session ) {
                // Set browser sessions
                foreach( $user as $k => $v ) {
                    if( !is_numeric( $k ) ) {
                        $_SESSION['user'][ str_replace( 'user_', '', $k ) ] = $v;
                    }
                }
                $_SESSION['db_session'] = $cry->encrypt( $session );
                return [ 1, T('Logged in successfully!') ];
            } else {
                return [ 0, T('User login failed!') ];
            }
        } else {
            return [ 0, T('User login failed due to wrong password!') ];
        }
    }

    /**
     * Updates user password or data
     * @param string $login User Login
     * @param string $old_pass User old password
     * @param string $new_pass User new password
     * @return array
     */
    function update_password( string $login, string $old_pass, string $new_pass ): array {
        $db = new DB();
        $uq = is_numeric( $login ) ? 'user_id = \''.$login.'\'' : 'user_login = \''.$login.'\'';
        $user = $db->select( 'users', 'user_id', $uq, 1 );
        if( isset( $user['user_id'] ) && !empty( $user['user_id'] ) ) {
            $access = $db->select( 'access', 'access_pass', 'access_uid = \''.$user['user_id'].'\'', 1 );
            if( !empty( $access['access_pass'] ) && password_verify( $old_pass, $access['access_pass'] ) ) {
                $update = $db->update( 'access', [ 'access_pass' ], [ password_hash( $new_pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ) ], 'access_uid = \''.$user['user_id'].'\'' );
                return $update ? [ 1, T('Password updated successfully!') ] : [ 0, T('Failed to update password, please try again later') ];
            } else {
                return [ 0, T('The password do not match registered user password!') ];
            }
        } else {
            return [ 0, T('User not found!') ];
        }
    }

    function overwrite_password( string $login, string $new_pass ): array {
        $db = new DB();
        $user = $db->select( 'users', 'user_id', 'user_login = \''.$login.'\'', 1 );
        if( isset( $user['user_id'] ) && !empty( $user['user_id'] ) ) {
            $update = $db->update( 'access', [ 'access_pass' ], [ password_hash( $new_pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ) ], 'access_uid = \''.$user['user_id'].'\'' );
            return $update ? [ 1, T('Password updated successfully!') ] : [ 0, T('Failed to update password, please try again later') ];
        } else {
            return [ 0, T('User not found!') ];
        }
    }

    /**
     * Updates user data
     * @param string $login_or_id
     * @param array $columns
     * @param array $data
     * @return array
     */
    function update( string $login_or_id, array $columns = [], array $data = [] ): array {
        $db = new DB();
        $user = is_numeric( $login_or_id ) ? $db->select( 'users', 'user_id', 'user_id = \''.$login_or_id.'\'', 1 ) : $db->select( 'users', 'user_id', 'user_login = \''.$login_or_id.'\'', 1 );
        if( isset( $user['user_id'] ) && !empty( $user['user_id'] ) ) {
            $keys = $values = [];
            // Columns
            if( !empty( $columns ) ) {
                foreach( $columns as $ck => $cv ) {
                    if( !empty( $cv ) ) {
                        $keys[] = $ck;
                        $values[] = $cv;
                    }
                }
            }
            // Data
            if( !empty( $data ) ) {
                $keys[] = 'user_data';
                $values[] = serialize( $data );
            }
            $update = $db->update( 'users', $keys, $values, 'user_id = \''.$user['user_id'].'\'' );
            return $update ? [ 1, T('Successfully updated user information!') ] : [ 0, T('Failed to update user information, please check log!') ];
        } else {
            return [ 0, T('User not found!') ];
        }
    }

    /**
     * Updates user permissions
     * @param string $login_or_id
     * @param array $perms
     * @return array
     */
    function update_access( string $login_or_id, array $perms = [] ): array {
        $db = new DB();
        $user = is_numeric( $login_or_id ) ? $db->select( 'users', 'user_id', 'user_id = \''.$login_or_id.'\'', 1 ) : $db->select( 'users', 'user_id', 'user_login = \''.$login_or_id.'\'', 1 );
        if( !empty( $user['user_id'] ) && !empty( $perms ) ) {
            $update = $db->update( 'users', [ 'user_access' ], [ serialize( $perms ) ], 'user_id = \''.$user['user_id'].'\'' );
            return $update ? [ 1, T('Successfully updated user permissions!') ] : [ 0, T('Failed to update user permissions, please check log!') ];
        } else {
            return [ 0, T('User not found!') ];
        }
    }

    /**
     * Add default users from config
     */
    function config_users() {
        if( defined('CONFIG') && APPDEBUG ) {
            $c = json_decode( CONFIG, 1 );
            if( isset( $c['users'] ) && is_array( $c['users'] ) ) {
                foreach ( $c['users'] as $u ) {
                    if (isset($u[0]) && isset($u[1])) {
                        $u[3] = isset($u[3]) ? $u[3] : '';
                        $u[4] = isset($u[4]) ? $u[4] : '';
                        $u[5] = isset($u[5]) ? $u[5] : [];
                        $u[6] = isset($u[6]) ? $u[6] : [];
                        $u[7] = isset($u[7]) ? $u[7] : [];
                        $u[8] = isset($u[8]) ? $u[8] : 1;
                        $a = new ACCESS();
                        $this->register($u[0], $u[1], $u[2], $u[3], $u[4], $u[5], $u[6], $u[7], $u[8]);
                    }
                }
            }
            //set_config( 'users_added', 1 );
        }
    }
}

function access_login_ajax() {
    $login = isset( $_POST['login_username'] ) && !empty( $_POST['login_username'] ) ? $_POST['login_username'] : '';
    $pass = isset( $_POST['login_password'] ) && !empty( $_POST['login_password'] ) ? $_POST['login_password'] : '';

    if( !empty( $login ) && !empty( $pass ) ) {
        $a = new ACCESS();
        $login = $a->login($login, $pass);
        echo json_encode( $login );
    } else {
        ef('Missing user login or password!');
    }
}

function access_register_ajax() {
    $login = isset( $_POST['register_username'] ) && !empty( $_POST['register_username'] ) ? $_POST['register_username'] : '';
    $pass = isset( $_POST['register_password'] ) && !empty( $_POST['register_password'] ) ? $_POST['register_password'] : '';
    $email = isset( $_POST['register_email'] ) && !empty( $_POST['register_email'] ) ? $_POST['register_email'] : '';
    $name = isset( $_POST['register_name'] ) && !empty( $_POST['register_name'] ) ? $_POST['register_name'] : '';
    $picture = isset( $_POST['register_picture'] ) && !empty( $_POST['register_picture'] ) ? $_POST['register_picture'] : '';
    $columns = isset( $_POST['register_columns'] ) && !empty( $_POST['register_columns'] ) ? json_decode( $_POST['register_columns'], 1 ) : [];
    $data = isset( $_POST['register_data'] ) && !empty( $_POST['register_data'] ) ? $_POST['register_data'] : [];
    $access = isset( $_POST['register_access'] ) && !empty( $_POST['register_access'] ) ? $_POST['register_access'] : [];
    $status = isset( $_POST['register_status'] ) && !empty( $_POST['register_status'] ) ? $_POST['register_status'] : 1;

    if( !empty( $login ) && !empty( $pass ) ) {
        $a = new ACCESS();
        $register = $a->register( $login, $pass, $email, $name, $picture, $columns, $data, $access, $status );
        echo json_encode( $register );
    } else {
        ef('Missing user login or password!');
    }
}

function access_update_ajax() {
    if( isset( $_POST ) && is_array( $_POST ) ) {
        $cry = Crypto::initiate();
        $id = isset( $_POST['login'] ) && !empty( $_POST['login'] ) ? $cry->decrypt( $_POST['login'] ) : get_user_id();
        unset( $_POST['pre'] );
        unset( $_POST['t'] );
        $a = new ACCESS();
        echo json_encode( $a->update( $id, $_POST ) );
    } else {
        ef('Incomplete data received!');
    }
}

function access_reset_ajax() {

}

function user_change_ajax() {
    $p = $_POST;
    if( isset( $p['old'] ) && isset( $p['new'] ) ) {
        $u = isset( $p['login'] ) ? $p['login'] : get_user_id();
        $a = new ACCESS();
        $r = $a->update_password( $u, $p['old'], $p['new'] );
        echo json_encode( $r );
    } else {
        ef( 'Empty data received!' );
    }
}

/**
 * Renders frontend code for user login
 * @param string $login_title Replacement text for default "Username" title
 * @param string $pass_title Replacement text for default "Password" title
 */
function login_html( string $login_title = 'Username or Email', string $pass_title = 'Password' ) {
    if( user_logged_in() ) {
        return;
    }
    $rand = rand( 0, 9999 );
    $cry = Crypto::initiate();
    ?>
    <div class="login_wrap" data-t data-pre="login_" data-data="log" data-notify="3" data-reload="3" data-empty="login" data-reset="login">
        <div class="inputs">
            <label for="login_name_<?php echo $rand; ?>"><?php E( $login_title ); ?></label>
            <input type="text" id="login_name_<?php echo $rand; ?>" data-key="username" placeholder="<?php E($login_title); ?>" data-log>
            <label for="login_pass_<?php echo $rand; ?>"><?php E( $pass_title ); ?></label>
            <input type="password" id="login_pass_<?php echo $rand; ?>" data-key="password" placeholder="<?php E($pass_title); ?>" data-log>
        </div>
        <button id="aio_init_login" onclick="process_data(this)" data-action="<?php echo $cry->encrypt( 'access_login_ajax' ); ?>"><?php E('Login'); ?></button>
    </div>
    <?php
}

/**
 * Renders frontend code for user registration
 * @param array $columns Additional user columns as assoc array
 * @param bool $columns_before Show columns before (true) or after (false), default true
 * @param array $data User data as json array
 * @param array $access User access permissions as json array
 * @param array $hide Fields to now render
 * @param array $compulsory Fields that are compulsory to submit
 */
function register_html( array $columns = [], bool $columns_before = true, array $data = [], array $access = [], array $hide = [], array $compulsory = [] ) {
    if( user_logged_in() ) {
        return;
    }
    $rand = rand( 0, 9999 );
    $cry = Crypto::initiate();
    $f = new FORM();
    ?>
    <div class="register_wrap" data-t data-pre="register_" data-data="reg" data-notify="5" data-empty="register" data-reset="register">
        <div class="inputs">
            <?php
            $columns_html = '';
            foreach( $columns as $ck => $cv ) {
                $empty_logic = in_array( $ck, $compulsory ) ? 'data-empty' : '';
                $columns_html .= '<label for="'.$ck.'_'.$rand.'">'.T( $cv ).'</label>';
                $columns_html .= '<input type="text" id="'.$ck.'_'.$rand.'" data-key="'.$ck.'" data-array="register_columns" placeholder="'.T( $cv ).'" data-reg '.$empty_logic.'>';
            }
            echo $columns_before ? $columns_html : '';
            $f->text('username','Username','Username','','data-reg data-empty');
            $f->input('password','password','Password','Password','','data-reg data-empty');
            $empty_logic = in_array( 'email', $compulsory ) ? 'data-empty' : '';
            $f->input('email','email','Email','Email','','data-reg '.$empty_logic);
            $defs = [ 'name' => 'Name', 'picture' => 'Picture' ];
            foreach( $defs as $dk => $dv ) {
                if( !in_array( $dk, $hide ) ) {
                    $empty_logic = in_array( $dk, $compulsory ) ? 'data-empty' : '';
                    echo '<label for="register_'.$dk.'_'.$rand.'">'.T( $dv ).'</label>';
                    echo '<input type="text" class="'.$dk.'" data-reg name="'.$dk.'" id="register_'.$dk.'_'.$rand.'" data-key="'.$dk.'" placeholder="'.$dv.'" '.$empty_logic.'>';
                }
            }
            if( !empty( $data ) ) {
                echo '<input type="text" id="register_data_'.$rand.'" data-key="data" value="'.$cry->encrypt( json_encode( $data ) ).'" data-register>';
            }
            if( !empty( $permissions ) ) {
                echo '<input type="text" id="register_access_'.$rand.'" data-key="access" value="'.$cry->encrypt( json_encode( $access ) ).'" data-register>';
            }
            echo !$columns_before ? $columns_html : '';
            ?>
        </div>
        <button onclick="process_data(this)" data-action="<?php echo $cry->encrypt( 'access_register_ajax' ); ?>"><?php E('Register'); ?></button>
    </div>
    <?php
}

/**
 * Gets user id if logged in
 * @return string
 */
function get_user_id(): string {
    return user_logged_in() ? $_SESSION['user']['id'] : '';
}

/**
 * Redirects user to certain url if not logged in
 * @param string $url URL to redirect to
 */
function login_redirect( $url = 'login' ) {
    if( !user_logged_in() ){
        header( "Location:" .APPURL.$url );
        die();
    }
}

/**
 * Checks if user is logged in
 * @return bool
 */
function user_logged_in(): bool {
    return ( isset( $_SESSION['user']['login'] ) && isset( $_SESSION['user']['id'] ) );
}


function verify_user_logged_in() {
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

function logout_ajax() {
    $a = new ACCESS();
    $a->clear_current_sessions();
    if( !isset( $_SESSION['user'] ) ) {
        es('Logged out Successfully!');
    } else {
        ef('Failed to logout!');
    }
}

if( isset( $_GET['logout'] ) ) {
    if ( $_GET['logout'] == 'true' ) {
        logout_ajax();
    }
}

// Set autoload user options as session
if( user_logged_in() ) {
    $db = new DB();
    $options = $db->select('options', 'option_name,option_value', 'option_scope = "' . $_SESSION['user']['id'] . '" AND option_load = 1');
    if (is_array($options)) {
        foreach ($options as $opt) {
            $_SESSION[$opt['option_name']] = $opt['option_value'];
        }
    }
}