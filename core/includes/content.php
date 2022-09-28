<?php

class CONTENT {

    function __construct() {

    }

    /**
     * @param string $attrs Attributes for <body> tag
     * @param string|array $ex_styles External Styles
     * @param string $art Art Components to be added
     * @param string|array $styles Styles to be linked
     * @param string|array $scripts Scripts to be added
     * @return void
     */
    function pre_html( string $attrs = '', string|array $ex_styles = [], string $art = '', string|array $styles = [], string|array $scripts = [] ): void {

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
        $fonts = [ [ 'MaterialIcons' ] ];
        $font1 = $options['font_1'] ?? 'Lato';
        $weight1 = $options['font_1_weights'] ?? '400';
        $fonts[] = [ $font1, $weight1 ];
        $weights = explode( ',', $weight1 );
        // TODO: Properly Reset
        if( !empty( $options['font_2'] ) ) {
            reset_styles( $font1.','.$options['font_2'], $weights[0] );
            $weight2 = $options['font_2_weights'] ?? '400';
            $fonts[] = [ $options['font_2'], $weight2 ];
        } else {
            reset_styles( $font1, $weights[0] );
        }
        fonts( $fonts );

        // Appearance
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'jquery' ] ) : $scripts . ',jquery';
        get_styles( $ex_styles );
        get_scripts( $scripts );
        $theme = $options['default_theme'] ?? '';
        $theme = $options['theme'] ?? $theme;
        if( str_contains( $theme, 'dark' ) ) {
            $class .= $theme . ' d';
            $is_light = false;
        } else {
            $class .= $theme . ' l';
        }
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

    function post_html( string|array $scripts = [] ): void {
        get_scripts( $scripts );
        get_script( PAGEPATH );
        echo '<div class="notices t r"></div></body></html>';
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

}

function store_template_ajax(): void {
    elog( $_POST );
}