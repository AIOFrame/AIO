<?php

$path = ROOTPATH . 'core/includes/structure/';

include_once( $path . 'options.php' );

include_once( $path . 'log.php' );

/**
 * Get App Config and create database tables
 */
$feats = get_config( 'features' );
if( is_array( $feats ) ) {

    // Create User tables if featured
    if( in_array( 'users', $feats ) || in_array( 'auth', $feats ) || in_array( 'authentication', $feats ) ) {
        include_once( $path . 'users.php' );
    }

    // Create Translation tables if featured
    if( in_array( 'translations', $feats ) || in_array( 'languages', $feats ) ) {
        include_once( $path . 'i18n.php' );
    }

    // Create File uploader tables if featured
    if( in_array( 'storage', $feats ) || in_array( 'uploads', $feats ) ) {
        include_once( $path . 'storage.php' );
    }

    // Create CMS tables if featured
    if( in_array( 'cms', $feats ) ) {
        include_once( $path . 'cms.php' );
    }

    // Customer Relationship Manager
    if( in_array( 'crm', $feats ) ) {
        include_once( $path . 'crm.php' );
    }

    // Employee Management System
    if( in_array( 'ems', $feats ) ) {
        include_once( $path . 'ems.php' );
    }

    // Create File uploader tables if featured
    /* if( in_array( 'log', $feats ) || in_array( 'uploads', $feats ) ) {
        include_once( $path . 'storage.php' );
    } */

    // Create Countries and Currencies data
    //if( in_array( 'data', $feats ) || in_array( 'countries', $feats ) || in_array( 'world', $feats ) ) {
        // Check if world data exists
        // TODO: Convert this table sql to json
        /* $db = new DB();
        $table_exist = $db->table_exists( 'countries' );
        if( !$table_exist ) {
            $db->import( ROOTPATH . 'core/components/data/world.sql' );
        } */
    //}

}

