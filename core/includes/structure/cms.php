<?php

/**
 * Creates CMS table to store pages
 */

$db = new DB();

$pages_struct[] = [ 'pages', [
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

$pages_struct[] = [ 'page_data', [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'value', 'VARCHAR', 4096, 1 ],
    [ 'load', 'BOOL', '', 0 ],
], 'pd', 1 ];

$db->automate_tables( $pages_struct );