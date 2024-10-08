<?php

/**
 * Defines the Root Path of AIO Framework Repository
 * Ex: /home/user/public_html/
 */
!defined( 'ROOTPATH' ) ? define( 'ROOTPATH', dirname( __FILE__ ) . '/' ) : '';
//print_r( ROOTPATH );

require_once ROOTPATH . 'core/includes/defines.php';

/**
 * Checks and creates AIO htaccess
 */
if( !file_exists( ROOTPATH . '.htaccess' ) ){
    $htaccess = '<IfModule mod_rewrite.c>
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
    for( $page = 0; $page < (count($abcs) * 2); $page++ ){
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
        $htaccess .= 'RewriteRule ^'.$pre. '$ index.php?'. $post . ' [L,QSA]
';
        $x++;
    }
    $htaccess .= '</IfModule>';
    if( $htaccess_file = fopen( ROOTPATH . '.htaccess', 'w' ) ){
        fwrite( $htaccess_file, $htaccess );
        fclose( $htaccess_file );
    }
}

/**
 * Loads AIO Core Logics
 */
require ROOTPATH . '/core/core.php';