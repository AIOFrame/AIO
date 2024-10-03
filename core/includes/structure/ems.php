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

// Departments & Designations
$pages_struct[] = [ 'aio_departments', [
    [ 'title', 'TEXT', '', 1 ],
    [ 'desc', 'VARCHAR', 512, 1 ],
    [ 'color', 'VARCHAR', 26, 0 ],
    [ 'icon', 'VARCHAR', 64, 0 ],
    [ 'status', 'int', 2, 1 ],
], 'dept', 1 ];
$pages_struct[] = [ 'aio_designations', [
    [ 'title', 'TEXT', '', 1 ],
    [ 'desc', 'VARCHAR', 512, 1 ],
    [ 'seniority', 'VARCHAR', 26, 0 ],
    [ 'dept', 'INT', 13, 0 ],
    [ 'color', 'VARCHAR', 26, 0 ],
    [ 'icon', 'VARCHAR', 64, 0 ],
    [ 'status', 'int', 2, 1 ],
], 'des', 1 ];

$db->automate_tables( $pages_struct );