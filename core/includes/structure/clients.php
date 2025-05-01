<?php

$client_struct = [
    [ 'clients', [
        [ 'name', 'VARCHAR', 256, 1 ],
        [ 'status', 'TINYINT', 1, 1 ],
        [ 'logo', 'VARCHAR', 512, 0 ],
        [ 'tno', 'VARCHAR', 128, 0 ],
        [ 'rid', 'VARCHAR', 128, 0 ],
        [ 'address', 'VARCHAR', 256, 0 ],
        [ 'city', 'VARCHAR', 256, 0 ],
        [ 'state', 'VARCHAR', 256, 0 ],
        [ 'country', 'VARCHAR', 128, 0 ],
        [ 'zip', 'VARCHAR', 64, 0 ],
        [ 'website', 'VARCHAR', 64, 0 ],
    ], 'client', 1 ],
    [ 'client_contacts', [
        [ 'client', 'INT', 13, 1 ],
        [ 'name', 'VARCHAR', 256, 1 ],
        [ 'status', 'TINYINT', 1, 1 ],
        [ 'notes', 'VARCHAR', 256, 0 ],
        [ 'phone_code', 'VARCHAR', 5, 0 ],
        [ 'phone', 'VARCHAR', 16, 0 ],
        //[ 'fax', 'VARCHAR', 24, 0 ],
        [ 'email', 'VARCHAR', 64, 0 ],
    ], 'client', 1 ],
];
$db = new DB();
$db->automate_tables( $client_struct );