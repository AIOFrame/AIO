<?php

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
    [ 'uid', 'INT', '13', 'NOT NULL' ],
    [ 'name', 'VARCHAR', '15', 'NULL' ],
    [ 'message', 'VARCHAR', '155', 'NOT NULL' ],
    [ 'type', 'TINYTEXT', '15', 'NULL' ],
    [ 'link', 'VARCHAR', '35', 'NOT NULL' ],
    [ 'seen', 'BOOLEAN', '', 'NOT NULL' ],
    [ 'time', 'DATETIME', '', 'NOT NULL' ],
]];

create_tables( $tables );