<?php

!defined( 'COREPATH' ) ? exit() : '';

/**
 * Processes routing based on URL hierarchy
 */
$sd = get_domain('sub');
$range = range('a','l');
$url_routes = [];
foreach( $range as $p ){
    if( isset( $_GET[$p] ) && !empty( $_GET[$p] ) ){
        $url_routes[] = $_GET[$p];
    }
    ${$p} = isset( $_GET[$p] ) && !empty( $_GET[$p] ) ? $_GET[$p] : 'index';
}
define( 'PAGE', !empty( $url_routes ) ? $url_routes[count($url_routes) - 1] : 'index' );

$t = !empty( $sd ) ? $sd : APPDIR; // Sets if sub domain or prime domain url browsing

// Directory Hierarchy

$x = 1;

$page_link = APPPATH . 'pages/';
$page = '';
if( !empty( $url_routes ) ){
    // Loop pages till we reach last page, then append .php
    foreach( $url_routes as $n ){
        if( $x == count( $url_routes ) ){
            if( is_numeric( $n ) ){
                $page_link = substr( $page_link, 0, -1) . '.php';
                $page = substr( $page, 0, -1);
                !defined( 'PAGENAME' ) ? define( 'PAGENAME', substr($n, 0, -1) ) : '';
            } else {
                !defined( 'PAGENAME' ) ? define( 'PAGENAME', $n ) : '';
                $page_link .= $n . '.php';
                $page .= $n;
            }
        } else {
            $page_link .= $n . '/';
            $page .= $n . '/';
        }
        $x++;
    }
} else {
    $page_link .= 'index.php';
    $page = 'index';
}
$group_page = str_replace( '.php', '', $page_link ) . '/index.php';

$paths = explode( '/', $page_link );
$parent_page = $parent_group_page = '';
foreach( $paths as $k => $path ){
    $parent_page .= $k !== count($paths) - 1 ? $path.'/' : '';
}
if( !empty( $parent_page ) && $parent_page !== '' ) {
    $parent_page = rtrim( $parent_page, '/' ) . '.php';
    $parent_group_page = str_replace( '.php', '', $parent_page ) . '/index.php';
}
!defined( 'PAGENAME' ) ? define( 'PAGENAME', 'home') : '';

function define_path( $title, $parent = 0 ){
    if( $parent ){
        $array = explode( '/', $title );
        unset($array[count($array)-1]);
        $title = implode('/',$array);
    }

    !defined( 'PAGENAME' ) ? define( 'PAGENAME', str_replace(' ','', $title) ) : '';
    //echo PAGEPATH;
}
function define_page( $page ) {
    !defined( 'PAGELOAD' ) ? define( 'PAGELOAD', $page ) : '';
}

!defined( 'PAGEPATH' ) ? define( 'PAGEPATH', str_replace( APPPATH.'pages/', '', str_replace( '.php', '', $page_link ) ) ) : '';

// This the CORE ROUTING logic

if( !isset( $_POST['action'] ) ) { // Checks if AJAX Action Request is sent so content wont be rendered

    /**
     * Sets page by url to page.php if file exists
     */
    if( file_exists( $page_link ) ){
        define_path( $page );
        define_page( $page_link );
        return;
    }

    /**
     * Sets page by url to page/index.php if folder/file exists
     */
    if( file_exists( $group_page ) ) {
        define_path( $page );
        define_page( $group_page );
        return;
    }

    /**
     * Set page to parent if page.php is missing
     */
    if( file_exists( $parent_page ) ) {
        define_path( $page, 1 );
        define_page( $parent_page );
        return;
    }

    /**
     * Sets page to parent group if page.php is missing
     */
    if( file_exists( $parent_group_page ) ) {
        define_path( $page, 1 );
        define_page( $parent_group_page );
        return;
    }

    /**
     * Loads 404 page if exists
     */
    if( file_exists( COREPATH . 'apps/' . $t . '/pages/404.php' ) ) { // Doesn't exist on sub domain or defaults, so check if 404 page exists
        define_path( '404' );
        define_page( COREPATH . 'apps/' . $t . '/pages/404.php'); // Exists so load 404
    } else {
        $error = '404';
        include_once( COREPATH . 'core/pages/error.php'); // Loads super 404
    }
}