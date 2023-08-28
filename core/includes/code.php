<?php

class CODE {

    function __construct() {
        // TODO: Automate Pages
    }

    /**
     * Renders HTML5 Template
     * @param string $class Class for <body> tag
     * @param string $attrs Attributes for <body> tag
     * @param string|array $pre_styles Pre Styles
     * @param string $primary_color Primary color for theme (without #)
     * @param string $secondary_color Secondary color for theme (without #)
     * @param string $art Art Components to be added
     * @param string|array $styles Styles to be linked
     * @param string|array $scripts Scripts to be added
     * @param string|array $primary_font Array of primary font and weights Ex: [ 'Lato', '300, 400' ]
     * @param string|array $secondary_font Array of secondary font and weights Ex: [ 'Cairo', '300, 400' ]
     * @param string|array $icon_fonts Icon Fonts Ex: [ 'MaterialIcons', 'BootstrapIcons' ]
     * @return void
     */
    function pre_html( string $class = '', string $attrs = '', string|array $pre_styles = [], string $primary_color = '00A99D', string $secondary_color = '', string $art = '', string|array $styles = [], string|array $scripts = [], string|array $primary_font = [], string|array $secondary_font = [], string|array $icon_fonts = [] ): void {

        // Defines
        global $dark_mode;
        global $options;
        $theme = !empty( $options['theme'] ) ? $options['theme'] : ( !empty( $options['default_theme'] ) ? $options['default_theme'] : '' );
        $dark_mode = !empty( $theme ) ? ( str_contains( $theme, 'dark' ) ? 1 : 0 ) : 0;
        $class = $dark_mode ? $class . ' d' : $class;
        $class = isset( $_GET['add'] ) ? $class.' add' : $class;

        // <head>
        echo '<!doctype html><html ';
        html_class();
        echo '><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge">';

        // SEO
        $c = get_config();
        if( defined( 'PAGEPATH' ) ) {
            $seo = !empty( $c['seo'] ) && !empty( $c['seo'][PAGEPATH] ) ?? ( $options['seo'][PAGEPATH] ?? '' );
            echo !empty( $seo ) ? '<meta name="description" content="'.T( $seo ).'">' : '';
        }

        // Colors
        $dark_mode = 0;
        if( $primary_color == '222' && $secondary_color == '000' ) {
            $theme = $options['default_theme'] ?? '';
            $theme = $options['theme'] ?? $theme;
            $disabled_color = $options['disabled_color'] ?? '';
            $progress_color = $options['progress_color'] ?? '';
            $warning_color = $options['warning_color'] ?? '';
            $error_color = $options['error_color'] ?? '';
            $success_color = $options['success_color'] ?? '';
            $dark_mode = str_contains( $theme, 'dark' );
            if( $dark_mode ) {
                $color = $options['color_dark'] ?? '#fff';
                $filled_color = $options['filled_color_dark'] ?? '#fff';
                $color1 = $options['primary_color_dark'] ?? $primary_color;
                $color2 = $options['secondary_color_dark'] ?? $secondary_color;
            } else {
                $color = $options['color_light'] ?? '#000';
                $filled_color = $options['filled_color_dark'] ?? '#fff';
                $color1 = $options['primary_color'] ?? '#111';
                $color2 = $options['secondary_color'] ?? '#222';
            }
        } else {
            $color = '#fff';
            $color1 = '#00A99D';
            $color2 = '#00A99D';
            $filled_color = '#fff';
            $disabled_color = 'lightgrey';
            $progress_color = '#00A99D';
            $warning_color = 'orange';
            $error_color = 'firebrick';
            $success_color = '#00A99D';
        }
        echo '<style>:root {';
        //skel( $options );
        echo $dark_mode ? '--dark_mode:1;' : '--dark_mode:0;';
        echo '--primary_color:'.$color1.';--secondary_color:'.$color2.';--color:'.$color.';--filled_color:'.$filled_color.';--disabled_color:'.$disabled_color.';--progress_color:'.$progress_color.';--warning_color:'.$warning_color.';--error_color:'.$error_color.';--success_color:'.$success_color;
        echo '}.c1{color:'.$color1.'}.c2{color:'.$color2.'}.bg1{background:'.$color1.'}.bg2{background:'.$color2.'}.bs{border:1px solid '.$color1.'}.bf:focus{border:1px solid var(--primary_color)}.grad{color:var(--color);background-color:var(--primary_color);background:-moz-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background:-webkit-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background-image:linear-gradient(45deg,var(--primary_color) 0%,var(--secondary_color) 100%);}.grad-text{background: -webkit-linear-gradient(var(--primary_color), var(--secondary_color));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}</style>';

        // Fav Icon
        $favicon = isset( $options['fav'] ) ? storage_url( $options['fav'] ) : 'fav';
        favicon( $favicon );

        // Fonts
        $fonts = [];
        // Primary Font
        if( !empty( $primary_font ) ) {
            $font1 = is_array( $primary_font ) ? $primary_font[0] : $primary_font;
            $weights1 = $primary_font[1] ?? '400';
            $weight = $primary_font[2] ?? '400';
        } else {
            $font1 = $options['font_1'] ?? 'Lato';
            $weights1 = $options['font_1_weights'] ?? '400';
            $weight = $options['font_weight'] ?? '400';
        }
        $fonts[ $font1 ] = $weights1;
        // Secondary Font
        if( !empty( $secondary_font ) ) {
            $font2 = is_array( $secondary_font ) && isset( $secondary_font[0] ) ? $secondary_font[0] : $secondary_font;
            $weights2 = is_array( $secondary_font ) && isset( $secondary_font[1] ) ? $secondary_font[1] : 400;
            //$weight2 = is_array( $secondary_font ) ? $secondary_font[2] : '400';
        } else {
            $font2 = $options['font_2'] ?? '';
            $weights2 = $options['font_2_weights'] ?? '';
            //$weight2 = $options['font_2_weight'] ?? '';
        }
        if( !empty( $font2 ) ) {
            $fonts[ $font2 ] = $weights2;
            reset_styles( $font1.','.$font2, $weight );
        } else {
            reset_styles( $font1, $weight );
        }
        // Icon Fonts
        if( !empty( $icon_fonts ) && is_array( $icon_fonts ) ) {
            foreach( $icon_fonts as $if ) {
                $fonts[ $if ] = '';
            }
            !defined( 'ICONS' ) ? define( 'ICONS', $icon_fonts[0] ) : '';
        } else if( !empty( $icon_fonts ) ) {
            !defined( 'ICONS' ) ? define( 'ICONS', $icon_fonts ) : '';
            $fonts[ $icon_fonts ] = '';
        }
        //skel( $fonts );
        fonts( $fonts );

        // Appearance
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'jquery' ] ) : $scripts . ',jquery';
        get_styles( $pre_styles );
        get_scripts( $scripts );

        if( !empty( $art ) ) {
            art( $art );
        }
        get_styles( $styles );
        get_styles( defined( 'PAGEPATH' ) ? PAGEPATH . ',micro' : 'micro' );

        get_title();

        //$f = new FORM();
        //$c = Encrypt::initiate();

        // Attributes
        //$attrs = $attrs.' data-out="'. $c->encrypt('logout_ajax').'"';

        // Google Analytics
        if( defined( 'CONFIG' ) && isset( CONFIG['api']['google_analytics'] ) ) {
            echo '<script async src="https://www.googletagmanager.com/gtag/js?id=UA-'.str_replace('UA-','',CONFIG['api']['google_analytics'])."></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', 'UA-".str_replace('UA-','',CONFIG['api']['google_analytics'])."');</script>";
        }

        // </head>
        echo '</head><body ';
        body_class( $class );
        echo $attrs . '>';

    }

    function post_html( string|array $scripts = [], string $alert_position = 'top right' ): void {
        echo '<div class="'.$alert_position.'" data-alerts></div>';
        get_scripts( $scripts );
        if( defined( 'PAGEPATH' ) )
            get_script( PAGEPATH );
        echo '</body></html>';
    }

    /**
     * Renders Admin Login HTML
     * @param string $login_redirect_url URL to redirect after login
     * @param string $attrs Attributes to add to the login wrapper
     * @param string|array $pre_styles Pre styles to add
     * @param string $primary_color Primary color for theme (without #)
     * @param string $secondary_color Secondary color for theme (without #)
     * @param string|array $styles Internal styles to add
     * @param string|array $scripts Scripts to add
     * @param array $primary_font Array of primary font and weights Ex: [ 'Lato', '300, 400' ]
     * @param array $secondary_font Array of secondary font and weights Ex: [ 'Cairo', '300, 400' ]
     * @param string|array $icon_fonts Icon Fonts Ex: 'MaterialIcons' or [ 'MaterialIcons', 'BootstrapIcons' ]
     * @return void
     */
    function auth_page( string $login_redirect_url = '', string $attrs = '', string|array $pre_styles = [], string $primary_color = '00A99D', string $secondary_color = '', string|array $styles = [], string|array $scripts = [], array $primary_font = [], array $secondary_font = [], string|array $icon_fonts = '' ): void {

        if( user_logged_in() ) {
            $redirect = 'Location:'.APPURL.$login_redirect_url;
            header( $redirect );
            exit;
        }

        // Defines
        global $options;
        global $dark_mode;
        $a = new ACCESS();
        $aos = $a->get_options();

        // Head
        $this->pre_html( '', $attrs, $pre_styles, '', '', 'icons,inputs,buttons,alerts', $styles, $scripts, $primary_font, $secondary_font, $icon_fonts );

        // Content
        //skel( $aos );
        //skel( $options );
        $logo_img = $dark_mode ? ( !empty( $aos['ac_logo_d'] ) ? $aos['ac_logo_d'] : ( !empty( $options['logo_dark'] ) ? $options['logo_dark'] : '' ) ) : ( !empty( $aos['ac_logo_l'] ) ? $aos['ac_logo_l'] : ( !empty( $options['logo_light'] ) ? $options['logo_light'] : '' ) );
        $logo_img = !empty( $logo_img ) ? $logo_img : ( $dark_mode ? APPURL.'assets/images/aio_l.svg' : APPURL.'assets/images/aio_d.svg' );
        $logo = !empty( $logo_img ) ? 'style="background:url(\''.storage_url( $logo_img ).'\') no-repeat center / contain"' : '';
        $bg_style = !empty( $aos['ac_bg_repeat'] ) && $aos['ac_bg_repeat'] == 1 ? 'repeat center / 100%' : 'no-repeat center / contain';
        $bg_img = $dark_mode ? ( $aos['ac_bg_d'] ?? '' ) : ( $aos['ac_bg_l'] ?? '' );
        $bg = !empty( $bg ) ? 'style="background:url(\''.storage_url( $bg_img ).'\') '.$bg_style.'"' : '';
        //$options['ac_bg_repeat']
        echo '<article '.$bg.'><div class="access_wrap"><div class="access_panel">';
        echo !isset( $aos['ac_show_logo'] ) || $aos['ac_show_logo'] !== 1 ? '<a href="'. APPURL . $login_redirect_url . '" class="brand" '.$logo.'></a>' : '';
        $u_text = $aos['ac_username_text'] ?? 'User Login / Email';
        $p_text = $aos['ac_password_text'] ?? 'Password';
        $l_text = $aos['ac_login_btn_text'] ?? 'Login';
        login_html( $u_text, $p_text, 'Remember for', $l_text, 2, 2, $login_redirect_url );
        echo '</div></div></article>';

        // Foot
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'data', 'validator', 'login' ] ) : $scripts . ',data,validator,login';
        $this->post_html( $scripts );
    }

    function manage_templates(): void {
        if( file_exists( APPPATH . 'templates' ) ) {
            $templates = glob( APPPATH . 'templates/*' );
            ?>
            <div id="aio_templates">
                <div id="aio_template_cards">
                    <div class="row cards">
                        <div class="col-12 col-md-3">
                            <div class="card br15 tac" data-show="#aio_edit_template" data-hide="#aio_template_cards">
                                <div class="mat-ico xxl">add_circle</div>
                            </div>
                        </div>
                        <?php if( !empty( $templates ) ) {
                            foreach( $templates as $t ) {
                                echo '<div class="col-12 col-md-3"><div class="card br15">'.$t.'</div></div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div id="aio_edit_template" class="dn card no-float p20 br15">
                    <?php
                    get_script('ace');
                    $f = new FORM();
                    $f->text( 'template_name','Template Name','','','data-tl' );
                    $f->input( 'textarea','template_code','Template Code','Ex: <html>','','class="dn"' );
                    echo '<div id="template_code" class="html_code"></div>';
                    ?>
                    <script>
                        $(document).ready(function(){
                            ace.config.set("basePath", "<?php echo APPURL; ?>assets/ext/ace/" );

                            let code_editor = ace.edit( 'template_code' );
                            code_editor.session.setMode("ace/mode/html");
                            code_editor.session.setValue($('[data-key=template_code]').val(),-1);
                            code_editor.session.on('change', function(d) {
                                $('[data-key=template_code]').val( code_editor.getValue() );
                            });
                        });

                        function frame_height() {
                            let f = $('iframe');
                            //console.log( $(f).contents().find('html').height() );
                            $(f).height( $(f).contents().find('html').height() );
                        }
                    </script>
                    <div class="tac">
                        <button data-hide="#aio_edit_template" data-show="#aio_template_cards" data-action class="store grad"><?php E('Back to Templates'); ?></button>
                        <?php $f->process_html('Save Template','store grad','','store_template_ajax'); ?>
                    </div>
                </div>
            </div>
            <?php
        } else {
            mkdir( APPPATH . 'templates' );
        }
    }

    /**
     * Renders a coming / launching soon HTML
     * @param string $date Estimated Launch Date
     * @param string $text Coming Soon Text
     * @param string $bg Background Image URL
     * @param string $logo Logo URL
     * @return void
     */
    function soon( string $date, string $text = 'Coming Soon...', string $bg = '', string $logo = '' ): void {
        $this->pre_html();
        get_style( 'soon' );
        global $options;
        global $is_light;
        $bg = !empty( $bg ) ? 'style="background:url(\''.$bg.'\') no-repeat center / cover"' : '';
        $app = $options['app_name'] ?? APPNAME;
        //skel( $options );
        if( $is_light ) {
            $logo = !empty( $logo ) ? $logo : $options['logo_light'];
        } else {
            $logo = !empty( $logo ) ? $logo : $options['logo_dark'];
        }
        $logo = !empty( $logo ) ? 'style="background:url(\''.storage_url($logo).'\') no-repeat center / contain"' : '';
        ?>
        <div class="aio_soon <?php echo $is_light ? '' : 'd'; ?>" <?php echo $bg; ?>>
            <div class="vc">
                <div class="brand" <?php echo $logo; ?> title="<?php echo $app; ?>"></div>
                <div class="box">
                    <div class="text"><?php echo $text; ?></div>
                    <div class="date"><?php echo $date; ?></div>
                </div>
                <div class="credits">Powered by <a target="_blank" href="https://github.com/AIOFrame/AIO">AIO</a></div>
            </div>
        </div>
        <?php
        get_script( 'soon' );
        $this->post_html();
    }

    /**
     * @param string $title Modal Title singular Ex: Contact, Student...
     * @param string $size Modal size s = small, m = medium, l = large, xl, f = full, l20, l40, l50, l60, l80, r20, r40, r50, r60, r80
     * @param string $target Database name if the data is supposed to store directly to db or ajax function name with _ajax at the end
     * @param array $fields Input fields to render
     * @param array $hidden Hidden data for Database
     * @param string $pre String to prepend to keys for database table columns
     * @param int $notify Notification Time in Seconds
     * @param int $reload Reload in Seconds
     * @param string $success_alert Text to notify upon successfully storing data
     * @param string $callback A JS Function to callback on results
     * @param string $confirm A confirmation popup will execute further code
     * @param string $redirect Redirect user to page on successful submission
     * @param string $validator Frontend JS script to add custom validation to the form data
     * @param string $reset_fields Reset input fields with data attribute (Tip: Use 1 to reset provided data fields)
     * @param string $submit_text Text on submit button
     * @return void
     */
    function modal( string $title = '', string $size = 'm', string $target = '', array $fields = [], array $hidden = [], string $pre = '', int $notify = 0, int $reload = 0, string $success_alert = '', string $callback = '', string $confirm = '', string $redirect = '', string $validator = '', string $reset_fields = '', string $submit_text = '' ): void {
        $f = new FORM();
        $r = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
        $this->pre_modal( $title, $size );
        $f->pre_process( 'data-wrap', $target, $r, $pre, $notify, $reload, $hidden, $success_alert, $callback, $confirm, $redirect, $validator, $reset_fields );
        $f->form( $fields, 'row', $r );
        $f->process_trigger( !empty( $submit_text ) ? $submit_text : 'Save '.$title, 'mb0' );
        $f->post_process();
        $this->post_modal();
    }

    function pre_modal( string $title = '', string $size = '' ): void {
        $s = strtolower( str_replace( ' ', '_', $title ) );
        ?>
        <div id="<?php echo $s.'_modal'; ?>" class="modal <?php echo $size . ' ' . $s.'_modal'; ?>">
            <h2 class="title" data-add><?php E( 'New '.$title ); ?></h2>
            <h2 class="title" data-edit style="display: none;"><?php E( 'Update '.$title ); ?></h2>
            <div class="close"></div>
            <div class="modal_body">
        <?php
    }

    function post_modal(): void {
        echo '</div></div>';
    }

    /**
     * Logout HTML
     * @param string $tag HTML element type
     * @param string $class Class for the logout element
     * @param string $text Logout text
     * @param string $confirm Confirmation message if needed
     * @return void
     */
    function logout_html( string $tag = 'div', string $class = '', string $text = 'Logout', string $confirm = 'Are you sure to Logout?' ): void {
        $e = Encrypt::initiate();
        $action = 'data-action="' . ( APPDEBUG ? 'logout_ajax' : $e->encrypt('logout_ajax') ) . '"';
        $click = 'onclick="process_data(this)"';
        $confirm = !empty( $confirm ) ? 'data-confirm="' . T($confirm) . '"' : '';
        echo '<' . $tag . ' class="' . $class . '" ' . $action . ' ' . $click . ' ' . $confirm . ' data-reload="2" data-notify="2">' . $text . '</' . $tag . '>';
    }

    // TODO: Page Management
    function page_management(): void {

    }

    // TODO: SEO Options
    function seo_options(): void {

    }

    function table_view( string $wrapper = '', array $rows = [], string $table_class = '' ): void {
        echo "<div id='{$wrapper}_list_view' data-view='list'>";
        $this->table( $rows, $table_class );
        echo '</div>';
    }

    /**
     * @param array $rows Array of table data [ 'head' => [ 'Name', 'Age' ], 'body' => [ 'Ahmed', 25 ] ]
     * @param string $class Class for the table
     * @return void
     */
    function table( array $rows = [], string $class = '' ): void {
        echo $this->_table( $rows, $class );
    }

    /**
     * @param array $rows Array of table data [ 'head' => [ 'Name', 'Age' ], 'body' => [ 'Ahmed', 25 ] ]
     * @param string $class Class for the table
     * @return string
     */
    function _table( array $rows = [], string $class = '' ): string {
        // TODO: Support art designs like statuses
        $return = '<table class="'.$class.'">';
        foreach( $rows as $row ) {
            $type = array_key_first( $row );
            $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '<thead>' : ( in_array( $type, [ 'tfoot', 'foot', 'f' ] ) ? '<tfoot>' : '<tbody>' );
            foreach( $row as $cols ) {
                $return .= '<tr>';
                foreach( $cols as $c ) {
                    $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '<th>'.$c.'</th>' : '<td>'.$c.'</td>';
                }
                $return .= '</tr>';
            }
            $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '</thead>' : ( in_array( $type, [ 'tfoot', 'foot', 'f' ] ) ? '</tfoot>' : '</tbody>' );
        }
        $return .= '</table>';
        return $return;
    }

    /**
     * Returns Card
     * @param string|array|float $col
     * @param string $class
     * @param string $title Title of the Card
     * @param string $link Hyperlink for the card to navigate to
     * @param string $desc Description
     * @param string $image Image or Logo URL
     * @param string $image_class Image or Logo class [ 'logo', 'image', 'logo xl', 'image f' ]
     * @param string $status Status text for the card
     * @param string $status_class Status class [ 'orange', 'blue', 'green', 'red', 'l', 'r' ]
     * @param array $data Information to be displayed as table list [ [ 'Age', '25 Years' ], [ 'Gender', 'Male' ] ]
     * @param array $actions General actions like view a page, print a page [ [ 'url' => '', 'title' => '', 'ico' => 'mat-ico printer' ] ]
     * @param string $edit_modal Modal identifier to insert editable data json
     * @param array $edit_data Editable data json
     * @param string $delete_table Database table name to delete data from Ex: 'contacts'
     * @param string $delete_logic Database deletion logic Ex: 'contact_id = 5'
     * @return string
     */
    function _card( string|array|float $col = '', string $class = '', string $title = '', string $link = '', string $desc = '', string $image = '', string $image_class = '', string $status = '', string $status_class = '', array $data = [], array $actions = [], string $edit_modal = '', array $edit_data = [], string $delete_table = '', string $delete_logic = '' ): string {
        $pre = '';
        $post = '';
        if( !empty( $col ) ) {
            $f = new FORM();
            $pre = $f->_pre( $col );
            $post = $f->_post( $col );
        }

        $f = new FORM();
        $return = !empty ( $link ) ? '<a class="card '.($class ?? '').'" href="'.$link.'">' : '<div class="card '.($class ?? '').'">';
        $return .= !empty( $image ) ? '<div style="background-image:url(\''.$image.'\')" class="'.$image_class.'"></div>' : '';
        $return .= !empty( $title ) ? '<h2 class="title gbg">'.$title.'</h2>' : '';
        $return .= !empty( $desc ) ? '<h4 class="desc gbg">'.$desc.'</h4>' : '';
        $return .= !empty( $status ) ? '<div class="status '.$status_class.'">'.$status.'</div>' : '';
        if( !empty( $data ) ) {
            $return .= $this->_table( [ 'body' => $data ], 'plain' );
        }
        if( !empty( $actions ) || ( !empty( $edit_data ) && !empty( $edit_modal ) ) || ( !empty( $delete_table ) && !empty( $delete_logic ) ) ) {
            $return .= '<div class="acts">';
            if( !empty( $actions ) ) {
                foreach( $actions as $act ) {
                    $return .= $f->_view_html( $act['url'] ?? '', $act['html'] ?? 'div', $act['title'] ?? ( $act['text'] ?? '' ), 'grad', '', $act['ico'] ?? '' );
                }
            }
            if( !empty( $edit_data ) && !empty( $edit_modal ) ) {
                $return .= $f->_edit_html( $edit_modal, $edit_data, 'div', '', 'grad', '', 'mat-ico', 'edit' );
            }
            if( !empty( $delete_table ) && !empty( $delete_logic ) ) {
                $return .= $f->_trash_html( $delete_table, $delete_logic, 'div', '', 'grad', '', 'mat-ico', 2, 2, 'Are you sure to delete?', 'delete' );
            }
            $return .= '</div>';
        }
        $return .= !empty ( $link ) ? '</a>' : '</div>';
        return $pre.$return.$post;
    }

    /**
     * Renders Card
     * @param string|array|float $col
     * @param string $class
     * @param string $title Title of the Card
     * @param string $link Hyperlink for the card to navigate to
     * @param string $desc Description
     * @param string $image Image or Logo URL
     * @param string $image_class Image or Logo class [ 'logo', 'image', 'logo xl', 'image f' ]
     * @param string $status Status text for the card
     * @param string $status_class Status class [ 'orange', 'blue', 'green', 'red', 'l', 'r' ]
     * @param array $data Information to be displayed as table list [ [ 'Age', '25 Years' ], [ 'Gender', 'Male' ] ]
     * @param array $actions General actions like view a page, print a page [ [ 'url' => '', 'title' => '', 'ico' => 'mat-ico printer' ] ]
     * @param string $edit_modal Modal identifier to insert editable data json
     * @param array $edit_data Editable data json
     * @param string $delete_table Database table name to delete data from Ex: 'contacts'
     * @param string $delete_logic Database deletion logic Ex: 'contact_id = 5'
     * @return void
     */
    function card( string|array|float $col = '', string $class = '', string $title = '', string $link = '', string $desc = '', string $image = '', string $image_class = '', string $status = '', string $status_class = '', array $data = [], array $actions = [], string $edit_modal = '', array $edit_data = [], string $delete_table = '', string $delete_logic = '' ): void {
        echo $this->_card( $col, $class, $title, $link, $desc, $image, $image_class, $status, $status_class, $data, $actions, $edit_modal, $edit_data, $delete_table, $delete_logic );
    }

    function grid_view( string $wrapper = '', string $content = '', string $wrapper_class = '', string|int|float $col = '' ): void {
        echo '<div id="'.$wrapper.'_grid_view" class="'.$wrapper_class.'" data-view="grid">';
        echo !empty( $col ) ? '<div class="row">' : '';
        echo $content;
        echo !empty( $col ) ? '</div>' : '';
        echo '</div>';
    }
}

class RANGE {

    /**
     * Returns array of 12 Months
     * @param bool $assoc Whether to return indexed months or associative months
     * @return array
     */
    function months( bool $assoc = true ): array {
        return $assoc ? [ '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' ] : [ 'January' => 'January', 'February' => 'February', 'March' => 'March', 'April' => 'April', 'May' => 'May', 'June' => 'June', 'July' => 'July', 'August' => 'August', 'September' => 'September', 'October' => 'October', 'November' => 'November', 'December' => 'December' ];
    }

    /**
     * Returns array of range of years
     * @param int $from From year
     * @param int $to To year
     * @param bool $desc Reverses the order of years
     * @return array
     */
    function years( int $from, int $to, bool $desc = true ): array {
        if( is_numeric( $from ) && is_numeric( $to )) {
            $years = [];
            if( strlen( $from ) > 3 && strlen( $to ) > 3 && $from < $to ) {
                if( $desc ) {
                    for ($x = $from; $x <= $to; $x++) {
                        $years[] = $x;
                    }
                } else {
                    for ($x = $to; $x >= $from; $x--) {
                        $years[] = $x;
                    }
                }
            } else {
                for( $x = date('Y', strtotime('-'.$from.' years')); $x <= date('Y', strtotime('+'.$to.' years')); $x++){
                    $years[] = $x;
                }
            }
            return $years;
        } else {
            return [];
        }
    }

    /**
     * Years from given date till now
     * @param string $date Date to start counting years till now
     * @return false|string
     */
    function years_from_date( string $date ): false|string {
        $then = date('Ymd', strtotime( $date ));
        $diff = date('Ymd') - $then;
        return substr($diff, 0, -4);
    }

}

/**
 * Displays a NO ACCESS content and end further code execution
 * @param string $message Message to be displayed
 * @param string $suggestion Suggestions like Try clearing filters or reload page!
 * @param string $class Wrapping class
 * @return void
 */
function no_content( string $message = "No data found!", string $suggestion = "", string $class = '' ): void {
    $db = new DB();
    $image = $db->get_option('no_content_image') ?? '';
    $message = T( $message );
    echo '<div class="no_content '.$class.'" style="padding: 20px; text-align: center"><h1 class="tac">'.$message.'</h1>';
    echo !empty( $suggestion ) ? '<h4>'.T($suggestion).'</h4>' : '';
    echo !empty( $image ) ? '<img src="'.storage_url($image).'" alt="'.$message.'" class="no_content_image" />' : '';
    echo '</div>';
}

function store_template_ajax(): void {
    elog( $_POST );
}

function __fn( $num, $decimals = 2, $locale = 'AE' ): void {
    echo _fn( $num, $decimals, $locale );
}

function _fn( $num, $decimals = 2, $locale = 'AE' ): string {
    $fmt = new NumberFormatter($locale = 'en_'.$locale, NumberFormatter::CURRENCY);
    $fmt->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $decimals);
    return str_replace( 'AED', '', $fmt->format( $num ) );
}

function nth( $num ): void {
    $fmt = new NumberFormatter($locale = 'en', 6);
    echo $fmt->format( $num );
}

function _nth( $num ): string {
    $fmt = new NumberFormatter($locale = 'en', 6);
    return $fmt->format( $num );
}

/**
 * Returns a readable format of programming code
 * @param string $text Code string
 * @return string
 */
function _pre( string $text ): string {
    return str_replace( '<', '&lt;', str_replace( '>', '&gt;', $text ) );
}

/**
 * Echos a readable format of programming code
 * @param string $text Code string
 * @return void
 */
function pre( string $text ): void {
    echo _pre( $text );
}

function easy_date( $date = '', $format = '', bool $show_time = false ): string {
    if( $date == '' ){
        $date = date('Y-m-d H:i:s');
    }
    $date = date_create( $date );
    if( empty( $format ) ) {
        global $options;
        $format = $options['date_format'] ?? '';
        if( empty( $format ) ) {
            //$c = json_decode(CONFIG, 1);
            $format = is_array(CONFIG) && isset(CONFIG['date_format']) ? CONFIG['date_format'] : 'd M, Y';
        }
    }
    return date_format( $date, $format );
}

function easy_dt( $datetime = '', $format = 'd M, Y H:i a' ): string {
    return easy_date( $datetime, $format );
}

/**
 * Converts a text to image
 * @param string $text
 * @param string $type
 * @param int $break
 * @param int $size
 * @param int $rotate
 * @param int $padding
 * @param bool $transparent
 * @param array $color
 * @param array $bg_color
 * @return string
 */
function text_to_image( string $text, string $type = 'img', int $break = 40, int $size = 24, int $rotate = 0, int $padding = 2, bool $transparent = false, array $color = ['r'=>255,'g'=>255,'b'=>255], array $bg_color = ['r'=>0,'g'=>0,'b'=>0] ): string {
    $e = Encrypt::initiate();
    $text = $e->encrypt( $text );
    $url = APPURL . 'core/modules/tti.php?t='.$text.'&b='.$break.'&s='.$size.'&r='.$rotate.'&p='.$padding.'&tr='.$transparent.'&c='.json_encode($color).'&bg='.json_encode($bg_color);
    if( $type == 'img' ) {
        return '<img class=\'text_to_image\' src=\''.$url.'\' title=\'Encrypted Text\' />';
    } else {
        return '<div class=\'text_to_image\' style=\'background-image: url(\''.$url.'\')\' title=\'Encrypted Text\'></div>';
    }
}

// ACCORDION TAGS

function accordion( string $title = '', string $content = '', string $class = '' ): void {
    echo "<div class='accordion {$class}'><div class='accordion_head'>{$title}<div class='act'><div class='mat-ico' data-close>expand_less</div><div class='mat-ico' data-open>expand_more</div></div></div><div class='accordion_body'>{$content}</div></div>";
}

function _r(): void {
    echo '<div class="row">';
}

function r_(): void {
    echo '</div>';
}

function _c( string|int $column = 12 ): void {
    echo '<div class="col-12 col-md-'.$column.'">';
}

function c_(): void {
    echo '</div>';
}