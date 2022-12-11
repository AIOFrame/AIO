<?php

/**
 * Creates file upload database
 */

$storage_struct[] = [ 'storage', [
    [ 'name', 'VARCHAR', 512, 1 ],
    [ 'url', 'VARCHAR', 512, 1 ],
    [ 'scope', 'INT', 13, 0 ],
    [ 'type', 'VARCHAR', 32, 0 ],
    [ 'size', 'INT', 13, 1 ],
    //[ 'small', 'VARCHAR', 512, 0 ],
    //[ 'medium', 'VARCHAR', 512, 0 ],
    [ 'delete', 'BOOLEAN', '', 0 ],
], 'file', 1 ];
$db = new DB();
$db->automate_tables( $storage_struct );