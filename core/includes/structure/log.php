<?php

/**
 * Creates log table to store activity user log
 */

$db = new DB();

$log_struct[] = [ 'log', [
    [ 'dt', 'DATETIME', '', 1 ],
    [ 'type', 'VARCHAR', 64, 1 ],
    [ 'table', 'VARCHAR', 64, 1 ],
    [ 'data', 'VARCHAR', 4096, 0 ],
    [ 'url', 'VARCHAR', 512, 0 ],
    [ 'line', 'INT', 13, 0 ],
    [ 'uid', 'INT', 13, 0 ],
    [ 'name', 'VARCHAR', 256, 0 ],
    [ 'client', 'VARCHAR', 512, 0 ],
    [ 'device', 'VARCHAR', 256, 0 ],
    [ 'os', 'VARCHAR', 256, 0 ],
], 'log', 1 ];

$db->automate_tables( $log_struct );