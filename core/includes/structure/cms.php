<?php

/**
 * Creates CMS table to store pages
 */

$page_struct = [
    [ 'date', 'DATETIME', '', 1 ],
    [ 'update', 'DATETIME', '', 1 ],
    [ 'title', 'TEXT', '', 1 ],
    [ 'content', 'LONGTEXT', '', 0 ],
    [ 'url', 'VARCHAR', 128, 1 ],
    [ 'password', 'VARCHAR', 256, 0 ],
    [ 'type', 'VARCHAR', 64, 0 ],
    [ 'parent', 'INT', 13, 0 ],
    //[ 'image', 'VARCHAR', 512, 0 ],
    [ 'status', 'VARCHAR', 1, 1 ],
    [ 'birth', 'DATETIME', '', 0 ],
    [ 'expiry', 'DATETIME', '', 0 ],
    [ 'meta_desc', 'VARCHAR', 512, 0 ],
    [ 'meta_words', 'VARCHAR', 512, 0 ],
    [ 'meta_author', 'VARCHAR', 256, 0 ],
    [ 'by', 'INT', 13, 0 ],
    //[ 'pre', 'varchar', 128, 0 ],
    //[ 'post', 'varchar', 128, 0 ],
];
$page_data = [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'value', 'VARCHAR', 4096, 1 ],
    [ 'load', 'BOOL', '', 0 ],
];
$page_terms = [
    [ 'name', 'VARCHAR', 256, 1 ],
    [ 'value', 'VARCHAR', 4096, 1 ],
    [ 'data', 'TEXT', '', 1 ],
    [ 'load', 'BOOL', '', 0 ],
];

$pages_struct[] = [ 'pages', $page_struct, 'page', 1 ];
$pages_struct[] = [ 'page_data', $page_data, 'pd', 1 ];
$pages_struct[] = [ 'page_terms', $page_terms, 'pt', 1 ];

$db = new DB();
$db->automate_tables( $pages_struct );