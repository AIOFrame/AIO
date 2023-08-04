<?php

/**
 * Creates table to manage employees
 */

$db = new DB();
// TODO: Employee user meta
// TODO: Employee user meta regional
// TODO: Employee Contracts
// TODO: Employee contracts regional fields
// TODO: Expenses
$pages_struct[] = [ 'leads', [
    [ 'user', 'INT', 13, 0 ],
    [ 'date', 'DATETIME', '', 1 ],
    [ 'up_date', 'DATETIME', '', 1 ],
    [ 'title', 'TEXT', '', 1 ],
    [ 'desc', 'VARCHAR', 512, 1 ],
    [ 'content', 'LONGTEXT', '', 0 ],
    [ 'url', 'TEXT', '', 0 ],
    [ 'password', 'VARCHAR', 256, 0 ],
    [ 'type', 'VARCHAR', 64, 0 ],
    [ 'parent', 'INT', 13, 0 ],
    [ 'pre', 'varchar', 128, 0 ],
    [ 'post', 'varchar', 128, 0 ],
], 'page', 1 ];

//$db->automate_tables( $pages_struct );