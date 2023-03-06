<?php

// Ex 1: website.com could be serving in India, China
// Ex 2: website.com, website.net, website.in could be serving is India

// Detect by domain
$dex = explode( '.', rtrim($_SERVER['HTTP_HOST'],'/') );
$domain = implode( '.', $dex );
$d = new DB();
$region_by_domain = $d->select( 'regions', '', 'reg_domain LIKE \''.$domain.'\'', 1 );

// Detect by Regional IP - Overrides domain region because one domain could be serving multiple regions
require_once( ROOTPATH . 'core/modules/geoip.php' );
$r = new REGION();
$code = $r->get_country_code();
$region_by_ip = $d->select( 'regions', '', 'reg_country = \''.$code.'\'', 1 );

// Fallback to first region
$region_fallback = $d->select( 'regions', '', '', 1 );

if( !defined( 'REGION' ) ) {
    !empty( $region_by_ip ) ? define( 'REGION', $region_by_ip ) : ( !empty( $region_by_domain ) ? define( 'REGION', $region_by_domain ) : define( 'REGION', $region_fallback ) );
}