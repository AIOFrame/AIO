<?php

!defined( 'ROOTPATH' ) ? exit() : '';

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

$t = !empty( $sd ) ? $sd : APPDIR; // Sets if subdomain or prime domain url browsing

// Fetch Manual Routing

$c = !empty( CONFIG ) ? json_decode( CONFIG, 1 ) : [];
$routes = $c['router'] ?? ( $c['routing'] ?? ( $c['routes'] ?? ( $c['map'] ?? ( $c['mapping'] ?? ( $c['urls'] ?? ( $c['pages'] ?? [] ) ) ) ) ) );

// Directory Hierarchy

$x = 1;
$page_link = APPPATH . 'pages/';
$page = '';
if( isset( $routes[ implode( '/', $url_routes ) ] ) ) {
    $page = implode( '/', $url_routes );
    $page_link .= $routes[ implode( '/', $url_routes ) ] . '.php';
    !defined( 'PAGENAME' ) ? define( 'PAGENAME', $url_routes[ count($url_routes) - 1 ] ) : '';
} else if( !empty( $url_routes ) ){
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
    !defined( 'PAGENAME' ) ? define( 'PAGENAME', 'index' ) : '';
}
//print_r( $page );
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

function define_path( $title, $parent = 0 ): void {
    if( $parent ){
        $array = explode( '/', $title );
        unset($array[count($array)-1]);
        $title = implode('/',$array);
    }
    !defined( 'PAGEPATH' ) ? define( 'PAGEPATH', str_replace(' ','', $title) ) : '';
    !defined( 'APPURI' ) ? define( 'APPURI', APPURL . PAGEPATH ) : '';
    !defined( 'ASSETURI' ) ? define( 'ASSETURI', APPURL . 'apps/' . APPDIR . '/assets/' ) : '';
}

function define_page( $page ): void {
    !defined( 'PAGELOAD' ) ? define( 'PAGELOAD', $page ) : '';
}

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
    if( file_exists( ROOTPATH . 'apps/' . $t . '/pages/404.php' ) ) { // Doesn't exist on sub domain or defaults, so check if 404 page exists
        define_path( '404' );
        define_page( ROOTPATH . 'apps/' . $t . '/pages/404.php'); // Exists so load 404
    } else {
        $error = '404';
        include_once( ROOTPATH . 'core/pages/error.php'); // Loads super 404
    }
    !defined( 'PAGEPATH' ) ? define( 'PAGEPATH', str_replace( APPPATH.'pages/', '', str_replace( '.php', '', $page_link ) ) ) : '';
}

