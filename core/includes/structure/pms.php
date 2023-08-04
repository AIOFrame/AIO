<?php
$db = new DB();

$projects_struct = [
    [ 'client', 'INT', 13, 1 ],
    [ 'note', 'TEXT', 2048, 1 ],
    [ 'status', 'TINYINT', 1, 1 ],
    [ 'by', 'INT', 13, 1 ],
    [ 'dt', 'DATETIME', '', 1 ],
];

$struct[] = [ 'projects', $projects_struct, 'pro', 1 ];

//skel( $struct );
$db->automate_tables( $struct );