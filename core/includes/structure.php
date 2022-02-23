<?php

$path = ROOTPATH . 'core/includes/structure/';

/**
 * Creates options Table to save App settings, not optional
 */
$option_struct = [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'value', 'VARCHAR', 4096, 1 ],
    [ 'scope', 'INT', 13, 0 ],
    [ 'load', 'BOOLEAN', '', 0 ],
];
$db = new DB();
$db->create_table( 'options', $option_struct, 'option', 1 );

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

