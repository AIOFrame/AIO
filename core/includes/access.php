<?php
if( session_status() === PHP_SESSION_NONE ) {
    session_start();
}
global $options;
if( isset( $_SESSION['user'] ) && isset( $_SESSION['db_session'] ) ) {
    $db = new DB();
    $user_options = $db->select( 'options', '', 'option_scope = \''.get_user_id().'\'' );
    if( !empty( $user_options ) ) {
        foreach( $user_options as $uo ) {
            $options[ $uo['option_name'] ] = $uo['option_value'];
        }
    }
}
// skel( $options );
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

class ACCESS {

    function __construct() {
        ob_start();
        $now = time();

        $device = get_user_device();
        $device_type = get_user_device_type();
        $os = get_user_os();
        $browser = get_user_browser();
        $secure = isset($_SERVER['HTTPS']);
        if( session_status() === PHP_SESSION_NONE ) {
            session_name(str_replace(' ', '', APPNAME));
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

        // Destroy sessions if exceeded time
        $db = new DB();
        $db->delete( 'sessions', 'session_expiry < \''.date('Y-m-d H:i:s').'\'' );
        ob_end_clean();
    }


    function clear_local_sessions(): void {
        setcookie( session_name(), "", time() - 3600, '/', APPURL, '', true );
        session_unset();
        session_destroy();
    }

    function clear_live_sessions(): void {
        $db = new DB();
        $cry = Encrypt::initiate();
        $session_id = isset( $_SESSION['db_session'] ) ? $cry->decrypt( $_SESSION['db_session'] ) : '';
        if( !empty( $session_id ) && is_numeric( $session_id ) ) {
            $db->delete( 'sessions', 'session_id = \''.$session_id.'\'' );
        }
    }

    function clear_current_sessions(): void {
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
            $deleted = $db->delete( 'sessions', 'session_uid = \''.$_SESSION['user_id'].'\'' );
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
            $deleted = $db->delete( 'sessions', 'session_uid = \''.$_SESSION['user_id'].'\'' );
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
     * @param string|array $data User meta to be stored in user_data column
     * @param array $access User Permissions, Custom array with permission name key and boolean value
     * @param string $status User status, 1 for active and 0 for inactive, default 1
     * @param string $email_subject Subject for automated email
     * @param string $email_content Content placeholder for automated email, also adds header and footer templates
     * @return array
     */
    function register( string $login, string $pass, string $email = '', string $name = '', string $picture = '', array $columns = [], string|array $data = [], array $access = [], string $status = '1', string $email_subject = '', string $email_content = '' ) : array {
        $data = !is_array( $data ) ? json_decode( $data, 1 ) : $data;
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
        if( str_contains( $login, '@' ) && empty( $email ) ) {
            $email = $login;
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
            if( empty( $columns['dob'] ) ) {
                unset( $columns['dob'] );
            }
            foreach( $columns as $k => $v ) {
                //elog( $v );
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
                if( !empty( $email_subject ) || !empty( $email_content ) ) {
                    // $subject = $db->get_option('email_subject_new_user');
                    $subject = $email_subject == 1 ? 'Welcome to ' . APPNAME : str_replace('{{username}}', $login, $email_subject);
                    $content = $email_content == 1 ? 'You are successfully registered with ' . APPNAME . '. Your login username is ' . $login . ' and your recovery email is ' . $email . '!' : str_replace('{{username}}', $login, str_replace('{{email}}', $email, str_replace( '{{password}}', $pass, $email_content )));
                    $this->mail( $email, $subject, $content );
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
     * @param string $login User Login
     * @param string $pass User Password
     * @param int $time Login session expiry time in hours
     * @return array
     */
    function login( string $login, string $pass, int $time = 1 ): array {
        $db = new DB();
        $login = strip_tags( preg_replace('/\s+/', '', $login ) );

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
            $cry = Encrypt::initiate();
            $expiry = date('Y-m-d H:i:s', strtotime( date('Y-m-d H:i:s') ) + ( $time * 60 * 60 ) );
            $session_data = [
                'uid' => $user['user_id'],
                'time' => date('Y-m-d H-i-s'),
                'expiry' => $expiry,
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
                return [ $user['user_id'], T('Logged in successfully!') ];

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
        $cry = Encrypt::initiate();
        $pass = $cry->random(8);
        $update = $db->update( 'access', [ 'access_pass' ], [ password_hash( $pass, PASSWORD_DEFAULT, [ 'cost' => 12 ] ) ], 'access_uid = \''.$user['user_id'].'\'' );

        if( $update ) {
            elog( 'Access updated ' . $pass );
            // Email that password to user
            $content = T('Your have successfully reset your password, your new password is ').'<span style="font-weight:bold">' . $pass . '</span><br/><br/>' . T('You can login with your new password and change to a new password from profile.');
            $mail = $this->mail($user['user_email'], 'Your password has been Reset!', $content);
            //elog( $mail );
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
                return [ 0, T('The current password do not match registered user password!') ];
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

    function overwrite_password( string|int $login_or_id, string $new_pass ): array {
        $db = new DB();
        if( is_numeric( $login_or_id ) ) {
            $e = Encrypt::initiate();
            $uid = $e->decrypt( $login_or_id );
            $user = $db->select( 'users', 'user_id', 'user_id = \''.$uid.'\'', 1 );
        } else {
            $user = $db->select( 'users', 'user_id', 'user_login = \''.$login_or_id.'\'', 1 );
        }
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
     * @param array $access User Permissions, Custom array with permission name key and boolean value
     * @return array
     */
    function update( string $login_or_id, string $pass = '', array $columns = [], array $data = [], array $access = [] ): array {
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
            // Password
            if( !empty( $pass ) ) {
                $this->overwrite_password( $login_or_id, $pass );
            }
            // Data
            if( !empty( $data ) ) {
                $keys[] = 'user_data';
                $values[] = json_encode( $data );
            }
            // Access
            if( !empty( $access ) ) {
                $keys[] = 'user_access';
                $values[] = json_encode( $access );
                // Sets that user permissions have changed
                $_SESSION['user']['update'] = 1;
            }
            $update = $db->update( 'users', $keys, $values, 'user_id = \''.$user['user_id'].'\'' );
            return $update ? [ $user['user_id'], T('Successfully updated user information!') ] : [ 0, T('Failed to update user information, please check log!') ];
        } else {
            return [ 0, T('User not found!') ];
        }
    }

    private array $access_options = [
        //[ 'id' => 'ac_logo_l', 'type' => 'upload', 'title' => 'Brand Logo - Light', 'col' => 3 ],
        //[ 'id' => 'ac_logo_d', 'type' => 'upload', 'title' => 'Brand Logo - Dark', 'col' => 3 ],
        [ 'id' => 'ac_bg_l', 'type' => 'upload', 'title' => 'Background - Light', 'col' => 2 ],
        [ 'id' => 'ac_bg_d', 'type' => 'upload', 'title' => 'Background - Dark', 'col' => 2 ],
        [ 'id' => 'ac_show_logo', 'type' => 'slide', 'title' => 'Show Logo ?', 'col' => 2 ],
        [ 'id' => 'ac_show_a11y', 'type' => 'slide', 'title' => 'Show Accessibility', 'col' => 2 ],
        [ 'id' => 'ac_bg_repeat', 'type' => 'slide', 'title' => 'BG Repeat', 'col' => 2 ],
        [ 'id' => 'ac_show_dark', 'type' => 'slide', 'title' => 'Show Dark Toggle', 'col' => 2 ],
        //[ 'id' => 'ac_show_a11y', 'type' => 'slide', 'title' => 'Show Accessibility', 'col' => 4 ],
    ];
    private array $login_options = [
        [ 'id' => 'ac_username_text', 'type' => 'text', 'title' => 'Username Text', 'col' => 3 ],
        [ 'id' => 'ac_password_text', 'type' => 'text', 'title' => 'Password Text', 'col' => 3 ],
        [ 'id' => 'ac_forgot_text', 'type' => 'text', 'title' => 'Forgot Pass Text', 'col' => 3 ],
        [ 'id' => 'ac_reset_btn_text', 'type' => 'text', 'title' => 'Reset Pass Button Text', 'col' => 3 ],
        [ 'id' => 'ac_return_text', 'type' => 'text', 'title' => 'Return to Login Text', 'col' => 3 ],
        [ 'id' => 'ac_login_btn_text', 'type' => 'text', 'title' => 'Login Button Text', 'col' => 3 ],
        [ 'id' => 'ac_register_text', 'type' => 'text', 'title' => 'Register Link Text', 'col' => 3 ],
        [ 'id' => 'ac_register_btn_text', 'type' => 'text', 'title' => 'Register Button Text', 'col' => 3 ],
        [ 'id' => 'ac_session_hours', 'type' => 'select', 'title' => 'Login Session Duration', 'options' => [1=>'1 Hour',8=>'8 Hours',24=>'1 Day',168=>'1 Week'], 'col' => 3 ],
        [ 'id' => 'ac_reset', 'type' => 'slide', 'title' => 'Allow Reset Password', 'col' => 3 ],
        [ 'id' => 'ac_remember', 'type' => 'slide', 'title' => 'Show Remember Options', 'col' => 3 ],
        [ 'id' => 'ac_register', 'type' => 'slide', 'title' => 'Allow Registration?', 'col' => 3 ],
    ];
    private array $register_options = [
        [ 'id' => 'ac_register_name', 'type' => 'slide', 'title' => 'Ask Full Name?', 'col' => 3 ],
    ];

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
     * Removes user by id or login or email
     * @param string $login_or_id
     * @return void
     */
    function remove( string $login_or_id ) {
        $db = new DB();
        // Find existing User
        if( is_numeric( $login_or_id ) ) {
            $user = $db->select( 'users', 'user_id', 'user_id = \''.$login_or_id.'\'', 1 );
        } else if( !empty( $login_or_id ) ) {
            $user = $db->select( 'users', 'user_id', 'user_login = \''.$login_or_id.'\' OR user_email = \''.$login_or_id.'\'', 1 );
        }
        // Remove User
        if( $user ) {
            $db->delete( 'users', 'user_id = \''.$login_or_id.'\'' );
            $db->delete( 'access', 'access_uid = \''.$login_or_id.'\'' );
            $db->delete( 'sessions', 'session_uid = \''.$login_or_id.'\'' );
            es('Successfully removed the user!');
        } else {
            ef('Failed to find existing user!');
        }
    }

    /**
     * Add default users from config
     */
    function config_users(): void {
        // TODO: Automate this so it does not repeat
        if( defined('CONFIG') && APPDEBUG ) {
            //$c = json_decode( CONFIG, 1 );
            //skel( CONFIG );
            if( isset( CONFIG['users'] ) && is_array( CONFIG['users'] ) ) {
                foreach ( CONFIG['users'] as $u ) {
                    if ( isset( $u['login'] ) && isset( $u['password'] ) ) {
                        $email = $u['email'] ?? '';
                        $name = $u['name'] ?? '';
                        $picture = $u['picture'] ?? '';
                        $columns = $u['columns'] ?? [];
                        $data = $u['data'] ?? [];
                        $access = $u['access'] ?? [];
                        $status = $u['status'] ?? '1';
                        $r = $this->register( $u['login'], $u['password'], $email, $name, $picture, $columns, $data, $access, $status );
                        //elog( json_encode( $r ) );
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
        } else if( preg_match( '/[^a-z0-9@_.-]/', $name ) ) {
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
        return '';
    }

    function get_options( int $type = 1 ): array {
        $db = new DB();
        $ao_structure = $type == 1 ? $this->access_options : ( $type == 2 ? $this->login_options : $this->register_options );
        $param_array = [];
        foreach( $ao_structure as $aok ) {
            if( isset( $aok['id'] ) ) {
                $param_array[] = $aok['id'];
            }
        }
        return $db->get_options( $param_array );
    }

    /**
     * Renders ACCESS page options
     * @param int $type
     * @return void
     */
    function access_options( int $type = 1 ): void {
        $f = new FORM();
        $ops = $this->get_options( $type );
        $f->option_params_wrap('ao',2,2);
        //$f->process_params('','ei','',2,2,[],'Successfully saved user access options!','','Are you sure to save user access options','','','','row');
        $ao_structure = $type == 1 ? $this->access_options : ( $type == 2 ? $this->login_options : $this->register_options );
        foreach( $ao_structure as $aok ) {
            $v = isset( $aok['id'] ) && isset( $ops[ $aok['id'] ] ) ? $ops[ $aok['id'] ] : '';
            $c = $aok['col'] ?? '';
            if( $aok['type'] == 'text' ) {
                $f->text( $aok['id'], $aok['title'], $aok['title'], $v, 'data-ao', $c );
            } else if( $aok['type'] == 'upload' ) {
                $f->upload( $aok['id'], $aok['title'], 'Upload', $v, 0, 0, 'upload', 'data-ao', 'jpg,png,svg,bmp', '0.2', 1, '', $c );
            } else if( $aok['type'] == 'slide' ) {
                $check = $ops[ $aok['id'] ] ?? 0;
                //skel( $check );
                $f->slide( $aok['id'], $aok['title'], 'No', 'Yes', $check, 'm', 'data-ao', $c );
            } else if( $aok['type'] == 'select' ) {
                $f->select2( $aok['id'], $aok['title'], $aok['title'], $aok['options'], '', 'data-ao', $c, 1 );
            } else if( isset( $aok['id'] ) ) {
                $f->input( $aok['type'], $aok['id'], $aok['title'], 'No', 'Yes', $check, 'm', 'data-ao', $c );
            }
        }
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
    }
}

function access_login_ajax(): void {
    $login = !empty( $_POST['login_username'] ) ? $_POST['login_username'] : '';
    $pass = !empty( $_POST['login_password'] ) ? $_POST['login_password'] : '';
    $time = !empty( $_POST['login_remember'] ) ? $_POST['login_remember'] : 2;

    if( !empty( $login ) && !empty( $pass ) ) {
        $a = new ACCESS();
        $login = $a->login($login, $pass, $time);
        echo json_encode( $login );
    } else {
        ef('User login or password is empty!');
    }
}

function access_forgot_ajax(): void {
    if( !empty( $_POST['forgot_username'] ) ) {
        $a = new ACCESS();
        $forgot = $a->forgot( $_POST['forgot_username'] );
        echo json_encode( $forgot );
    } else {
        ef('User login is empty!');
    }
}

function access_register_ajax(): void {
    $login = isset( $_POST['register_username'] ) && !empty( $_POST['register_username'] ) ? $_POST['register_username'] : '';
    $pass = isset( $_POST['register_password'] ) && !empty( $_POST['register_password'] ) ? $_POST['register_password'] : '';
    $email = isset( $_POST['register_email'] ) && !empty( $_POST['register_email'] ) ? $_POST['register_email'] : '';
    $name = isset( $_POST['register_name'] ) && !empty( $_POST['register_name'] ) ? $_POST['register_name'] : '';
    $picture = isset( $_POST['register_picture'] ) && !empty( $_POST['register_picture'] ) ? $_POST['register_picture'] : '';
    $columns = isset( $_POST['register_columns'] ) && !empty( $_POST['register_columns'] ) ? json_decode( $_POST['register_columns'], 1 ) : [];
    $data = isset( $_POST['register_data'] ) && !empty( $_POST['register_data'] ) ? $_POST['register_data'] : [];
    $access = isset( $_POST['register_access'] ) && !empty( $_POST['register_access'] ) ? $_POST['register_access'] : [];
    $status = isset( $_POST['register_status'] ) && !empty( $_POST['register_status'] ) ? $_POST['register_status'] : 1;
    $subject = isset( $_POST['register_subject'] ) && !empty( $_POST['register_subject'] ) ? $_POST['register_subject'] : 1;
    $content = isset( $_POST['register_content'] ) && !empty( $_POST['register_content'] ) ? $_POST['register_content'] : 1;

    if( !empty( $login ) && !empty( $pass ) ) {
        $a = new ACCESS();
        $register = $a->register( $login, $pass, $email, $name, $picture, $columns, $data, $access, $status, $subject, $content );
        echo json_encode( $register );
    } else {
        ef('Missing user login or password!');
    }
}

function access_ajax(): void {
    $login = $_POST['login'] ?? ( $_POST['email'] ?? ( $_POST['id'] ?? '' ) );
    $pass = $_POST['pass'] ?? '';
    if( !empty( $login ) ) {

        $e = Encrypt::initiate();

        // Prepare Parameters
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $gender = $_POST['gender'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $phone_code = $_POST['phone_code'] ?? '';
        $picture = $_POST['picture'] ?? '';
        $dob = $_POST['dob'] ?? '';
        $role = $_POST['role'] ?? '';
        $type = $_POST['type'] ?? '';
        $access = !empty( $_POST['access'] ) ? json_decode( $_POST['access'], 1 ) : [];
        $status = $_POST['status'] ?? '';
        $data_bypass = ['login','pass','email','name','dob','picture','gender','phone','phone_code','type','role','access','callback','h','status','pre','t','id','acs','action'];
        $data = $_POST['data'] ?? array_diff_key( $_POST, array_flip($data_bypass));
        $columns_bypass = ['login','pass','email','name','picture','data','callback','h','pre','t','id','acs','access','action'];
        $register_columns = array_diff_key( $_POST, array_flip($columns_bypass));
        $update_columns = [ 'name' => $name, 'email' => $email, 'gender' => $gender, 'dob' => $dob, 'phone' => $phone, 'phone_code' => $phone_code, 'role' => $role, 'type' => $type, 'status' => $status ];

        // Hidden Data
        if( isset( $_POST['h'] ) ) {
            $h = $e->decrypt_array( $_POST['h'] );
            //unset( $_POST['h'] );
            foreach( $h as $hk => $hv ) {
                $register_columns[ $hk ] = $hv;
                $update_columns[ $hk ] = $hv;
            }
        }

        // Check If User Exists
        $db = new DB();

        if( is_numeric( $login ) || isset( $_POST['id'] ) ) {
            $eid = isset( $_POST['id'] ) ? $e->decrypt( $_POST['id'] ) : $login;
            $user_query = 'user_id = \''.$eid.'\'';
        } else {
            $user_query = 'user_login = \''.$login.'\'';
        }
        $user = $db->select( 'users', '', $user_query, 1 );

        // Either Update User or Register
        $a = new ACCESS();

        if( !empty( $user ) ) {
            $r = $a->update( $login, $pass, $update_columns, $data, $access );
        } else {
            $r = $a->register( $login, $pass, $email, $name, $picture, $register_columns, $data, $access, 1 );
        }

        elog( $_POST );

        // Callback Function
        if( !empty( $_POST['callback'] ) && $r[0] ) {
            $new_user = $db->select( 'users', '', 'user_id = \''.$r[0].'\'', 1 );
            $callback = $e->decrypt( $_POST['callback'] );
            $callback( $new_user );
        }

        $r[0] ? es( $r[1], 'Successfully updated user account!' ) : ef('Failed to register or update user account!');
    } else {
        ef('User not found!');
    }
}

/* function access_ajax(): void {
    if( !empty( $_POST['login'] ) ) {
        $cols = [
            'phone_code' => $_POST['phone_code'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'dob' => $_POST['dob'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'type' => $_POST['type'] ?? '',
            'role' => $_POST['role'] ?? ''
        ];
        if( isset( $_POST['h'] ) ) {
            $c = Encrypt::initiate();
            $h = $c->decrypt_array( $_POST['h'] );
            unset( $_POST['h'] );
            foreach( $h as $hk => $hv ) {
                $cols[ $hk ] = $hv;
            }
        }
        $l = $_POST['login'];
        $p = $_POST['pass'] ?? '';
        $email = $_POST['email'] ?? $l;
        $name = $_POST['name'] ?? $l;
        $access = !empty( $_POST['access'] ) ? json_decode( $_POST['access'], 1 ) : [];
        $data_bypass = ['login','pass','email','name','dob','gender','phone','phone_code','access','pre','t','id','acs','action'];
        $a = new ACCESS();
        if( empty( $_POST['id'] ) ) {
            $user = $a->register( $l, $p, $email, $name, '', $cols, array_diff_key( $_POST, array_flip($data_bypass)), $access, 1 );
            $user[0] ? es('Successfully registered User!') : ef($user[1]);
        } else {
            $e = Encrypt::initiate();
            $uid = $e->decrypt( $_POST['id'] );
            //if( !empty( $p ) ) {
                //$a->overwrite_password( $uid, $p );
            //}
            $cols['login'] = $l;
            $cols['email'] = $email;
            $cols['name'] = $name;
            $user = $a->update( $uid, $p, $cols, array_diff_key( $_POST, array_flip($data_bypass) ), $access );
            $user ? es('Successfully updated User!') : ef('Failed to update User!');
        }
    } else {
        ef('Failed to add user due to empty data!');
    }
} */

function access_change_ajax(): void {
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

// TODO: Add language switcher during login
/**
 * Renders frontend code for user login
 * @param string $login_title Replacement text for default "Username" title
 * @param string $pass_title Replacement text for default "Password" title
 * @param string $session_title Replacement text for default "Remember access for" title
 * @param string $login_button_title Replacement text for "Login" button title
 * @param int|string $reload_in Seconds to Reload Page
 * @param int|string $notify_for Seconds to Notify
 * @param string $redirect_to Page to redirect to upon success
 * @param string $class Login, Forgot Password button class
 */
function login_html( string $login_title = 'Username or Email', string $pass_title = 'Password', string $session_title = 'Remember access for', string $login_button_title = 'Login', int|string $reload_in = 1, int|string $notify_for = 1, string $redirect_to = '', string $class = '' ): void {
    if( user_logged_in() ) {
        return;
    }
    $f = new FORM();
    $a = new ACCESS();
    $aos = $a->get_options(1);
    //skel( $aos );
    $login_title = $login_title ?? $aos['ac_username_text'];
    $pass_title = $pass_title ?? $aos['ac_password_text'];
    $login_button_title = !empty( $login_button_title ) ? $login_button_title : ( $aos['login_button_text'] ?? '' );
    $show_reset = $aos['ac_reset'] ?? 0;
    $forgot_pass_title = !empty( $aos['ac_forgot_text'] ) ? $aos['ac_forgot_text'] : T('Forgot Password?');
    $register_title = !empty( $aos['register_text'] ) ? $aos['register_text'] : T('Register');
    $return_text = !empty( $aos['ac_return_text'] ) ? $aos['ac_return_text'] : T('Return to Login');
    $f->pre_process('class="login_wrap"','','log','login_',$notify_for,$reload_in,[],'','','',$redirect_to,'',1); ?>
        <form class="inputs">
            <?php
            $f->text('username',$login_title,$login_title,'','data-log required autocomplete="username" data-click-on-enter="#aio_login_init"','<div class="user_wrap">','</div>');
            $f->input('password','password',$pass_title,$pass_title,'','data-assist data-log required autocomplete="current-password" data-click-on-enter="#aio_login_init"','<div class="pass_wrap">','</div>');
            if( !empty( $aos ) && isset( $aos['ac_remember'] ) && $aos['ac_remember'] == 1 ) {
                $f->radios('remember',$session_title,[1=>'1 Hour',8=>'8 Hours',24=>'1 Day',168=>'1 Week'],1,'data-log',0,'.mb20','','row df fg','.col');
            }
            ?>
        </form>
        <?php
        $f->process_trigger( T( $login_button_title ), 'grad '. $class, 'id="aio_login_init"', 'access_login_ajax' );
        if( $show_reset == 1 ) { ?>
        <div class="more" data-hide=".login_wrap" data-show=".forgot_wrap"><?php E( $forgot_pass_title ); ?></div>
        <?php }
        if( empty( $aos ) || ( isset( $aos['ac_register'] ) && $aos['ac_register'] == 1 ) ) { ?>
            <div class="more" data-hide=".login_wrap" data-show=".register_outer_wrap"><?php E( $register_title ); ?></div>
        <?php }
    $f->post_process();
    if( $show_reset == 1 ) {
        $reset_btn_title = !empty( $aos['ac_reset_btn_text'] ) ? $aos['ac_reset_btn_text'] : T('Reset Password');
        $f->pre_process('class="forgot_wrap"  style="display:none;"','forg','forgot_',$notify_for,$reload_in,[],'','','',$redirect_to,'',1);
        ?>
        <div class="inputs">
            <?php
            $f->text('username',$login_title,$login_title,'','onkeyup="aio_login_init(event)" data-key="username" data-forg required="true"','<div class="forgot_user_wrap">','</div>');
            ?>
        </div>
        <?php $f->process_trigger( $reset_btn_title, 'grad '. $class, 'id="aio_forgot_init"', 'access_forgot_ajax' ); ?>
        <div class="more" data-hide=".forgot_wrap" data-show=".login_wrap"><?php E( $return_text ); ?></div>
    <?php
        $f->post_process();
    }
    if( empty( $aos ) || ( isset( $aos['ac_register'] ) && $aos['ac_register'] == 1 ) ) { ?>
        <div class="register_outer_wrap" style="display: none;">
        <?php register_html(); ?>
        <div class="more" data-hide=".register_outer_wrap" data-show=".login_wrap"><?php E( $return_text ); ?></div>
        </div>
    <?php }
    get_script('access');
    $a->config_users();
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
 * @param string $callback JS Callback
 */
function register_html( array $columns = [], bool $columns_before = true, array $data = [], array $access = [], array $hide = [], array $compulsory = [], int|string $reload_in = 3, int|string $notify_for = 3, string $redirect_to = '', string $callback = '' ): void {
    if( user_logged_in() ) {
        return;
    }
    $rand = rand( 0, 9999 );
    $cry = Encrypt::initiate();
    $f = new FORM();
    $a = new ACCESS();
    $aos = $a->get_options(2);
    $login_title = $ops['username_text'] ?? 'User Login / Email';
    $pass_title = $ops['password_text'] ?? 'Password';
    $register_button_title = $aos['register_button_text'] ?? '';
    $f->pre_process('class="register_wrap"','','reg','register_',$notify_for,$reload_in,[],'',$callback,'',$redirect_to,'',1);
    ?>
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
        $f->text('username',$login_title,$login_title,'','data-reg minlength="8" data-minlength="'.$min_string.'" data-help required','<div>','</div>');
        $f->input('password','password',$pass_title,'Password','','data-reg minlength="8" data-minlength="'.$min_string.'" data-help required','<div>','</div>');
        $empty_logic = in_array( 'email', $compulsory ) ? 'required="true"' : '';
        $f->input('email','email','Email','Email','','data-reg data-help required'.$empty_logic,'<div>','</div>');
        $defs = [ [ 'id' => 'name', 'title' => 'Full Name' ], [ 'id' => 'picture', 'title' => 'Picture', 'type' => 'upload' ], [ 'id' => 'dob', 'title' => 'Date of Birth', 'type' => 'date' ] ];
        foreach( $defs as $df ) {
            if( !in_array( $df['id'], $hide ) ) {
                $req = in_array( $df['id'], $compulsory ) ? 'required' : '';
                if( isset( $df['type'] ) && $df['type'] == 'upload' ) {
                    $f->upload( $df['id'], $df['title'], 'Upload Profile Picture', '', 0, 0, '', 'data-reg', '.jpg,.jpeg', .2, 0, '', '<div>', '</div>' );
                } else if( isset( $df['type'] ) && $df['type'] == 'date' ) {
                    $f->date( $df['id'], $df['title'], '', '', 'data-reg', 'top center', '<div>', 0, 0, '', '', '', '</div>' );
                } else {
                    $f->text( $df['id'], $df['title'], $df['placeholder'] ?? $df['title'], '', 'data-reg', '<div>', '</div>' );
                }
                //echo '<div><label for="register_'.$dk.'_'.$rand.'">'.T( $dv ).'</label>';
                //echo '<input type="text" class="'.$dk.'" data-reg name="'.$dk.'" id="register_'.$dk.'_'.$rand.'" data-key="'.$dk.'" placeholder="'.$dv.'" '.$empty_logic.'></div>';
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
    <?php
    file_upload();
    $f->process_trigger( 'Register', 'grad', 'id="aio_forgot_init"', 'access_register_ajax' );
    $f->post_process();
}

/**
 * Renders frontend pre-wrap html for user registration
 * @param int|string $reload_in Seconds to Reload Page
 * @param int|string $notify_for Seconds to Notify
 * @param string $redirect_to Page to redirect to upon success
 * @param string $callback Callback a JS Function with response
 * @return void
 */
function register_html_pre( int|string $reload_in = 3, int|string $notify_for = 3, string $redirect_to = '', string $callback = '' ): void {
    if( user_logged_in() ) {
        return;
    }
    $f = new FORM();
    $f->pre_process('class="register_wrap"','','reg','register_',$notify_for,$reload_in,[],'',$callback,'',$redirect_to);
}

/**
 * Renders frontend post-wrap html for user registration
 **/
function register_html_post( string $class = '' ): void {
    $f = new FORM();
    $f->process_trigger( 'Register', $class, '', 'access_register_ajax' );
    $f->post_process();
}

/**
 * Gets user id if logged in
 * @return string
 */
function get_user_id(): string {
    return user_logged_in() ? $_SESSION['user']['id'] : 0;
}

/**
 * Gets username if logged in
 * @return string
 */
function get_user_name(): string {
    return user_logged_in() ? $_SESSION['user']['name'] : '';
}

/**
 * Redirects user to certain url if not logged in
 * @param string $url URL to redirect to
 */
function login_redirect( string $url = 'login' ): void {
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

// Obsolete
function verify_user_logged_in(): bool {
    // TODO: Change define to global vars
    if( !defined('LOGGED_IN') ){
        if ( isset($_SESSION['user_login']) && isset($_SESSION['user_id']) && isset($_SESSION['login_string']) ) {

            // Check if encryption matches access
            $pw = select('access', '*', 'access_uid = "' . $_SESSION['user_id'] . '"',1)['access_pass'];
            $hash_check = hash_equals(hash('sha512', $pw . access::get_user_browser()), $_SESSION['login_string']);

            // Check if session is stored online
            $session_check = 0; global $access;
            $ss = select('sessions', '', 'session_uid = "'.$_SESSION['user_id'].'"');
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

function logout_ajax(): void {
    $a = new ACCESS();
    $a->clear_current_sessions();
    if( !isset( $_SESSION['user'] ) ) {
        echo json_encode([1,'Logged out Successfully!']);
    } else {
        echo json_encode([0,'Failed to logout!']);
    }
}

if( isset( $_GET['logout'] ) ) {
    if ( $_GET['logout'] == 'true' ) {
        logout_ajax();
    }
}

/**
 * Function to authorize user logged in else redirect to
 * @param string $login_page Login page url path without site url Ex: admin/login
 * @return void
 */
function restrict_auth( string $login_page = 'admin/login' ): void {
    if( !user_logged_in() ) {
        header('Location: '.APPURL.$login_page);
    }
}

// Set autoload user options as session
/* if( user_logged_in() ) {
    $db = new DB();
    $options = $db->select('options', 'option_name,option_value', 'option_scope = "' . $_SESSION['user']['id'] . '" AND option_load = 1');
    if (is_array($options)) {
        foreach ($options as $opt) {
            $_SESSION[$opt['option_name']] = $opt['option_value'];
        }
    }
} */

/**
 * Check if user has specific permission
 * @param string|array $perms
 * @return bool
 */
function user_can( string|array $perms ): bool {
    $uid = isset( $_SESSION['user'] ) && isset( $_SESSION['user']['id'] ) ? $_SESSION['user']['id'] : 0;
    if( is_numeric( $uid ) && $uid > 0 ) {
        if( isset( $_SESSION['user']['update'] ) && $_SESSION['user']['update'] == 1 ) {
            $db = new DB();
            $ua = $db->select( 'users', 'user_access', 'user_id = \''.$uid.'\'', 1 );
            $access = !empty( $ua ) && isset( $ua['user_access'] ) ? json_decode( $ua['user_access'] ) : [];
            // Update SESSION
            $_SESSION['user']['access'] = json_encode( $access );
            unset( $_SESSION['user']['update'] );
        } else {
            $access = !is_array( $_SESSION['user']['access'] ) ? json_decode( $_SESSION['user']['access'] ) : $_SESSION['user']['access'];
        }
        $perms = !is_array( $perms ) ? explode( ',', $perms ) : $perms;
        $return = [];
        foreach( $perms as $p ) {
            $return[] = in_array( $p, $access ) ? 1 : 0;
        }
        return in_array( 1, $return );
    } else {
        return 0;
    }
}

function user_type_is( $type = '' ): bool {
    return user_logged_in() ? $_SESSION['user']['type'] == $type : 0;
}

function user_role_is( $role = '' ): bool {
    return user_logged_in() ? $_SESSION['user']['role'] == $role : 0;
}

/**
 * Renders User Registration Fields
 * @param string $pre Pre text for keys
 * @param string $data data attribute to get values
 * @param string $array if the data has to be grouped as array
 * @param int $name
 * @param int $last_name
 * @param int $email
 * @param int $pass
 * @param int $gender
 * @param int $dob
 * @param int $phone
 * @return void
 */
function user_registration_fields( string $pre = 'user_', string $data = '', string $array = '', int $name = 2, int $last_name = 2, int $email = 4, int $pass = 4, int $gender = 0, int $dob = 0, int $phone = 0 ): void {

    $f = new FORM();
    global $genders;
    $genders = $genders ?? [ 'Male', 'Female', 'Others', 'No Specify' ];
    global $phone_code;
    $phone_code = $phone_code ?? '+971';
    $data = $data ?? 'data';
    $attr = $array !== '' ? 'data-'.$data.' data-empty data-array="'.$array.'"' : 'data-'.$data.' data-empty';
    //$f->text([$pre.'login', 'login'], '', 'Ex: john_doe', '', $attr.' hidden required', 12 );
    !empty( $name ) ? $f->text('name', ( $last_name == 0 ? 'Full Name' : 'First Name' ), 'Ex: John', 'fake_name', $attr.' required', $name ) : '';
    !empty( $last_name ) ? $f->text('last_name', 'Last Name', 'Ex: Doe', 'fake_name', $attr.' required', $name ) : '';
    !empty( $email ) ? $f->input('email','login', 'Login Email Address', 'Ex: john_doe@gmail.com', 'fake_email', $attr.'  data-help required', $email ) : '';
    !empty( $pass ) ? $f->input('password', 'pass', 'Login Password', '***********', 'fake_password', $attr.' data-help autocomplete="new-password"', $pass ) : '';
    !empty( $gender ) ? $f->select2('gender', 'Gender', 'Choose Gender...', $genders, 'Male', $attr, $gender ) : '';
    !empty( $dob ) ? $f->date('dob', 'Date of Birth', 'Ex: 15-05-1990', 'fake_date', $attr, 'top center', $dob, 0, 0, '', '', date('Y-m-d', strtotime('-18 year', time())) ) : '';
    !empty( $phone ) ? $f->phone( 'phone_code', 'phone', 'Code', 'Phone', 'Ex: +1', 'Ex: 50012345', $phone_code, '', $attr. ' required', $phone ) : '';
}

/**
 * Displays a NO ACCESS content and end further code execution
 * @param string $message Message to be displayed
 * @param string $class Wrapping class
 * @param bool $show_return To show return to previous page
 * @param bool $die To end loading PHP
 * @return void
 */
function no_access( string $message = "You are trying to reach restricted content!", string $class = '', bool $show_return = true, bool $die = true ): void {
    $db = new DB();
    $image = $db->get_option('no_access_image') ?? '';
    $message = T( $message );
    echo '<div class="no_access '.$class.'"><h1 class="tac">'.$message.'</h1>';
    echo !empty( $image ) ? '<img src="'.storage_url($image).'" alt="'.$message.'" class="no_access_image" />' : '';
    echo $show_return ? '<a onclick="history.back()">'.T('Return to Previous Page').'</a>' : '';
    echo '</div>';
    if( $die ) {
        die();
    }
}