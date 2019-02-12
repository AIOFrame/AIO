<?php


// This page loads the actual page content

if( !defined( 'COREPATH' ) ) { exit(); }

$sd = get_domain('sub');

$als = ['a','b','c','d','e','f'];

$all = [];
foreach($als as $al){
    if(isset($_GET[$al]) && !empty($_GET[$al])){
        $all[] = $_GET[$al];
    }
    ${$al} = isset($_GET[$al]) && !empty($_GET[$al]) ? $_GET[$al] : "index";
}

define( 'PAGE', !empty( $all ) ? $all[count($all) - 1] : 'ROOT' ); // Defines which page user is in

$t = !empty( $sd ) ? $sd : APPDIR; // Sets if sub domain or prime domain url browsing

// Directory Hierarchy

$x = 1;

$fl = APPPATH . '/pages/';
$st = '';
if( !empty( $all ) ){
    foreach( $all as $n ){
        if( $x == count( $all ) ){
            if( is_numeric( $n ) ){
                $fl = substr($fl, 0, -1) . '.php';
                $st = substr($st, 0, -1);
                !defined( 'PAGENAME' ) ? define( 'PAGENAME', substr($n, 0, -1) ) : '';
            } else {
                !defined( 'PAGENAME' ) ? define( 'PAGENAME', $n ) : '';
                $fl .= $n . '.php';
                $st .= $n;
            }
        } else {
            $fl .= $n . '/';
            $st .= $n . '/';
        }
        $x++;
    }
} else {
    $fl .= 'index.php';
    $st = 'index';
}
$ifl = str_replace( '.php', '', $fl ) . '/index.php';

$paths = explode( '/', $fl );
$lfl = '';
foreach( $paths as $k => $path ){
    $lfl .= $k !== count($paths) - 1 ? $path.'/' : '';
}
if( !empty( $lfl ) && $lfl !== '' ) {
    $lfl = rtrim( $lfl, '/' ) . '.php';
    $ilfl = str_replace( '.php', '', $lfl ) . '/index.php';
}

!defined( 'PAGENAME' ) ? define( 'PAGENAME', 'home') : '';

function define_pagename( $title, $last = false ){
    if( $last ){
        $array = explode( '/', $title );
        unset($array[count($array)-1]);
        $title = implode('/',$array);
        !defined( 'PAGEPATH' ) ? define( 'PAGEPATH', str_replace(' ','', $title) ) : '';
    } else {
        !defined( 'PAGEPATH' ) ? define( 'PAGEPATH', str_replace(' ','', $title) ) : '';
    }
}

// This the CORE ROUTING logic

if( !isset( $_POST['action'] ) ) { // Checks if AJAX Action Request is sent so content wont be rendered

    if( file_exists( $fl ) ){ // If sub domain or defaults -> page or index exists
        define_pagename( $st );
        include( $fl ); // Exists so load page
    } else if( file_exists( $ifl ) ) {
        define_pagename( $st );
        include( $ifl );
    } else if( file_exists( $lfl ) ) {
        define_pagename( $st, 1 );
        include( $lfl );
    } else if( file_exists( $ilfl ) ) {
        define_pagename( $st, 1 );
        include( $ilfl );
    } else if( file_exists( COREPATH . 'apps/' . $t . '/pages/404.php' ) ) { // Doesn't exist on sub domain or defaults, so check if 404 page exists
        include( COREPATH . 'apps/' . $t . '/pages/404.php'); // Exists so load 404
    } else {
        $error = '404';
        include( COREPATH . 'core/pages/error.php'); // Loads super 404
    }

}