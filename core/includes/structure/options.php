<?php

/**
 * Creates options Table to save App settings, not optional
 */

$db = new DB();

$option_struct[] = [ 'options', [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'value', 'VARCHAR', 4096, 1 ],
    [ 'scope', 'INT', 13, 0 ],
    [ 'load', 'BOOL', '', 0 ],
], 'option', 1 ];

$db->automate_tables( $option_struct );