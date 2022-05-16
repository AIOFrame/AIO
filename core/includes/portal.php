<?php

class PORTAL {

    private array $brand_options = [ 'app_name', 'primary_color', 'secondary_color', 'logo_light', 'logo_dark', 'primary_color_dark', 'secondary_color_dark', 'fav', 'font_1', 'font_1_weights', 'font_2', 'font_2_weights', 'scrollbar' ];

    /**
     * @param string $attrs
     * @param string|array $ex_styles
     * @param string|array $styles
     * @param string|array $scripts
     * @return void
     */
    function pre_html( string $attrs = '', string|array $ex_styles = [], string|array $styles = [], string|array $scripts = [] ): void {

        // Defines
        // $db = new DB();
        global $is_light;
        $is_light = true;
        $class = isset( $_GET['add'] ) ? 'add' : '';

        // Load Options
        global $options;
        //skel( $options );


        // <head>
        echo '<!doctype html><html ';
        html_class();
        echo '><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge">';

        // Fav Icon
        $favicon = isset( $options['fav'] ) ? storage_url( $options['fav'] ) : 'fav';
        favicon( $favicon );

        // Fonts
        $fonts = [];
        $font1 = $options['font_1'] ?? 'Lato';
        $weight1 = $options['font_1_weights'] ?? 400;
        $fonts[] = [ $font1, $weight1 ];
        reset_styles( $font1, $weight1, 5 );
        if( !empty( $options['font_2'] ) ) {
            $weight2 = $options['font_2_weights'] ?? 400;
            $fonts[] = [ $options['font_2'], $weight2 ];
        }
        fonts( $fonts );

        // Appearance
        $color1 = $options['primary_color'] ?? '111';
        $color2 = $options['secondary_color'] ?? '222';
        get_styles( $ex_styles );
        get_scripts( $scripts );
        $theme = $options['theme'] ?? '';
        if( str_contains( $theme, 'dark' ) ) {
            $class .= $theme . ' d';
            $is_light = false;
            $color1 = $options['primary_color_dark'] ?? $color1;
            $color2 = $options['secondary_color_dark'] ?? $color2;
        } else {
            $class .= $theme . ' l';
        }
        art('cards,modal,buttons,inputs,icons,tabs,steps,color,table,alerts',$color1,$color2);
        //skel( $options );
        if( is_array( $styles ) ) {
            array_unshift( $styles, 'portal/portal' );
            array_unshift( $styles, 'portal/ui/'.$theme );
        } else {
            $styles = 'portal/portal,portal/ui/'.$theme.','.$styles;
        }
        get_styles( $styles );
        get_styles( [ PAGEPATH, 'micro' ] );

        get_title();


        $f = new FORM();
        $c = Encrypt::initiate();

        // Attributes
        $attrs = $attrs.' data-out="'. $c->encrypt('logout_ajax').'"';

        // </head>
        echo '<style>.grad { background-color: '.$color1.'; background-image: linear-gradient(45deg, '.$color1.' 0%, '.$color2.' 100%); }</style></head><body ';
        body_class( $class );
        echo $attrs . '>';

    }

    function post_html( string|array $scripts = [] ): void {
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'iro', 'portal/portal' ] ) : $scripts.',iro,portal/portal';
        get_scripts( $scripts );
        get_script( PAGEPATH );
        ?>
        <div class="notices t r"></div>
        </body>
        </html>
        <?php
    }

    function user_profile( $user ): void {
        $f = new FORM();
        $c = Encrypt::initiate();
        $db = new DB();
        $ss = $db->select( 'sessions', '', 'session_uid = \''.get_user_id().'\'' );
        ?>
        <div class="row">
            <?php echo !is_mobile() ? '<div class="col-2"></div>' : ''; ?>
            <div class="col-12 col-lg-8">
                <div class="tabs">
                    <div class="tab_heads" data-store>
                        <div class="tab on" data-t="#looks"><?php is_mobile() ? E('UI') : E('Appearance'); ?></div>
                        <div class="tab" data-t="#basic"><?php is_mobile() ? E('User') : E('User Details'); ?></div>
                        <div class="tab" data-t="#pass"><?php is_mobile() ? E('Pass') : E('Change Password'); ?></div>
                        <div class="tab" data-t="#sess"><?php is_mobile() ? E('Sessions') : E('Active Sessions'); ?><?php echo ' ('.count($ss).')'; ?></div>
                        <?php if( is_admin() ) { ?>
                            <div class="tab" data-t="#status">Log</div>
                        <?php } ?>
                    </div>
                    <div class="tab_data wbg <?php echo is_mobile() ? 'p20' : 'p40'; ?>">

                        <div id="looks" <?php $f->option_params('data',2,2,'','theme,input_theme'); ?>>
                            <div class="row">
                                <?php
                                $uis = [ 'default' => 'Default - Light' ];
                                $ui_list = scandir( ROOTPATH . 'assets/styles/aio/portal/ui' );
                                foreach( $ui_list as $ui ) {
                                    if( str_contains( $ui, '.scss' ) ) {
                                        $s = str_replace( '.scss', '', $ui );
                                        $uis[ $s ] = ucwords( str_replace( '-', ' ', $s ) );
                                    }
                                }
                                $f->select( 'theme', 'Dashboard Style', 'Select Theme...', $uis, '', 'data-data class="select2"', 6, 1 );
                                $f->select( 'input_theme', 'Input Style', 'Select Theme...', [], '', 'data-data class="select2"', 6, 1 );
                                $f->process_options('Update Preferences','r5 xl mb0','','col-12 tar');
                                //skel( $uis );
                                //$f->texts([['']])
                                ?>
                            </div>
                        </div>

                        <div id="basic" class="dn" <?php $f->process_params('','user','user_',3,3,[],'Successfully updated user details!'); ?>>
                            <div class="row">
                                <?php
                                $f->texts([['login','User Login','',$user['user_login']],['since','User Since','',easy_date($user['user_since'])]],'disabled','6');
                                $f->texts([['name','Full Name','Ex: John Doe',$user['user_name']]],'required data-user','6');
                                $f->input('email','email','E Mail','Ex: john@company.com',$user['user_email'],'data-help',6);
                                $f->upload('picture','Upload Picture','Upload',$user['user_picture'],0,0,'upload','data-user','svg,jpg,png',10,1,'',4);
                                $f->process_html('Update Profile','r5 xl mb0','','update_profile_ajax','col-12 tar');
                                ?>
                            </div>
                        </div>

                        <div id="pass" class="dn" <?php $f->process_params('','ps','',3,3,[],'Successfully updated user password!'); ?>>
                            <div class="row">
                                <?php
                                $min_string = T('Minimum Characters');
                                $f->inputs('password',[['pass_old','Old Password'],['pass','New Password','','','data-length-notify="Password minimum length is 8 Characters"']],'data-ps minlength="8" data-minlength="'.$min_string.'" data-help required',6);
                                $f->process_html('Change Password','r5 xl mb0','','change_password_ajax','col-12 tar');
                                ?>
                            </div>
                        </div>

                        <div id="sess" class="dn">
                            <table class="">
                                <thead>
                                <tr>
                                    <th><?php E('OS'); ?></th>
                                    <th><?php E('Date'); ?></th>
                                    <th><?php E('Time'); ?></th>
                                    <th><?php E('Browser'); ?></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if( !empty( $ss ) ){
                                    foreach( $ss as $s ){
                                        $id = !empty($s['session_id']) ? $c->encrypt($s['session_id']) : '';

                                        ?>
                                        <tr class="tac">
                                            <td><?php echo $s['session_os']; ?></td>
                                            <td><?php echo easy_date($s['session_time']); ?></td>
                                            <td><?php echo easy_date($s['session_time'],'h:i a'); ?></td>
                                            <td><?php echo $s['session_client']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                            <div class="tar">
                                <?php
                                //$f->process_html('Logout All Sessions','r5 l','','clear_sessions_ajax');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo !is_mobile() ? '<div class="col-2"></div>' : ''; ?>
        </div>
        <?php
        file_upload();
    }

}