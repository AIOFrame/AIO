<?php

$user_struct[] = [ 'users', [
    [ 'login', 'VARCHAR', 128, 1 ],
    [ 'email', 'VARCHAR', 128, 0 ],
    [ 'name', 'VARCHAR', 128, 0 ],
    [ 'picture', 'VARCHAR', 512, 0 ],
    [ 'data', 'VARCHAR', 4096, 0 ],
    [ 'type', 'VARCHAR', 256, 0 ],
    [ 'role', 'VARCHAR', 256, 0 ],
    [ 'status', 'BOOLEAN', '', 1 ],
    [ 'access', 'VARCHAR', 4096, 0 ],
    [ 'since', 'DATETIME', '', 1 ],
], 'user', 1 ];

$user_struct[] = [ 'access', [
    [ 'uid', 'INT', 13, 1 ],
    [ 'pass', 'VARCHAR', 256, 1 ],
    [ 'recent', 'DATETIME', '', 0 ],
    [ 'reset', 'VARCHAR', 24, 0 ],
], 'access', 1 ];

$user_struct[] = [ 'sessions', [
    [ 'uid', 'INT', 13, 1 ],
    [ 'time', 'DATETIME', '', 1 ],
    [ 'expiry', 'DATETIME', '', 1 ],
    [ 'code', 'VARCHAR', 256, 1 ],
    [ 'os', 'VARCHAR', 256, 1 ],
    [ 'client', 'VARCHAR', 256, 1 ],
    [ 'device', 'VARCHAR', 256, 1 ],
    [ 'status', 'BOOLEAN', '', 1 ],
], 'session', 1 ];

/* $tables[] = [ 'levels', [
    [ 'name', 'VARCHAR', 128, 1 ],
    [ 'status', 'BOOLEAN', '', 0 ],
], 'level', 1 ]; */

$user_struct[] = [ 'alerts', [
    [ 'from', 'INT', 13, 0 ],
    [ 'user', 'INT', 13, 1 ],
    [ 'name', 'VARCHAR', 128, 1 ],
    [ 'note', 'VARCHAR', 512, 0 ],
    [ 'type', 'VARCHAR', 32, 0 ],
    [ 'link', 'VARCHAR', 512, 0 ],
    [ 'seen', 'BOOLEAN', '', 0 ],
    [ 'time', 'DATETIME', '', 0 ],
], 'alert', 1 ];

$db = new DB();
$db->automate_tables( $user_struct );