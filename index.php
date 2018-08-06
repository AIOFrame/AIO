<?php

// Defines the Path of Main Application
!defined( 'COREPATH' ) ? define( 'COREPATH', dirname( __FILE__ ) . '/' ) : '';
!defined( 'ROOTPATH' ) ? define( 'ROOTPATH', __DIR__ . '/' ) : '';

$pre = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://";

// Defines the URL of the Main Application
!defined( 'APPURL' ) ? define( 'APPURL', $pre.$_SERVER['HTTP_HOST']."/" ) : '';

require( dirname( __FILE__ ) . '/core/core.php' );