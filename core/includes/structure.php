<?php

function user_tables() {
    $tables[] = [ 'users', 'user', [
        [ 'login', 'VARCHAR', '15', 'NOT NULL' ],
        [ 'email', 'VARCHAR', '45', 'NULL' ],
        [ 'name', 'VARCHAR', '45', 'NOT NULL' ],
        [ 'since', 'DATETIME', '', 'NOT NULL' ],
        [ 'role', 'VARCHAR', '45', 'NULL' ],
        [ 'eid', 'VARCHAR', '10', 'NULL' ],
        [ 'phone', 'VARCHAR', '20', 'NULL' ],
        [ 'pic', 'VARCHAR', '255', 'NULL' ],
        [ 'data', 'VARCHAR', '9999', 'NULL' ],
        [ 'level', 'INT', '13', 'NULL' ],
        [ 'reset_pass', 'VARCHAR', 11, 'NULL' ],
    ] ];

    $tables[] = [ 'access', 'access', [
        [ 'uid', 'INT', '13', 'NOT NULL' ],
        [ 'pass', 'VARCHAR', '155', 'NOT NULL' ],
        [ 'status', 'BOOLEAN', '', 'NOT NULL' ],
    ] ];

    $tables[] = [ 'sessions', 'ss', [
        [ 'uid', 'INT', '13', 'NOT NULL' ],
        [ 'time', 'DATETIME', '', 'NOT NULL' ],
        [ 'ip', 'TINYTEXT', '', 'NOT NULL' ],
        [ 'os', 'TINYTEXT', '', 'NOT NULL' ],
        [ 'client', 'TINYTEXT', '', 'NOT NULL' ],
        [ 'status', 'BOOLEAN', '', 'NOT NULL' ],
    ] ];

    $tables[] = [ 'levels', 'lv', [
        [ 'name', 'VARCHAR', '55', 'NOT NULL' ],
        [ 'status', 'BOOLEAN', '', 'NULL' ],
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
        [ 'name', 'VARCHAR', '255', 1 ],
        [ 'url', 'VARCHAR', '255', 1 ],
        [ 'scope', 'INT', '13', 0 ],
        [ 'type', 'TINYTEXT', '', 'NOT NULL' ],
        [ 'size', 'MEDIUMINT', '30', 'NOT NULL' ],
        [ 'small', 'VARCHAR', '255', 'NULL' ],
        [ 'medium', 'VARCHAR', '255', 'NULL' ],
        [ 'delete', 'BOOLEAN', 1, 0 ],
    ] ];
    create_tables( $tables );
}

// Get config and database features
$data = get_config( 'data' );
if( is_array( $data ) && isset( $data['features'] ) && is_array( $data['features'] ) ) {

    // Create User tables if featured
    if( in_array( 'users', $data['features'] ) ) {
        user_tables();
    }

    // Create Translation tables if featured
    if( in_array( 'translations', $data['features'] ) ) {
        language_tables();
    }

    // Create File uploader tables if featured
    if( in_array( 'storage', $data['features'] ) ) {
        storage_tables();
    }

}

function basic_tables() {
    $tables[] = [ 'options', 'option', [
        [ 'name', 'VARCHAR', '200', 'NOT NULL' ],
        [ 'value', 'VARCHAR', '9999', 'NOT NULL' ],
        [ 'scope', 'INT', '13', 'NULL' ],
        [ 'load', 'BOOLEAN', '', 'NULL' ],
    ] ];
    create_tables( $tables );
}
basic_tables();