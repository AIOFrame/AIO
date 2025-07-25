<?php

class CODE {

    function __construct() {
        // TODO: Automate Pages
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
     * @param array $fonts Array of fonts and weights Ex: [ 'Lato' => '300, 400', 'MaterialIcons ]
     * @return void
     */
    function auth_page( string $login_redirect_url = '', string $attrs = '', string|array $pre_styles = [], string $primary_color = '00A99D', string $secondary_color = '', string|array $styles = [], string|array $scripts = [], array $fonts = [] ): void {

        if( user_logged_in() ) {
            $redirect = 'Location:'.APPURL.$login_redirect_url;
            header( $redirect );
            exit;
        }

        // Defines
        global $light_mode;
        global $options;
//        $region = $options['region'] ?? '';
//        $region = !empty( $region ) ? strtolower( $region ) . '_' : '';
        $a = new ACCESS();
        $aos = $a->get_options();
        //skel( $aos );
        $show_logo = $aos['ac_show_logo'] ?? 1;
        $logo = $light_mode == 'd' ? ( $aos['ac_logo_d'] ?? ( $options['logo_d'] ?? '' ) ) : ( $aos['ac_logo_l'] ?? ( $options['logo_l'] ?? '' ) );
        $logo = !empty( $logo ) ? 'style="background:url(\''.storage_url( $logo ).'\') no-repeat center / contain"' : '';
        $bg = $light_mode == 'd' ? ( $aos['ac_bg_d'] ?? '' ) : ( $aos['ac_bg_l'] ?? '' );
        $bg_style = $aos['ac_bg_style'] ?? 'no-repeat center / cover';
        $attrs .= !empty( $bg ) ? ' style="background:url(\''.storage_url( $bg ).'\') '.$bg_style.'"' : '';
        $style = $aos['ac_style'] ?? 'c b';

        // Head
        $styles = is_array( $styles ) ? array_merge( $styles, [ 'air-datepicker' ] ) : $styles . ',air-datepicker';
        pre_html( '', $attrs, $pre_styles, '', '', 'icons,inputs,buttons,alerts', $styles, $scripts, $fonts );

        // Content
        _article();
            pre( '', 'access_wrap ' . $style );
                pre( '', 'access_panel' );
                    $show_logo == 1 ? a( APPURL . $login_redirect_url, '', 'brand', APPNAME, $logo.' title="'.APPNAME.'"' ) : '';
                    //!isset( $aos['ac_show_logo'] ) || $aos['ac_show_logo'] !== 1 ? a( APPURL . $login_redirect_url, '', 'brand' ) : '';
                    access_html( '', '', '', '', '', $login_redirect_url );
                post();
            post();
        article_();

        // Foot
        $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'air-datepicker', 'data', 'validator', 'login' ] ) : $scripts . ',air-datepicker,data,validator,login';
        post_html( $scripts );
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
 * Renders HTML5 Template
 * @param string $class Class for <body> tag
 * @param string $attrs Attributes for <body> tag
 * @param string|array $pre_styles Pre Styles
 * @param string $primary_color Primary color for theme (without #)
 * @param string $secondary_color Secondary color for theme (without #)
 * @param string $art Art Components to be added
 * @param string|array $styles Styles to be linked
 * @param string|array $scripts Scripts to be added
 * @param array $fonts Array of font and weights Ex: [ 'Lato' => '300,400', 'MaterialIcons' ]
 * @param string $page_title
 * @return void
 */
function pre_html( string $class = '', string $attrs = '', string|array $pre_styles = [], string $primary_color = '', string $secondary_color = '', string $art = '', string|array $styles = [], string|array $scripts = [], array $fonts = [ 'Lato', '400,600' ], string $page_title = '' ): void {

    // Defines
    global $light_mode;
    global $options;
    $theme = !empty( $options['theme'] ) ? $options['theme'] : ( !empty( $options['default_theme'] ) ? $options['default_theme'] : '' );
    $light_mode = !empty( $theme ) ? ( str_contains( $theme, 'dark' ) ? 'd' : 'l' ) : 'l';
    $class = $light_mode == 'l' ? $class : $class . ' d';
    $class = isset( $_GET['add'] ) ? $class.' add' : $class;

    // <head>
    echo '<!doctype html><html ';
    html_class();
    echo '><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, minimum-scale=1.0"><meta http-equiv="X-UA-Compatible" content="ie=edge">';

    // SEO
    $c = get_config();
    $track_body = '';
    if( defined( 'PAGEPATH' ) ) {
        $track_head = $options['app_track_head'] ?? ( $c['seo']['track_head'] ?? '' );
        $track_body = $options['app_track_body'] ?? ( $c['seo']['track_body'] ?? '' );

        // Get from options
        $seo_desc = $options['seo']['description'] ?? ( $c['seo'][PAGEPATH]['description'] ?? '' );
        $seo_keys = $options['seo']['keywords'] ?? ( $c['seo'][PAGEPATH]['keywords'] ?? '' );

        // Get from config
        echo !empty( $seo_desc ) ? '<meta name="description" content="'.T( $seo_desc ).'">' : '';
        echo !empty( $seo_keys ) ? '<meta name="keywords" content="'.$seo_keys.'">' : '';
        echo html_entity_decode( $track_head );
    }

    // Colors
    $disabled = $options['disabled_color'] ?? ( $c['colors']['disabled_color'] ?? 'lightgrey' );
    $info = $options['info_color'] ?? ( $c['colors']['info_color'] ?? 'cadetblue' );
    $progress = $options['progress_color'] ?? ( $c['colors']['progress_color'] ?? 'darkgoldenrod' );
    $warning = $options['warning_color'] ?? ( $c['colors']['warning_color'] ?? 'orange' );
    $error = $options['error_color'] ?? ( $c['colors']['error_color'] ?? 'firebrick' );
    $success = $options['success_color'] ?? ( $c['colors']['success_color'] ?? 'forestgreen' );
    //skel( $primary_color );
    $color = !empty( $options['color_'.$light_mode] ) ? $options['color_'.$light_mode] : ( $c['colors']['color_'.$light_mode] ?? '#fff' );
    $filled_color = !empty( $options['filled_color_'.$light_mode] ) ? $options['filled_color_'.$light_mode] : ( $c['colors']['filled_color_'.$light_mode] ?? '#fff' );
    $color1 = !empty( $primary_color ) ? $primary_color : ( !empty( $options['primary_color_'.$light_mode] ) ? $options['primary_color_'.$light_mode] : ( $c['colors']['primary_color_'.$light_mode] ?? '00A99D' ) );
    $color2 = !empty( $secondary_color ) ? $secondary_color : ( !empty( $options['secondary_color_'.$light_mode] ) ? $options['secondary_color_'.$light_mode] : ( $c['colors']['secondary_color_'.$light_mode] ?? '01756d' ) );
    $angle = !empty( $options['angle_'.$light_mode] ) ? $options['angle_'.$light_mode] : 45;
    $color1 = strlen( $color1 ) == 6 ? '#' . $color1 : $color1;
    $color2 = strlen( $color2 ) == 6 ? '#' . $color2 : $color2;
    //$light_logo = !empty( $options['logo_light'] ) ? storage_url( $options['logo_light'] ) : APPURL . 'assets/images/aio.svg';
    //$dark_logo = !empty( $options['logo_dark'] ) ? storage_url( $options['logo_dark'] ) : APPURL . 'assets/images/aio.svg';
    //$logo = $light_mode == 'l' ? $light_logo : $dark_logo;
    echo '<style>:root {';
    echo '--dark_mode:'.$light_mode.';';
    //echo "--logo:url($logo);--light_logo:url($light_logo);--dark_logo:url($dark_logo);";
    echo '--primary_color:'.$color1.';--secondary_color:'.$color2.';--color:'.$color.';--filled_color:'.$filled_color.';--gradient-angle:'.$angle.';--disabled_color:'.$disabled.';--info_color:'.$info.';--progress_color:'.$progress.';--warning_color:'.$warning.';--error_color:'.$error.';--success_color:'.$success;
    echo '}.c1{color:'.$color1.'}.c2{color:'.$color2.'}.bg1{background:'.$color1.'}.bg2{background:'.$color2.'}.bs{border:1px solid '.$color1.'}.bf:focus{border:1px solid var(--primary_color)}.grad{color:var(--filled_color);background-color:var(--primary_color);background:-moz-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background:-webkit-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background-image:linear-gradient(45deg,var(--primary_color) 0%,var(--secondary_color) 100%);}.grad-text{background: -webkit-linear-gradient(var(--primary_color), var(--secondary_color));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}</style>';

    // Fav Icon
    $favicon = isset( $options['fav'] ) ? storage_url( $options['fav'] ) : 'fav';
    favicon( $favicon );

    // Fonts
    if( !empty( $fonts ) ) {
        $font_names = [];
        foreach( $fonts as $name => $weight ) {
            if( is_numeric( $name ) ) {
                unset( $fonts[ $name ] );
                $fonts[ $weight ] = '';
                $name = $weight;
            }
            //$icons = defined( 'ICONS' ) ? ( is_array( ICONS ) ? implode( ',', ICONS ) : ICONS ) : '';
            $icon_fonts = [];
            if( str_contains( strtolower( $name ), 'material' ) ) {
                $icon_fonts[] = 'MaterialIcons';
            } else if( str_contains( strtolower( $name ), 'social' ) ) {
                $icon_fonts[] = 'SocialIcons';
            } else if( str_contains( strtolower( $name ), 'bootstrap' ) ) {
                //unset( $fonts[ $name ] );
                !empty( $pre_styles ) ? ( is_array( $pre_styles ) ? ( $pre_styles[] = 'bootstrap-icons' ) : ( $pre_styles .= $pre_styles.',bootstrap-icons' ) ) : ( $pre_styles = 'bootstrap-icons' );
            } else {
                $font_names[] = $name;
            }
            !defined( 'ICONS' ) ? define( 'ICONS', $icon_fonts ) : '';
        }
        reset_styles( implode( ',', $font_names ) );
    }
    //skel( $fonts );
    fonts( $fonts );

    // Appearance
    $scripts = is_array( $scripts ) ? array_merge( $scripts, [ 'jquery' ] ) : $scripts . ',jquery';
    //skel( $styles );
    get_styles( $pre_styles );
    get_scripts( $scripts );

    if( !empty( $art ) ) {
        art( $art );
    }
    get_styles( $styles );
    get_styles( defined( 'PAGEPATH' ) ? PAGEPATH . ',micro' : 'micro' );

    get_title( $page_title );

    //$f = new FORM();
    //$c = Encrypt::initiate();

    // Attributes
    //$attrs = $attrs.' data-out="'. $c->encrypt('logout_ajax').'"';

    // </head>
    echo '</head><body '.__body_class( $class ).$attrs . '>' . html_entity_decode( $track_body );

}

function post_html( string|array $scripts = [], string $alert_position = 'top right', string $html = '' ): void {
    echo __post_html( $scripts, $alert_position, $html );
}

function __post_html( string|array $scripts = [], string $alert_position = 'top right', string $html = '' ): string {
    global $options;
    $icon_class = ( $options['icon_class'] ?? 'mico' );
    $r = __div( $alert_position, '', '', 'data-alerts' );
    $r .= __get_scripts( $scripts );
    $r .= defined( 'PAGEPATH' ) ? __get_script( PAGEPATH ) : '';
    $close_button = __div( $icon_class .' close ' . ( $options['ico_close'] ?? '' ), $options['ico_close'] ?? 'close' );
    $icon = __div( 'ico', __div( $icon_class .' ico {{icon}} ' . ( $options['ico_alerts'] ?? '' ), $options['ico_alerts'] ?? 'notifications' ) );
    // '<div class="alert in '+type+' n_'+r+'"><div class="data"><div class="mat-ico bi bi-x-lg close">close</div>'+ico+'<div class="message">'+text+'</div></div><div class="time"></div></div>'
    $r .= __div( 'dn', __div( 'alert in {{type}} n_{{random}}', __div( 'data', $close_button . $icon . __div( 'message', '{{text}}' ) ) . __div( 'time' ) ), '', 'alert-html-template' );
    return $r . $html . '</body></html>';
}

function _dyn() {
    echo __pre( '', '', 'main', 'data-barba="container" data-barba-namespace="'.APP_NAME.'"' );
}

function dyn_() {
    echo __post( 'main' );
}

function pre( string $id = '', string $class = '', string $element = 'div', string $attr = '' ): void {
    echo __pre( $id, $class, $element, $attr );
}

function __pre( string $id = '', string $class = '', string $element = 'div', string $attr = '' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $attr = !empty( $attr ) ? ' '.$attr : '';
    return ( APPDEBUG ? PHP_EOL : '' ).'<'.$element.$id.$class.$attr.'>';
}

function post( string $element = 'div' ): void {
    echo __post( $element );
}

function __post( string $element = 'div' ): string {
    return '</'.$element.'>'.( APPDEBUG ? PHP_EOL : '' );
}

function h1( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h1', $class, $title, '', $attrs, $translate );
}

function __h1( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'h1', $class, $title, '', $attrs, $translate );
}

function h2( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h2', $class, $title, '', $attrs, $translate );
}

function __h2( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'h2', $class, $title, '', $attrs, $translate );
}

function h3( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h3', $class, $title, '', $attrs, $translate );
}

function __h3( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'h3', $class, $title, '', $attrs, $translate );
}

function h4( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h4', $class, $title, '', $attrs, $translate );
}

function __h4( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'h4', $class, $title, '', $attrs, $translate );
}

function h5( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h5', $class, $title, '', $attrs, $translate );
}

function __h5( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'h5', $class, $title, '', $attrs, $translate );
}

function h6( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h6', $class, $title, '', $attrs, $translate );
}

function __h6( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'h6', $class, $title, '', $attrs, $translate );
}

function el( string $element = 'div', string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): void {
    echo __el( $element, $class, $content, $id, $attrs, $translate );
}

function __p( string $content = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return __el( 'p', $class, $content, '', $attrs, $translate );
}

function p( string $content = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    echo __p( $content, $translate, $class, $attrs );
}

function __ol( array $list = [], bool $translate = true, string $class = '', string $attrs = '' ): string {
    $ls = '';
    foreach( $list as $l ) {
        $ls .= __el( 'li', '', $translate ? T( $l ) : $l );
    }
    return __el( 'ol', $class, $ls, '', $attrs, 0 );
}

function ol( array $list = [], bool $translate = true, string $class = '', string $attrs = '' ): void {
    echo __ol( $list, $translate, $class, $attrs );
}

function __ul( array $list = [], bool $translate = true, string $class = '', string $attrs = '' ): string {
    $ls = '';
    foreach( $list as $l ) {
        $ls .= __el( 'li', '', $translate ? T( $l ) : $l );
    }
    return __el( 'ul', $class, $ls, '', $attrs, 0 );
}

function ul( array $list = [], bool $translate = true, string $class = '', string $attrs = '' ): void {
    echo __ul( $list, $translate, $class, $attrs );
}

function _article( string $class = '', string $attr = '', string $id = '' ): void {
    echo __article( $class, $attr, $id );
}

function __article( string $class = '', string $attr = '', string $id = '' ): string {
    return __pre( $id, $class, 'article', $attr );
}

function article_(): void {
    echo article__();
}

function article__(): string {
    return __post( 'article' );
}

function color_block( string $color = '', int $width = 40, int $height = 30, int $radius = 8 ): void {
    echo __cb( $color, $width, $height, $radius );
}

function cb( string $color = '', int $width = 40, int $height = 30, int $radius = 8 ): void {
    echo __cb( $color, $width, $height, $radius );
}

function __color_block( string $color = '', int $width = 40, int $height = 30, int $radius = 8 ): string {
    return __cb( $color, $width, $height, $radius );
}

function __cb( string $color = '', int $width = 40, int $height = 30, int $radius = 8 ): string {
    return __div( 'color_block', '', '', 'style="background:'.$color.';width:'.$width.'px;height:'.$height.'px;border-radius:'.$radius.'px;margin:0 auto"' );
}

/**
 * @param string $class Design class for tabs ( 'vertical' or 'material' )
 * @param bool $remember_tab Remember the active tab on refresh (default false)
 * @return void
 */
function pre_tabs( string $class = '', bool $remember_tab = false ): void {
    pre('','tabs '.$class);
    $store = $remember_tab ? 'data-store="tabs_'.str_replace( ' ', '_', $class ).'"' : '';
    pre('','tab_heads '.$class,'div',$store);
}

function tab( string $title = '', bool $active = false, string $target = '', string $icon = '' ): void {
    $target = empty( $target ) ? '#'.strtolower( str_replace( '___', '_', str_replace( ' ', '_', str_replace( '/', '_', $title ) ) ) ).'_data' : $target;
    $class = $active ? 'tab on' : 'tab';
    if( !empty( $icon ) ) {
        $title = '<i class="mat-ico">'.$icon.'</i>'.$title;
    }
    div($class,$title,'','data-tab="'.$target.'"',1);
}

/**
 * Renders Vertical Tabs
 * @param array $tabs Tabs as array [ 'div_id' => 'Information' ]
 * @param string $class Class for the wrapper
 * @return void
 */
function vertical_sliding_tabs( array $tabs = [], string $class = '' ): void {
    echo __vertical_sliding_tabs( $tabs, $class );
}

/**
 * Returns Vertical Tabs HTML
 * @param array $tabs Tabs as array [ 'div_id' => 'Information' ]
 * @param string $class Class for the wrapper
 * @return string
 */
function __vertical_sliding_tabs( array $tabs = [], string $class = '' ): string {
    $r = __pre( '', 'vertical_tabs ' . $class );
    $x = 1;
    foreach( $tabs as $tk => $tv ) {
        $r .= __div( $x == 1 ? 'on' : '', $tv, '', 'data-e="#'.$tk.'"', 1 );
        $x++;
    }
    $r .= __post();
    return $r;
}

function post_tabs(): void {
    post();
    post();
}

function __el( string $element = 'div', string $class = '', string|null $content = '', string $id = '', string $attrs = '', bool $translate = false ): string {
    if( $element == 'hr' ) {
        return __pre( $id, $class, $element, $attrs );
    } else {
        $content = $translate ? T( $content ) : $content;
        return __pre( $id, $class, $element, $attrs ) . $content . __post( $element );
    }
}

/** Renders a div element
 * @param string $class
 * @param string $content
 * @param string $id
 * @param string $attrs
 * @param bool $translate
 * @return void
 */
function div( string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): void {
    echo __div( $class, $content, $id, $attrs, $translate );
}

function __d( string $class = '', string $id = '', string $attrs = '' ): string {
    return __pre( $id, $class, 'div', $attrs );
}

function _d( string $class = '', string $id = '', string $attrs = '' ): void {
    pre( $id, $class, 'div', $attrs );
}

function d_(): void {
    post();
}

function d__(): string {
    return __post();
}

/**
 * Writes div html element start or end tag
 * @param string $class Class for the div element
 * @param string $id ID for the div element
 * @param string $attrs HTML Attributes for the div element
 * @return void
 */
function d_experimental( string $class = '', string $id = '', string $attrs = '' ): void {
    global $code;
    if( isset( $code['div'] ) && $code['div'] ) {
        post();
        $code['div'] = false;
    } else {
        pre( $id, $class, 'div', $attrs );
        $code['div'] = true;
    }
}

function _sp( string $class = '', string $id = '', string $attrs = '' ): void {
    pre( $id, $class, 'span', $attrs );
}

function sp_(): void {
    post('span');
}

function _nav( string $class = '', string $id = '', string $attrs = '' ): void {
    pre( $id, $class, 'nav', $attrs );
}

function nav_(): void {
    post('nav');
}

function _as( string $class = '', string $id = '', string $attrs = '' ): void {
    pre( $id, $class, 'aside', $attrs );
}

function as_(): void {
    post('aside');
}

function _sec( string $class = '', string $id = '', string $attrs = '' ): void {
    pre( $id, $class, 'section', $attrs );
}

function sec_(): void {
    post('section');
}

function ico( string $icon = '', string $class = '', string $element = '' ): void {
    echo __ico( $icon, $class, $element );
}

function __ico( string $icon = '', string $class = '', string $element = 'i', string $id = '', string $attr = '' ): string {
    $o = new OPTIONS();
    $is = $o->icon_options;
    global $options;
    $ico = $options['icon_class'] ?? 'mico';
    $icon = $options[ $icon ] ?? ( $is[ $icon ] ?? $icon );
    return __el( $element, $class . ' ico ' . $ico . ' ' . $icon, $icon, $id, $attr );
}

/** Returns a div element
 * @param string $class
 * @param string $content
 * @param string $id
 * @param string $attrs
 * @param bool $translate
 * @return string
 */
function __div( string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): string {
    return __el( 'div', $class, $content, $id, $attrs, $translate );
}

function status( bool $active = false ): void {
    echo __status( $active );
}

function __status( bool $active = false ): string {
    $i = $active ? 'check_circle' : 'radio_button_unchecked';
    return __ico( $i, ( $active ? 'green' : 'red' ) );
}

function __a( string $hyperlink = '#', string $content = '', string $class = '', string $hover_title = '', string $attr = '', string $id = '' ): string {
    return __anchor( $hyperlink, $class, $hover_title, $attr, $id ) . $content . anchor__();
}
function a( string $hyperlink = '#', string $content = '', string $class = '', string $hover_title = '', string $attr = '', string $id = '' ): void {
    echo __a( $hyperlink, $content, $class, $hover_title, $attr, $id );
}

function __anchor( string $hyperlink = '#', string $class = '', string $hover_title = '', string $attr = '', string $id = '', string $target = '' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    //$class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $alt = !empty( $hover_title ) ? ' title="'.$hover_title.'"' : '';
    $target = !empty( $target ) ? ' target="'.$target.'"' : '';
    return __pre( $id, $class, 'a', 'href="'.$hyperlink.'" '.$alt.$attr.$target );
    //'<a  '.$id.$class.$alt.$target.' '.$attr.'>';
}

function __h( string $hyperlink = '#', string $class = '', string $hover_title = '' ): string {
    return __anchor( $hyperlink, $class, $hover_title );
}

function anchor__(): string {
    return __post('a');
}

function h__(): string {
    return anchor__();
}

function _a( string $hyperlink = '#', string $class = '', string $hover_title = '', string $attr = '', string $id = '', string $target = '' ): void {
    echo __anchor( $hyperlink, $class, $hover_title, $attr, $id, $target );
}

function a_(): void {
    echo anchor__();
}

function __i( string $class = '', string $content = '', string $attrs = '' ): string {
    return __el( 'i', $class, $content, $attrs );
}

/* function __a( string $hyperlink = '#', string $content = '', string $class = '', string $hover_title = '', string $attr = '', string $id = '', string $target = '_blank' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $alt = !empty( $hover_title ) ? ' title="'.$hover_title.'"' : '';
    $target = !empty( $target ) ? ' target="'.$target.'"' : '';
    return '<a href="'.$hyperlink.'" '.$id.$class.$alt.$target.' '.$attr.'>'.$content.'</a>';
} */

function _sc(): void {
    echo '<script>';
}

function sc_(): void {
    echo '</script>';
}

function _f( string $class = '', string $method = '', string $action = '', string $attr = '', string $enctype = '' ): void {
    $a = in_array( $method, [ 'post', 'p' ] ) ? 'method="POST"' : ( in_array( $method, [ 'get', 'g' ] ) ? 'method="GET"' : '' );
    $a .= !empty( $action ) ? ' action="'.$action.'"' : '';
    $a .= !empty( $enctype ) ? ' enctype="'.$enctype.'"' : '';
    echo __pre( '', $class, 'form', $a . ' ' . $attr );
}

function f_(): void {
    echo '</form>';
}

function b( string $class = '', string $content = '', string $id = '', string $attr = '', bool $translate = false ): void {
    echo __el( 'button', $class, $content, $id, $attr, $translate );
}

function __b( string $class = '', string $content = '', string $id = '', string $attr = '', bool $translate = false ): string {
    return __el( 'button', $class, $content, $id, $attr, $translate );
}

function img( string $image_url, string $id = '', string $class = '', string $alt = '', string $title = '', string $attr = '' ): void {
    echo __img( $image_url, $id, $class, $alt, $title, $attr );
}
function __img( string $image_src, string $id = '', string $class = '', string $alt = '', string $title = '', string $attr = '' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $alt = !empty( $alt ) ? ' alt="'.$alt.'"' : '';
    $title = !empty( $title ) ? ' title="'.$title.'"' : '';
    return '<img src="'.$image_src.'" '.$id.$class.$alt.$title.$attr.' />';
}

function image( string $image_url, string $id = '', string $class = '', string $attr = '' ): void {
    echo __image( $image_url, $id, $class, $attr );
}
function __image( string $image_url, string $id = '', string $class = '', string $attr = '' ): string {
    $attr = $attr . ' style="background-image:url(\''.$image_url.'\')"';
    return __div( $class, '', $id, $attr );
}

function grid_view( string $wrapper = '', array $cards = [], string $wrapper_class = '', string|int|float $col = '' ): void {
    pre($wrapper.'_grid_view',$wrapper_class,'div','data-view="grid"');
    $content = '';
    $pre = '';
    $post = '';
    if( !empty( $col ) ) {
        $f = new FORM();
        $pre = $f->_pre( $col );
        $post = $f->_post( $col );
    }
    foreach( $cards as $c ) {
        $content .= $pre.$c.$post;
    }
    div('row',$content);
    post();
}

function table_view( string $wrapper = '', array $rows = [], string $table_class = '' ): void {
    pre($wrapper.'_list_view','','div','data-view="list"');
    table( $rows, $table_class );
    post();
}

/**
 * @param array $rows Array of table data [ 'head' => [ 'Name', 'Age' ], 'body' => [ 'Ahmed', 25 ] ]
 * @param string $class Class for the table
 * @param string $attr HTML Attributes for the table
 * @return void
 */
function table( array $rows = [], string $class = '', string $attr = '' ): void {
    echo __table( $rows, $class, $attr );
}

/**
 * @param array $rows Array of table data [ 'head' => [ 'Name', 'Age' ], 'body' => [ 'Ahmed', 25 ] ]
 * @param string $class Class for the table
 * @param string $attr HTML Attributes for the table
 * @return string
 */
function __table( array $rows = [], string $class = '', string $attr = '' ): string {
    // TODO: Support art designs like statuses
    $return = __pre('',$class,'table',$attr);
    foreach( $rows as $row ) {
        $type = array_key_first( $row );
        $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '<thead>' : ( in_array( $type, [ 'tfoot', 'foot', 'f' ] ) ? '<tfoot>' : '<tbody>' );
        if( is_array( $row ) ) {
            foreach( $row as $cols ) {
                //skel( $cols );
                $return .= '<tr>';
                if( is_array( $cols ) ) {
                    foreach( $cols as $ck => $cv ) {
                        //skel( $type );
                        //skel( 'key' );
                        //skel( $ck );
                        //skel( 'value' );
                        //skel( $cv );
                        $return .= __el( ( !empty( $type ) && in_array( $type, [ 'thead', 'head', 'h' ] ) ? 'th' : 'td' ), ( !is_numeric( $ck ) ? $ck : '' ), $cv );
                    }
                }
                $return .= '</tr>';
            }
        }
        $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '</thead>' : ( in_array( $type, [ 'tfoot', 'foot', 'f' ] ) ? '</tfoot>' : '</tbody>' );
    }
    $return .= __post('table');
    return $return;
}

function __table_pre( array $heads = [], string $class = '', string $attr = '', bool $translate = true ): string {
    $return = __pre('',$class,'table',$attr);
    if( !empty( $heads ) ) {
        $return .= __pre( '', '', 'thead' );
        foreach( $heads as $head ) {
            echo __el( 'th', '', ( $translate ? T($head) : $head ) );
        }
        $return .= __post( 'thead' );
    }
    return $return;
}

function table_pre( array $heads = [], string $class = '', string $attr = '' ): void {
    echo __table_pre( $heads, $class, $attr );
}

function __table_post( array $foots = [] ): string {
    $return = $foot = '';
    if( !empty( $heads ) ) {
        $foot .= __pre( '', '', 'tfoot' );
        foreach( $heads as $head ) {

        }
        $foot .= __post( 'tfoot' );
    }
    $return .= $foot . __post( 'table' );
    return $return;
}

function table_post( array $foots ): void {
    echo __table_post( $foots );
}

/**
 * Returns Card
 * @param string $class
 * @param string $title Title of the Card
 * @param string $link Hyperlink for the card to navigate to
 * @param string $desc Description
 * @param string|null $image Image or Logo URL
 * @param string $image_class Image or Logo class [ 'logo', 'image', 'logo xl', 'image f' ]
 * @param string $status Status text for the card
 * @param string $status_class Status class [ 'orange', 'blue', 'green', 'red', 'l', 'r' ]
 * @param array $data Information to be displayed as table list [ [ 'Age', '25 Years' ], [ 'Gender', 'Male' ] ]
 * @param string $table_class Class for the table displaying data
 * @param array $actions General actions like view a page, print a page [ [ 'url' => '', 'title' => '', 'ico' => 'mat-ico printer' ] ]
 * @param string $actions_class Class to actions wrapper div
 * @param string $edit_modal Modal identifier to insert editable data json
 * @param array $edit_data Editable data json
 * @param string $delete_table Database table name to delete data from Ex: 'contacts'
 * @param string $delete_logic Database deletion logic Ex: 'contact_id = 5'
 * @param bool $show_edit Show edit action (default true)
 * @param bool $show_delete Show delete action (default true)
 * @return string
 */
function __card( string $class = '', string $title = '', string $link = '', string $desc = '', string|null $image = '', string $image_class = '', string $status = '', string $status_class = '', array $data = [], string $table_class = '', array $actions = [], string $actions_class = '', string $edit_modal = '', array $edit_data = [], string $delete_table = '', string $delete_logic = '', bool $show_edit = true, bool $show_delete = true ): string {
    $f = new FORM();
    global $options;
    //skel( $options );
    $icon_class = $options['icon_class'] ?? 'mico';
    $return = ( !empty ( $link ) ? __pre('','card '.($class??''),'a','href="'.$link.'"') : __pre('','card '.($class ?? '')) )
    . ( !empty( $image ) ? ( str_contains( $image, '<' ) ? $image : ( str_contains( $image_class, 'ico' ) ? __i( $image_class . ' ' . $image, $image ) : ( __image( str_contains( $image, 'http' ) ? $image : storage_url( $image ), '', $image_class ) ) ) ) : '' )
    . __pre('','head tac')
    . ( !empty( $title ) ? __el( 'h2', 'title grad', $title ) : '' )
    . ( !empty( $desc ) ? __el( 'h5', 'desc', $desc ) : '' )
    . __post()
    . ( !empty( $status ) ? __div('status '.$status_class,$status) : '' );
    if( !empty( $data ) ) {
        $return .= __table( [ 'body' => $data ], 'plain mb10 ' . $table_class );
    }
    if( !empty( $actions ) || ( !empty( $edit_data ) && !empty( $edit_modal ) ) || ( !empty( $delete_table ) && !empty( $delete_logic ) ) ) {
        $return .= __pre( '', 'acts '. $actions_class );
        if( !empty( $actions ) ) {
            foreach( $actions as $act ) {
                $return .= $f->__view_html( ( $act['url'] ?? ( $act['href'] ?? ( $act['h'] ) ) ), $act['html'] ?? 'div', $act['title'] ?? ( $act['text'] ?? '' ), $act['class'] ?? ( $act['c'] ?? 'grad' ), $act['attr'] ?? ( $act['a'] ?? '' ), $act['icon_class'] ?? ( $act['ico_class'] ?? ( $act['icon'] ?? ( $act['ico'] ?? ( $act['i'] ?? ( $act['ic'] ?? 'ico' ) ) ) ) ), $act['icon_text'] ?? ( $act['ico_text'] ?? ( $act['icon'] ?? ( $act['ico'] ?? ( $act['it'] ?? '' ) ) ) ) );
            }
        }
        if( !empty( $edit_data ) && !empty( $edit_modal ) && $show_edit ) {
            $return .= $f->__edit_html( $edit_modal, $edit_data, 'div', '', 'grad' );
        }
        if( !empty( $delete_table ) && !empty( $delete_logic ) && $show_delete ) {
            $return .= $f->__trash_html( $delete_table, $delete_logic, 'div', '', 'grad' );
        }
        $return .= __post();
    }
    $return .= __post( !empty ( $link ) ? 'a' : 'div' );
    return $return;
}

/**
 * Renders Card
 * @param string $class
 * @param string $title Title of the Card
 * @param string $link Hyperlink for the card to navigate to
 * @param string $desc Description
 * @param string|null $image Image or Logo URL
 * @param string $image_class Image or Logo class [ 'logo', 'image', 'logo xl', 'image f' ]
 * @param string $status Status text for the card
 * @param string $status_class Status class [ 'orange', 'blue', 'green', 'red', 'l', 'r' ]
 * @param array $data Information to be displayed as table list [ [ 'Age', '25 Years' ], [ 'Gender', 'Male' ] ]
 * @param string $table_class Class for the table displaying data
 * @param array $actions General actions like view a page, print a page [ [ 'url' => '', 'title' => '', 'ico' => 'mat-ico printer' ] ]
 * @param string $actions_class Class to actions wrapper div
 * @param string $edit_modal Modal identifier to insert editable data json
 * @param array $edit_data Editable data json
 * @param string $delete_table Database table name to delete data from Ex: 'contacts'
 * @param string $delete_logic Database deletion logic Ex: 'contact_id = 5'
 * @param bool $show_edit Show edit action (default true)
 * @param bool $show_delete Show delete action (default true)
 * @return void
 */
function card( string $class = '', string $title = '', string $link = '', string $desc = '', string|null $image = '', string $image_class = '', string $status = '', string $status_class = '', array $data = [], string $table_class = '', array $actions = [], string $actions_class = '', string $edit_modal = '', array $edit_data = [], string $delete_table = '', string $delete_logic = '', bool $show_edit = true, bool $show_delete = true ): void {
    echo __card( $class, $title, $link, $desc, $image, $image_class, $status, $status_class, $data, $table_class, $actions, $actions_class, $edit_modal, $edit_data, $delete_table, $delete_logic );
}

function __pre_modal( string $title = '', string $size = '' ): string {
    global $options;
    $i = $options['icon_class'] ?? 'mico';
    $ai = $options['icon_ai'] ?? 'auto_awesome';
    $x = $options['ico_close'] ?? 'close';
    $s = strtolower( str_replace( ' ', '_', $title ) );
    $r = __pre($s.'_modal','modal '.$size.' '.$s.'_modal')
        . __pre('','modal_head');
            $r .= __h2('New '.$title,1,'title','data-add')
            . __h2('Update '.$title,1,'title','data-edit');
        $r .= __post()
    . __d( 'modal_controls' );
    if( !empty( CONFIG['gemini_key'] ) || !empty( $options['gemini_key'] ) ) {
        $r .= __el( 'div', $i . ' act ico ai_fill ' . $ai, $ai . __div( 'loader' ), '', 'title="'.T('Auto Fill with AI').'"' );
    }
    $r .= __el( 'div', $i . ' ico close ' . $x, $x, '', 'title="'.T('Close').'"' )
    . d__()
    . __pre('','modal_body');
    return $r;
}

function pre_modal( string $title = '', string $size = '' ): void {
    echo __pre_modal( $title, $size );
}

function post_modal(): void {
    echo __post_modal();
}

function __post_modal(): string {
    return __post().__post();
}

function video( string $url, string $class = '', string $id = '', string $width = '640px', string $height = '480px', bool $show_controls = true, bool $autoplay = false, bool $muted = false ): void {
    echo __video( $url, $class, $id, $width, $height, $show_controls, $autoplay, $muted );
}

function __video( string $url, string $class = '', string $id = '', string $width = '640px', string $height = '480px', bool $show_controls = true, bool $autoplay = false, bool $muted = false ): string {
    $attr = !empty( $width ) ? 'width="'.$width.'"' : '';
    $attr .= !empty( $height ) ? 'height="'.$height.'"' : '';
    $attr .= !empty( $autoplay ) ? ' autoplay ' : '';
    $attr .= !empty( $muted ) ? ' muted ' : '';
    $attr .= !empty( $show_controls ) ? ' controls ' : '';
    $r = __pre( $id, $class, 'video', $attr );
        $r .= '<source src="'.$url.'">';
    $r .= __post( 'video' );
    return $r;
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
    pre_html();
    get_style( 'soon' );
    global $options;
    global $light_mode;
    $bg = !empty( $bg ) ? 'style="background:url(\''.$bg.'\') no-repeat center / cover"' : '';
    $app = $options['app_name'] ?? APPNAME;
    $logo = $light_mode == 'd' ? ( $logo ?? $options['logo_dark'] ) : ( $logo ?? $options['logo_light'] );
    $logo = !empty( $logo ) ? 'style="background:url(\''.storage_url($logo).'\') no-repeat center / contain"' : '';
    pre('','aio_soon '.$light_mode,'div',$bg);
        pre('','vc');
            pre('','box');
                div( 'brand', '', '', $logo.' title="'.$app.'"' );
                div('text',$text);
                div('date mb20',$date);
                div( 'credits', __a('https://github.com/AIOFrame/AIO','Powered by AIO'));
            post();
        post();
    post();
    get_script( 'soon' );
    post_html();
}

/**
 * @param string $title Modal Title singular Ex: Contact, Student...
 * @param bool $editable
 * @param string $size Modal size s = small, m = medium, l = large, xl, f = full, l20, l40, l50, l60, l80, r20, r40, r50, r60, r80
 * @param string $target Database name if the data is supposed to store directly to db or ajax function name with _ajax at the end
 * @param array|string $fields Input fields to render
 * @param string $form_style Style of the form
 * @param array $hidden Hidden data for Database
 * @param string $prepend_to_keys String to prepend to keys for database table columns
 * @param string $success_alert Text to notify upon successfully storing data
 * @param string $callback A JS Function to callback on results
 * @param string $confirm A confirmation popup will execute further code
 * @param string $redirect Redirect user to page on successful submission
 * @param string $validator Frontend JS script to add custom validation to the form data
 * @param string $reset_fields Reset input fields with data attribute (Tip: Use 1 to reset provided data fields)
 * @param string $submit_text Text on submit button
 * @param string $submit_class Class on submit trigger
 * @param string $submit_wrap Wrapper class for submit trigger
 * @return void
 */
function modal( string $title = '', string $size = 'm', string $target = '', array|string $fields = [], string $form_style = 'row', array $hidden = [], string $prepend_to_keys = '', string $success_alert = '', string $callback = '', string $confirm = '', string $redirect = '', string $validator = '', string $reset_fields = '', string $submit_text = '', string $submit_class = '', string $submit_wrap = '' ): void {
    $f = new FORM();
    $r = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
    pre_modal( $title, $size );
    if( is_array( $fields ) || empty( $fields ) ) {
        global $structure;
        //skel( $structure[ $target ] );
        $f->pre_process( 'data-wrap', $target, $r, $prepend_to_keys, $hidden, $success_alert, $callback, $confirm, $redirect, $validator, $reset_fields );
            $f->form( empty( $fields ) ? ( $structure[ $target ] ?? [] ) : $fields, $form_style, $r );
            $f->process_trigger( !empty( $submit_text ) ? $submit_text : 'Save '.$title, $submit_class.' mb0', '', '', $submit_wrap.' .tac' );
        $f->post_process();
    } else {
        echo $fields;
    }
    post_modal();
}

function access_modal( string $title = '', string $size = '', string $target = '', array $permissions = [], int $perms_col = 3, string $form_style = 'row', array $hidden = [], string $prepend_to_keys = '', string $success_alert = '', string $callback = '', string $confirm = '', string $redirect = '', string $validator = '', string $reset_fields = '', string $submit_text = '', string $submit_class = '', string $submit_wrap = '' ): void {
    $f = new FORM();
    $r = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
    if( !empty( $target ) ) {
        global $structure;
        // Prepare Fields
        $fields = $structure[ $target ] ?? [];
        foreach( $fields as $i => $field ) {
            $d = new DB();
            //skel( $field );
            //skel( $fields[ $i ] );
            $key = $field['dk'] ?? $field['dt'] . '_id';
            $data = $d->select( $field['dt'], [ $key, $field['dv'] ?? '' ], $field['w'] ?? '', $field['l'] ?? 0, $field['off'] ?? 0 );
            $array = [];
            if( !empty( $data ) ) {
                $array = array_to_assoc( $data, $key, $field['dv'] ?? '' );
            }
            //skel( $array );
            $fields[ $i ]['t'] = 'select2';
            $fields[ $i ]['k'] = 1;
            $fields[ $i ]['c'] = $fields[ $i ]['c'] ?? 5;
            $fields[ $i ]['options'] = $array;
            //$fields[ $i ]['t'] = 'select2';
        }
        $fields[] = [ 'i' => 'status', 'n' => 'Status', 't' => 'slide', 'r' => 1, 'on' => 'Active', 'c' => 2, 'v' => 1 ];
        if( !empty( $permissions ) ) {
            $fields[] = [ 'i' => 'permissions', 'n' => 'Permissions', 't' => 'checkboxes', 'options' => $permissions, 'c' => 12, 'cc' => $perms_col, 'a' => 'data-array="permissions"' ];
        }
        if( !empty( $fields ) ) {
            pre_modal( $title, $size );
                $f->pre_process( 'data-wrap', $target, $r, $prepend_to_keys, $hidden, $success_alert, $callback, $confirm, $redirect, $validator, $reset_fields );
                    $f->form( $fields, $form_style, $r );
                    $f->process_trigger( !empty( $submit_text ) ? $submit_text : 'Save '.$title, $submit_class.' mb0', '', '', $submit_wrap.' .tac' );
                $f->post_process();
            post_modal();
        }
    }
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

function modal_trigger( string $modal_identifier = '', string $title = '' ): void {
    global $options;
    $i = __el( 'span', ( $options['icon_class'] ?? 'mico' ) . ' ico ' . ( $options['ico_new'] ?? '' ), ( $options['ico_new'] ?? 'add_circle' ) );
    el('button','pop grad r',$i.$title,'','data-modal="'.$modal_identifier.'"',1);
}

function float_triggers( array $triggers, string $wrap_class = '' ): void {
    pre('','actions float '.$wrap_class);
    if( is_assoc( $triggers ) ) {
        foreach( $triggers as $tk => $tv ) {
            modal_trigger( $tk, $tv );
        }
    } else {
        foreach( $triggers as $t ) {
            modal_trigger( $t[0], $t[1] );
        }
    }
    post();
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
 * @param int $size
 * @param string $type
 * @param int $break
 * @param int $rotate
 * @param int $padding
 * @param bool $transparent
 * @param array $color
 * @param array $bg_color
 * @return void
 */
function text_to_image( string $text, int $size = 32, string $type = 'img', int $break = 40, int $rotate = 0, int $padding = 2, bool $transparent = false, array $color = ['r'=>255,'g'=>255,'b'=>255], array $bg_color = ['r'=>0,'g'=>0,'b'=>0] ): void {
    echo __text_to_image( $text, $type, $break, $size, $rotate, $padding, $transparent, $color, $bg_color );
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
function __text_to_image( string $text, string $type = 'img', int $break = 40, int $size = 24, int $rotate = 0, int $padding = 2, bool $transparent = false, array $color = ['r'=>255,'g'=>255,'b'=>255], array $bg_color = ['r'=>0,'g'=>0,'b'=>0] ): string {
    $e = Encrypt::initiate();
    $params = $e->encrypt_array([
        't' => $text,
        'b' => $break,
        's' => $size,
        'r' => $rotate,
        'p' => $padding,
        'tr' => $transparent,
        'c' => json_encode($color),
        'bg' => json_encode($bg_color)
    ]);
    $url = APPURL . 'core/modules/tti.php?r='.$params;
    if( $type == 'img' ) {
        return '<img class=\'text_to_image\' src=\''.$url.'\' title=\'Encrypted text as image\' />';
    } else {
        return '<div class=\'text_to_image\' style=\'background-image: url(\''.$url.'\')\' title=\'Encrypted text as image\'></div>';
    }
}

// ACCORDION TAGS

function accordion( string $title = '', string $content = '', string $class = '', string $attr = '' ): void {
    //echo "<div class='accordion {$class}' ".$attr."><div class='accordion_head'>{$title}<div class='act'><div class='mat-ico' data-close>expand_less</div><div class='mat-ico' data-open>expand_more</div></div></div><div class='accordion_body'>{$content}</div></div>";
    echo __accordion( $title, $content, $class, $attr );
}

function __accordion( string $title = '', string $content = '', string $class = '', string $attr = '' ): string {
    //echo "<div class='accordion {$class}' ".$attr."><div class='accordion_head'>{$title}<div class='act'><div class='mat-ico' data-close>expand_less</div><div class='mat-ico' data-open>expand_more</div></div></div><div class='accordion_body'>{$content}</div></div>";
    $acts = __div( 'act', __div( 'mat-ico', 'expand_less', '', 'data-close' ) . __div( 'mat-ico', 'expand_more', '', 'data-open' ) );
    return __div( 'accordion '.$class, __div( 'accordion_head', $title . $acts ) . __div( 'accordion_body', $content ) );
}

function _accordion( string $title = '', string $class = '' ): void {
    $acts = __div( 'act', __div( 'mat-ico', 'expand_less', '', 'data-close' ) . __div( 'mat-ico', 'expand_more', '', 'data-open' ) );
    pre( '', 'accordion '.$class );
        div( 'accordion_head', $title . $acts );
            pre( '', 'accordion_body' );
}

function accordion_(): void {
    post();
    post();
}

/**
 * Renders Tabs
 * @param array $tabs [ [ 'User' => 'User Content' ], [ 'Account Details' => 'Acc Details Content' ] ]
 * @param string $wrap_style CSS Style
 * @param string $head_wrap Wrapper class for tab heads
 * @param bool $translate_titles True, to translate titles (Default False)
 * @param bool $remember_tab Remembers active tab on page reload and sets last active tab to active
 * @return void
 */
function tabs( array $tabs = [], string $wrap_style = '', string $head_wrap = '', bool $translate_titles = false, bool $remember_tab = true ): void {
    echo __tabs( $tabs, $wrap_style, $head_wrap, $translate_titles, $remember_tab );
}

/**
 * Renders Tabs
 * @param array $tabs [ 'User' => 'User Content', 'Account Details' => 'Acc Details Content' ]
 * @param string $wrap_style CSS Style
 * @param string $head_wrap Wrapper class for tab heads
 * @param bool $translate_titles True, to translate titles (Default False)
 * @param bool $remember_tab Remembers active tab on page reload and sets last active tab to active
 * @return string
 */
function __tabs( array $tabs = [], string $wrap_style = '', string $head_wrap = '', bool $translate_titles = false, bool $remember_tab = true ): string {
    global $tab_storage;
    $tab_storage[] = $tabs;
    $r = count( $tab_storage ) + 1;
    $data = __d( 'tabs '.$wrap_style );
        $data .= __d( 'tab_heads ' . $head_wrap, 'div', ( $remember_tab ? 'data-store' : '' ) );
            $x = 0;
            foreach( $tabs as $i => $content ) {
                $id = '#tab_'.$r.'_'.$x; //str_replace(' ','_',strtolower( $i ));
                $data .= __div( $x == 0 ? 'tab on' : 'tab', $i, '', 'data-tab="'.$id.'"', $translate_titles );
                $x++;
            }
        $data .= d__();
        $data .= __d( 'tab_content' );
            $x = 0;
            foreach( $tabs as $i => $content ) {
                $id = 'tab_'.$r.'_'.$x; //str_replace(' ','_',strtolower( $i ));
                $data .= __div( ( $x !== 0 ? 'dn' : '' ), $content, $id );
                $x++;
            }
        $data .= d__();
    $data .= d__();
    return $data;
}

function tab_heads( array $tab_titles = [], string $style = '', string $wrap_class = '', bool $translate_titles = false, string $type = 'tab' ): void {
    echo __tab_heads( $tab_titles, $style, $wrap_class, $type );
}

/**
 * @param array $tab_titles [ 'User Accounts' ] or [ 'user' => 'User Account' ]
 * @param string $style
 * @param string $wrap_class
 * @param string $type
 * @return string
 */
function __tab_heads( array $tab_titles = [], string $style = '', string $wrap_class = '', string $type = 'tab' ): string {
    $data = __pre( '', 'tabs separate '.$style );
        $data .= __pre( '', 'tab_heads fluid '.$wrap_class, 'div', 'data-store' );
            $x = 0;
            foreach( $tab_titles as $i => $title ) {
                $id = ( is_numeric( $i ) ? '.' : '' ) . str_replace(' ','_',strtolower( is_assoc( $tab_titles ) ? $i : $title )) . ( is_numeric( $i ) ? '_data' : '' );
                if( in_array( $type, [ 'url', 'a', 'link', 'page' ] ) ) {
                    $data .= __a( ( str_contains( $i, 'http' ) ? $i : APPURL . $i ), $title, ( PAGEPATH == $i ? 'tab on' : 'tab' ), $title );
                    //$data .= _a( $id, ( $x == 0 ? 'tab on' : 'tab' ) ).( $translate_titles ? T($title) : $title ).a_();
                } else {
                    $data .= __div( $x == 0 ? 'tab on' : 'tab', $title, '', 'data-tab="'.$id.'"' );
                }
                $x++;
            }
        $data .= __post();
    $data .= __post();
    return $data;
}

/**
 * Renders Tabs HTML
 * @param array $steps [ [ 'title' => 'User', 'icon' => '', 'icon_class' => '', 'color' => '', 'content' => 'User Content' ], ... ]
 * @param string $style CSS Style
 * @param bool $translate_titles True, to translate titles (Default False)
 * @return void
 */
function steps( array $steps = [], string $style = '', bool $translate_titles = false ): void {
    echo __steps( $steps, $style, $translate_titles );
}

/**
 * Returns Tabs HTML
 * @param array $steps [ [ 'title' => 'User', 'icon' => '', 'icon_class' => '', 'color' => '', 'content' => 'User Content' ], ... ]
 * @param string $style CSS Style
 * @param bool $translate_titles True, to translate titles (Default False)
 * @param bool $show_controls
 * @param bool $show_arrows
 * @return string
 */
function __steps( array $steps = [], string $style = '', bool $translate_titles = false, bool $show_controls = true, bool $show_arrows = true ): string {
    $r = rand( 0, 9999 );
    global $options;
    $icon_class = $options['icon_class'] ?? 'mico';
    $prev_ico = $options['ico_previous'] ?? 'arrow_back';
    $next_ico = $options['ico_forward'] ?? 'arrow_forward';
    $data = __pre( '', 'steps '.$style );
        $data .= __pre( '', 'step_heads' );
            $x = 0;
            foreach( $steps as $s ) {
                $title = $s['title'] ?? ( $s['name'] ?? ( $s['t'] ?? ( $s['n'] ?? '' ) ) );
                $id = str_replace('&','and',str_replace(' ','_',strtolower( $title ))) . '_' . $r;
                $icon = $s['icon'] ?? ( $s['ico'] ?? ( $s['i'] ?? '' ) );
                $ic = $s['icon_class'] ?? ( $s['ic'] ?? 'mat-ico' );
                $big_title = ( empty( $icon ) ? __div( 'step_no', $x + 1 ) : '' ) . ( !empty( $icon ) ? __div( $ic.' step_no ico', $icon ) : '' ) . __div( 'step_title', $title );
                $color = $s['color'] ?? ( $s['c'] ?? '' );
                $color = !empty( $color ) ? 'style="background-color:'.$color.'"' : '';
                $data .= __div( $x == 0 ? 'step on' : 'step', $big_title, '', 'data-step="#'.$id.'" '.$color, $translate_titles );
                $x++;
            }
        $data .= __post();
        $data .= __pre( '', 'steps_content' );
            $data .= __pre( '', 'steps_pages' );
                $x = 0;
                foreach( $steps as $s ) {
                    $title = $s['title'] ?? ( $s['name'] ?? ( $s['t'] ?? ( $s['n'] ?? '' ) ) );
                    $id = str_replace('&','and',str_replace(' ','_',strtolower( $title ))) . '_' . $r;
                    $content = $s['content'] ?? ( $s['con'] ?? '' );
                    $data .= __div( ( $x !== 0 ? 'dn' : '' ), $content, $id );
                    $x++;
                }
            $data .= __post();
            $prev = ( $show_arrows ? __div( $icon_class . ' ' . $prev_ico, $prev_ico ) : '' ) . __div( 'arrow_title', T('Previous') );
            $next = ( $show_arrows ? __div( $icon_class . ' ' . $next_ico, $next_ico ) : '' ) . __div( 'arrow_title', T('Next') );
            $data .= $show_controls ? __div( 'steps_controls', __div( '', $prev, '', 'data-prev' ) . __div( '', $next, '', 'data-next' ) ) : '';
        $data .= __post();
    $data .= __post();
    return $data;
}

/**
 * Renders Tabs HTML
 * @param array $steps [ [ 'title' => 'User', 'icon' => '', 'icon_class' => '', 'color' => '', 'content' => 'User Content' ], ... ]
 * @param string $style CSS Style
 * @param bool $translate_titles True, to translate titles (Default False)
 * @return void
 */
function step_heads( array $steps = [], string $style = '', bool $translate_titles = false ): void {
    echo __step_heads( $steps, $style, $translate_titles );
}

function __step_heads( array $steps = [], string $style = '', bool $translate_titles = false ): string {
    $data = __pre( '', 'steps separate '.$style );
        $data .= __pre( '', 'step_heads fluid' );
            $x = 0;
            foreach( $steps as $s ) {
                $title = is_assoc( $steps ) || is_array( $s ) ? $s['title'] ?? ( $s['name'] ?? ( $s['t'] ?? ( $s['n'] ?? '' ) ) ) : $s;
                $id = str_replace(' ','_',strtolower( $title ));
                $icon = $s['icon'] ?? ( $s['ico'] ?? ( $s['i'] ?? '' ) );
                $ic = $s['icon_class'] ?? ( $s['ic'] ?? 'mico ico' );
                $big_title = ( empty( $icon ) ? __div( 'step_no', $x + 1 ) : '' ) . ( !empty( $icon ) ? __div( $ic.' step_no ico', $icon ) : '' ) . __div( 'step_title', $title );
                $color = $s['color'] ?? ( $s['c'] ?? '' );
                $color = !empty( $color ) ? 'style="background-color:'.$color.'"' : '';
                $data .= __div( $x == 0 ? 'step on' : 'step', $big_title, '', 'data-step="#'.$id.'" '.$color, $translate_titles );
                $x++;
            }
        $data .= __post();
    $data .= __post();
    return $data;
}

function r_experimental(): void {
    global $code;
    if( isset( $code['row'] ) && $code['row'] ) {
        post();
        $code['row'] = false;
    } else {
        pre( '', 'row' );
        $code['row'] = true;
    }
}

/**
 * Renders Logo or Picture, if empty shows name first character
 * @param string|null $image
 * @param string $name
 * @param string $class
 * @param string $element Render div or image element
 * @return string
 */
function __render_image( string|null $image = '', string $name = '', string $class = '', string $element = 'div' ): string {
    if( !empty( $image ) ) {
        //skel( !file_exists( APPPATH . $logo ) );
        $n = ''; //!file_exists( APPPATH . $image ) && !empty( $name ) ? '<span>'.$name[0].'</span>' : '';
        $image = $element == 'div' ? ' style="background-image:url(\''.storage_url( $image ).'\')"' : storage_url( $image );
        return $element == 'div' ? __div( 'image '.$class, $n, '', $image ) : '<img class="'.$class.'" src="'.$image.'" alt="'.$name.'">';
    } else {
        return '';
    }
}

/**
 * Renders Logo or Picture, if empty shows name first character
 * @param string|null $image
 * @param string $name
 * @param string $class
 * @param string $element Render div or image element
 * @return void
 */
function render_image( string|null $image = '', string $name = '', string $class = '', string $element = 'div' ): void {
    echo __render_image( $image, $name, $class, $element );
}

function _r( string $class = '', string $attr = '' ): void {
    echo __r( $class, $attr );
}

function __r( string $class = '', string $attr = '' ): string {
    return __pre( '', ( !empty( $class ) ? 'row '.$class : 'row' ), 'div', $attr );
}

function r_(): void {
    echo r__();
}

function r__(): string {
    return __post();
}

/**
 * Writes start and end bootstrap column div html elements
 * @param string|int $col_md Column for middle size screens
 * @param string $class Overwrite class
 * @param string $id ID for the element
 * @param string $attr HTML Attributes for the element
 * @return void
 */
function c_experimental( string|int $col_md = 12, string $class = '', string $id = '', string $attr = '' ): void {
    global $code;
    if( isset( $code['col'] ) && $code['col'] ) {
        post();
        $code['col'] = false;
    } else {
        d_experimental( 'col-md-'.$col_md. ( str_contains( $class, 'col-' ) ? $class : ' col-12 '. $class ) );
        $code['col'] = true;
    }
}

/**
 * Writes bootstrap column div html element start tag
 * @param string|int $col_md Column for middle size screens
 * @param string $class Overwrite class
 * @param string $id ID for the element
 * @param string $attr HTML Attributes for the element
 * @return void
 */
function _c( string|int $col_md = 12, string $class = '', string $id = '', string $attr = '' ): void {
    echo __c( $col_md, $class, $id, $attr );
}

function __c( string|int $col_md = 12, string $class = '', string $id = '', string $attr = '' ): string {
    if( in_array( $col_md, [ 'col', 'c', 0 ] ) ) {
        $class = 'col' . ' ' . $class;
    } else {
        $class = 'col-md-'.$col_md . ( str_contains( $class, 'col-' ) ? $class : ' col-12 '. $class );
    }
    return __pre( $id, $class, 'div', $attr );
}

/**
 * Writes bootstrap column div html element end tag
 * @return void
 */
function c_(): void {
    post();
}

function c__(): string {
    return __post();
}

// Social Share

function mini_browser_tab( int $width = 600, int $height = 300 ): string {
    return 'onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='.$height.',width='.$width.'\');return false;" target="_blank"';
}

function _fb( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return _a( 'https://www.facebook.com/sharer/sharer.php?u='.$page_link.'&amp;t='.$page_title, $class, T('Share on Facebook'), mini_browser_tab() ).$content.a_();
}

function _tw( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return _a( 'https://www.twitter.com/share?url='.$page_link.'&amp;text='.$page_title, $class, T('Share on Twitter'), mini_browser_tab() ).$content.a_();
}

function _ln( string $page_link = '', string $content = '', string $class = '' ): string {
    return _a( 'https://www.linkedin.com/sharing/share-offsite/?url='.$page_link, $class, T('Share on LinkedIn'), mini_browser_tab() ).$content.a_();
}

function _pn( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return _a( 'https://pinterest.com/pin/create/button/?url='.$page_link.'&amp;description='.$page_title, $class, T('Share on Pinterest'), mini_browser_tab() ).$content.a_();
}

function _wa( string $page_link = '', string $content = '', string $class = '' ): string {
    return _a( 'https://api.whatsapp.com/send?text='.$page_link, $class, T('Share on Whatsapp'), mini_browser_tab() ).a_();
}

function _tg( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return _a( 'https://telegram.me/share/url?url='.$page_link.'&amp;text='.$page_title, $class, T('Share on Telegram'), mini_browser_tab() ).$content.a_();
}

function _em( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return _a( 'mailto:?subject'.$page_title.'&body='.$page_link, $class, T('Share thru email'), mini_browser_tab() ).$content.a_();
}

function notice( string $content = '', string $type = 'info', string $class = '', string $icon_text = 'info', string $icon_class = '' ): void {
    global $options;
    $icon = $options['icon_class'] ?? 'mico';
    _d( 'notice ' . $class . ' ' . $type );
        div( $icon . ' ico l ' . $icon_class . ' ' . $icon, $icon_text );
        div( 'message', $content );
    d_();
}

/**
 * Displays a NO ACCESS content and end further code execution
 * @param string $message Message to be displayed
 * @param string $suggestion Suggestions like Try clearing filters or reload page!
 * @param string $link_text URL text to redirect user to somewhere else
 * @param string $link_url URL link to redirect user to somewhere else
 * @param string $class Wrapping class
 * @param bool $translate
 * @return void
 */
function no_content( string $message = "No data available!", string $suggestion = '', string $link_text = '', string $link_url = '', string $class = '', bool $translate = false ): void {
    echo __no_content( $message, $suggestion, $link_text, $link_url, $class, $translate );
}

/**
 * Displays a NO ACCESS content and end further code execution
 * @param string $message Message to be displayed
 * @param string $suggestion Suggestions like Try clearing filters or reload page!
 * @param string $link_text URL text to redirect user to somewhere else
 * @param string $link_url URL link to redirect user to somewhere else
 * @param string $class Wrapping class
 * @param bool $translate
 * @return string
 */
function __no_content( string $message = "No data available!", string $suggestion = '', string $link_text = '', string $link_url = '', string $class = '', bool $translate = false ): string {
    $db = new DB();
    $image = $db->get_option('no_content_image') ?? '';
    $message = T( $message );
    $r = __d( 'no_content '.$class ) .
        __h1( $message, $translate, 'tac' ) .
        __h4( $suggestion, $translate, 'tac' );
        if( !empty( $image ) )
            $r .= __img( storage_url( $image ), '', 'no_content_image', $message, $message );
        if( !empty( $link_text ) || $link_url ) {
            $link_url = str_contains( $link_url, 'http' ) ? $link_url : APPURL . $link_url;
            $r .= __a( $link_url, $translate ? T( $link_text ) : $link_text, 'btn link' );
        }
    d__();
    return $r;
}

// Statistic Widgets

function mini_stat( int|float $count = 0, string $text = '', string $ico = '', string $url = '', string $class = '', string $attr = '' ): void {
    echo __mini_stat( $count, $text, $ico, $url, $attr );
}

function __mini_stat( int|float $count = 0, string $text = '', string $ico = '', string $url = '', string $class = '', string $attr = '' ): string {
    $c = ( !empty( $ico ) ? __ico( $ico ) : '' ) . __div( 'text', $text ) . __div( 'count', $count );
    return !empty( $url ) ? __a( $url, $c, 'widget stat mini '.$class, '', $attr ) : __div( 'widget stat mini '.$class, $c, '', $attr );
}