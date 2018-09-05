<?php

// This page loads the actual template content

if( !defined( 'COREPATH' ) ) { exit(); }

foreach( glob( APPPATH . '/functions/*.php' ) as $file ) {
    include( $file );
}

// Fetch necessary data from database and define as variables

$pa = unserialize( get_option('permissions') ); // This checks the page permissions

//$ul = get_user_level(); // Gets the current user access level

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
!defined( 'PAGEPATH' ) ? define( 'PAGEPATH', str_replace(' ','', $st) ) : '';
!defined( 'PAGENAME' ) ? define( 'PAGENAME', 'home') : '';

// This the CORE ROUTING logic

if( !isset( $_POST['action'] ) ) { // Checks if AJAX Action Request is sent so content wont be rendered

    if( file_exists( $fl ) ){ // If sub domain or defaults -> page or index exists
        include( $fl ); // Exists so load page
    } else if( file_exists( $ifl ) ) {
        include( $ifl );
    } else {
        if( file_exists( COREPATH . 'apps/' . $t . '/pages/404.php' ) ) // Doesn't exist on sub domain or defaults, so check if 404 page exists
            include( COREPATH . 'apps/' . $t . '/pages/404.php' ); // Exists so load 404
        else if( file_exists( COREPATH . 'pages/404.php' ) )
            include( COREPATH . 'pages/404.php' ); // Seems like sub folder doesn't exist, so loads super 404
    }

}

// Checks if user is visiting a template page
/* if( isset( $_GET['a'] ) ) {
    if( user_logged_in() ) {
        if($_GET['a'] == 'login') {
            header('Location: '.APPURL);
        } else if( template_exists() ) {
            $po = unserialize( get_option('menu_order') );
            foreach( $po[0]['menus'] as $p ) {
                if( $_GET['a'] == $p['slug'] ){
                    if( in_array( $ul, $p['access'] ) ){
                        //print_r( $p['slug'] );
                        ! defined( 'PAGE' ) ? define( 'PAGE', template_exists() ) : '';
                        include( COREPATH.'apps/'.UINAME.'/templates/'.$_GET['a'].'.php' );
                    } else {
                        ! defined( 'PAGE' ) ? define( 'PAGE', '404' ) : '';
                        include( COREPATH.'apps/'.UINAME.'/404.php' );
                    }
                }
            }
        } else {
            ! defined( 'PAGE' ) ? define( 'PAGE', '404' ) : '';
            include( COREPATH.'apps/'.UINAME.'/404.php' );
        }
    } else {
        switch( $_GET['a'] ) {
            case 'login':
                if ( file_exists( COREPATH.'apps/'.UINAME.'/access/login.php' ) ) {
                    ! defined( 'PAGE' ) ? define( 'PAGE', 'login' ) : '';
                    include( COREPATH.'apps/'.UINAME.'/access/login.php' );
                } else {
                    include( COREPATH.'access/login.php' );
                }
                break;
            case 'reset-password':
                if ( file_exists( COREPATH.'apps/'.UINAME.'/access/reset.php' ) ) {
                    ! defined( 'PAGE' ) ? define( 'PAGE', 'reset' ) : '';
                    include( COREPATH.'apps/'.UINAME.'/access/reset.php' );
                } else {
                    include( COREPATH.'access/reset.php' );
                }
                break;
            case 'register':
                if ( file_exists( COREPATH.'apps/'.UINAME.'/access/register.php' ) ) {
                    ! defined( 'PAGE' ) ? define( 'PAGE', 'register' ) : '';
                    include( COREPATH.'apps/'.UINAME.'/access/register.php' );
                } else {
                    include( COREPATH.'access/register.php' );
                }
                break;
            case 'profile':
                include( COREPATH.'apps/'.UINAME.'/access/profile.php' );
                break;
            case 'settings':
                include( COREPATH.'apps/'.UINAME.'/access/settings.php' );
                break;
            case 'display':
                ! defined( 'PAGE' ) ? define( 'PAGE', 'display' ) : '';
                include( COREPATH.'apps/'.UINAME.'/access/display.php' );
                break;
            case 'tv':
                ! defined( 'PAGE' ) ? define( 'PAGE', 'tv' ) : '';
                include( COREPATH.'apps/'.UINAME.'/access/tv.php' );
                break;
            default:
                header('Location: '.APPURL.'login');
                break;
        }
    }
} else if( isset( $_GET['admin'] ) ) {
    if( user_logged_in() ) {
        if( template_exists() ) {
            $po = unserialize( get_option('menu_order') );
            foreach( $po[1]['menus'] as $p ) {
                if( $_GET['admin'] == $p['slug'] ){
                    if( in_array( $ul, $p['access'] ) ){
                        ! defined( 'PAGE' ) ? define( 'PAGE', $_GET['admin'] ) : '';
                        include( COREPATH.'apps/'.UINAME.'/admin/'.$_GET['admin'].'.php' );
                    } else {
                        include( COREPATH.'apps/'.UINAME.'/404.php' );
                    }
                }
            }
        } else {
            ! defined( 'PAGE' ) ? define( 'PAGE', '404' ) : '';
            include( COREPATH.'apps/'.UINAME.'/404.php' );
        }
    } else {
        header('Location: '.APPURL.'login');
    }
} else if( isset( $_POST['action'] ) ) {

} else {
    if( user_logged_in() ) {
        $hp = get_option('default_home');
        if( !empty( $hp ) ){
            if( file_exists( COREPATH.'apps/'.UINAME.'/templates/'.$hp.'.php' ) ){
                ! defined( 'PAGE' ) ? define( 'PAGE', $hp ) : '';
                include_once( COREPATH.'apps/'.UINAME.'/templates/'.$hp.'.php');
            } else {
                include( COREPATH.'apps/'.UINAME.'/index.php' );
            }
        } else {
            include( COREPATH.'apps/'.UINAME.'/index.php' );
        }
    } else {
        header('Location: '.APPURL.'login');
    }
} */