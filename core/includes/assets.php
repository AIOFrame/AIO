<?php

if( !defined( 'COREPATH' ) ) { exit(); }

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
 * @param string $weight Default font weight
 * @param int $scrollbar Default scrollbar width
 * @author Shaikh <hey@shaikh.dev>
 */
function reset_styles( $font = '', $weight = 'normal', $scrollbar = 5 ) {
    $cache = get_config( 'cache' );
    $v = $cache ? '&v=' . round( time() / ( $cache * 60 ) ) : '';
    echo '<link rel="stylesheet" href="'.APPURL.'assets/styles/aio/reset.php?font='.str_replace(' ','$',$font).'&weight='.$weight.'&scrollbar='.$scrollbar.'">';
}

/**
 * Fetches AIO Art style modules
 * @param string $color1 color code hex for first color
 * @param string $color2 color code hex for second color
 * @param string $arts art modules separated by commas
 * @author Shaikh <hey@shaikh.dev>
 */
function art( $color1 = '222', $color2 = '000', $arts = '' ) {
    $cache = get_config( 'cache' );
    $v = $cache ? '&v=' . round( time() / ( $cache * 60 ) ) : '';
    $arts = is_array( $arts ) ? implode( ',', $arts ) : $arts;
    global $universal_assets;
    if( !in_array( 'art', $universal_assets['styles'] )) {
        $universal['styles'][] = 'art';
        echo '<link rel="stylesheet" href="'.APPURL.'assets/art.php?fc='.$color1.'&sc='.$color2.'&arts='.$arts. $v . '">';
    }
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
            COREPATH . 'assets/styles/ext/'. $f . '/' . $f => APPURL . 'assets/styles/ext/' . $f . '/' . $f,
            COREPATH . 'assets/styles/aio/' . $f => APPURL . 'assets/styles/aio/' . $f,
        ];
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
 * @author Shaikh <hey@shaikh.dev>
 */
function get_script(string $f, $params = [], $page_of = '') {

    // Gets cache config
    $cache = get_config( 'cache' );
    $v = $cache ? '?v=' . round( time() / ( $cache * 60 ) ) : '';

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
    if( !empty($f) && !in_array( $f, $universal_assets['scripts'] )) {
        $universal_assets['scripts'][] = $f;
        $script_paths = [
            APPPATH . 'assets/scripts/' . $f => APPURL . 'apps/' . APPDIR . '/assets/scripts/' . $f,
            COREPATH . 'assets/scripts/ext/' . $f . '/' . $f => APPURL . 'assets/scripts/ext/' . $f . '/' . $f,
            COREPATH . 'assets/scripts/aio/' . $f => APPURL . 'assets/scripts/aio/' . $f,
        ];
        $url = asset_exists( $script_paths, $f, 'js' );

        // If script is recaptcha, get recaptcha key and link to google recaptcha
        if( $f == 'recaptcha' ) {
            $site_key = get_config('recaptcha_site_key');
            if( !empty( $site_key ) ) {
                $url = 'https://www.google.com/recaptcha/api.js?render='.$site_key;
                echo '<script data-recaptcha src="' . $url . '"></script>';
                return;
            }
        }
        if( $page_of !== '' ){
            echo page_of( $page_of ) && $url !== '' ? '<script src="' . $url . $v . '"></script>' : '';
        } else {
            echo $url !== '' ? '<script src="' . $url . $v . '"></script>' : '';
        }

    }
}

/**
 * Finds .js files and outputs <script>'s
 * @param string $ar JS files separated by ,
 * @param string $page_of Load only if current page is of
 * @author Shaikh <hey@shaikh.dev>
 */
function get_scripts( $ar = '', $page_of = '' ) {
    if( !empty( $ar ) ){
        $ar = is_array( $ar ) ? $ar : explode( ',', str_replace( ' ', '', $ar ) );
        foreach( $ar as $f ){
            if( $page_of !== '' ){
                page_of( $page_of ) ? get_script( $f ) : '';
            } else {
                get_script( $f );
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
function asset_exists( $paths = [], $f = '', $ext = '' ): string {
    $url = '';
    foreach( $paths as $path => $link ) {
        if( file_exists( $path . '.min.' . $ext ) )
            $url = $link . '.min.' . $ext;
        else if( file_exists( $path . '.' . $ext ) )
            $url = $link . '.' . $ext;
        else if( strpos( $f, 'http') !== false )
            $url = $f;
        else
            elog( $f, 'error' );
    }
    return $url;
}

/**
 * Stylesheet <link> to load multiple Fonts
 * @param array $array Fonts array Ex: [['Lato','300,500'],['Dubai','Light']]
 * @author Shaikh <hey@shaikh.dev>
 */
function fonts( $array = [] ){
    $appdir = !empty( get_domain('sub') ) ? get_domain( 'sub' ) : get_domain();
    !defined( 'APPDIR' ) ? define( 'APPDIR', $appdir ) : '';

    $fonts = [];
    foreach( $array as $f ){
        $weights = isset( $f[1] ) && !empty( $f[1] ) ? $f[1] : '400';
        $fonts[] = $f[0].':'.$weights;
    }
    echo !empty( $fonts ) ? '<link rel="stylesheet" href="' . APPURL . 'assets/fonts.php?'.APPDIR.'='. implode( '|', $fonts ) .'">' : '';
}

/**
 * Stylesheet <link> to single Font
 * @param array $array Font name and weights Ex: ['Lato','300,500']
 * @author Shaikh <hey@shaikh.dev>
 */
function font( $array = [] ){
    $weights = isset( $array[1] ) && !empty( $array[1] ) ? $array[1] : '400';
    $fonts[] = $array[0].':'.$weights;
    echo !empty( $fonts ) ? '<link rel="stylesheet" href="' . APPURL . 'assets/fonts.php?'.APPDIR.'='. implode( '|', $fonts ) .'">' : '';
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
 * @param string $n Name of the comp
 * @param string $dir Name of directory
 * @return string
 */
function comp_exists( string $n, string $dir ) {
    $ns = explode('/',str_replace('.php','',$n));
    $x = 1;
    $fl = APPPATH . '/' . $dir . '/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    return file_exists( $fl ) ? $fl : false;
}

/**
 * Load component file if exists
 * @param string $n Comp file name excluding .php
 */
function get_comp( string $n ){
    comp_exists( $n, 'components' ) ? include( comp_exists( $n, 'components' ) ) : '';
}

/**
 * Loads modal file if exists
 * @param string $n Modal file name excluding .php
 */
function get_modal( string $n ){
    comp_exists( $n, 'modals' ) ? include( comp_exists( $n, 'modals' ) ) : '';
}

/**
 * Gets page file
 * @param string $n Page file name excluding .php
 */
function get_page( string $n ){
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

function UI( $ui, $array = [] ) {

    $f = COREPATH . 'core/ui/' . $ui . '.php';

    if( file_exists( $f ) ) {

        if( !empty( $array ) ) {
            global $ui_params;
            $ui_params = $array;
        }
        include( $f );

    }

}