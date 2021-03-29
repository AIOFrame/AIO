<?php

$db = new DB();

// Create Table Tests
$tables = [
    [ 'test1', 't1_', [
        [ 't1_name', 'VARCHAR', 15, 1 ],
        [ 't1_age', 'INT', 10, 1 ],
        [ 't1_dob', 'DATE', '', 0 ],
        [ 't1_join', 'DATETIME', '', 0 ],
        [ 't1_credits', 'FLOAT', '', 0 ],
        [ 't1_status', 'BOOL', 10, 0 ],
    ] ],
];
echo 'Testing Create Tables'.PHP_EOL;
//$created_tables = $db->create_tables( $tables );