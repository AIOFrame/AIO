<?php

class PORTAL {

    private array $brand_options = [ 'brand_logo', 'brand_name', 'brand_favicon', 'color_1', 'color_2', 'font_1', 'font_1_weights', 'font_2', 'font_2_weights', 'scrollbar' ];

    /**
     * @param string $attrs
     * @param string|array $ex_styles
     * @param string|array $styles
     * @param string|array $scripts
     * @return void
     */
    function header( string $attrs = '', string|array $ex_styles = [], string|array $styles = [], string|array $scripts = [] ): void {

        // Defines
        $db = new DB();

        // Load Options
        global $options;
        $options = $db->select( 'options', 'option_scope = \'0\' AND option_load = \'1\'' );

        // <head>
        echo '<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge">';

        // Fav Icon
        $favicon = $os['brand_favicon'] ?? 'fav';
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
        $color1 = $options['color1'] ?? '111';
        $color2 = $options['color2'] ?? '222';
        get_styles( $ex_styles );
        get_scripts( $scripts );
        art('cards,modal,buttons,inputs,icons,tabs,steps,color,table,alerts',$color1,$color2);
        get_styles( $styles );
        get_styles( [ PAGEPATH, 'micro' ] );

        get_title();
        $modal_add = isset( $_GET['add'] ) ? 'add' : '';

        $f = new FORM();
        $c = Encrypt::initiate();

        // Payment Gateway
        $attrs = $attrs.' data-out="'. $c->encrypt('logout_ajax').'"';


        // </head>
        echo '</head><body ';
        body_class( $modal_add );
        echo $attrs . '>';

    }

    function footer( string|array $scripts = [] ): void {
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
                    <div class="tab_heads">
                        <div class="tab on" data-t="#basic"><?php is_mobile() ? E('Info') : E('Basic Info'); ?></div>
                        <div class="tab" data-t="#pass"><?php is_mobile() ? E('Pass') : E('Change Password'); ?></div>
                        <div class="tab" data-t="#sess"><?php is_mobile() ? E('Sessions') : E('Active Sessions'); ?><?php echo ' ('.count($ss).')'; ?></div>
                        <?php if( is_admin() ) { ?>
                            <div class="tab" data-t="#status">Log</div>
                        <?php } ?>
                    </div>
                    <div class="tab_data wbg <?php echo is_mobile() ? 'p20' : 'p40'; ?>">
                        <div id="basic" <?php $f->process_params('','user','user_',3,3,[],'Successfully updated user details!'); ?>>
                            <div class="row">
                                <?php
                                $f->texts([['login','User Login','',$user['user_login']],['since','User Since','',easy_date($user['user_since'])]],'disabled','6');
                                $f->texts([['name','Full Name','Ex: John Doe',$user['user_name']],['email','E Mail','Ex: john@company.com',$user['user_email']]],'data-user','6');
                                $f->upload('picture','Upload Picture','Upload',$user['user_picture'],0,0,'upload','data-user','svg,jpg,png',10,1,'',6);
                                ?>
                            </div>
                            <div class="tar">
                                <?php
                                $f->process_html('Update Profile','r5 xl mb0','','update_profile_ajax');
                                ?>
                            </div>
                        </div>
                        <div id="pass" class="dn" <?php $f->process_params('','ps','',3,3,[],'Successfully updated user password!'); ?>>
                            <div class="row">
                                <?php $f->texts([['pass_old','Old Password'],['pass','New Password','','','data-length-notify="Password minimum length is 8 Characters"']],'data-ps','6'); ?>
                            </div>
                            <div class="tar">
                                <?php
                                $f->process_html('Change Password','r5 xl mb0','','change_password_ajax');
                                ?>
                            </div>
                        </div>
                        <div id="sess" class="dn">
                            <table class="">
                                <thead>
                                <tr>
                                    <th><?php E('Time'); ?></th>
                                    <th><?php E('Date'); ?></th>
                                    <th><?php E('OS'); ?></th>
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
                                            <td><?php echo easy_date($s['session_time'],'h:i a'); ?></td>
                                            <td><?php echo easy_date($s['session_time']); ?></td>
                                            <td><?php echo $s['session_os']; ?></td>
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