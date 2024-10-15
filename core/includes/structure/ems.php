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
    [ 'title', 'VARCHAR', 128, 1 ],
    [ 'desc', 'VARCHAR', 512, 0 ],
    [ 'color', 'VARCHAR', 26, 0 ],
    [ 'icon', 'VARCHAR', 64, 0 ],
    [ 'status', 'int', 2, 1 ],
], 'dept', 1 ];
$pages_struct[] = [ 'aio_designations', [
    [ 'title', 'VARCHAR', 128, 1 ],
    [ 'desc', 'VARCHAR', 512, 0 ],
    [ 'seniority', 'VARCHAR', 26, 0 ],
    [ 'dept', 'INT', 13, 0 ],
    [ 'color', 'VARCHAR', 26, 0 ],
    [ 'icon', 'VARCHAR', 64, 0 ],
    [ 'status', 'int', 2, 1 ],
], 'des', 1 ];
$pages_struct[] = [ 'aio_contract_types', [
    [ 'title', 'VARCHAR', 128, 1 ],
    [ 'desc', 'VARCHAR', 512, 0 ],
    [ 'icon', 'VARCHAR', 512, 0 ],
    [ 'color', 'VARCHAR', 26, 0 ],
    [ 'duration', 'VARCHAR', 3, 0 ],
    [ 'work', 'TEXT', '', 0 ],
    [ 'leaves', 'TEXT', '', 0 ],
    [ 'visa', 'VARCHAR', 2, 1 ],
    [ 'visa_term', 'VARCHAR', 2, 0 ],
    [ 'visa_alert', 'VARCHAR', 2, 0 ],
    [ 'visa_cost', 'FLOAT', '', 0 ],
    [ 'li', 'VARCHAR', 2, 1 ],
    [ 'li_term', 'VARCHAR', 2, 0 ],
    [ 'li_alert', 'VARCHAR', 2, 0 ],
    [ 'li_cost', 'FLOAT', '', 0 ],
    [ 'mi', 'VARCHAR', 2, 1 ],
    [ 'mi_term', 'VARCHAR', 2, 0 ],
    [ 'mi_alert', 'VARCHAR', 2, 0 ],
    [ 'mi_cost', 'FLOAT', '', 0 ],
    [ 'ei', 'VARCHAR', 2, 1 ],
    [ 'ei_term', 'VARCHAR', 2, 0 ],
    [ 'ei_alert', 'VARCHAR', 2, 0 ],
    [ 'ei_cost', 'FLOAT', '', 0 ],
    [ 'renew_alert', 'VARCHAR', 2, 0 ],
    [ 'status', 'int', 2, 1 ],
], 'con_type', 1 ];

$db->automate_tables( $pages_struct );