<?php

class CONTENT {

    function __construct() {

    }

    /**
     * Renders HTML5 Template
     * @param string $class Class for <body> tag
     * @param string $attrs Attributes for <body> tag
     * @param string|array $pre_styles Pre Styles
     * @param string $art Art Components to be added
     * @param string|array $styles Styles to be linked
     * @param string|array $scripts Scripts to be added
     * @param string|array $font1
     * @param string|array $font2
     * @return void
     */
    function pre_html( string $class = '', string $attrs = '', string|array $pre_styles = [], string $art = '', string|array $styles = [], string|array $scripts = [], string|array $font_1 = [], string|array $font_2 = [] ): void {

        // Defines
        global $is_light;
        $is_light = true;
        $class = isset( $_GET['add'] ) ? $class.' add' : $class;

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
        $fonts = [ [ 'MaterialIcons' ] ];
        if( !empty( $font_1 ) ) {
            $font1 = is_array( $font_1 ) ? $font_1[0] : $font_1;
            $weight1 = is_array( $font_1 ) ? $font_1[1] : '400';
        } else {
            $font1 = $options['font_1'] ?? 'Lato';
            $weight1 = $options['font_1_weights'] ?? '400';
        }
        $fonts[] = [ $font1, $weight1 ];
        if( !empty( $font2 ) ) {
            $font2 = is_array( $font2 ) ? $font2[0] : $font2;
            $weight2 = is_array( $font2 ) ? $font2[1] : '400';
        } else {
            $font2 = $options['font_2'] ?? '';
            $weight2 = $options['font_2_weights'] ?? '';
        }
        if( !empty( $font2 ) ) {
            $fonts[] = [ $font2, $weight2 ];
            $weights = explode( ',', $weight1 );
            reset_styles( $font1.','.$font2, $weights[0] );
        } else {
            reset_styles( $font1, $weight1 );
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
        get_styles( [ PAGEPATH, 'micro' ] );

        get_title();


        $f = new FORM();
        $c = Encrypt::initiate();

        // Attributes
        $attrs = $attrs.' data-out="'. $c->encrypt('logout_ajax').'"';

        // </head>
        echo '</head><body ';
        body_class( $class );
        echo $attrs . '>';

    }

    function post_html( string|array $scripts = [], string $alert_position = 'top right' ): void {
        get_scripts( $scripts );
        get_script( PAGEPATH );
        echo '<div class="'.$alert_position.'" data-alerts></div></body></html>';
    }

    /**
     * Renders Admin Login HTML
     * @param string $login_redirect_url URL to redirect after login
     * @param string $attrs Attributes to add to the login wrapper
     * @param string|array $pre_styles Pre styles to add
     * @param string|array $styles Internal styles to add
     * @param string|array $scripts Scripts to add
     * @return void
     */
    function login_html( string $login_redirect_url = '', string $attrs = '', string|array $pre_styles = [], string|array $styles = [], string|array $scripts = [] ): void {

        if( user_logged_in() ) {
            $redirect = 'Location:'.APPURL.$login_redirect_url;
            header( $redirect );
            exit;
        }

        // Head
        $this->pre_html( '', $attrs, $pre_styles, 'inputs,buttons,alerts', $styles, $scripts );

        // Content
        global $options;
        global $is_light;
        if( $is_light ) {
            $logo = isset( $options['logo_light'] ) && !empty( $options['logo_light'] ) ? 'style="background:url(\''.storage_url( $options['logo_light'] ).'\') no-repeat center / contain"' : '';
        } else {
            $logo = isset( $options['logo_dark'] ) && !empty( $options['logo_dark'] ) ? 'style="background:url(\''.storage_url( $options['logo_dark'] ).'\') no-repeat center / contain"' : '';
        }
        echo '<article><div class="access_wrap"><div class="access_panel">';
        echo '<a href="'. APPURL . $login_redirect_url . '" class="brand" '.$logo.'></a>';
        login_html( 'User Login / Email', 'Password', 2, 2, $login_redirect_url );
        echo '</div></div></article>';

        // Foot
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'data', 'login' ] ) : $scripts . ',data,login';
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
     * Logout HTML
     * @param string $tag HTML element type
     * @param string $class Class for the logout element
     * @param string $text Logout text
     * @param string $confirm Confirmation message if needed
     * @return void
     */
    function logout_html( string $tag = 'div', string $class = '', string $text = 'Logout', string $confirm = 'Are you sure to Logout?' ): void {
        $e = Encrypt::initiate();
        $action = 'data-action="' . $e->encrypt('logout_ajax') . '"';
        $click = 'onclick="process_data(this)"';
        $confirm = !empty( $confirm ) ? 'data-confirm="' . T($confirm) . '"' : '';
        echo '<' . $tag . ' class="' . $class . '" ' . $action . ' ' . $click . ' ' . $confirm . ' data-reload="2" data-notify="2">' . $text . '</' . $tag . '>';
    }

}

function store_template_ajax(): void {
    elog( $_POST );
}