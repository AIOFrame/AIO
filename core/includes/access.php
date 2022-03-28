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

        $device = get_user_device();
        $device_type = get_user_device_type();
        $os = get_user_os();
        $browser = get_user_browser();
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
     * @param bool $send_email Send an automated email
     * @return array
     */
    function register( string $login, string $pass, string $email = '', string $name = '', string $picture = '', array $columns = [], array $data = [], array $access = [], string $status = '1', bool $send_email = false ) : array {
        // User login restrictions
        $login = strtolower( $login );
        $valid_name = $this->valid_name( $login );
        if( !empty( $valid_name ) ) {
            return [0, $valid_name];
        }
        $valid_pass = $this->valid_pass( $pass );
        if( !empty( $valid_pass ) ) {
            return [0, $valid_pass];
        }

        $db = new DB();
        if( empty( $name ) ) {
            $name = !empty($data['name']) ? $data['name'] : ucwords(str_replace('_', ' ', $login));
        }

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
                // Email user to notify registration
                if( $send_email ) {
                    $subject = $db->get_option('email_subject_new_user');
                    $content = $db->get_option('email_content_new_user');
                    $subject = !empty($subject) ? str_replace('{{username}}', $login, $subject) : 'Welcome to ' . APPNAME;
                    $content = !empty($content) ? str_replace('{{username}}', $login, $content) : 'You are successfully registered with ' . APPNAME . '. Your login username is ' . $login . ' and your recovery email is ' . $email . '!';
                    $this->mail($email, $subject, $content);
                }
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
            $cry = Crypto::initiate();
            $session_data = [
                'uid' => $user['user_id'],
                'time' => date('Y-m-d H-i-s'),
                'code' => session_id(),
                'os' => get_user_os(),
                'client' => get_user_browser(),
                'device' => get_user_device_type(),
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
     * Initiates Reset Password
     * @param string $login
     * @return array
     */
    function forgot ( string $login ): array {
        $db = new DB();

        // Checks if user with same login name or email exists
        $user = $db->select( 'users', '', '(user_login = \'' . $login . '\' OR user_email = \'' . $login . '\') AND user_status = \'1\'', 1 );
        if( empty( $user ) || empty( $user['user_id'] ) ){
            return [ 0, T('The login or email address does not exist or disabled!') ];
        }

        // Generate and update password
        $cry = Crypto::initiate();
        $pass = $cry->random(8);
        $update = $db->update( 'access', [ 'access_pass' ], [ password_hash( $pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ) ], 'access_uid = \''.$user['user_id'].'\'' );

        if( $update ) {
            elog( 'Access updated ' . $pass );
            // Email that password to user
            $content = T('Your have successfully reset your password, your new password is ').'<span style="font-weight:bold">' . $pass . '</span><br/><br/>' . T('You can login with your new password and change to a new password from profile.');
            $mail = $this->mail($user['user_email'], 'Your password has been Reset!', $content);
            elog( $mail );
            return [ 1, T('Password successfully reset! Please check your registered email.') ];
        } else {
            return [ 0, T('Failed to reset user password!') ];
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
        if( strlen( $new_pass ) <= 8 ) {
            return [ 0, T('User password must be at least 8 characters in length!') ];
        }
        $db = new DB();
        $uq = is_numeric( $login ) ? 'user_id = \''.$login.'\'' : 'user_login = \''.$login.'\'';
        $user = $db->select( 'users', 'user_id,user_email', $uq, 1 );
        if( isset( $user['user_id'] ) && !empty( $user['user_id'] ) ) {
            $access = $db->select( 'access', 'access_pass', 'access_uid = \''.$user['user_id'].'\'', 1 );
            if( !empty( $access['access_pass'] ) && password_verify( $old_pass, $access['access_pass'] ) ) {
                $update = $db->update( 'access', [ 'access_pass' ], [ password_hash( $new_pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ) ], 'access_uid = \''.$user['user_id'].'\'' );
                $content = T('Your have successfully updated your password on').date('d M, Y H:i').'.<br/><br/>' . T('You can login with your new password.');
                $this->mail($user['user_email'], 'Your password has been updated!', $content);
                return $update ? [ 1, T('Password updated successfully!') ] : [ 0, T('Failed to update password, please try again later') ];
            } else {
                return [ 0, T('The password do not match registered user password!') ];
            }
        } else {
            return [ 0, T('User not found!') ];
        }
    }

    /**
     * @param string $email Receiver's email address
     * @param string $subject Subject for the email
     * @param string $content HTML content for the email
     */
    function mail( string $email, string $subject, string $content ) {
        // Email the user
        if( class_exists( 'MAIL' ) ) {
            $e = new MAIL();
            $content = '<div style="text-align:center">'.$content.'</div>';
            $e->send( $email, $subject, $content );
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
                elog( $columns );
                foreach( $columns as $ck => $cv ) {
                    if( !empty( $cv ) ) {
                        $keys[] = 'user_'.$ck;
                        $values[] = $cv;
                    }
                }
            }
            // Data
            if( !empty( $data ) ) {
                $keys[] = 'user_data';
                $values[] = json_encode( $data );
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
                    if ( isset( $u['login'] ) && isset( $u['password'] ) ) {
                        $email = $u['email'] ?? '';
                        $name = $u['name'] ?? '';
                        $picture = $u['picture'] ?? '';
                        $columns = $u['columns'] ?? [];
                        $data = $u['data'] ?? [];
                        $access = $u['access'] ?? [];
                        $status = $u['status'] ?? '1';
                        $r = $this->register( $u['login'], $u['password'], $email, $name, $picture, $columns, $data, $access, $status );
                        elog( json_encode( $r ) );
                    }
                }
            }
        }
    }

    /**
     * Validates password string
     * @param string $pass Password string
     * @return string
     */
    function valid_pass( string $pass = '' ): string {
        return ( strlen( $pass ) <= 8 ) ? T('User password must be at least 8 characters in length!') : '';
    }

    /**
     * Validates username
     * @param string $name Username string
     * @return string
     */
    function valid_name( string $name = '' ): string {
        if( strlen( $name ) <= 6 ) {
            return T('User login must be at least 8 characters in length!');
        } else if( preg_match( '/[^a-z0-9_]/', $name ) ) {
            return T('User login cannot contain special characters!');
        } else {
            return '';
        }
    }

    /**
     * Validates email string
     * @param string $email Email string
     * @return string
     */
    function valid_email( string $email = '' ): string {

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
        ef('User login or password is empty!');
    }
}

function access_forgot_ajax() {
    if( !empty( $_POST['forgot_username'] ) ) {
        $a = new ACCESS();
        $forgot = $a->forgot( $_POST['forgot_username'] );
        echo json_encode( $forgot );
    } else {
        ef('User login is empty!');
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

function access_change_ajax() {
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
 * @param string $forgot_title Replacement text for default "Forgot Password?" title
 * @param string $back_title Replacement text for default "Back to Login" title
 * @param int|string $reload_in Seconds to Reload Page
 * @param int|string $notify_for Seconds to Notify
 * @param string $redirect_to Page to redirect to upon success
 */
function login_html( string $login_title = 'Username or Email', string $pass_title = 'Password', string $forgot_title = 'Forgot Password?', string $back_title = 'Back to Login', int|string $reload_in = 1, int|string $notify_for = 1, string $redirect_to = '' ) {
    if( user_logged_in() ) {
        return;
    }
    $cry = Crypto::initiate();
    $f = new FORM();
    $rand = rand(0,9999);
    $redirect = !empty( $redirect_to ) ? ' data-redirect="'.$redirect_to.'"' : '';
    $callback = !empty( $callback ) ? ' data-redirect="'.$callback.'"' : '';
    ?>
    <div class="login_wrap" data-t data-pre="login_" data-data="log"<?php echo $redirect.$callback; ?> data-notify="<?php echo $notify_for; ?>" data-reload="<?php echo $reload_in; ?>" data-reset="log">
        <form class="inputs">
            <?php
            $f->text(['username_'.$rand,'username'],$login_title,$login_title,'','onkeyup="aio_login_init(event)" data-log required autocomplete="username"','<div>','</div>');
            $f->input('password',['password_'.$rand,'password'],$pass_title,$pass_title,'','onkeyup="aio_login_init(event)" data-log required autocomplete="current-password"','<div>','</div>');
            ?>
        </form>
        <button id="aio_login_init" onclick="process_data(this)" data-action="<?php echo $cry->encrypt( 'access_login_ajax' ); ?>"><?php E('Login'); ?></button>
        <div class="more" onclick="aio_forgot_view()"><?php E($forgot_title); ?></div>
    </div>
    <div class="forgot_wrap" data-t data-pre="forgot_" data-data="forg" data-notify="3" data-reload="3" data-reset="forg" style="display:none;">
        <div class="inputs">
            <?php
            $rand = rand(0,9999);
            $f->text(['username_'.$rand,'username'],$login_title,$login_title,'','onkeyup="aio_login_init(event)" data-key="username" data-forg required="true"','<div>','</div>');
            ?>
        </div>
        <button id="aio_forgot_init" onclick="process_data(this)" data-action="<?php echo $cry->encrypt( 'access_forgot_ajax' ); ?>"><?php E('Reset my Password'); ?></button>
        <div class="more" onclick="aio_login_view()"><?php E($back_title); ?></div>
    </div>
    <?php
    get_script('access');
}

/**
 * Renders frontend code for user registration
 * @param array $columns Additional user columns as assoc array
 * @param bool $columns_before Show columns before (true) or after (false) default fields, default true
 * @param array $data User data as json array
 * @param array $access User access permissions as json array
 * @param array $hide Fields to now render
 * @param array $compulsory Fields that are compulsory to submit
 * @param int|string $reload_in Seconds to Reload Page
 * @param int|string $notify_for Seconds to Notify
 * @param string $redirect_to Page to redirect to upon success
 */
function register_html( array $columns = [], bool $columns_before = true, array $data = [], array $access = [], array $hide = [], array $compulsory = [], int|string $reload_in = 3, int|string $notify_for = 3, string $redirect_to = '' ) {
    if( user_logged_in() ) {
        return;
    }
    $rand = rand( 0, 9999 );
    $cry = Crypto::initiate();
    $f = new FORM();
    $redirect = !empty( $redirect_to ) ? ' data-redirect="'.$redirect_to.'"' : '';
    $callback = !empty( $callback ) ? ' data-callback="'.$callback.'"' : '';
    ?>
    <div class="register_wrap" data-t data-pre="register_" data-data="reg"<?php echo $redirect.$callback; ?> data-notify="<?php echo $notify_for; ?>" data-reload="<?php echo $reload_in; ?>" data-reset="register">
        <div class="inputs">
            <?php
            $columns_html = '';
            foreach( $columns as $ck => $cv ) {
                $empty_logic = in_array( $ck, $compulsory ) ? 'required="true"' : '';
                $columns_html .= '<label for="'.$ck.'_'.$rand.'">'.T( $cv ).'</label>';
                $columns_html .= '<input type="text" id="'.$ck.'_'.$rand.'" data-key="'.$ck.'" data-array="register_columns" placeholder="'.T( $cv ).'" data-reg '.$empty_logic.'>';
            }
            echo $columns_before ? $columns_html : '';
            $min_string = T('Minimum Characters');
            $f->text(['reg_name_'.$rand,'username'],'Username','Username','','data-reg minlength="8" data-minlength="'.$min_string.'" data-help required','<div>','</div>');
            $f->input('password',['reg_pass_'.$rand,'password'],'Password','Password','','data-reg minlength="8" data-minlength="'.$min_string.'" data-help required','<div>','</div>');
            $empty_logic = in_array( 'email', $compulsory ) ? 'required="true"' : '';
            $f->input('email',['reg_email_'.$rand,'email'],'Email','Email','','data-reg data-help required'.$empty_logic,'<div>','</div>');
            $defs = [ 'name' => 'Name', 'picture' => 'Picture' ];
            foreach( $defs as $dk => $dv ) {
                if( !in_array( $dk, $hide ) ) {
                    $empty_logic = in_array( $dk, $compulsory ) ? 'required="true"' : '';
                    echo '<div><label for="register_'.$dk.'_'.$rand.'">'.T( $dv ).'</label>';
                    echo '<input type="text" class="'.$dk.'" data-reg name="'.$dk.'" id="register_'.$dk.'_'.$rand.'" data-key="'.$dk.'" placeholder="'.$dv.'" '.$empty_logic.'></div>';
                }
            }
            if( !empty( $data ) ) {
                echo '<input type="text" id="register_data_'.$rand.'" data-key="data" value="'.$cry->encrypt( json_encode( $data ) ).'" data-register required="true">';
            }
            if( !empty( $permissions ) ) {
                echo '<input type="text" id="register_access_'.$rand.'" data-key="access" value="'.$cry->encrypt( json_encode( $access ) ).'" data-register required="true">';
            }
            echo !$columns_before ? $columns_html : '';
            ?>
        </div>
        <button onclick="process_data(this)" data-action="<?php echo $cry->encrypt( 'access_register_ajax' ); ?>"><?php E('Register'); ?></button>
    </div>
    <?php
}

/**
 * Renders frontend pre-wrap html for user registration
 * @param int|string $reload_in Seconds to Reload Page
 * @param int|string $notify_for Seconds to Notify
 * @param string $redirect_to Page to redirect to upon success
 * @param string $callback Callback a JS Function with response
 * @return void
 */
function register_html_pre( int|string $reload_in = 3, int|string $notify_for = 3, string $redirect_to = '', string $callback = '' ) {
    if( user_logged_in() ) {
        return;
    }
    $redirect = !empty( $redirect_to ) ? ' data-redirect="'.$redirect_to.'"' : '';
    $callback = !empty( $callback ) ? ' data-redirect="'.$callback.'"' : '';
    echo '<div class="register_wrap" data-t data-pre="register_" data-data="reg"'.$redirect.$callback.' data-notify="'.$notify_for.'" data-reload="'.$reload_in.'" data-reset="register">';
}

/**
 * Renders frontend post-wrap html for user registration
 **/
function register_html_post() {
    $cry = Crypto::initiate();
    ?>
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

function user_can( $perm ): bool {
    $uid = isset( $_SESSION['user'] ) && isset( $_SESSION['user']['id'] ) ? $_SESSION['user']['id'] : 0;
    if( is_numeric( $uid ) && $uid > 0 ) {
        $db = new DB();
        $ua = $db->select( 'users', 'user_access', 'user_id = \''.$uid.'\'', 1 );
        $access = !empty( $ua ) && isset( $ua['user_access'] ) ? json_decode( $ua['user_access'] ) : [];
        return in_array( $perm, $access );
    } else {
        return 0;
    }
}