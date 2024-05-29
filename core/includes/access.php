<?php
// TODO: OTP login via sms
// TODO: OTP login via email
// TODO: Social Login (Google, Facebook)
// TODO: Remember User Name Feature
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

    function profile( string $wrap_id = '', string $wrap_class = '' ): void {
        $db = new DB();
        $user = $db->select( 'users', '', 'user_id = \''.get_user_id().'\'', 1 );
        $f = new FORM();
        global $genders;
        $genders = $genders ?? [ 'Male', 'Female', 'Others', 'No Specify' ];
        pre( $wrap_id, $wrap_class );
            $form = [
                [ 'i' => 'login', 'n' => 'User Login', 'v' => $user['user_login'] ?? '', 'a' => 'disabled', 'c' => 6 ],
                [ 'i' => 'since', 'n' => 'Registered Since', 'v' => ( !empty( $user['user_since'] ) ? easy_date( $user['user_since'] ) : '' ), 'a' => 'disabled', 'c' => 6 ],
                [ 'i' => 'name', 'n' => 'Full Name', 'a' => 'data-user', 'p' => 'Ex: John Doe','v' => $user['user_name'] ?? '', 'c' => 6 ],
                [ 'i' => 'email', 'n' => 'Email Address', 'a' => 'data-user required data-help', 'p' => 'Ex: john@company.com', 'v' => $user['user_email'] ?? '', 't' => 'email', 'c' => 6 ],
                [ 'i' => 'phone_code', 'i2' => 'phone', 'n' => 'Code', 'n2' => 'Phone', 'a' => 'data-user', 'p' => 'Code', 'p2' => 'Phone', 't' => 'phone', 'v1' => $user['user_phone_code'] ?? '', 'v2' => $user['user_phone'] ?? '', 'c' => 6 ],
                [ 'i' => 'picture', 'n' => 'Profile Picture', 'p' => 'Upload Picture', 't' => 'upload', 'e' => 'jpg,jpeg,bmp,png,gif,webp', 's' => 1, 'a' => 'data-user', 'v' => $user['user_picture'] ?? '', 'c' => 6 ],
                [ 'i' => 'gender', 'n' => 'Gender', 'p' => 'Select gender...', 't' => 'select', 'o' => $genders, 'a' => 'data-user', 'v' => $user['user_gender'] ?? '', 'c' => 6 ],
                [ 'i' => 'dob', 'n' => 'Date of Birth', 't' => 'date', 'a' => 'data-user', 'v' => $user['user_dob'] ?? '', 'c' => 6 ],
            ];
            $f->pre_process('','','user');
                $f->form( $form, 'row' );
                $f->process_trigger('Update Profile','r5 xl mb0','','update_user_ajax','.tac');
            $f->post_process();
        post();
        file_upload();
    }

    function change( string $wrap_id = '', string $wrap_class = '' ): void {
        $f = new FORM();
        pre( $wrap_id, $wrap_class );
            $f->pre_process(' ','change_password_ajax','ps','',[],'Successfully updated user password!');
                _r();
                    $min_string = T('Minimum Characters');
                    $f->inputs('password',[['pass_old','Old Password'],['pass','New Password','','','data-length-notify="Password minimum length is 8 Characters"']],'data-ps minlength="8" data-minlength="'.$min_string.'" data-help required',6);
                r_();
                $f->process_trigger('Change Password','r5 xl mb0');
            $f->post_process();
        post();
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
     * @param string $pass
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
    public array $session_times = [ 1 => '1 Hour' , 8 => '8 Hours', 24 => '1 Day', 168 => '1 Week' ];
    private array $access_options = [
        [ 'i' => 'ac_style', 't' => 'radios', 'n' => 'Access Panel Style', 'c' => 12, 'o' => [ 'l f' => 'Left Full Panel', 'c f' => 'Center Full Panel', 'c b' => 'Center Boxed', 'r f' => 'Right Full Panel' ], 'v' => 'c b', '_i' => '.col-3', 'iw' => 'row' ],
        [ 't' => 'break' ],
        [ 'i' => 'ac_logo_l', 't' => 'file', 'n' => 'Brand Logo - Light', 'p' => 'Upload', 'c' => 4, 'e' => 'svg,png,jpg,jpeg', 's' => .5 ],
        [ 'i' => 'ac_logo_d', 't' => 'upload', 'n' => 'Brand Logo - Dark', 'p' => 'Upload', 'c' => 4, 'e' => 'svg,png,jpg,jpeg', 's' => .5 ],
        [ 'i' => 'ac_show_logo', 't' => 'slide', 'n' => 'Show Logo ?', 'c' => 4, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        [ 'i' => 'ac_bg_l', 't' => 'upload', 'n' => 'Background - Light', 'p' => 'Upload', 'c' => 4, 'e' => 'svg,png,jpg,jpeg', 's' => 1 ],
        [ 'i' => 'ac_bg_d', 't' => 'upload', 'n' => 'Background - Dark', 'p' => 'Upload', 'c' => 4, 'e' => 'svg,png,jpg,jpeg', 's' => 1 ],
        [ 'i' => 'ac_bg_style', 't' => 'radios', 'n' => 'Background Style', 'c' => 4, 'o' => [ 'no-repeat center / cover' => 'Cover', 'repeat center' => 'Pattern' ], '_i' => '.col-6', 'iw' => 'row', 'v' => 'no-repeat center / cover' ],
        //[ 'i' => 'ac_show_a11y', 't' => 'slide', 'n' => 'Show Accessibility', 'c' => 4, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        //[ 'i' => 'ac_show_dark', 't' => 'slide', 'n' => 'Show Dark Mode Toggle', 'c' => 4, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        [ 'i' => 'ac_show_labels', 't' => 'slide', 'n' => 'Show Input Labels?', 'c' => 4, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        [ 'i' => 'ac_show_region', 't' => 'slide', 'n' => 'Show Region Picker?', 'c' => 4, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        [ 'i' => 'ac_show_lang', 't' => 'slide', 'n' => 'Let change Language?', 'c' => 4, 'no' => 'No', 'yes' => 'Show', 'v' => 1 ],
        //[ 'i' => 'ac_labels_align', 't' => 'radio', 'n' => 'Labels Text Alignment', 'p' => 'Select...', 'o' => [ 'tal' => 'Left' , 'tac' => 'Center', 'tar' => 'Right' ], 'c' => 4, '_ip' => '.col-4', 'iw' => 'row', 'v' => 'tal' ],
        //[ 'i' => 'ac_inputs_align', 't' => 'radio', 'n' => 'Inputs Text Alignment', 'p' => 'Select...', 'o' => [ 'tal' => 'Left' , 'tac' => 'Center', 'tar' => 'Right' ], 'c' => 4, '_ip' => '.col-4', 'iw' => 'row', 'v' => 'tal' ],
        //[ 'i' => 'ac_buttons_align', 't' => 'radio', 'n' => 'Buttons Text Alignment', 'p' => 'Select...', 'o' => [ 'tal' => 'Left' , 'tac' => 'Center', 'tar' => 'Right' ], 'c' => 4, '_ip' => '.col-4', 'iw' => 'row', 'v' => 'tal' ],
        //[ 'i' => 'ac_alert_time', 't' => 'number', 'n' => 'Alert Visible Duration (sec)', 'c' => 4, 'v' => 4 ],
        //[ 'i' => 'ac_reload_time', 't' => 'number', 'n' => 'Reload in Duration (sec)', 'c' => 4, 'v' => 4 ],
        //[ 'id' => 'ac_show_a11y', 'type' => 'slide', 'n' => 'Show Accessibility', 'c' => 4 ],
    ];
    private array $login_options = [
        [ 'i' => 'ac_username_text', 'n' => 'Username Text', 'c' => 3, 'p' => 'Ex: User Login' ],
        [ 'i' => 'ac_username_icon', 'n' => 'Username Icon', 'c' => 3, 'p' => 'Ex: account_circle', 'v' => 'account_circle' ],
        [ 'i' => 'ac_password_text', 'n' => 'Password Text', 'c' => 3, 'p' => 'Ex: Password' ],
        [ 'i' => 'ac_password_icon', 'n' => 'Password Icon', 'c' => 3, 'p' => 'Ex: lock', 'v' => 'lock' ],
        [ 'i' => 'ac_pass_show_icon', 'n' => 'Password Visible Icon', 'c' => 3, 'p' => 'Ex: visibility', 'v' => 'visibility' ],
        [ 'i' => 'ac_pass_hide_icon', 'n' => 'Password Hide Icon', 'c' => 3, 'p' => 'Ex: visibility_off', 'v' => 'visibility_off' ],
        [ 'i' => 'ac_login_btn_text', 'n' => 'Login Button Text', 'c' => 3, 'p' => 'Ex: Sign In or Login or Get In etc.' ],
        [ 'i' => 'ac_login_btn_icon', 'n' => 'Login Button Icon', 'c' => 3, 'p' => 'Ex: login', 'v' => 'login' ],
        [ 'i' => 'ac_reset', 't' => 'slide', 'n' => 'Allow Password Reset?', 'c' => 3, 'no' => 'No', 'yes' => 'Allow', 'v' => 1 ],
        [ 'i' => 'ac_forgot_text', 'n' => 'Forgot Password Link Text', 'c' => 3, 'p' => 'Ex: Forgot Password?' ],
        [ 'i' => 'ac_reset_btn_text', 'n' => 'Reset Password Button Text', 'c' => 3, 'p' => 'Ex: Reset Password' ],
        [ 'i' => 'ac_reset_btn_icon', 'n' => 'Reset Password Icon', 'c' => 3, 'p' => 'Ex: forward_to_inbox', 'v' => 'forward_to_inbox' ],
        [ 'i' => 'ac_return_text', 'n' => 'Return to Login Text', 'c' => 6, 'p' => 'Ex: Back to Login' ],
        [ 'i' => 'ac_remember_text', 'n' => 'Remember Me For Text', 'c' => 3, 'p' => 'Ex: Remember Me' ],
        [ 'i' => 'ac_remember', 't' => 'slide', 'n' => 'Show Remember Options', 'c' => 3, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        //[ 'i' => 'ac_session_hours', 't' => 'select', 'n' => 'Default Session Duration', 'p' => 'Select...', 'o' => [ 1 => '1 Hour' , 8 => '8 Hours', 24 => '1 Day', 168 => '1 Week' ], 'c' => 3 ],
    ];
    private array $register_options = [
        [ 'i' => 'ac_register', 't' => 'slide', 'n' => 'Allow Registration?', 'c' => 3, 'no' => 'No', 'yes' => 'Allow', 'v' => 1 ],
        [ 'i' => 'ac_register_link_text', 'n' => 'Register Link Text', 'c' => 3, 'p' => 'Ex: Dont have an account?' ],
        [ 'i' => 'ac_register_btn_text', 'n' => 'Register Button Text', 'c' => 3, 'p' => 'Ex: Sign Up' ],
        [ 'i' => 'ac_register_btn_icon', 'n' => 'Register Button Icon', 'c' => 3, 'cl' => 'mica', 'p' => 'Ex: how_to_reg', 'v' => 'how_to_reg', 'a' => 'data-icon' ],
        [ 't' => 'break' ],
        [ 'i' => 'ac_register_login', 't' => 'slide', 'n' => 'Ask User Login?', 'c' => 3, 'no' => 'Hide', 'yes' => 'Show', 'v' => 1 ],
        [ 'i' => 'ac_register_login_must', 't' => 'slide', 'n' => 'Required', 'c' => 3, 'no' => 'Optional', 'yes' => 'Must' ],
        [ 'i' => 'ac_register_login_text', 'n' => 'User Login Text', 'c' => 3, 'p' => 'Ex: User Login' ],
        [ 'i' => 'ac_register_login_icon', 'n' => 'User Login Icon', 'c' => 3, 'p' => 'Ex: account_circle', 'v' => 'account_circle' ],
        [ 'i' => 'ac_register_name', 't' => 'slide', 'n' => 'Ask Full Name?', 'c' => 3, 'no' => 'Hide', 'yes' => 'Show' ],
        [ 'i' => 'ac_register_name_must', 't' => 'slide', 'n' => 'Required', 'c' => 3, 'no' => 'Optional', 'yes' => 'Must' ],
        [ 'i' => 'ac_register_name_text', 'n' => 'Full Name Text', 'c' => 3, 'p' => 'Ex: Full Name' ],
        [ 'i' => 'ac_register_name_icon', 'n' => 'User Name Icon', 'c' => 3, 'p' => 'Ex: record_voice_over', 'v' => 'record_voice_over' ],
        [ 'i' => 'ac_register_pic', 't' => 'slide', 'n' => 'Ask User Picture?', 'c' => 3, 'no' => 'Hide', 'yes' => 'Show' ],
        [ 'i' => 'ac_register_pic_must', 't' => 'slide', 'n' => 'Required', 'c' => 3, 'no' => 'Optional', 'yes' => 'Must' ],
        [ 'i' => 'ac_register_pic_text', 'n' => 'User Picture Text', 'c' => 3, 'p' => 'Ex: Upload Picture' ],
        [ 'i' => 'ac_register_email_icon', 'n' => 'User Email Icon', 'c' => 3, 'p' => 'Ex: mail', 'v' => 'mail' ],
        [ 'i' => 'ac_register_dob', 't' => 'slide', 'n' => 'Ask Date of Birth?', 'c' => 3, 'no' => 'Hide', 'yes' => 'Show' ],
        [ 'i' => 'ac_register_dob_must', 't' => 'slide', 'n' => 'Required', 'c' => 3, 'no' => 'Optional', 'yes' => 'Must' ],
        [ 'i' => 'ac_register_dob_text', 'n' => 'User DOB Text', 'c' => 6, 'p' => 'Ex: Date of Birth' ],
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
    function remove( string $login_or_id ): void {
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

    function get_options( bool $get_access = true, bool $get_login = true, bool $get_register = true ): array {
        $db = new DB();
        $fields = array_merge( ( $get_access ? $this->access_options : [] ), ( $get_login ? $this->login_options : [] ), ( $get_register ? $this->register_options : [] ) );
        //skel( $fields );
        $param_array = [];
        foreach( $fields as $aok ) {
            if( isset( $aok['i'] ) ) {
                $param_array[] = $aok['i'];
            }
        }
        //skel( $param_array );
        //skel( $db->get_options( $param_array ) );
        return $db->get_options( $param_array );
    }

    /**
     * Renders ACCESS page options
     * @param bool $show_access Show access page options (Default true)
     * @param bool $show_login Show login form options (Default true)
     * @param bool $show_register Show register form options (Default true)
     * @param string $form_type 'row' or 'settings' type to render inputs
     * @return void
     */
    function options( bool $show_access = true, bool $show_login = true, bool $show_register = true, string $form_type = 'row' ): void {
        // TODO: Update access options
        $f = new FORM();
        $o = new OPTIONS();
        $form = array_merge( ( $show_access ? $this->access_options : [] ), ( $show_login ? $this->login_options : [] ), ( $show_register ? $this->register_options : [] ) );
        // skel( $form );
        $o->form( $form, $form_type, 1 );
        //$ops = $this->get_options( $type );
        //$f->process_params('','ei','',2,2,[],'Successfully saved user access options!','','Are you sure to save user access options','','','','row');
        /* $ao_structure = $type == 1 ? $this->access_options : ( $type == 2 ? $this->login_options : $this->register_options );
        foreach( $ao_structure as $aok ) {
            $v = isset( $aok['id'] ) && isset( $ops[ $aok['id'] ] ) ? $ops[ $aok['id'] ] : '';
            $c = $aok['c'] ?? '';
            if( $aok['type'] == 'text' ) {
                $f->text( $aok['id'], $aok['n'], $aok['n'], $v, 'data-ao', $c );
            } else if( $aok['type'] == 'upload' ) {
                $f->upload( $aok['id'], $aok['n'], 'Upload', $v, 0, 0, 'upload', 'data-ao', 'jpg,jpeg,png,svg,bmp', '0.2', 1, '', $c );
            } else if( $aok['type'] == 'slide' ) {
                $check = $ops[ $aok['id'] ] ?? 0;
                //skel( $check );
                $f->slide( $aok['id'], $aok['n'], 'No', 'Yes', $check, 'm', 'data-ao', $c );
            } else if( $aok['type'] == 'select' ) {
                $f->select2( $aok['id'], $aok['n'], $aok['n'], $aok['options'], '', 'data-ao', $c, 1 );
            } else if( isset( $aok['id'] ) ) {
                $f->input( $aok['type'], $aok['id'], $aok['n'], 'No', 'Yes', $check, 'm', 'data-ao', $c );
            }
        }
        $f->process_options('Save Options','store grad','','.col-12 tac'); */
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

function update_user_ajax(): void {
    $uid = get_user_id();
    $cols = [];
    !empty( $_POST['email'] ) ? $cols['email'] = $_POST['email'] : '';
    !empty( $_POST['name'] ) ? $cols['name'] = $_POST['name'] : '';
    !empty( $_POST['phone'] ) ? $cols['phone'] = $_POST['phone'] : '';
    !empty( $_POST['phone_code'] ) ? $cols['phone_code'] = $_POST['phone_code'] : '';
    !empty( $_POST['picture'] ) ? $cols['picture'] = $_POST['picture'] : '';
    !empty( $_POST['dob'] ) ? $cols['dob'] = $_POST['dob'] : '';
    !empty( $_POST['gender'] ) ? $cols['gender'] = $_POST['gender'] : '';
    if( !empty( $cols ) ) {
        $a = new ACCESS();
        $r = $a->update( $uid, '', $cols );
        elog( $r );
        $r[0] == 1 ? es( $r[1] ) : ef( $r[1] );
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
 * @param string $user_title Replacement text for default "Username" title
 * @param string $pass_title Replacement text for default "Password" title
 * @param string $session_title Replacement text for default "Remember access for" title
 * @param string $login_button_title Replacement text for "Login" button title
 * @param string $login_button_icon Replacement icon for "Login" button
 * @param string $redirect_to Page to redirect to upon success
 * @param string $class Login, Forgot Password button class
 */
function access_html( string $user_title = 'Username or Email', string $pass_title = 'Password', string $session_title = 'Remember access for', string $login_button_title = 'Login', string $login_button_icon = 'login', string $redirect_to = '', string $class = '' ): void {
    if( user_logged_in() ) {
        return;
    }
    $f = new FORM();
    $a = new ACCESS();
    global $options;
    //skel( $options );
    $aos = $a->get_options(1,1,1);
    //skel( $aos );
    $user_title = !empty( $user_title ) ? $user_title : ( $aos['ac_username_text'] ?? 'User Login' );
    $icon_class = 'icon_preview ' . ( $options['icon_class'] ?? 'mico' );
    $user_icon = $aos['ac_username_icon'] ?? 'account_circle';
    $pass_title = !empty( $pass_title ) ? $pass_title : ( $aos['ac_password_text'] ?? 'Password' );
    $session_title = !empty( $session_title ) ? $session_title : ( $aos['ac_remember_text'] ?? 'Remember Me?' );
    $pass_icon = $aos['ac_password_icon'] ?? 'lock';
    $pass_show_icon = $aos['ac_pass_show_icon'] ?? 'visibility';
    $pass_hide_icon = $aos['ac_pass_hide_icon'] ?? 'visibility_off';
    $login_button_title = !empty( $login_button_title ) ? $login_button_title : ( $aos['ac_login_btn_text'] ?? 'Login' );
    $login_button_icon = !empty( $login_button_icon ) ? $login_button_icon : ( $aos['ac_login_btn_icon'] ?? 'login' );
    $show_reset = $aos['ac_reset'] ?? 1;
    $allow_register = $aos['ac_register'] ?? 0;
    $forgot_pass_title = !empty( $aos['ac_forgot_text'] ) ? $aos['ac_forgot_text'] : T('Forgot Password?');
    $register_title = !empty( $aos['ac_register_link_text'] ) ? $aos['ac_register_link_text'] : T('Create Account');
    $return_text = !empty( $aos['ac_return_text'] ) ? $aos['ac_return_text'] : T('Return to Login');
    $show_labels = !empty( $aos['ac_show_labels'] ) && $aos['ac_show_labels'] == 1 ? $aos['ac_show_labels'] : 0;
    $show_sessions = !empty( $aos['ac_remember'] ) && $aos['ac_remember'] == 1 ? 1 : 0;
    _d( 'access_inner_wrap' );
        _d( 'login_outer_wrap' );
            $f->pre_process('class="login_wrap"','access_login_ajax','log','login_',[],'','','',$redirect_to,'',1);
                pre( '', 'inputs', 'form' );
                    $session_times = [ 1 => '1 Hour' , 8 => '8 Hours' , 24 => '1 Day', 168 => '1 Week' ];
                    $login_form = [
                        [ 'i' => 'username', 'n' => ( $show_labels ? $user_title : '' ), 'p' => $user_title, 'c' => '.col-12 rel user_wrap', 'a' => 'data-log required autocomplete="username" data-click-on-enter="#aio_login_init"', 'p_' => __el( 'div', $icon_class, $user_icon ) . __post() ],
                        [ 'i' => 'password', 'n' => ( $show_labels ? $pass_title : '' ), 'p' => $pass_title, 'c' => '.col-12 rel pass_wrap', 'a' => 'data-assist data-log required autocomplete="current-password" data-click-on-enter="#aio_login_init"', 't' => 'password', 'p_' => __el( 'div', $icon_class, $pass_icon ) . __post() ],
                    ];
                    if( $show_sessions ) {
                        $login_form[] = [ 'i' => 'remember', 'n' => ( $show_labels ? $session_title : '' ), 'o' => $session_times, 's' => 1, 'd' => 'log', 't' => 'radios', 'iw' => 'row df fg', '_ip' => '.col' ];
                    }
                    $f->form( $login_form, 'row' );
                    div( 'dn', __div( 'password_visibility', __div( $icon_class . ' on', $pass_show_icon ) . __div( $icon_class . ' off', $pass_hide_icon ) ), '', 'data-password_toggle_template' );
                post( 'form' );
                $f->process_trigger( T( $login_button_title ), 'grad xxl'. $class, 'id="aio_login_init"', '', '', '', 'button', '', $login_button_icon, $icon_class . ' abs r2' );
                if( $show_reset == 1 ) {
                    div( 'more p5', T( $forgot_pass_title ), '', 'data-hide=".login_wrap" data-show=".forgot_wrap"' );
                }
                if( $allow_register ) {
                    div( 'more p5', T( $register_title ), '', 'data-hide=".login_wrap" data-show=".register_outer_wrap"' );
                }
            $f->post_process();
            if( $show_reset == 1 ) {
                $reset_btn_title = !empty( $aos['ac_reset_btn_text'] ) ? $aos['ac_reset_btn_text'] : T('Reset Password');
                $reset_btn_icon = !empty( $aos['ac_reset_btn_icon'] ) ? $aos['ac_reset_btn_icon'] : 'forward_to_inbox';
                $f->pre_process('class="forgot_wrap"  style="display:none;"','forg','forgot_','',[],'','','',$redirect_to,'',1);
                pre( '', 'inputs' );
                    $reset_form = [
                        [ 'i' => 'username', 'n' => ( $show_labels ? $user_title : '' ), 'p' => $user_title, 'd' => 'onkeyup="aio_login_init(event)" data-key="username" data-forg required="true"', 'c' => '.col-12 rel reset_user_wrap', 'p_' => __el( 'div', $icon_class, $user_icon ) . __post() ],
                    ];
                    $f->form( $reset_form );
                    //$f->text('username',($show_labels?$user_title:''),$user_title,'','onkeyup="aio_login_init(event)" data-key="username" data-forg required="true"','.forgot_user_wrap', );
                post();
                $f->process_trigger( $reset_btn_title, 'grad '. $class, 'id="aio_forgot_init"', 'access_forgot_ajax', '', '', 'button', '', $reset_btn_icon, 'mico abs r2' );
                div( 'more', T( $return_text ), '', 'data-hide=".forgot_wrap" data-show=".login_wrap"' );
                $f->post_process();
            }
        d_();
        //skel( $aos );
        if( empty( $aos ) || ( isset( $aos['ac_register'] ) && $aos['ac_register'] == 1 ) ) {
            pre( '', 'register_outer_wrap dn' );
                register_html();
                div( 'more p5', T( $return_text ), '', 'data-hide=".register_outer_wrap" data-show=".login_wrap"' );
            post();
        }
        get_script('access');
    d_();

    // Addons
    _d( 'access_addons_wrap' );
        // Language Picker
        if( isset( $aos['ac_show_lang'] ) && $aos['ac_show_lang'] == 1 ) {
            _d('access_lang_wrap');
                language_picker( 'languages', 'lang', 0 );
                get_script( 'language' );
            d_();
        }
        // Region Picker
        if( isset( $aos['ac_show_region'] ) && $aos['ac_show_region'] == 1 ) {
            _d('access_region_wrap');
                //skel($aos);
            d_();
        }
    d_();

    $a->config_users();
}

/**
 * Renders frontend code for user registration
 * @param array $columns Additional user columns as assoc array
 * @param bool $prepend_columns Show columns before (true) or after (false) default fields, default true
 * @param array $data User data as json array
 * @param array $access User access permissions as json array
 * @param array $hide Fields to now render
 * @param array $compulsory Fields that are compulsory to submit
 * @param int|string $reload_in Seconds to Reload Page
 * @param int|string $notify_for Seconds to Notify
 * @param string $redirect_to Page to redirect to upon success
 * @param string $callback JS Callback
 */
function register_html( array $columns = [], bool $prepend_columns = true, array $data = [], array $access = [], array $hide = [], array $compulsory = [], int|string $reload_in = 3, int|string $notify_for = 3, string $redirect_to = '', string $callback = '' ): void {
    if( user_logged_in() ) {
        return;
    }
    global $options;
    $f = new FORM();
    $a = new ACCESS();
    $aos = $a->get_options(1,0,1);
    $icon_class = 'icon_preview ' . ( $options['icon_class'] ?? 'mico' );
    $login_title = $ops['username_text'] ?? 'User Login / Email';
    $pass_title = $ops['password_text'] ?? 'Password';
    $register_button_text = $aos['ac_register_btn_text'] ?? 'Register';
    $register_button_icon = $aos['ac_register_btn_icon'] ?? 'how_to_reg';
    $user_icon = $aos['ac_username_icon'] ?? 'account_circle';
    $email_icon = $aos['ac_register_email_icon'] ?? 'email';
    $pass_icon = $aos['ac_password_icon'] ?? 'lock';
    $show_labels = !empty( $aos['ac_show_labels'] ) && $aos['ac_show_labels'] == 1 ? $aos['ac_show_labels'] : 0;
    $show_login = !empty( $aos['ac_register_login'] ) && $aos['ac_register_login'] == 1 ? $aos['ac_register_login'] : 0;
    $f->pre_process('class="register_wrap"','','reg','register_',[],'',$callback,'',$redirect_to,'',1);
        pre( '', 'inputs' );
            $append_fields = [];
            foreach( $columns as $ck => $cv ) {
                $append_fields[] = [ 'i' => $ck, 'n' => ( $show_labels ? $cv : '' ), 'a' => 'data-reg data-array="register_columns"', 'r' => in_array( $ck, $compulsory ) ];
                //$empty_logic = in_array( $ck, $compulsory ) ? 'required="true"' : '';
                //$columns_html .= $show_labels ? '<label for="'.$ck.'_'.$rand.'">'.T( $cv ).'</label>' : '';
                //$columns_html .= '<input type="text" id="'.$ck.'_'.$rand.'" data-key="'.$ck.'" data-array="register_columns" placeholder="'.T( $cv ).'" data-reg '.$empty_logic.'>';
            }
            //echo $columns_before ? $columns_html : '';
            $min_string = T('Minimum Characters');
            $reg_form[] = [ 'i' => 'email', 'n' => ( $show_labels ? 'Email' : '' ), 'p' => 'Ex: john_doe@email.com', 'a' => 'data-reg data-validate', 'r' => 1, 'c' => '.col-12 rel reg_email_wrap', 'p_' => __el( 'div', $icon_class, $email_icon ) . __post(), 't' => 'email' ];
            $show_login ? $reg_form[] = [ 'i' => 'username', 'n' => ( $show_labels ? $login_title : '' ), 'p' => $login_title, 'a' => 'data-reg minlength="8" data-minlength="'.$min_string.'" data-help', 'r' => 1, 'c' => '.col-12 rel reg_user_wrap', 'p_' => __el( 'div', $icon_class, $user_icon ) . __post() ] : '';
            $reg_form[] = [ 'i' => 'password', 'n' => ( $show_labels ? $pass_title : '' ), 'p' => $pass_title, 'a' => 'data-reg minlength="8" data-minlength="'.$min_string.'" data-help', 'r' => 1, 'c' => '.col-12 rel reg_pass_wrap', 't' => 'password', 'p_' => __el( 'div', $icon_class, $pass_icon ) . __post() ];
            $reg_form = $prepend_columns ? array_merge( $append_fields, $reg_form ) : array_merge( $reg_form, $append_fields );
            //$f->text('username',$login_title,$login_title,'','data-reg minlength="8" data-minlength="'.$min_string.'" data-help required','<div>','</div>');
            //$f->input('password','password',$pass_title,'Password','','data-reg minlength="8" data-minlength="'.$min_string.'" data-help required','<div>','</div>');
            //$empty_logic = in_array( 'email', $compulsory ) ? 'required="true"' : '';
            //$reg_form[] = [ 'i' => 'email', 'n' => 'Email', 'p' => 'Ex: john_doe@email.com', 'a' => 'data-reg data-help', 'r' => in_array( 'email', $compulsory ), 'c' => 12, 't' => 'email' ];
            //$f->input('email','email','Email','Email','','data-reg data-help required'.$empty_logic,'<div>','</div>');
            // $defs = [ [ 'id' => 'name', 'n' => 'Full Name' ], [ 'id' => 'picture', 'n' => 'Picture', 'type' => 'upload', 'c' => 6 ], [ 'id' => 'dob', 'n' => 'Date of Birth', 'type' => 'date', 'c' => 6 ] ];
            /* foreach( $defs as $df ) {
                if( !in_array( $df['id'], $hide ) ) {
                    $req = in_array( $df['id'], $compulsory ) ? 'required' : '';
                    if( isset( $df['type'] ) && $df['type'] == 'upload' ) {
                        //$f->upload( $df['id'], $df['n'], 'Upload Profile Picture', '', 0, 0, '', 'data-reg', 'jpg,jpeg,png', .2, 0, '', '<div>', '</div>' );
                        $reg_form[] = [ 'i' => $df['id'], 'n' => $df['n'], 'p' => 'Upload', 't' => 'upload', 'e' => 'jpg,jpeg,png,svg', 'd' => 'reg', 'c' => 6 ];
                    } else if( isset( $df['type'] ) && $df['type'] == 'date' ) {
                        $f->date( $df['id'], $df['n'], '', '', 'data-reg', 'top center', '<div>', 0, 0, '', '', '', '</div>' );
                    } else {
                        $f->text( $df['id'], $df['n'], $df['placeholder'] ?? $df['n'], '', 'data-reg', '<div>', '</div>' );
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
            } */
            $f->form( $reg_form );
            //echo !$columns_before ? $columns_html : '';
        post();
        file_upload();
        $f->process_trigger( $register_button_text, 'grad xxl', 'id="aio_forgot_init"', 'access_register_ajax', '', '', 'button', '', $register_button_icon, $icon_class . ' abs r2' );
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
    $f->pre_process('class="register_wrap"','','reg','register_',[],'',$callback,'',$redirect_to);
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
 * @param string $user_type
 * @return void
 */
function restrict_auth( string $login_page = 'admin/login', string $user_type = '' ): void {
    if( !user_logged_in() ) {
        header('Location: '.APPURL.$login_page);
    } else if( !empty( $user_type ) && !user_type_is( $user_type ) ) {
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

function __user_registration_fields(  string $pre = 'user_', string $data = '', string $array = '', int $name = 2, int $last_name = 2, int $email = 4, int $pass = 4, int $gender = 0, int $dob = 0, int $phone = 0 ): array {
    global $genders;
    $genders = $genders ?? [ 'Male', 'Female', 'Others', 'No Specify' ];
    global $phone_code;
    $phone_code = $phone_code ?? '+971';
    $data = $data ?? 'data';
    $attr = $array !== '' ? 'data-'.$data.' data-empty data-array="'.$array.'"' : 'data-'.$data.' data-empty';
    $return = [];
    !empty( $name ) ? $return[] = [ 'i' => 'name', 'l' => ( $last_name == 0 ? 'Full Name' : 'First Name' ), 'p' => 'Ex: John', 'v' => 'fake_name', 'a' => $attr.' required', 'c' => $name ] : '';
    !empty( $last_name ) ? $return[] = [ 'i' => 'last_name', 'l' => 'Last Name', 'p' => 'Ex: Doe', 'v' => 'fake_name', 'a' => $attr.' required', 'c' => $last_name ] : '';
    !empty( $email ) ? $return[] = [ 't' => 'email', 'i' => 'login', 'l' => 'Login Email Address', 'p' => 'Ex: john_doe@gmail.com', 'v' => 'fake_email', 'a' => $attr.'  data-help required', 'c' => $email ] : '';
    !empty( $pass ) ? $return[] = [ 't' => 'password', 'i' => 'pass', 'l' => 'Login Password', 'p' => '***********', 'v' => 'fake_password', 'a' => $attr.' data-help autocomplete="new-password"', 'c' => $pass ] : '';
    !empty( $gender ) ? $return[] = [ 't' => 'select', 'i' => 'gender', 'l' => 'Gender', 'p' => 'Choose Gender...', 'a' => $attr, 'c' => $gender ] : '';
    !empty( $dob ) ? $return[] = [ 't' => 'date', 'i' => 'dob', 'l' => 'Date of Birth', 'p' => 'Ex: 15-05-1990', 'a' => $attr, 'c' => $dob ] : '';
    !empty( $phone ) ? $return[] = [ 't' => 'phone', 'i' => 'phone_code', 'i2' => 'phone', 'l' => 'Code', 'l2' => 'Phone', 'p' => 'Ex: +1', 'p2' => 'Ex: 50012345', 'v' => $phone_code, 'a' => $attr. ' required', 'c' => $phone ] : '';
    return $return;
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
    _d( 'no_access '.$class );
    h1( $message, 1, 'tac' );
    if( !empty( $image ) )
        $image = str_contains( $image, 'http' ) ? $image : storage_url( $image );
        img( $image, '', 'no_access_image', $message, $message );
    if( $show_return )
        a( '', T('Return to Previous Page'), '', 'Return to Previous Page', 'onclick="history.back()"' );
    d_();
    if( $die ) {
        die();
    }
}

function tabbed_access_options(): void {
    $o = new OPTIONS();
    pre_tabs( 'material jcc mb20', 1 );
        foreach( [ 'Access Page', 'Login Options', 'Register Options' ] as $x => $option ) {
            tab( $option, $x == 0 );
        }
    post_tabs();
    _d( 'access_options' );
        _d( '', 'access_page_data' );
            $o->access_options( 1, 0, 0 );
        d_();
        _d( 'dn', 'login_options_data' );
            $o->access_options( 0, 1, 0 );
        d_();
        _d( 'dn', 'register_options_data' );
            $o->access_options( 0, 0, 1 );
        d_();
    d_();
}

function __user_card( string $class = '', array|int $user = [], string $type = '', string $pic_class = 'pic', string|array $data = 'email', string $edit_modal = '#user_modal', string $trash_table = '', string $trash_logic = '' ): string {
    $f = new FORM();
    if( is_numeric( $user ) ) {
        $d = new DB();
        $user = $d->select( 'users', '', 'user_id = '.$user, 1 );
    }
    $pic = $user['user_picture'] ?? '';
    $pic = !empty( $pic ) ? storage_url( $pic ) : '';
    $r = __pre( '', $class )
    . __div( 'status '.( $user['user_status'] == 1 ? 'g' : 'error' ) , ( $user['user_status'] == 1 ? T('Active') : T('Inactive') ) )
    . __image( $pic, '', $pic_class )
    . __div( 'tac', __div( 'id grad-bg', ( $user['user_name'] ?? '' ) ) . __div( 'type grad-bg', $type ) );
    if( !empty( $data ) ) {
        $data = is_array( $data ) ? $data : explode( ',', $data );
        $table_data = [];
        foreach( $data as $k ) {
            //skel( 'user_'.$k );
            if( isset( $user['user_'.$k] ) ) {
                $v = $k == 'phone' ? ( $user['user_phone_code'] . $user['user_phone'] ) : $user['user_'.$k];
                $table_data['body'][] = [ ucwords( str_replace( '_', '', $k ) ), $v ];
            }
        }
        $r .= __table( $table_data, 'xs plain' );
    }
    $r .= __pre( '', 'acts' )
        . ( !empty( $edit_modal ) ? $f->__edit_html( $edit_modal, $user ) : '' )
        . ( !empty( $trash_table ) ? $f->__trash_html( $trash_table, $trash_logic ) : '' )
    . __post() . __post();
    return $r;
}

/**
 * @param string $class
 * @param array|int $user
 * @param string $type
 * @param string $pic_class
 * @param string|array $data
 * @param string $edit_modal
 * @param string $table
 * @param string $trash_logic
 * @return void
 */
function user_card( string $class = '', array|int $user = [], string $type = '', string $pic_class = 'pic', string|array $data = 'email', string $edit_modal = '#user_modal', string $table = '', string $trash_logic = '' ): void {
    echo __user_card( $class, $user, $type, $pic_class, $data, $edit_modal, $table, $trash_logic );
}