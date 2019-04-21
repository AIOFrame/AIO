<?php

function basic_tables() {
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

    $tables[] = [ 'storage', 'file', [
        [ 'name', 'VARCHAR', '55', 'NOT NULL' ],
        [ 'url', 'VARCHAR', '255', 'NOT NULL' ],
        [ 'scope', 'INT', '13', 'NOT NULL' ],
        [ 'type', 'TINYTEXT', '', 'NOT NULL' ],
        [ 'size', 'MEDIUMINT', '30', 'NOT NULL' ],
        [ 'small', 'VARCHAR', '255', 'NULL' ],
        [ 'medium', 'VARCHAR', '255', 'NULL' ],
        [ 'delete', 'BOOLEAN', 1, 0 ],
    ] ];

    $tables[] = [ 'levels', 'lv', [
        [ 'name', 'VARCHAR', '55', 'NOT NULL' ],
        [ 'status', 'BOOLEAN', '', 'NULL' ],
    ] ];

    $tables[] = [ 'options', 'option', [
        [ 'name', 'VARCHAR', '200', 'NOT NULL' ],
        [ 'value', 'VARCHAR', '9999', 'NOT NULL' ],
        [ 'scope', 'INT', '13', 'NULL' ],
        [ 'load', 'BOOLEAN', '', 'NULL' ],
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

    $tables[] = [ 'translations', 'trans', [
        [ 'base', 'VARCHAR', 9999, 1 ],
        [ 'ln', 'TINYTEXT', 2, 1 ],
        [ 'replace', 'VARCHAR', 9999, 0 ],
        [ 'page', 'VARCHAR', 255, 0 ]
    ]];

    create_tables( $tables );
}

if( APPDEBUG ){
    basic_tables();
}

basic_tables();