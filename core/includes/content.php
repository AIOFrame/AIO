<?php

class CONTENT {

    function __construct() {

    }

    /**
     * @param string $attrs Attributes for <body> tag
     * @param string|array $ex_styles External Styles
     * @param string|array $styles Styles to be linked
     * @param string|array $scripts Scripts to be added
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
        $fonts = [ [ 'MaterialIcons' ] ];
        $font1 = $options['font_1'] ?? 'Lato';
        $weight1 = $options['font_1_weights'] ?? '300,400';
        $fonts[] = [ $font1, $weight1 ];
        reset_styles( $font1, $weight1 );
        if( !empty( $options['font_2'] ) ) {
            $weight2 = $options['font_2_weights'] ?? '300,400';
            $fonts[] = [ $options['font_2'], $weight2 ];
        }
        fonts( $fonts );

        // Appearance
        $color1 = $options['primary_color'] ?? '#111';
        $color2 = $options['secondary_color'] ?? '#222';
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'jquery' ] ) : $scripts . ',jquery';
        get_styles( $ex_styles );
        get_scripts( $scripts );
        $theme = $options['default_theme'] ?? '';
        $theme = $options['theme'] ?? $theme;
        if( str_contains( $theme, 'dark' ) ) {
            $class .= $theme . ' d';
            $is_light = false;
            $color1 = $options['primary_color_dark'] ?? $color1;
            $color2 = $options['secondary_color_dark'] ?? $color2;
        } else {
            $class .= $theme . ' l';
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