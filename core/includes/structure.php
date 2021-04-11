<?php

/**
 * Creates options Table to save App settings, not optional
 */
$tables[] = [ 'options', [
    [ 'name', 'VARCHAR', 200, 1 ],
    [ 'value', 'VARCHAR', 4999, 1 ],
    [ 'scope', 'INT', 13, 0 ],
    [ 'load', 'BOOLEAN', '', 0 ],
], 'option', 1 ];
$db = new DB();
$db->create_tables( $tables );

/**
 * User tables for user login and restricted access, optional by App Config
 * 'features' => ['users'] or ['auth'] or ['authentication']
 */
function user_tables() {
    $tables[] = [ 'users', [
        [ 'login', 'VARCHAR', 15, 1 ],
        [ 'email', 'VARCHAR', 45, 0 ],
        [ 'name', 'VARCHAR', 45, 1 ],
        [ 'picture', 'VARCHAR', 255, 0 ],
        [ 'data', 'VARCHAR', 4999, 0 ],
        [ 'access', 'VARCHAR', 4999, 0 ],
        [ 'status', 'BOOLEAN', '', 1 ],
        [ 'since', 'DATETIME', '', 1 ],
    ], 'user', 1 ];

    $tables[] = [ 'access', [
        [ 'uid', 'INT', 13, 1 ],
        [ 'pass', 'VARCHAR', 155, 1 ],
        [ 'recent', 'DATETIME', '', 0 ],
        [ 'reset', 'VARCHAR', 11, 0 ],
    ], 'access', 1 ];

    $tables[] = [ 'sessions', [
        [ 'uid', 'INT', 13, 1 ],
        [ 'time', 'DATETIME', '', 1 ],
        [ 'code', 'VARCHAR', 255, 1 ],
        [ 'os', 'VARCHAR', 50, 1 ],
        [ 'client', 'VARCHAR', 50, 1 ],
        [ 'status', 'BOOLEAN', '', 1 ],
    ], 'session', 1 ];

    $tables[] = [ 'levels', [
        [ 'name', 'VARCHAR', 55, 1 ],
        [ 'status', 'BOOLEAN', '', 0 ],
    ], 'level', 1 ];

    $tables[] = [ 'alerts', [
        [ 'from', 'INT', 13, 0 ],
        [ 'user', 'INT', 13, 1 ],
        [ 'name', 'VARCHAR', 15, 1 ],
        [ 'note', 'VARCHAR', 155, 0 ],
        [ 'type', 'VARCHAR', 15, 0 ],
        [ 'link', 'VARCHAR', 55, 0 ],
        [ 'seen', 'BOOLEAN', '', 0 ],
        [ 'time', 'DATETIME', '', 0 ],
    ], 'alert', 1 ];

    $db = new DB();
    $db->automate_tables( $tables );
}

/**
 * Language translation tables, optional by App Config
 * 'features' => ['translations'] or ['languages']
 */
function language_tables() {
    $db = new DB();

    $tables = [];
    $trans = [ 'translations', [
        [ 'base', 'VARCHAR', 4999, 1 ],
        [ 'page', 'VARCHAR', 255, 0 ],
    ], 't', 1 ];


    $ln = $db->get_option( 'languages' );
    $ln = !empty( $ln ) ? unserialize( $ln ) : [];
    if( is_array( $ln ) && !empty( $ln ) ) {
        foreach( $ln as $l ) {
            if( $l !== 'en' )
                $trans[2][] = [ $l, 'VARCHAR', 4999, 0 ];
        }
    }
    $tables[] = $trans;

    $db->automate_tables( $tables );
}

/**
 * File upload tables, optional by App Config
 * 'features' => ['uploads'] or ['storage']
 */
function storage_tables() {
    $tables[] = [ 'storage', [
        [ 'name', 'VARCHAR', 255, 1 ],
        [ 'url', 'VARCHAR', 255, 1 ],
        [ 'scope', 'INT', 13, 0 ],
        [ 'type', 'VARCHAR', 30, 1 ],
        [ 'size', 'INT', 30, 1 ],
        [ 'small', 'VARCHAR', 255, 0 ],
        [ 'medium', 'VARCHAR', 255, 0 ],
        [ 'delete', 'BOOLEAN', '', 0 ],
    ], 'file', 1 ];
    $db = new DB();
    $db->automate_tables( $tables );
}

/**
 * Get App Config and create database tables
 */
$feats = get_config( 'features' );
if( is_array( $feats ) ) {

    // Create User tables if featured
    if( in_array( 'users', $feats ) || in_array( 'auth', $feats ) || in_array( 'authentication', $feats ) ) {
        user_tables();
    }

    // Create Translation tables if featured
    if( in_array( 'translations', $feats ) || in_array( 'languages', $feats ) ) {
        language_tables();
    }

    // Create File uploader tables if featured
    if( in_array( 'storage', $feats ) || in_array( 'uploads', $feats ) ) {
        storage_tables();
    }

    // Create Countries and Currencies data
    if( in_array( 'data', $feats ) || in_array( 'countries', $feats ) || in_array( 'world', $feats ) ) {
        // Check if world data exists
        // TODO: Convert this table sql to json
        $db = new DB();
        $table_exist = $db->table_exists( 'countries' );
        if( !$table_exist ) {
            $db->import( ROOTPATH . 'core/components/data/world.sql' );
        }
    }

}

