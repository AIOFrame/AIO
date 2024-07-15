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
    global $light_mode;

    $cache = get_config( 'cache' );
    $v = $cache ? '&v=' . round( time() / ( $cache * 60 ) ) : '';
    $arts = !is_array( $arts ) ? explode( ',', $arts ) : $arts;

    // Setting Colors
    // TODO: Colors logic is repeating
    if( $color1 == '222' && $color2 == '000' ) {
        $theme = $options['default_theme'] ?? '';
        $theme = $options['theme'] ?? $theme;
        $dark_mode = str_contains( $theme, 'dark' );
        if( $light_mode == 'l' ) {
            $color = $options['color_dark'] ?? '#fff';
            $filled_color = $options['filled_color_dark'] ?? '#fff';
            $color1 = $options['primary_color_dark'] ?? $color1;
            $color2 = $options['secondary_color_dark'] ?? $color2;
        } else if( $light_mode == 'd' ) {
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

    // Loop colors
    if( defined('DB_TYPE') ) {
        $o = new OPTIONS();
        $colors = $o->colors;
    }
    $f = new FORM();
    $input_options = $f->input_options;
    $themed_options = $f->themed_options;
    //skel( $options );
    //skel( $input_options );
    echo '<style>:root {';
    if( !empty( $colors ) ) {
        foreach( $colors as $c ) {
            echo isset( $options[$c] ) ? '--'.$c.':'.$options[$c].';' : '';
        }
    }
    if( !empty( $input_options ) ){
        $rendered = [];
        // Loop through input options from DB
        foreach( $input_options as $ik => $div ){
            $iv = $options[ $ik ] ?? $div;
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
                $tv = !empty( $options[ $tk ] ) ? $options[ $tk ] : $input_options[ $tk ];
                $tv = strlen( $tv ) > 2 ? $tv.';' : $tv.'px;';
                if( !empty( $tv ) ) {
                    echo '--'.$to.':'.$tv;
                    $rendered[] = $to;
                }
            }
        }
        //skel( $rendered );
        // Loop through left out default options
        /* foreach( $default_options as $do => $dv ) {
            if( !in_array( $do, $rendered ) ) {
                echo '--'.$do.':'.( strlen( $dv ) > 2 ? $dv.';' : $dv.'px;' );
            }
        } */
    }
    echo '}</style>';
    echo '<link rel="stylesheet" href="'.APPURL.'assets/art.php?arts='.rtrim($art_ui,',').'&fc='.$color1.'&sc='.$color2. $v . '">';
    echo '<script src="'.APPURL.'assets/art.php?type=ux&arts='.rtrim($art_ux,',').$v.'" defer></script>';

}

function get_style( string $f, array $params = [], string $page_of = '' ): void {
    echo __get_style( $f, $params, $page_of );
}

/**
 * Finds .css and outputs <link>
 * Fetches min.css or css from root/apps/your_app/assets/styles else root/assets/styles/ext else root/assets/styles/aio
 * @param string $f style file name excluding extension
 * @param array $params ['color'=>'red'] becomes ?color=red in link
 * @param string $page_of loads the stylesheet only if you are on this page
 * @author Shaikh <hey@shaikh.dev>
 */
function __get_style( string $f, array $params = [], string $page_of = '' ): string {
    $r = '';
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
            $r = page_of($page_of) && $url !== '' ? '<link rel="stylesheet" href="' . $url . $v . '">' : '';
        } else {
            $r = $url !== '' ? '<link rel="stylesheet" href="' . $url . $v . '">' : '';
        }
    }
    return $r;
}

function get_styles( $styles = '', string $page_of = '' ): void {
    echo __get_styles( $styles, $page_of );
}

/**
 * Finds .css files and outputs <link>'s
 * @param string|array $styles CSS files separated by ,
 * @param string $page_of Load only if current page is of
 * @author Shaikh <hey@shaikh.dev>
 */
function __get_styles( string|array $styles = '', string $page_of = '' ): string|null {
    $r = '';
    if( !empty( $styles ) ){
        $styles = is_array( $styles ) ? $styles : explode( ',', str_replace( ' ', '', $styles ) );
        foreach( $styles as $f ){
            if( $page_of !== '' ){
                $r .= page_of( $page_of ) ? __get_style( $f ) : '';
            } else {
                $r .= __get_style( $f );
            }
        }
    }
    return $r;
}

function get_script( string $f, array $params = [], string $page_of = '', string $load_mode = 'defer' ): void {
    echo __get_script( $f, $params, $page_of, $load_mode );
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
function __get_script( string $f, array $params = [], string $page_of = '', string $load_mode = 'defer', int|float|string|bool $cache = '' ): string {
    $r = '';
    // Gets cache config
    if( is_numeric( $cache ) || is_float( $cache ) ) {
        $v = '?v=' . round( time() / ( $cache * 60 ) );
    } else if( in_array( $cache, [ 'avoid', 'dont', 'nope', 'false' ] ) || !$cache ) {
        $v = '';
    } else if( defined( 'CONFIG' ) ) {
        $cache = CONFIG['cache'];
        $v = '?v=' . round( time() / ( $cache * 60 ) );
    } else {
        $v = '?v=31536000';
    }
    //$cache = empty( $cache ) ? get_config( 'cache' ) : $cache;
    //$v = $cache ?  :

    // Process GET parameters
    $p = '';
    if( is_array( $params ) ) {
        foreach( $params as $pk => $pv ) {
            $p .= '&'.$pk.'='.$pv;
        }
    }

    // Sets version of script
    $v = !empty( $v ) ? $v . $p : '?' . ltrim( $p, '&' );

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
            $r = '<script src="' . $f . '" '.$load_mode.'></script>';
        } else if( $f == 'recaptcha' ) {
            $site_key = get_config('recaptcha_site_key');
            if( !empty( $site_key ) ) {
                $url = 'https://www.google.com/recaptcha/api.js?render='.$site_key;
                $r = '<script data-recaptcha src="' . $url . '" '.$load_mode.'></script>';
            }
        } else if( $f == 'stripe' ) {
            $r = '<script src="https://js.stripe.com/v3/" '.$load_mode.'></script>';
        }
        if( $page_of !== '' ){
            $r = page_of( $page_of ) && $url !== '' ? '<script src="' . $url . $v . '" '.$load_mode.'></script>' : '';
        } else {
            $r = $url !== '' ? '<script src="' . $url . $v . '" '.$load_mode.'></script>' : '';
        }

    }
    return $r;
}

function get_scripts( string|array $ar = '', string|array $page_of = '', string $load_mode = 'defer' ): void {
    echo __get_scripts( $ar, $page_of, $load_mode );
}

/**
 * Finds .js files and outputs <script>'s
 * @param string|array $ar JS files separated by ,
 * @param string|array $page_of Load only if current page is of
 * @param string $load_mode 'async' or 'defer' tag to load the script
 * @author Shaikh <hey@shaikh.dev>
 */
function __get_scripts( string|array $ar = '', string|array $page_of = '', string $load_mode = 'defer' ): string {
    $r = '';
    if( !empty( $ar ) ){
        $ar = is_array( $ar ) ? $ar : explode( ',', str_replace( ' ', '', $ar ) );
        foreach( $ar as $f ){
            if( $page_of !== '' ){
                $r .= page_of( $page_of ) ? __get_script( $f, [], '', $load_mode ) : '';
            } else {
                $r .= __get_script( $f, [], '', $load_mode );
            }
        }
    }
    return $r;
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

    // Region Info
    $rm = '';
    if( defined( 'REGION' ) && !empty( REGION ) ) {
        $rm = ' data-country="'.REGION['country'].'" data-call="'.REGION['call'].'" data-cca2="'.REGION['cca2'].'" data-cca3="'.REGION['cca3'].'" data-symbol="'.REGION['symbol'].'"';
    }

    // If Ecommerce is enabled add related params
    $em = '';

    // Final output
    echo 'class="'.$dc.$pc.$ec.$dm.'" '.$dd.$rm;
}

/**
 * Renders easily readable <title> tag with title
 * @param string $title
 * @param bool $join_app_name
 */
function get_title( string $title = '', bool $join_app_name = true ): void {
    global $options;
    $region = $options['region'] ?? '';
    $region = !empty( $region ) ? strtolower( $region ) . '_' : '';
    //$app_name = defined( 'APP_NAME' ) ? APP_NAME : ( defined( 'APPNAME' ) ? APPNAME : '' );
    $app_name = $options[ $region . 'app_name' ] ?? ( $options['app_name'] ?? ( defined( 'APP_NAME' ) ? APP_NAME : ( defined( 'APPNAME' ) ? APPNAME : 'AIO Application' ) ) );
    $add = $join_app_name ? ' - '.T( $app_name ) : '';
    if( !empty( $title ) ) {
        el( 'title', '', $title.$add );
        return;
    }
    if( defined( 'PAGE' ) && !empty( PAGE ) ) {
        $p = PAGE == 'index' ? 'Welcome' : PAGE;
        $pp = explode('/',PAGEPATH);
        $p = is_numeric( $p ) ? $pp[count($pp)-1] : $p;
        el( 'title', '', T( ucwords( str_replace('-',' ', str_replace('_',' ', $p )) ) ) .$add );
    } else {
        el( 'title', '', T( $app_name ) );
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
    $url = APPURL . $url;
    a( APPURL . $url, $title, 'nico', T('Go Back'), 'id="back"' );
}

/**
 * @param string $number Phone Number (with code)
 * @param string $class Additional Class
 */
function call_link( string $number = '', string $class = '' ): void {
    a( 'tel:' . $number, $number, 'call_link '.$class, $number );
}

/**
 * @param string $email Email address
 * @param string $class Additional Class
 */
function mail_link( string $email = '', string $class = '' ): void {
    a( 'mailto:' . $email, $email, 'mail_link '.$class, $email );
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
 * @param string $url_prefix
 * @param string $wrap_class
 * @param string $list_class
 */
function render_menu( array $array, string $url_prefix = '', string $wrap_class = '', string $list_class = '' ): void {
    global $options;
    //$array = is_array( $array ) ? $array : explode(',',$array);
    $wrap_class = !empty( $wrap_class ) ? ' class="'.$wrap_class.'"' : '';
    $list_class_text = !empty( $list_class ) ? $list_class : '';
    $list_class = !empty( $list_class ) ? ' class="'.$list_class.'"' : '';
    pre( '', $wrap_class, 'ul' );
    if( is_assoc( $array ) ) {
        foreach( $array as $url => $data ) {
            $title = $data['title'] ?? ( $data['t'] ?? ( $data['name'] ?? ( $data['n'] ?? '' ) ) );
            $ico = $data['icon'] ?? ( $data['ico'] ?? ( $data['i'] ?? '' ) );
            $ico = !empty( $ico ) ? __div( $options['icon_class'] ?? '', $ico ) : '';
            el( 'li', '', _a( APPURL . $url_prefix . $url ) . $ico . $title . a_() );
        }
    } else {
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
                    $sec .= '<li'.$list_class.'><a href="'.APPURL.$url_prefix.$first_row[0].'/'.$url_prefix.$sec_row[0].'" '.$sec_row_class.'>'.T($sec_title).'</a></li>';
                }
                $sec .= '</ul>';
            }
            echo '<li '.$first_li_class.'><a href="'.APPURL.$url_prefix.$first_row[0].'" '.$first_row_class.'>'.T($title).'</a>'.$sec.'</li>';
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
    }
    post('ul');

    !empty( $title ) && !defined( 'PAGET' ) ? define( 'PAGET', $title ) : '';
}

function dashboard_menu() {

}

/**
 * Returns info sets
 * @param string $title Title of the whole group of details
 * @param array $data Data as assoc array [ 'key' => 'value' ] or [ [ 'key' => '', 'v' => '', 'col' => '' ] ]
 * @param int|string $col Bootstrap column for each data set
 * @param string $wrap_class Wrapper class
 * @return string
 */
function __render_details( string $title = '', array $data = [], int|string $col = 4, string $wrap_class = '' ): string {
    $return = __pre( '', 'group_details '.$wrap_class ) . ( !empty( $title ) ? __h2( T( $title ) ) : '' ) . __pre( '', 'row' );
    if( !empty( $data ) ) {
        if( is_assoc( $data ) ) {
            foreach( $data as $dk => $d ) {
                $return .= __div( 'col-12 col-md-' . $col, __div( 'set', __div( 'key', $dk ) . __div( 'tags', __div( 'tag', $d ) ) ) );
            }
        } else {
            foreach( $data as $d ) {
                $k = $d['key'] ?? ( $d['k'] ?? '' );
                $v = $d['v'] && isset( $d['s'] ) ? implode( '</div><div class="tag">', explode( $d['s'], $d['v'] ) ) : $d['v'];
                $v = !empty( $v ) ? __div( 'tag', $v ) : '';
                $c = $d['col'] ?? ( $d['c'] ?? $col );
                $return .= __div( 'col-12 col-md-' . $c, __div( 'set', __div( 'key', $k ) . __div( 'tags', $v ) ) );
            }
        }
    }
    $return .= __post() . __post();
    return $return;
}

/**
 * Renders info sets
 * @param string $title Title of the whole group of details
 * @param array $data Data as assoc array [ 'key' => 'value' ] or [ [ 'key' => '', 'v' => '', 'col' => '' ] ]
 * @param int|string $col Bootstrap column for each data set
 * @param string $wrap_class Wrapper class
 * @return void
 */
function render_details( string $title = '', array $data = [], int|string $col = 4, string $wrap_class = '' ): void {
    echo __render_details( $title, $data, $col, $wrap_class );
}

function shuffle_assoc($array): array {
    $keys = array_keys($array);
    shuffle($keys);
    foreach($keys as $key) {
        $new[$key] = $array[$key];
    }
    return $new;
}

/**
 * Returns pagination
 * @param int $page Current page
 * @param int $records Number of total records in database
 * @param int $limit Maximum items limit per page
 * @param string $wrap_class = '' Class for page links wrapper element
 * @param string $class Class for page links
 */
function __pagination( int $page, int $records, int $limit = 24, string $wrap_class = '', string $class = 'page_link', string $page_link = '/' ): string {
    $url = APPURL . PAGEPATH;
    $r = '';
    if( $limit > 0 ) {
        $wrap_class = !empty( $wrap_class ) ? $wrap_class . ' pagination' : 'pagination';
        $r .= __pre( '', $wrap_class );
            $total_pages = !empty( $records ) ? ceil( $records / $limit ) : 0;
            //skel( $total_pages );
            if( $total_pages > 3 ) {
                $r .= $page > 3 ? __anchor( $url.$page_link.'1', $class.' first', 'Goto first page' ) . 1 . anchor__() : '';
                $r .= $page > 4 ? __anchor( '', 'blank' ).'...'.anchor__() : '';
                for ($x = ($page - 2); $x < $page; $x++) {
                    $r .=$x > 0 ? __anchor( $url.$page_link.$x, $class.' pre', 'Goto page '.$x ) . $x . anchor__() : '';
                }
                $r .= __anchor( $url.$page_link.$page, $class.' on', T('Reload current page') ) . $page . anchor__();
                for ($y = ($page + 1); $y <= ($page + 2); $y++) {
                    $r .= $y <= $total_pages ? __anchor( $url.$page_link.$y, $class.' post', 'Goto page '.$y ) . $y . anchor__() : '';
                }
                $r .= $page < ($total_pages - 3) ? __anchor( '', 'blank' ) . '...' . anchor__() : '';
                $r .= $page < ($total_pages - 2) ? __anchor( $url.$page_link.$total_pages, $class.' last', 'Goto last page' ) . $total_pages . anchor__() : '';
            } else if( $total_pages > 1 ) {
                for($x = 1; $x <= $total_pages; $x++) {
                    $on = $x == $page ? ' on' : '';
                    $r .= __anchor( $url.$page_link.$x, $class.$on, 'Goto page '.$x ) . $x . anchor__();
                }
            }
            // echo '<a href="'.$url.'/'.$total_pages.'" class="last '.$class.'"></a>';
        $r .= __post();
    }
    return $r;
}

/**
 * Renders pagination
 * @param int $page Current page
 * @param int $records Number of total records in database
 * @param int $limit Maximum items limit per page
 * @param string $wrap_class = '' Class for page links wrapper element
 * @param string $class Class for page links
 */
function pagination( int $page, int $records, int $limit = 24, string $wrap_class = '', string $class = 'page_link', string $page_link = '/' ): void {
    echo __pagination( $page, $records, $limit, $wrap_class, $class, $page_link );
}

/**
 * Create jQuery Notification
 * @param string $message
 */
function notify( string $message ): void {
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
    pre( '', 'row' );
    $col = is_numeric( $col ) ? 'col-'.$col : $col;
    if( $show_prev_button ) {
        previous_tab( $prev_text, $class, $col );
    } else {
        div( $col );
    }
    if( $show_next_button ) {
        next_tab( $next_text, $class, $col );
    }
    post();
}

function previous_tab( string $text = '', string $class = 'prev', string $col = '6' ): void {
    $col = is_numeric( $col ) ? 'col-'.$col : $col;
    pre( '', $col );
        b( $class, $text, '', 'data-prev', 1 );
    post();
    //echo '<button type="button" class="'.$class.'" data-prev>'. T( $text ). '</button>';
}

function next_tab( string $text = '', string $class = 'next', string $col = '6' ): void {
    $col = is_numeric( $col ) ? 'col-'.$col : $col;
    pre( '', $col );
        b( $class, $text, '', 'data-next', 1 );
    post();
    //echo '<div class="'.$col.' tar"><button type="button" class="'.$class.'" data-next>'. T( $text ). '</button></div>';
}
