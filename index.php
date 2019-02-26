<?php

// Defines the Path of Main Application
!defined( 'COREPATH' ) ? define( 'COREPATH', dirname( __FILE__ ) . '/' ) : '';
!defined( 'ROOTPATH' ) ? define( 'ROOTPATH', __DIR__ . '/' ) : '';

$pre = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";

// Defines the URL of the Main Application
!defined( 'APPURL' ) ? define( 'APPURL', $pre.$_SERVER['HTTP_HOST']."/" ) : '';

// Check if HTACCESS exists
if( !file_exists( COREPATH . '.htaccess' ) ){
    $htdata = '<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /

RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)/$ /$1 [L,R]

RewriteRule ^(.*)/$ index.php [QSA,L]
';
    $x = 0; $y = 1;
    $abcs = range( 'a', 'i' );
    $pre = $post = '';
    for( $page = 0; $page < 14; $page++ ){
        if( empty( $pre ) ){
            $pre = '([a-zA-Z0-9-z\-\_]+)';
            $post = 'a=$1';
        } else {
            if ($x % 2 == 0) {
                $pre = $pre . '([a-zA-Z0-9-z\-\_]+)';
                $post = $post . '&'.$abcs[$y].'=$'.($y+1);
                $y++;
            } else {
                $pre = $pre . '/';
            }
        }
        $htdata .= 'RewriteRule ^'.$pre. '$ index.php?'. $post . ' [L,QSA]
';
        $x++;
    }
    $htdata .= '</IfModule>';
    if( $htfile = fopen( COREPATH . '.htaccess', 'w' ) ){
        fwrite( $htfile, $htdata );
        fclose( $htfile );
    }
}
require( dirname( __FILE__ ) . '/core/core.php' );