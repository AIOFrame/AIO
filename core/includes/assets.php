<?php

if( !defined( 'ROOTPATH' ) ) { exit(); }

/**
 * Global assets array, logs assets to prevent repeated assets being used!
 * @author Shaikh <hey@shaikh.dev>
 */
global $universal_assets;
$universal_assets = [ 'styles' => [], 'scripts' => [] ];

/**
 * RESET STYLES
 * @version 1.3
 * @param string $font Default Fonts (separate by ,) to be applied to all elements
 * @param string|int $weight Default font weight
 * @param int $scrollbar Default scrollbar width
 * @author Shaikh <hey@shaikh.dev>
 */
function reset_styles( string $font = 'Lato', string|int $weight = 'normal', int $scrollbar = 5 ): void {
    $cache = get_config( 'cache' );
    $v = $cache ? '&v=' . round( time() / ( $cache * 60 ) ) : '';
    global $universal_assets;
    $universal_assets = empty( $universal_assets ) ? [] : $universal_assets;
    if( !in_array( 'reset', $universal_assets['styles'] ) ) {
        echo '<link rel="stylesheet" href="'.APPURL.'assets/styles/aio/reset.php?font='.str_replace(' ','$',$font).'&weight='.$weight.'&scrollbar='.$scrollbar.'">';
        $universal_assets['styles'][] = 'reset';
    }
}

/**
 * Fetches AIO Art style modules
 * @param array|string $arts art modules separated by commas
 * @param string $color1 color code hex for first color
 * @param string $color2 color code hex for second color
 * @author Shaikh <hey@shaikh.dev>
 */
function art( array|string $arts = '', string $color1 = '222', string $color2 = '000' ): void {
    global $options;
    global $universal_assets;
    global $dark_mode;

    $cache = get_config( 'cache' );
    $v = $cache ? '&v=' . round( time() / ( $cache * 60 ) ) : '';
    $arts = !is_array( $arts ) ? explode( ',', $arts ) : $arts;

    // Setting Colors
    $dark_mode = 0;
    if( $color1 == '222' && $color2 == '000' ) {
        $theme = $options['default_theme'] ?? '';
        $theme = $options['theme'] ?? $theme;
        $dark_mode = str_contains( $theme, 'dark' );
        if( $dark_mode ) {
            $color = $options['color_dark'] ?? '#fff';
            $filled_color = $options['filled_color_dark'] ?? '#fff';
            $color1 = $options['primary_color_dark'] ?? $color1;
            $color2 = $options['secondary_color_dark'] ?? $color2;
        } else {
            $color = $options['color_light'] ?? '#000';
            $filled_color = $options['filled_color_dark'] ?? '#fff';
            $color1 = $options['primary_color'] ?? '#111';
            $color2 = $options['secondary_color'] ?? '#222';
        }
    } else {
        $color = '#000';
        $filled_color = '#fff';
    }
    //$color1 = str_contains( $color1, '#' ) ? str_replace( '#', '', $color1 ) : $color1;
    //$color1 = strlen( $color1 ) == 6 ? '#' . $color1 : $color1;
    //$color2 = str_contains( $color2, '#' ) ? str_replace( '#', '', $color2 ) : $color2;
    //$color2 = strlen( $color2 ) == 6 ? '#' . $color2 : $color2;

    // Loop for Art Styles and Scripts
    $art_ui = $art_ux = '';
    $ui_dir = ROOTPATH . 'assets/styles/aio/art/';
    $ux_dir = ROOTPATH . 'assets/scripts/aio/art/';
    foreach( $arts as $a ) {
        if( file_exists( $ui_dir . $a . '.css' ) && ( !isset( $universal_assets['styles']['art'] ) || !in_array( $a, $universal_assets['styles']['art'] ) ) ) {
            $universal_assets['styles']['art'][] = $a;
            $art_ui .= $a.',';
        }
        if( file_exists( $ux_dir . $a . '.js' ) && ( !isset( $universal_assets['scripts']['art'] ) || !in_array( $a, $universal_assets['scripts']['art'] ) ) ) {
            $universal_assets['scripts']['art'][] = $a;
            $art_ux .= $a.',';
        }
    }
    echo '<style>:root {';
    //skel( $options );
    echo $dark_mode ? '--dark_mode:1;' : '--dark_mode:0;';
    echo '--primary_color:'.$color1.';--secondary_color:'.$color2.';--color:'.$color.';--filled_color:'.$filled_color.';';
    // Loop colors

    if( defined('DB_TYPE') ) {
        $o = new OPTIONS();
        $input_options = $o->input_options;
        $themed_options = $o->themed_options;
        $colors = $o->colors;
    }
    //skel( $options );
    if( !empty( $colors ) ) {
        foreach( $colors as $c ) {
            echo isset( $options[$c] ) ? '--'.$c.':'.$options[$c].';' : '';
        }
    }
    if( !empty( $input_options ) ){
        //skel( $input_options );
        $default_options = [
            'input_radius' => 4,
            'input_bg_light' => '#efefef',
            'input_bg_dark' => 'rgba(0,0,0,.05)',
            'input_border_top' => 1,
            'input_border_right' => 1,
            'input_border_bottom' => 1,
            'input_border_left' => 1,
            'input_border_color_light' => 'rgba(0,0,0,0.1)',
            'input_border_color_active_light' => '#000',
            'input_border_color_dark' => 'rgba(255,255,255,0.1)',
            'input_border_color_active_dark' => '#aaa',
            'input_color_light' => '#000',
            'input_color_dark' => '#fff',
            'input_color_active_dark' => '#fff',
            'input_padding_top' => 8,
            'input_padding_right' => 12,
            'input_padding_bottom' => 8,
            'input_padding_left' => 12,
            'input_margin_top' => 0,
            'input_margin_right' => 0,
            'input_margin_bottom' => 20,
            'input_margin_left' => 0,
        ];
        $rendered = [];
        // Loop through input options from DB
        foreach( $input_options as $ik ){
            $iv = !empty( $options[ $ik ] ) ? $options[ $ik ] : $default_options[ $ik ];
            $iv = strlen( $iv ) > 2 ? $iv.';' : $iv.'px;';
            if( !empty( $iv ) ) {
                echo '--'.$ik.':'.$iv;
                $rendered[] = $ik;
            }
        }
        // Loop through themed options
        if( !empty( $themed_options ) ) {
            foreach( $themed_options as $to ) {
                $tk = $dark_mode ? $to.'_dark' : $to.'_light';
                $tv = !empty( $options[ $tk ] ) ? $options[ $tk ] : $default_options[ $tk ];
                $tv = strlen( $tv ) > 2 ? $tv.';' : $tv.'px;';
                if( !empty( $tv ) ) {
                    echo '--'.$to.':'.$tv;
                    $rendered[] = $to;
                }
            }
        }
        //skel( $rendered );
        // Loop through left out default options
        foreach( $default_options as $do => $dv ) {
            if( !in_array( $do, $rendered ) ) {
                echo '--'.$do.':'.( strlen( $dv ) > 2 ? $dv.';' : $dv.'px;' );
            }
        }
    }
    echo '}.c1{color:'.$color1.'}.c2{color:'.$color2.'}.bg1{background:'.$color1.'}.bg2{background:'.$color2.'}.bs{border:1px solid '.$color1.'}.bf:focus{border:1px solid var(--primary_color)}.grad{color:var(--color);background-color:var(--primary_color);background:-moz-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background:-webkit-linear-gradient(326deg,var(--primary_color) 0%,var(--secondary_color) 100%);background-image:linear-gradient(45deg,var(--primary_color) 0%,var(--secondary_color) 100%);}.grad-text{background: -webkit-linear-gradient(var(--primary_color), var(--secondary_color));-webkit-background-clip:text;-webkit-text-fill-color:transparent;}</style>';
    echo '<link rel="stylesheet" href="'.APPURL.'assets/art.php?arts='.rtrim($art_ui,',').'&fc='.$color1.'&sc='.$color2. $v . '">';
    echo '<script src="'.APPURL.'assets/art.php?type=ux&arts='.rtrim($art_ux,',').$v.'" defer></script>';

}

/**
 * Finds .css and outputs <link>
 * Fetches min.css or css from root/apps/your_app/assets/styles else root/assets/styles/ext else root/assets/styles/aio
 * @param string $f style file name excluding extension
 * @param array $params ['color'=>'red'] becomes ?color=red in link
 * @param string $page_of loads the stylesheet only if you are on this page
 * @author Shaikh <hey@shaikh.dev>
 */
function get_style(string $f, $params = [], $page_of = '') {

    // Gets cache config
    $cache = get_config( 'cache' );
    $v = !empty( $cache ) ? '?v=' . round( time() / ( $cache * 60 ) ) : '';

    // Process GET parameters
    $p = '';
    if( is_array( $params ) ) {
        foreach( $params as $pk => $pv ) {
            $p .= '&'.$pk.'='.$pv;
        }
    }

    // Sets version of stylesheet
    $v = !empty( $v ) ? $v . $p : $p;

    global $universal_assets;

    if( !empty($f) && !in_array( $f, $universal_assets['styles'] )) {
        $universal_assets['styles'][] = $f;
        $style_paths = [
            APPPATH . 'assets/styles/' . $f => APPURL . 'apps/' . APPDIR . '/assets/styles/' . $f,
            ROOTPATH . 'assets/styles/'. $f => APPURL . 'assets/styles/' . $f,
            ROOTPATH . 'assets/styles/ext/'. $f => APPURL . 'assets/styles/ext/' . $f,
            ROOTPATH . 'assets/styles/ext/'. $f . '/' . $f => APPURL . 'assets/styles/ext/' . $f . '/' . $f,
            ROOTPATH . 'assets/styles/aio/' . $f => APPURL . 'assets/styles/aio/' . $f,
            ROOTPATH . 'assets/ext/' . $f => APPURL . 'assets/ext/' . $f,
            ROOTPATH . 'assets/ext/' . $f . '/' . $f => APPURL . 'assets/ext/' . $f . '/' . $f,
            ROOTPATH . 'assets/ext/' . $f .'/css/' . $f => APPURL . 'assets/ext/' . $f . '/css/' . $f,
        ];
        //skel( ROOTPATH . 'assets/ext/' . $f .'css/' . $f );
        $url = asset_exists( $style_paths, $f, 'css' );
        if ( $page_of !== '' ) {
            echo page_of($page_of) && $url !== '' ? '<link rel="stylesheet" href="' . $url . $v . '">' : '';
        } else {
            echo $url !== '' ? '<link rel="stylesheet" href="' . $url . $v . '">' : '';
        }
    }
}

/**
 * Finds .css files and outputs <link>'s
 * @param string $ar CSS files separated by ,
 * @param string $page_of Load only if current page is of
 * @author Shaikh <hey@shaikh.dev>
 */
function get_styles( $ar = '', $page_of = '' ) {
    if( !empty( $ar ) ){
        $ar = is_array( $ar ) ? $ar : explode( ',', str_replace( ' ', '', $ar ) );
        foreach( $ar as $f ){
            if( $page_of !== '' ){
                page_of( $page_of ) ? get_style( $f ) : '';
            } else {
                get_style( $f );
            }
        }
    }
}

/**
 * Finds .js and outputs <script>
 * Fetches min.js or js from root/apps/your_app/assets/scripts else root/assets/scripts/ext else root/assets/scripts/aio
 * @param string $f style file name excluding extension
 * @param array $params ['color'=>'red'] becomes ?color=red in link
 * @param string $page_of loads the stylesheet only if you are on this page
 * @param string $load_mode 'async' or 'defer' tag to load the script
 * @author Shaikh <hey@shaikh.dev>
 */
function get_script(string $f, array $params = [], string $page_of = '', string $load_mode = 'defer' ): void {

    // Gets cache config
    $cache = get_config( 'cache' );
    $v = $cache ? '?v=' . round( time() / ( $cache * 60 ) ) : '?v=31536000';

    // Process GET parameters
    $p = '';
    if( is_array( $params ) ) {
        foreach( $params as $pk => $pv ) {
            $p .= '&'.$pk.'='.$pv;
        }
    }

    // Sets version of script
    $v = !empty( $v ) ? $v . $p : $p;

    global $universal_assets;
    if( !empty($f) && !in_array( $f, $universal_assets['scripts'] )) {
        $universal_assets['scripts'][] = $f;
        $script_paths = [
            APPPATH . 'assets/scripts/' . $f => APPURL . 'apps/' . APPDIR . '/assets/scripts/' . $f,
            ROOTPATH . 'assets/scripts/' . $f => APPURL . 'assets/scripts/' . $f,
            ROOTPATH . 'assets/scripts/ext/' . $f => APPURL . 'assets/scripts/ext/' . $f,
            ROOTPATH . 'assets/scripts/ext/' . $f . '/' . $f => APPURL . 'assets/scripts/ext/' . $f . '/' . $f,
            ROOTPATH . 'assets/scripts/aio/' . $f => APPURL . 'assets/scripts/aio/' . $f,
            ROOTPATH . 'assets/ext/' . $f => APPURL . 'assets/ext/' . $f,
            ROOTPATH . 'assets/ext/' . $f . '/' . $f => APPURL . 'assets/ext/' . $f . '/' . $f,
            ROOTPATH . 'assets/ext/' . $f . '/js/' . $f => APPURL . 'assets/ext/' . $f . '/js/' . $f,
        ];
        $url = asset_exists( $script_paths, $f, 'js' );

        // If script is recaptcha, get recaptcha key and link to google recaptcha
        if( str_contains( $f, 'http' ) ) {
            echo '<script src="' . $f . '" '.$load_mode.'></script>';
            return;
        } else if( $f == 'recaptcha' ) {
            $site_key = get_config('recaptcha_site_key');
            if( !empty( $site_key ) ) {
                $url = 'https://www.google.com/recaptcha/api.js?render='.$site_key;
                echo '<script data-recaptcha src="' . $url . '" '.$load_mode.'></script>';
                return;
            }
        } else if( $f == 'stripe' ) {
            echo '<script src="https://js.stripe.com/v3/" '.$load_mode.'></script>';
        }
        if( $page_of !== '' ){
            echo page_of( $page_of ) && $url !== '' ? '<script src="' . $url . $v . '" '.$load_mode.'></script>' : '';
        } else {
            echo $url !== '' ? '<script src="' . $url . $v . '" '.$load_mode.'></script>' : '';
        }

    }
}

/**
 * Finds .js files and outputs <script>'s
 * @param string|array $ar JS files separated by ,
 * @param string|array $page_of Load only if current page is of
 * @param string $load_mode 'async' or 'defer' tag to load the script
 * @author Shaikh <hey@shaikh.dev>
 */
function get_scripts( string|array $ar = '', string|array $page_of = '', string $load_mode = 'defer' ): void {
    if( !empty( $ar ) ){
        $ar = is_array( $ar ) ? $ar : explode( ',', str_replace( ' ', '', $ar ) );
        foreach( $ar as $f ){
            if( $page_of !== '' ){
                page_of( $page_of ) ? get_script( $f, [], '', $load_mode ) : '';
            } else {
                get_script( $f, [], '', $load_mode );
            }
        }
    }
}

/**
 * Checks if asset file exist then return url
 * @param array $paths Paths and respective location of file
 * @param string $f Asset file name
 * @param string $ext File extension
 * @return string
 * @author Shaikh <hey@shaikh.dev>
 */
function asset_exists( array $paths = [], string $f = '', string $ext = '' ): string {
    $url = '';
    foreach( $paths as $path => $link ) {
        if( file_exists( $path . '.php' ) )
            $url = $link . '.php';
        else if( file_exists( $path . '.min.' . $ext ) )
            $url = $link . '.min.' . $ext;
        else if( file_exists( $path . '.' . $ext ) )
            $url = $link . '.' . $ext;
        else if( strpos( $f, 'http') !== false )
            $url = $f;
        //else
            //elog( $f, 'error' );
    }
    return $url;
}

/**
 * Stylesheet <link> to load multiple Fonts
 * @param array $array Fonts array Ex: [ 'Lato' => '300,500', 'Dubai' => 'Light' ]
 * @author Shaikh <hey@shaikh.dev>
 */
function fonts( array $array = [] ): void {
    $appdir = !empty( get_domain('sub') ) ? get_domain( 'sub' ) : get_domain();
    !defined( 'APPDIR' ) ? define( 'APPDIR', $appdir ) : '';

    $fonts = [];
    foreach( $array as $font => $weights ){
        $weights = $weights ?? '400';
        $fonts[] = $font.':'.$weights;
    }
    echo !empty( $fonts ) ? '<link rel="stylesheet" href="' . APPURL . 'assets/fonts.php?'.APPDIR.'='. implode( '|', $fonts ) .'">' : '';
}

/**
 * Stylesheet <link> to single Font
 * @param string $font Font name
 * @param string $weights Font weights Ex: 400,600
 * @author Shaikh <hey@shaikh.dev>
 */
function font( string $font = 'Lato', string $weights = '400' ): void {
    echo '<link rel="stylesheet" href="' . APPURL . 'assets/fonts.php?'.APPDIR.'='. $font . ':'. $weights .'">';
}

/**
 * Auto loads svg icons in assets/icons as stylesheet
 * @author Shaikh <hey@shaikh.dev>
 */
function icons() {
    echo '<link rel="stylesheet" href="' .APPURL . 'assets/icons.php?'.APPDIR.'=1">';
}

/**
 * Checks if php file exists in comp dir
 * @param string $component Name of the comp
 * @param string $dir Name of directory
 * @return string
 */
function comp_exists( string $component, string $dir ): string {
    $ns = explode( '/', str_replace( '.php', '', $component ) );
    $x = 1;
    $fl = APPPATH . $dir . '/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    return file_exists( $fl ) ? $fl : '';
}

/**
 * Load component file if exists
 * @param string $n Comp file name excluding .php
 */
function get_comp( string $n ): void {
    $app_file = comp_exists( $n, 'components' );
    if( $app_file ) {
        include($app_file);
    } else if( file_exists( ROOTPATH . 'core/components/' . $n . '.php' ) ) {
        include_once(ROOTPATH . 'core/components/' . $n . '.php');
    }
}

/**
 * Loads modal file if exists
 * @param string $n Modal file name excluding .php
 */
function get_modal( string $n ): void {
    $n = comp_exists( $n, 'modals' );
    $n ? include_once( $n ) : '';
}

/**
 * Gets page file
 * @param string $n Page file name excluding .php
 */
function get_page( string $n ): void {
    $ns = explode('/',$n);
    $x = 1;
    $fl = APPPATH . '/pages/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    file_exists( $fl ) ? include( $fl ) : '';
}

function aio_page( $ui, $array = [] ): void {
    $page = ROOTPATH . 'core/pages/' . $ui . '.php';
    if( file_exists( $page ) ) {
        if( !empty( $array ) ) {
            global $params;
            $params = $array;
        }
        include $page;
    }
}

/**
 * Renders a <link rel="icon"> from icon file name
 * @param string $icon
 */
function favicon( string $icon ): void {
    if( !empty( $icon ) ) {
        if( file_exists( APPPATH . 'assets/images/' . $icon . '.png' ) ) {
            $link = APPURL . 'apps/' . APPDIR . '/assets/images/'.$icon.'.png';
            echo '<link rel="shortcut icon" href="'.$link.'">';
            if (is_mobile()) {
                echo file_exists( APPPATH . 'assets/images/' . $icon . '.png' ) ? '<link rel="apple-touch-icon" href="'.$link.'"/>' : '';
                $sizes = [144,114,72,57];
                foreach( $sizes as $s ){
                    echo file_exists( APPPATH . 'assets/images/' . $icon . '-' . $s . '.png' ) ? '<link rel="apple-touch-icon" href="'.APPURL . 'apps/' . APPDIR . 'assets/images/'.$icon.'-'.$s.'.png"/>' : '';
                }
            }
        } else if( str_contains( $icon, 'http' ) ) {
            echo '<link rel="shortcut icon" href="'.$icon.'">';
        }
    }
}

/**
 * Renders attributes and class for <html> start tag
 * @param string $class
 */
function html_class( string $class = '' ): void {
    $lang = $_SESSION['lang'] ?? 'en';

    // Is Debug
    $dc = APPDEBUG ? 'debug ' : '';

    // Is RTL
    $dir = in_array( $lang, ['ar','iw','ku','fa','ur'] ) ? 'dir="rtl"' : 'dir="ltr"';

    // Custom class
    $ec = !empty( $class ) ? 'class="'.$class.' '.$dc.'"' : '';

    // Final Output
    echo 'lang="'.$lang.'" ' . $dir . ' ' . $ec;
}

/**
 * Renders attributes and class for <body> start tag
 * @param string $class
 */
function body_class( string $class = '' ): void {

    // Is Debug
    $dc = defined('APPDEBUG') && APPDEBUG ? 'debug ' : '';

    // Page path
    $pc = defined('PAGEPATH') && PAGEPATH ? str_replace('/',' ',PAGEPATH) : '';

    // Custom class
    $ec = !empty( $class ) ? ' '.$class : '';

    // Dark mode
    $dm = isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] == 'true';
    $dm = !empty( $dm ) ? ' d' : '';

    // Get Client Info
    $dd = '';
    if( class_exists('AGENT') ) {
        $client = new AGENT();
        $dev = strtolower(str_replace(' ', '_', $client->get_device_type()));
        $os = strtolower(str_replace(' ', '_', $client->get_os()));
        $brow = strtolower(str_replace(' ', '_', $client->get_browser()));
        $dd = 'data-device="'.$dev.'" data-os="'.$os.'" data-browser="'.$brow.'"';
    }

    // Final output
    echo 'class="'.$dc.$pc.$ec.$dm.'" '.$dd;
}

/**
 * Renders easily readable <title> tag with title
 * @param string $title
 * @param bool $join_app_name
 */
function get_title( string $title = '', bool $join_app_name = true ): void {
    $app_name = defined( 'APP_NAME' ) ? APP_NAME : ( defined( 'APPNAME' ) ? APPNAME : '' );
    $add = $join_app_name ? ' - '.T( $app_name ) : '';
    if( !empty( $title ) ) {
        echo '<title>'.$title.$add.'</title>';
        return;
    }
    if( defined( 'PAGE' ) && !empty( PAGE ) ) {
        $p = PAGE == 'index' ? 'Welcome' : PAGE;
        $pp = explode('/',PAGEPATH);
        $p = is_numeric( $p ) ? $pp[count($pp)-1] : $p;
        echo '<title>'.T( ucwords( str_replace('-',' ', str_replace('_',' ', $p )) ) ) .$add.'</title>';
    } else {
        echo '<title>'.T( $app_name ).'</title>';
    }
}

/**
 * Sets a title through jQuery, Not SEO friendly
 * @param $title
 */
function set_title( $title ): void {
    echo '<script>$(document).ready(function() { document.title = "' . $title . ' - ' . APPNAME . '"; });</script>';
}

/**
 * Renders APP URL from a string
 * @param string $link
 */
function APPURL( string $link ): void {
    echo APPURL.$link;
}

/**
 * Renders a back link relative to current page
 * @param string $url
 * @param string $title
 */
function back_link( string $url = './', string $title = '' ): void {
    echo '<a id="back" class="nico" href="'.$url.'">'.$title.'</a>';
}

/**
 * @param string $number Phone Number (with code)
 * @param string $class Additional Class
 */
function call_link( string $number = '', string $class = '' ): void {
    echo '<a href="tel:'.$number.'" class="call_link '.$class.'">'.$number.'</a>';
}

/**
 * @param string $email Email address
 * @param string $class Additional Class
 */
function mail_link( string $email = '', string $class = '' ): void {
    echo '<a href="mailto:'.$email.'" class="mail_link '.$class.'">'.$email.'</a>';
}

/**
 * Check if current page is given page name
 * @param string|array $p page name
 * @return bool
 */
function page_is( string|array $p = '' ): bool {
    return defined('PAGE') && ((is_array($p) ? in_array(PAGE, $p) : PAGE == $p));
}

/**
 * Check if current page is or child of given page name
 * @param string|array $p page name
 * @return bool
 */
function page_of( string|array $p ): bool {
    $results = [];
    $page_ofs = is_array( $p ) ? $p : explode( ',', $p );
    $pages = explode( '/', PAGEPATH );
    foreach( $page_ofs as $of ) {
        $results[] = in_array( $of, $pages );
    }
    return in_array( true, $results );
}

/**
 * Renders menu html from array
 * @param array $array
 * @param string $prefix
 */
function render_menu( array $array, string $prefix = '', string $wrap_class = '', string $list_class = '' ): void {
    //$array = is_array( $array ) ? $array : explode(',',$array);
    $wrap_class = !empty( $wrap_class ) ? ' class="'.$wrap_class.'"' : '';
    $list_class_text = !empty( $list_class ) ? $list_class : '';
    $list_class = !empty( $list_class ) ? ' class="'.$list_class.'"' : '';
    echo '<ul'.$wrap_class.'>';
    foreach( $array as $first_row ){

        $title = !empty( $first_row[1] ) && !is_array( $first_row[1] ) ? $first_row[1] : ucwords( $first_row[0] );

        $url = explode( '/', PAGEPATH );
        $page_class = strtolower(str_replace(' ','_',$title));
        $first_row_class = PAGEPATH == $first_row[0] ? 'class="on '.$page_class.'"' : 'class="'.$page_class.'"';
        $first_li_class = $url[0] == $first_row[0] ? 'class="open '.$list_class_text.'"' : '';

        $sec_rows = '';
        if( !empty( $first_row[2] ) && is_array( $first_row[2] ) ){
            $sec_rows = $first_row[2];
        } else if( !empty( $first_row[1] ) && is_array( $first_row[1] ) ) {
            $sec_rows = $first_row[1];
        }

        $sec = '';
        if( !empty( $sec_rows ) && is_array( $sec_rows ) ){
            $sec .= '<i></i><ul>';
            foreach( $sec_rows as $sec_row ){
                $sec_title = !empty( $sec_row[1] ) && !is_array( $sec_row[1] ) ? $sec_row[1] : ucwords( $sec_row[0] );
                $sec_row_class = $url[count($url)-1] == $sec_row[0] ? 'class="on '.strtolower(str_replace(' ','_',$sec_title)).'"' : '';
                if( $sec_row[0] == '' && $url[count($url)-1] == $url[0] ){
                    $sec_row_class = 'class="on '.strtolower(str_replace(' ','_',$sec_title)).'"';
                }
                $sec .= '<li'.$list_class.'><a href="'.APPURL.$prefix.$first_row[0].'/'.$prefix.$sec_row[0].'" '.$sec_row_class.'>'.T($sec_title).'</a></li>';
            }
            $sec .= '</ul>';
        }
        echo '<li '.$first_li_class.'><a href="'.APPURL.$prefix.$first_row[0].'" '.$first_row_class.'>'.T($title).'</a>'.$sec.'</li>';
        /*    $title = ucfirst( $l );
            $sls .= '<ul>';
            foreach( $t as $sl => $st ){
                $link = APPURL.$l.'/'.$prefix.$sl;
                if( PAGEPATH == $sl ) { $sc = 'class="on"'; $c = 'on'; } else { $sc = ''; }
                $sls .= '<li><a href="'.$link.'" '.$sc.'>'.$st.'</a></li>';
            }
            $sls .= '</ul>';
        }
        $s = !empty($sls) ? $c.' sub' : $c.'';
        echo '<li><a href="'.APPURL.$prefix.$l.'" class="'.$c.'">'.$title.'</a>'.$sls.'</li>'; */
    }
    echo '</ul>';

    !empty( $title ) && !defined( 'PAGET' ) ? define( 'PAGET', $title ) : '';
}

function dashboard_menu() {

}

function render_details( string $title = '', array $data = [], int $col = 4 ) {
    echo '<div class="group_details"><h2>'. T( $title ). '</h2><div class="row">';
    if( !empty( $data ) ) {
        foreach( $data as $dk => $d ) {
            $k = $d['k'] ?? '';
            $v = $d['v'] && isset( $d['s'] ) ? implode( '</div><div class="tag">', explode( $d['s'], $d['v'] ) ) : $d['v'];
            $v = !empty( $v ) ? '<div class="tag">'.$v.'</div>' : '';
            $c = $d['c'] ?? $col;
            echo '<div class="col-12 col-md-'.$c.'"><div class="set"><div class="key">'.$k.'</div><div class="tags">'.$v.'</div></div></div>';
        }
    }
    echo '</div></div>';
}

/**
 * Renders pagination
 * @param int $page Current page
 * @param int $records Number of total records in database
 * @param int $limit Maximum items limit per page
 * @param string $wrap_class = '' Class for page links wrapper element
 * @param string $class Class for page links
 */
function pagination( int $page, int $records, int $limit = 24, string $wrap_class = '', string $class = 'page_link' ): void {
    $url = APPURL . PAGEPATH;
    if( $limit > 0 ) {
        $wrap_class = !empty( $wrap_class ) ? $wrap_class . ' pagination' : 'pagination';
        ?>
        <div class="<?php echo $wrap_class; ?>">
            <?php
            $total_pages = !empty( $records ) ? ceil( $records / $limit ) : 0;
            //skel( $total_pages );
            if( $total_pages > 3 ) {
                // echo '<a href="'.$url.'/1" class="first '.$class.'"></a>';
                echo $page > 3 ? '<a href="' . $url . '/1" class="first ' . $class . '">1</a>' : '';
                echo $page > 3 ? '<a  class="blank">...</a>' : '';
                for ($x = ($page - 2); $x < $page; $x++) {
                    echo $x > 0 ? '<a href="' . $url . '/' . $x . '" class="pre ' . $class . '">' . $x . '</a>' : '';
                }
                echo '<a href="' . $url . '/' . $page . '" class="on ' . $class . '">' . $page . '</a>';
                for ($y = ($page + 1); $y <= ($page + 2); $y++) {
                    echo $page < $total_pages ? '<a href="' . $url . '/' . $y . '" class="post ' . $class . '">' . $y . '</a>' : '';
                }
                echo $page < ($total_pages - 3) ? '<a  class="blank">...</a>' : '';
                echo $page < ($total_pages - 2) ? '<a href="' . $url . '/' . $total_pages . '" class="last ' . $class . '">' . $total_pages . '</a>' : '';
            } else if( $total_pages > 1 ) {
                for($x = 1; $x <= $total_pages; $x++) {
                    $on = $x == $page ? ' on' : '';
                    echo '<a href="' . $url . '/' . $x . '" class="'. $class . $on . '">' . $x . '</a>';
                }
            }
            // echo '<a href="'.$url.'/'.$total_pages.'" class="last '.$class.'"></a>';
            ?>
        </div>
        <?php
    }
}

/**
 * Create jQuery Notification
 * @param string $message
 */
function notify( string $message ) {
    echo '<script>$(document).ready(function(){ notify("' . $message . '"); });</script>';
}

/**
 * Clears Log File
 */
function clear_log_viewer() {
    $file = $_POST['file'] ?? ini_get('error_log');
    if( $file = fopen( $file, 'w' ) ) {
        $clear = fwrite( $file, '' );
        if( $clear == '' ) {
            ES('Log Cleared!');
        } else {
            EF('Log could not be cleared');
        }
        fclose( $file );
    }
}

function tab_nav( bool $show_prev_button = false, bool $show_next_button = false, string $prev_text = '', string $next_text = '', string $class = '', string $col = '6' ): void {
    echo '<div class="row">';
    $col = is_numeric( $col ) ? 'col-'.$col : $col;
    if( $show_prev_button ) {
        previous_tab( $prev_text, $class, $col );
    } else {
        echo '<div class="'.$col.'"></div>';
    }
    if( $show_next_button ) {
        next_tab( $next_text, $class, $col );
    }
    echo '</div>';
}

function previous_tab( string $text = '', string $class = 'prev', string $col = '6' ): void {
    $col = is_numeric( $col ) ? 'col-'.$col : $col;
    echo '<div class="'.$col.'"><button type="button" class="'.$class.'" data-prev>'. T( $text ). '</button></div>';
}

function next_tab( string $text = '', string $class = 'next', string $col = '6' ): void {
    $col = is_numeric( $col ) ? 'col-'.$col : $col;
    echo '<div class="'.$col.' tar"><button type="button" class="'.$class.'" data-next>'. T( $text ). '</button></div>';
}
