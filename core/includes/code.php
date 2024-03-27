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
        $a = new ACCESS();
        $aos = $a->get_options();

        // Head
        $styles = is_array( $styles ) ? array_merge( $styles, [ 'air-datepicker' ] ) : $styles . ',air-datepicker';
        pre_html( '', $attrs, $pre_styles, '', '', 'icons,inputs,buttons,alerts', $styles, $scripts, $primary_font, $secondary_font, $icon_fonts );

        // Content
        pre( '', '', 'article' );
            pre( '', 'access_wrap' );
                pre( '', 'access_panel' );
                    !isset( $aos['ac_show_logo'] ) || $aos['ac_show_logo'] !== 1 ? a( APPURL . $login_redirect_url, '', 'brand' ) : '';
                    $u_text = $aos['ac_username_text'] ?? 'User Login / Email';
                    $p_text = $aos['ac_password_text'] ?? 'Password';
                    $l_text = $aos['ac_login_btn_text'] ?? 'Login';
                    access_html( $u_text, $p_text, 'Remember for', $l_text, 2, 2, $login_redirect_url );
                post();
            post();
        post( 'article' );

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
 * @param string|array $primary_font Array of primary font and weights Ex: [ 'Lato', '300, 400' ]
 * @param string|array $secondary_font Array of secondary font and weights Ex: [ 'Cairo', '300, 400' ]
 * @param string|array $icon_fonts Icon Fonts Ex: [ 'MaterialIcons', 'BootstrapIcons' ]
 * @param string $page_title
 * @return void
 */
function pre_html( string $class = '', string $attrs = '', string|array $pre_styles = [], string $primary_color = '00A99D', string $secondary_color = '00A99D', string $art = '', string|array $styles = [], string|array $scripts = [], string|array $primary_font = [], string|array $secondary_font = [], string|array $icon_fonts = [], string $page_title = '' ): void {

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
    /* $c = get_config();
    if( defined( 'PAGEPATH' ) ) {
        $seo = !empty( $c['seo'] ) && !empty( $c['seo'][PAGEPATH] ) ?? ( $options['seo'][PAGEPATH] ?? '' );
        echo !empty( $seo ) ? '<meta name="description" content="'.T( $seo ).'">' : '';
    } */

    // Colors
    $disabled = $options['disabled_color'] ?? 'lightgrey';
    $info = $options['info_color'] ?? '#d4f2ff';
    $progress = $options['progress_color'] ?? '#00A99D';
    $warning = $options['warning_color'] ?? 'orange';
    $error = $options['error_color'] ?? 'firebrick';
    $success = $options['success_color'] ?? '00A99D';
    if( $light_mode == 'd' ) {
        $color = $options['color_dark'] ?? '#fff';
        $filled_color = $options['filled_color_dark'] ?? '#fff';
        $color1 = $primary_color ?? ( $options['primary_color_dark'] ?? '00A99D' );
        $color2 = $secondary_color ?? ( $options['secondary_color_dark'] ?? '00A99D' );
    } else {
        $color = $options['color_light'] ?? '#000';
        $filled_color = $options['filled_color'] ?? '#fff';
        $color1 = $primary_color ?? ( $options['primary_color'] ?? '#00A99D' );
        $color2 = $secondary_color ?? ( $options['secondary_color'] ?? '#00A99D' );
    }
    $color1 = strlen( $color1 ) == 6 ? '#' . $color1 : $color1;
    $color2 = strlen( $color2 ) == 6 ? '#' . $color2 : $color2;
    $light_logo = !empty( $options['logo_light'] ) ? storage_url( $options['logo_light'] ) : APPURL . 'assets/images/aio.svg';
    $dark_logo = !empty( $options['logo_dark'] ) ? storage_url( $options['logo_dark'] ) : APPURL . 'assets/images/aio.svg';
    $logo = $light_mode == 'l' ? $light_logo : $dark_logo;
    echo '<style>:root {';
    echo '--dark_mode:'.$light_mode.';';
    echo "--logo:url($logo);--light_logo:url($light_logo);--dark_logo:url($dark_logo);";
    echo '--primary_color:'.$color1.';--secondary_color:'.$color2.';--color:'.$color.';--filled_color:'.$filled_color.';--disabled_color:'.$disabled.';--info_color:'.$info.';--progress_color:'.$progress.';--warning_color:'.$warning.';--error_color:'.$error.';--success_color:'.$success;
    echo '}.c1{color:'.$color1.'}.c2{color:'.$color2.'}.bg1{background:'.$color1.'}.bg2{background:'.$color2.'}.bs{border:1px solid '.$color1.'}.bf:focus{border:1px solid var(--primary_color)}.grad{color:var(--filled_color);background-color:var(--primary_color);background:-moz-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background:-webkit-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background-image:linear-gradient(45deg,var(--primary_color) 0%,var(--secondary_color) 100%);}.grad-text{background: -webkit-linear-gradient(var(--primary_color), var(--secondary_color));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}</style>';

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
    //skel( $icon_fonts );
    $icon_fonts = is_string( $icon_fonts ) && str_contains( $icon_fonts, ',' ) ? explode( ',', $icon_fonts ) : $icon_fonts;
    if( !empty( $icon_fonts ) && is_array( $icon_fonts ) ) {
        foreach( $icon_fonts as $if ) {
            $fonts[ $if ] = '';
        }
        !defined( 'ICONS' ) ? define( 'ICONS', $icon_fonts ) : '';
    } else if( !empty( $icon_fonts ) ) {
        !defined( 'ICONS' ) ? define( 'ICONS', $icon_fonts ) : '';
        $fonts[ $icon_fonts ] = '';
    } else {
        !defined( 'ICONS' ) ? define( 'ICONS', '' ) : '';
    }
    $icons = defined( 'ICONS' ) ? ( is_array( ICONS ) ? implode( ',', ICONS ) : ICONS ) : '';
    if( str_contains( strtolower( $icons ), 'bootstrap' ) ) {
        !empty( $pre_styles ) ? ( is_array( $pre_styles ) ? ( $pre_styles[] = 'bootstrap-icons' ) : ( $pre_styles .= $pre_styles.',bootstrap-icons' ) ) : ( $pre_styles = 'bootstrap-icons' );
    }
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

    get_title( $page_title );

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
    global $options;
    div($alert_position,'','','data-alerts');
    get_scripts( $scripts );
    if( defined( 'PAGEPATH' ) )
        get_script( PAGEPATH );
    $close_button = _div( ( $options['universal_icon_class'] ?? '' ) .' close ' . ( $options['port_ico_close'] ?? '' ), $options['port_ico_close'] ?? 'close' );
    $icon = _div( 'ico', _div( ( $options['universal_icon_class'] ?? '' ) .' ico {{icon}} ' . ( $options['port_ico_alerts'] ?? '' ), $options['port_ico_alerts'] ?? '' ) );
    // '<div class="alert in '+type+' n_'+r+'"><div class="data"><div class="mat-ico bi bi-x-lg close">close</div>'+ico+'<div class="message">'+text+'</div></div><div class="time"></div></div>'
    div( 'dn', _div( 'alert in {{type}} n_{{random}}', _div( 'data', $close_button . $icon . _div( 'message', '{{text}}' ) ) . _div( 'time' ) ), '', 'alert-html-template' );
    echo '</body></html>';
}

function pre( string $id = '', string $class = '', string $element = 'div', string $attr = '' ): void {
    echo _pre( $id, $class, $element, $attr );
}

function _pre( string $id = '', string $class = '', string $element = 'div', string $attr = '' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $attr = !empty( $attr ) ? ' '.$attr : $attr;
    return '<'.$element.$id.$class.$attr.'>';
}

function post( string $element = 'div' ): void {
    echo _post( $element );
}

function _post( string $element = 'div' ): string {
    return '</'.$element.'>';
}

function h1( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h1', $class, $title, '', $attrs, $translate );
}

function _h1( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return _el( 'h1', $class, $title, '', $attrs, $translate );
}

function h2( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h2', $class, $title, '', $attrs, $translate );
}

function _h2( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return _el( 'h2', $class, $title, '', $attrs, $translate );
}

function h3( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h3', $class, $title, '', $attrs, $translate );
}

function _h3( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return _el( 'h3', $class, $title, '', $attrs, $translate );
}

function h4( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h4', $class, $title, '', $attrs, $translate );
}

function _h4( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return _el( 'h4', $class, $title, '', $attrs, $translate );
}

function h5( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h5', $class, $title, '', $attrs, $translate );
}

function _h5( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return _el( 'h5', $class, $title, '', $attrs, $translate );
}

function h6( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): void {
    el( 'h6', $class, $title, '', $attrs, $translate );
}

function _h6( string $title = '', bool $translate = true, string $class = '', string $attrs = '' ): string {
    return _el( 'h6', $class, $title, '', $attrs, $translate );
}

function el( string $element = 'div', string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): void {
    echo _el( $element, $class, $content, $id, $attrs, $translate );
}

function _article( string $class = '', string $attr = '', string $id = '' ): void {
    echo __article( $class, $attr, $id );
}

function __article( string $class = '', string $attr = '', string $id = '' ): string {
    return _pre( $id, $class, 'article', $attr );
}

function article_(): void {
    echo article__();
}

function article__(): string {
    return _post( 'article' );
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

function post_tabs(): void {
    post();
    post();
}

function _el( string $element = 'div', string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): string {
    if( $element == 'hr' || $element == 'br' ) {
        return _pre( $id, $class, $element, $attrs );
    } else {
        $content = $translate ? T( $content ) : $content;
        return _pre($id,$class,$element,$attrs).$content._post($element);
    }
}

function div( string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): void {
    echo _div( $class, $content, $id, $attrs, $translate );
}

function __d( string $class = '', string $id = '', string $attrs = '' ): string {
    return _pre( $id, $class, 'div', $attrs );
}

function _d( string $class = '', string $id = '', string $attrs = '' ): void {
    pre( $id, $class, 'div', $attrs );
}

function d_(): void {
    post();
}

function d__(): string {
    return _post();
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

function _div( string $class = '', string $content = '', string $id = '', string $attrs = '', bool $translate = false ): string {
    return _el( 'div', $class, $content, $id, $attrs, $translate );
}

function a( string $hyperlink = '#', string $content = '', string $class = '', string $hover_title = '', string $attr = '', string $id = '' ): void {
    echo __a($hyperlink,$content,$class,$hover_title,$attr,$id);
}

function _a( string $hyperlink = '#', string $class = '', string $hover_title = '', string $attr = '', string $id = '' ): void {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $alt = !empty( $hover_title ) ? ' title="'.$hover_title.'"' : '';
    echo '<a href="'.$hyperlink.'" '.$id.$class.$alt.' '.$attr.'>';
}

function a_(): void {
    post('a');
}

function __a( string $hyperlink = '#', string $content = '', string $class = '', string $hover_title = '', string $attr = '', string $id = '' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $alt = !empty( $hover_title ) ? ' title="'.$hover_title.'"' : '';
    return '<a href="'.$hyperlink.'" '.$id.$class.$alt.' '.$attr.'>'.$content.'</a>';
}

function _sc(): void {
    echo '<script>';
}

function sc_(): void {
    echo '</script>';
}

function _f( string $class = '', string $method = '', string $action = '', string $id = '', string $enctype = '' ): void {
    $a = in_array( $method, [ 'post', 'p' ] ) ? 'method="POST"' : ( in_array( $method, [ 'get', 'g' ] ) ? 'method="GET"' : '' );
    $a .= !empty( $action ) ? ' action="'.$action.'"' : '';
    $a .= !empty( $enctype ) ? ' enctype="'.$enctype.'"' : '';
    echo _pre( $id, $class, 'form', $a );
}

function f_(): void {
    echo '</form>';
}

function b( string $class = '', string $content = '', string $id = '', string $attr = '', bool $translate = false ): void {
    echo _el( 'button', $class, $content, $id, $attr, $translate );
}

function _b( string $class = '', string $content = '', string $id = '', string $attr = '', bool $translate = false ): string {
    return _el( 'button', $class, $content, $id, $attr, $translate );
}

function img( string $image_url, string $id = '', string $class = '', string $alt = '', string $title = '', string $attr = '' ): void {
    echo _img( $image_url, $id, $class, $alt, $title, $attr );
}
function _img( string $image_src, string $id = '', string $class = '', string $alt = '', string $title = '', string $attr = '' ): string {
    $id = !empty( $id ) ? ' id="'.$id.'"' : '';
    $class = !empty( $class ) ? ' class="'.$class.'"' : '';
    $alt = !empty( $alt ) ? ' alt="'.$alt.'"' : '';
    $title = !empty( $title ) ? ' title="'.$title.'"' : '';
    return '<img src="'.$image_src.'" '.$id.$class.$alt.$title.$attr.' />';
}

function image( string $image_url, string $id = '', string $class = '' ): void {
    echo _image( $image_url, $id, $class );
}
function _image( string $image_url, string $id = '', string $class = '' ): string {
    return "<div style=\"background-image:url('".$image_url."')\" class=\"".$class."\"></div>";
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
 * @return void
 */
function table( array $rows = [], string $class = '' ): void {
    echo _table( $rows, $class );
}

/**
 * @param array $rows Array of table data [ 'head' => [ 'Name', 'Age' ], 'body' => [ 'Ahmed', 25 ] ]
 * @param string $class Class for the table
 * @param string $attr HTML Attributes for the table
 * @return string
 */
function _table( array $rows = [], string $class = '', string $attr = '' ): string {
    // TODO: Support art designs like statuses
    $return = _pre('',$class,'table',$attr);
    foreach( $rows as $row ) {
        $type = array_key_first( $row );
        $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '<thead>' : ( in_array( $type, [ 'tfoot', 'foot', 'f' ] ) ? '<tfoot>' : '<tbody>' );
        if( is_array( $row ) ) {
            foreach( $row as $cols ) {
                $return .= '<tr>';
                if( is_array( $cols ) ) {
                    foreach( $cols as $c ) {
                        $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '<th>'.$c.'</th>' : '<td>'.$c.'</td>';
                    }
                }
                $return .= '</tr>';
            }
        }
        $return .= in_array( $type, [ 'thead', 'head', 'h' ] ) ? '</thead>' : ( in_array( $type, [ 'tfoot', 'foot', 'f' ] ) ? '</tfoot>' : '</tbody>' );
    }
    $return .= _post('table');
    return $return;
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
function _card( string $class = '', string $title = '', string $link = '', string $desc = '', string|null $image = '', string $image_class = '', string $status = '', string $status_class = '', array $data = [], string $table_class = '', array $actions = [], string $actions_class = '', string $edit_modal = '', array $edit_data = [], string $delete_table = '', string $delete_logic = '', bool $show_edit = true, bool $show_delete = true ): string {
    $f = new FORM();
    $return = !empty ( $link ) ? _pre('','card '.($class??''),'a','href="'.$link.'"') : _pre('','card '.($class ?? ''));
    $return .= !is_null( $image ) ? ( str_contains( $image, '<' ) ? $image : _image( str_contains( $image, 'http' ) ? $image : storage_url( $image ), '', $image_class ) ) : '';
    $return .= _pre('','head tac');
    $return .= !empty( $title ) ? _el('h2','title grad',$title) : '';
    $return .= !empty( $desc ) ? _el('h5','desc',$desc) : '';
    $return .= _post();
    $return .= !empty( $status ) ? _div('status '.$status_class,$status) : '';
    if( !empty( $data ) ) {
        $return .= _table( [ 'body' => $data ], 'plain mb10 ' . $table_class );
    }
    if( !empty( $actions ) || ( !empty( $edit_data ) && !empty( $edit_modal ) ) || ( !empty( $delete_table ) && !empty( $delete_logic ) ) ) {
        $return .= _pre( '', 'acts '. $actions_class );
        if( !empty( $actions ) ) {
            foreach( $actions as $act ) {
                $return .= $f->_view_html( $act['url'] ?? '', $act['html'] ?? 'div', $act['title'] ?? ( $act['text'] ?? '' ), $act['class'] ?? ( $act['c'] ?? 'grad' ), $act['attr'] ?? ( $act['a'] ?? '' ), $act['icon_class'] ?? ( $act['ico_class'] ?? ( $act['icon'] ?? ( $act['ico'] ?? ( $act['i'] ?? ( $act['ic'] ?? 'ico' ) ) ) ) ), $act['icon_text'] ?? ( $act['ico_text'] ?? ( $act['icon'] ?? ( $act['ico'] ?? ( $act['it'] ?? '' ) ) ) ) );
            }
        }
        if( !empty( $edit_data ) && !empty( $edit_modal ) && $show_edit ) {
            $return .= $f->_edit_html( $edit_modal, $edit_data, 'div', '', 'grad', '', 'mat-ico', 'edit' );
        }
        if( !empty( $delete_table ) && !empty( $delete_logic ) && $show_delete ) {
            $return .= $f->_trash_html( $delete_table, $delete_logic, 'div', '', 'grad', '', 'mat-ico', 2, 2, 'Are you sure to delete?', 'delete' );
        }
        $return .= _post();
    }
    $return .= _post( !empty ( $link ) ? 'a' : 'div' );
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
    echo _card( $class, $title, $link, $desc, $image, $image_class, $status, $status_class, $data, $table_class, $actions, $actions_class, $edit_modal, $edit_data, $delete_table, $delete_logic );
}

function pre_modal( string $title = '', string $size = '', bool $editable = true ): void {
    global $options;
    $s = strtolower( str_replace( ' ', '_', $title ) );
    pre($s.'_modal','modal '.$size.' '.$s.'_modal');
        pre('','modal_head');
            if( $editable ) {
                h2('New '.$title,1,'title','data-add');
                h2('Update '.$title,1,'title','data-edit');
            } else {
                h2($title,1,'title');
            }
        post();
    el( 'div', ( $options['universal_icon_class'] ?? 'mico' ) . ' ico close ' . ( $options['port_ico_close'] ?? '' ), ( $options['port_ico_close'] ?? 'close' ) );
    pre('','modal_body');
}

function post_modal(): void {
    post();
    post();
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
    pre('','aio_soon '.$light_mode.' '.$bg);
        pre('','vc');
            pre('','brand','div',$logo.' title="'.$app.'"');
                pre('','box');
                    div('text',$text);
                    div('date mb20',$date);
                    div('credits',__a('https://github.com/AIOFrame/AIO','Powered by AIO'));
                post();
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
 * @param int $notify Notification Time in Seconds
 * @param int $reload Reload in Seconds
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
function modal( string $title = '', bool $editable = true, string $size = 'm', string $target = '', array|string $fields = [], string $form_style = 'row', array $hidden = [], string $prepend_to_keys = '', string $success_alert = '', string $callback = '', string $confirm = '', string $redirect = '', string $validator = '', string $reset_fields = '', string $submit_text = '', string $submit_class = '', string $submit_wrap = '' ): void {
    $f = new FORM();
    $r = substr(str_shuffle("abcdefghijklmnopqrstuvwxyz"), 0, 8);
    pre_modal( $title, $size, $editable );
    if( is_array( $fields ) ) {
        $f->pre_process( 'data-wrap', $target, $r, $prepend_to_keys, $hidden, $success_alert, $callback, $confirm, $redirect, $validator, $reset_fields );
            $f->form( $fields, $form_style, $r );
            $f->process_trigger( !empty( $submit_text ) ? $submit_text : 'Save '.$title, $submit_class.' mb0', '', '', $submit_wrap.' .tac' );
        $f->post_process();
    } else {
        echo $fields;
    }
    post_modal();
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
    $i = _el( 'span', ( $options['universal_icon_class'] ?? 'mico' ) . ' ico ' . ( $options['port_ico_new'] ?? '' ), ( $options['port_ico_new'] ?? 'add_circle' ) );
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
    echo _text_to_image( $text, $type, $break, $size, $rotate, $padding, $transparent, $color, $bg_color );
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
function _text_to_image( string $text, string $type = 'img', int $break = 40, int $size = 24, int $rotate = 0, int $padding = 2, bool $transparent = false, array $color = ['r'=>255,'g'=>255,'b'=>255], array $bg_color = ['r'=>0,'g'=>0,'b'=>0] ): string {
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

function accordion( string $title = '', string $content = '', string $class = '', string $attr = '' ): void {
    //echo "<div class='accordion {$class}' ".$attr."><div class='accordion_head'>{$title}<div class='act'><div class='mat-ico' data-close>expand_less</div><div class='mat-ico' data-open>expand_more</div></div></div><div class='accordion_body'>{$content}</div></div>";
    echo _accordion( $title, $content, $class, $attr );
}

function _accordion( string $title = '', string $content = '', string $class = '', string $attr = '' ): string {
    //echo "<div class='accordion {$class}' ".$attr."><div class='accordion_head'>{$title}<div class='act'><div class='mat-ico' data-close>expand_less</div><div class='mat-ico' data-open>expand_more</div></div></div><div class='accordion_body'>{$content}</div></div>";
    $acts = _div( 'act', _div( 'mat-ico', 'expand_less', '', 'data-close' ) . _div( 'mat-ico', 'expand_more', '', 'data-open' ) );
    return _div( 'accordion '.$class, _div( 'accordion_head', $title . $acts ) . _div( 'accordion_body', $content ) );
}

/**
 * Renders Tabs
 * @param array $tabs [ [ 'User' => 'User Content' ], [ 'Account Details' => 'Acc Details Content' ] ]
 * @param string $style CSS Style
 * @param bool $translate_titles True, to translate titles (Default False)
 * @return void
 */
function tabs( array $tabs = [], string $style = '', bool $translate_titles = false ): void {
    echo _tabs( $tabs, $style, $translate_titles );
}

/**
 * Renders Tabs
 * @param array $tabs [ [ 'User' => 'User Content' ], [ 'Account Details' => 'Acc Details Content' ] ]
 * @param string $style CSS Style
 * @param bool $translate_titles True, to translate titles (Default False)
 * @return string
 */
function _tabs( array $tabs = [], string $style = '', bool $translate_titles = false ): string {
    $data = _pre( '', 'tabs '.$style );
        $data .= _pre( '', 'tab_heads', 'div', 'data-store' );
            $x = 0;
            foreach( $tabs as $i => $content ) {
                $id = str_replace(' ','_',strtolower( $i ));
                $data .= _div( $x == 0 ? 'tab on' : 'tab', $i, '', 'data-tab="'.$id.'"', $translate_titles );
                $x++;
            }
        $data .= _post();
        $data .= _pre( '', 'tab_content' );
            $x = 0;
            foreach( $tabs as $i => $content ) {
                $id = str_replace(' ','_',strtolower( $i ));
                $data .= _div( ( $x !== 0 ? 'dn' : '' ), $content, $id );
                $x++;
            }
        $data .= _post();
    $data .= _post();
    return $data;
}

function tab_heads( array $tab_titles = [], string $style = '', string $wrap_class = '', bool $translate_titles = false ): void {
    echo _tab_heads( $tab_titles, $style, $wrap_class, $translate_titles );
}

/**
 * @param array $tab_titles [ 'User Accounts' ] or [ 'user' => 'User Account' ]
 * @param string $style
 * @param string $wrap_class
 * @param bool $translate_titles
 * @return string
 */
function _tab_heads( array $tab_titles = [], string $style = '', string $wrap_class = '', bool $translate_titles = false ): string {
    $data = _pre( '', 'tabs separate '.$style );
        $data .= _pre( '', 'tab_heads fluid '.$wrap_class, 'div', 'data-store' );
            $x = 0;
            foreach( $tab_titles as $i => $title ) {
                $id = str_replace(' ','_',strtolower( is_assoc( $tab_titles ) ? $i : $title ));
                $data .= _div( $x == 0 ? 'tab on' : 'tab', $title, '', 'data-tab="'.$id.'"', $translate_titles );
                $x++;
            }
        $data .= _post();
    $data .= _post();
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
    echo _steps( $steps, $style, $translate_titles );
}

/**
 * Returns Tabs HTML
 * @param array $steps [ [ 'title' => 'User', 'icon' => '', 'icon_class' => '', 'color' => '', 'content' => 'User Content' ], ... ]
 * @param string $style CSS Style
 * @param bool $translate_titles True, to translate titles (Default False)
 * @return string
 */
function _steps( array $steps = [], string $style = '', bool $translate_titles = false ): string {
    $r = rand( 0, 9999 );
    $data = _pre( '', 'steps '.$style );
        $data .= _pre( '', 'step_heads' );
            $x = 0;
            foreach( $steps as $s ) {
                $title = $s['title'] ?? ( $s['name'] ?? ( $s['t'] ?? ( $s['n'] ?? '' ) ) );
                $id = str_replace(' ','_',strtolower( $title )) . '_' . $r;
                $icon = $s['icon'] ?? ( $s['ico'] ?? ( $s['i'] ?? '' ) );
                $ic = $s['icon_class'] ?? ( $s['ic'] ?? 'mat-ico' );
                $big_title = ( empty( $icon ) ? _div( 'step_no', $x + 1 ) : '' ) . ( !empty( $icon ) ? _div( $ic.' step_no ico', $icon ) : '' ) . _div( 'step_title', $title );
                $color = $s['color'] ?? ( $s['c'] ?? '' );
                $color = !empty( $color ) ? 'style="background-color:'.$color.'"' : '';
                $data .= _div( $x == 0 ? 'step on' : 'step', $big_title, '', 'data-step="#'.$id.'" '.$color, $translate_titles );
                $x++;
            }
        $data .= _post();
        $data .= _pre( '', 'steps_content' );
            $data .= _pre( '', 'steps_pages' );
                $x = 0;
                foreach( $steps as $s ) {
                    $title = $s['title'] ?? ( $s['name'] ?? ( $s['t'] ?? ( $s['n'] ?? '' ) ) );
                    $id = str_replace(' ','_',strtolower( $title )) . '_' . $r;
                    $content = $s['content'] ?? ( $s['con'] ?? '' );
                    $data .= _div( ( $x !== 0 ? 'dn' : '' ), $content, $id );
                    $x++;
                }
            $data .= _post();
            $data .= _div( 'steps_controls', _div( 'mat-ico', 'chevron_left', '', 'data-prev' ) . _div( 'mat-ico', 'chevron_right', '', 'data-next' ) );
        $data .= _post();
    $data .= _post();
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
    echo _step_heads( $steps, $style, $translate_titles );
}

function _step_heads( array $steps = [], string $style = '', bool $translate_titles = false ): string {
    $data = _pre( '', 'steps separate '.$style );
        $data .= _pre( '', 'step_heads fluid' );
            $x = 0;
            foreach( $steps as $s ) {
                $title = is_assoc( $steps ) ? $s['title'] ?? ( $s['name'] ?? ( $s['t'] ?? ( $s['n'] ?? '' ) ) ) : $s;
                $id = str_replace(' ','_',strtolower( $title ));
                $icon = $s['icon'] ?? ( $s['ico'] ?? ( $s['i'] ?? '' ) );
                $ic = $s['icon_class'] ?? ( $s['ic'] ?? 'mat-ico' );
                $big_title = ( empty( $icon ) ? _div( 'step_no', $x + 1 ) : '' ) . ( !empty( $icon ) ? _div( $ic.' step_no ico', $icon ) : '' ) . _div( 'step_title', $title );
                $color = $s['color'] ?? ( $s['c'] ?? '' );
                $color = !empty( $color ) ? 'style="background-color:'.$color.'"' : '';
                $data .= _div( $x == 0 ? 'step on' : 'step', $big_title, '', 'data-step="#'.$id.'" '.$color, $translate_titles );
                $x++;
            }
        $data .= _post();
    $data .= _post();
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

function _r( string $class = '' ): void {
    pre( '', !empty( $class ) ? 'row '.$class : 'row' );
}

function r_(): void {
    post();
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
    if( $col_md == 'col' ) {
        $class = 'col';
    } else {
        $class = 'col-md-'.$col_md . ( str_contains( $class, 'col-' ) ? $class : ' col-12 '. $class );
    }
    pre( $id, $class, 'div', $attr );
}

/**
 * Writes bootstrap column div html element end tag
 * @return void
 */
function c_(): void {
    post();
}

// Social Share

function mini_browser_tab( int $width = 600, int $height = 300 ): string {
    return 'onclick="javascript:window.open(this.href, \'\', \'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height='.$height.',width='.$width.'\');return false;" target="_blank"';
}

function _fb( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return __a( 'https://www.facebook.com/sharer/sharer.php?u='.$page_link.'&amp;t='.$page_title, $content, $class, T('Share on Facebook'), mini_browser_tab() );
}

function _tw( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return __a( 'https://www.twitter.com/share?url='.$page_link.'&amp;text='.$page_title, $content, $class, T('Share on Twitter'), mini_browser_tab() );
}

function _ln( string $page_link = '', string $content = '', string $class = '' ): string {
    return __a( 'https://www.linkedin.com/sharing/share-offsite/?url='.$page_link, $content, $class, T('Share on LinkedIn'), mini_browser_tab() );
}

function _pn( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return __a( 'https://pinterest.com/pin/create/button/?url='.$page_link.'&amp;description='.$page_title, $content, $class, T('Share on Pinterest'), mini_browser_tab() );
}

function _wa( string $page_link = '', string $content = '', string $class = '' ): string {
    return __a( 'https://api.whatsapp.com/send?text='.$page_link, $content, $class, T('Share on Whatsapp'), mini_browser_tab() );
}

function _tg( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return __a( 'https://telegram.me/share/url?url='.$page_link.'&amp;text='.$page_title, $content, $class, T('Share on Telegram'), mini_browser_tab() );
}

function _em( string $page_link = '', string $page_title = '', string $content = '', string $class = '' ): string {
    return __a( 'mailto:?subject'.$page_title.'&body='.$page_link, $content, $class, T('Share thru email'), mini_browser_tab() );
}

function notice( string $content = '', string $type = 'info', string $class = '', string $icon_text = 'info', string $icon_class = '' ): void {
    global $options;
    _d( 'card df ais nf ' . $class . ' ' . $type );
        div( ( $options['universal_icon_class'] ?? 'mico' ) . ' l ' . $icon_class . ' ' . ( $options['universal_icon_class'] ?? '' ), $icon_text );
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
function no_content( string $message = "You are trying to reach restricted content!", string $suggestion = '', string $link_text = '', string $link_url = '', string $class = '', bool $translate = false ): void {
    $db = new DB();
    $image = $db->get_option('no_content_image') ?? '';
    $message = T( $message );
    _d( 'no_content '.$class );
        h1( $message, $translate, 'tac' );
        h4( $suggestion, $translate, 'tac' );
        if( !empty( $image ) )
            img( $image, '', 'no_content_image', $message, $message );
        if( !empty( $link_text ) || $link_url )
            a( $link_url, $translate ? T( $link_text ) : $link_text );
    d_();
}