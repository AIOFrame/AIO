<?php

function user_tables() {
    $tables[] = [ 'users', 'user', [
        [ 'login', 'VARCHAR', 15, 1 ],
        [ 'email', 'VARCHAR', 45, 0 ],
        [ 'name', 'VARCHAR', 45, 1 ],
        [ 'since', 'DATETIME', '', 1 ],
        //[ 'role', 'VARCHAR', 45, 0 ],
        //[ 'eid', 'VARCHAR', 10, 0 ],
        [ 'phone', 'VARCHAR', 20, 0 ],
        [ 'pic', 'VARCHAR', 255, 0 ],
        [ 'data', 'VARCHAR', 9999, 0 ],
        [ 'perms', 'VARCHAR', 9999, 0 ],
        [ 'reset_pass', 'VARCHAR', 11, 0 ],
    ] ];

    $tables[] = [ 'access', 'access', [
        [ 'uid', 'INT', 13, 1 ],
        [ 'pass', 'VARCHAR', 155, 1 ],
        [ 'status', 'BOOLEAN', '', 1 ],
    ] ];

    $tables[] = [ 'sessions', 'ss', [
        [ 'uid', 'INT', 13, 1 ],
        [ 'time', 'DATETIME', '', 1 ],
        [ 'ip', 'TINYTEXT', '', 1 ],
        [ 'os', 'TINYTEXT', '', 1 ],
        [ 'client', 'TINYTEXT', '', 1 ],
        [ 'status', 'BOOLEAN', '', 1 ],
    ] ];

    $tables[] = [ 'levels', 'lv', [
        [ 'name', 'VARCHAR', 55, 1 ],
        [ 'status', 'BOOLEAN', '', 0 ],
    ] ];

    $tables[] = [ 'alerts', 'al', [
        [ 'from', 'INT', 13, 0 ],
        [ 'user', 'INT', 13, 1 ],
        [ 'name', 'VARCHAR', 15, 1 ],
        [ 'note', 'VARCHAR', 155, 0 ],
        [ 'type', 'TINYTEXT', 15, 0 ],
        [ 'link', 'VARCHAR', 55, 0 ],
        [ 'seen', 'BOOLEAN', '', 0 ],
        [ 'time', 'DATETIME', '', 0 ],
    ]];

    create_tables( $tables );
}

function language_tables() {
    $tables = [];
    $trans = [ 'translations', 't', [
        [ 'base', 'TEXT', 9999, 1 ],
        [ 'page', 'VARCHAR', 255, 0 ],
    ]];

    $ln = get_option( 'languages' );
    $ln = !empty( $ln ) ? unserialize( $ln ) : [];
    if( is_array( $ln ) && !empty( $ln ) ) {
        foreach( $ln as $l ) {
            if( $l !== 'en' )
                $trans[2][] = [ $l, 'TEXT', 9999, 0 ];
        }
    }
    $tables[] = $trans;

    create_tables( $tables );
}

function storage_tables() {
    $tables[] = [ 'storage', 'file', [
        [ 'name', 'VARCHAR', 255, 1 ],
        [ 'url', 'VARCHAR', 255, 1 ],
        [ 'scope', 'INT', 13, 0 ],
        [ 'type', 'TINYTEXT', '', 1 ],
        [ 'size', 'MEDIUMINT', 30, 1 ],
        [ 'small', 'VARCHAR', 255, 0 ],
        [ 'medium', 'VARCHAR', 255, 0 ],
        [ 'delete', 'BOOLEAN', 1, 0 ],
    ] ];
    create_tables( $tables );
}

// Get config and database features
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
        $countries = select( 'countries', '1', '', 1 );
        if( empty( $countries ) ) {
            import( COREPATH . 'core/components/data/world.sql' );
        }
    }

}

function basic_tables() {
    $tables[] = [ 'options', 'option', [
        [ 'name', 'VARCHAR', 200, 1 ],
        [ 'value', 'VARCHAR', 9999, 1 ],
        [ 'scope', 'INT', 13, 0 ],
        [ 'load', 'BOOLEAN', '', 0 ],
    ] ];
    create_tables( $tables );
}
basic_tables();