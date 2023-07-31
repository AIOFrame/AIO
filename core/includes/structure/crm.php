<?php

/**
 * Creates table to manage leads and customers
 */

$db = new DB();

$client_struct = [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'short', 'VARCHAR', 12, 0 ],
    [ 'trade_name', 'VARCHAR', 12, 0 ],
    [ 'logo', 'VARCHAR', 512, 0 ],
    [ 'color', 'VARCHAR', 7, 0 ],
    /* [ 'p_code', 'VARCHAR', 6, 0 ],
    [ 'phone', 'VARCHAR', 32, 0 ],
    [ 'm_code', 'VARCHAR', 6, 0 ],
    [ 'mobile', 'VARCHAR', 32, 0 ],
    [ 'fax', 'VARCHAR', 32, 0 ],
    [ 'email', 'VARCHAR', 64, 0 ],
    [ 'website', 'VARCHAR', 64, 0 ],
    [ 'type', 'VARCHAR', 32, 0 ], */
    [ 'progress', 'VARCHAR', 32, 0 ],
    [ 'status', 'TINYINT', 1, 1 ],
    [ 'by', 'INT', 13, 1 ],
    [ 'dt', 'DATETIME', '', 1 ],
];

$notes_struct = [
    [ 'client', 'INT', 13, 1 ],
    [ 'note', 'TEXT', 2048, 1 ],
    [ 'status', 'TINYINT', 1, 1 ],
    [ 'by', 'INT', 13, 1 ],
    [ 'dt', 'DATETIME', '', 1 ],
];

$branch_struct = [
    [ 'client', 'INT', 13, 1 ],
    [ 'name', 'VARCHAR', 32, 1 ],
    [ 'p_code', 'VARCHAR', 6, 0 ],
    [ 'phone', 'VARCHAR', 32, 0 ],
    [ 'm_code', 'VARCHAR', 6, 0 ],
    [ 'mobile', 'VARCHAR', 32, 0 ],
    [ 'fax', 'VARCHAR', 32, 0 ],
    [ 'email', 'VARCHAR', 64, 0 ],
    [ 'website', 'VARCHAR', 64, 0 ],
    [ 'address', 'VARCHAR', 128, 0 ],
    [ 'street', 'VARCHAR', 128, 0 ],
    [ 'city', 'VARCHAR', 128, 0 ],
    [ 'country', 'VARCHAR', 3, 0 ],
    [ 'lat', 'VARCHAR', 64, 0 ],
    [ 'long', 'VARCHAR', 64, 0 ],
    [ 'type', 'VARCHAR', 32, 0 ],
    [ 'status', 'TINYINT', 1, 1 ],
    [ 'by', 'INT', 13, 1 ],
    [ 'dt', 'DATETIME', '', 1 ],
];

$rep_struct = [
    [ 'client', 'INT', 13, 1 ],
    [ 'name', 'VARCHAR', 32, 1 ],
    [ 'm_code', 'VARCHAR', 6, 0 ],
    [ 'mobile', 'VARCHAR', 32, 0 ],
    [ 'email', 'VARCHAR', 64, 0 ],
    [ 'type', 'VARCHAR', 32, 0 ],
    [ 'status', 'TINYINT', 1, 1 ],
    [ 'by', 'INT', 13, 1 ],
    [ 'dt', 'DATETIME', '', 1 ],
];

/* if( defined( 'FEATURES' ) && ( in_array( 'region', FEATURES ) || in_array( 'regions', FEATURES ) ) ) {
    global $options;
    if( !empty( $options['regions'] ) ) {
        $rs = array_map( 'trim', explode( ',', $options['regions'] ) );
        if( !empty( $rs ) ) {
            foreach( $rs as $r ) {
                $client_struct[] = [ $r.'_trade_license_no', 'VARCHAR', 64, 0 ];
            }
        }
    }
    //$client_struct
} */

//skel( $client_struct );

$struct[] = [ 'clients', $client_struct, 'client', 1 ];
$struct[] = [ 'client_branches', $branch_struct, 'cbr', 1 ];
$struct[] = [ 'client_notes', $notes_struct, 'cn', 1 ];
$struct[] = [ 'client_reps', $rep_struct, 'crep', 1 ];

//skel( $struct );
$db->automate_tables( $struct );