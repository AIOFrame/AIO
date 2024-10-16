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
    [ 'status', 'INT', 1, 1 ],
    [ 'birth', 'DATETIME', '', 0 ],
    [ 'expiry', 'DATETIME', '', 0 ],
    [ 'meta_desc', 'VARCHAR', 512, 0 ],
    [ 'meta_words', 'VARCHAR', 512, 0 ],
    [ 'author', 'VARCHAR', 256, 0 ],
    [ 'follow', 'BOOL', '', 1 ],
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
$widgets = [
    [ 'name', 'VARCHAR', 128, 1 ],
    [ 'desc', 'VARCHAR', 256, 1 ],
    [ 'image', 'VARCHAR', 512, 0 ],
    [ 'form', 'TEXT', '', 0 ],
    [ 'html', 'TEXT', '', 0 ],
    [ 'ui_front', 'TEXT', '', 0 ],
    [ 'ui_back', 'TEXT', '', 0 ],
    [ 'ux_front', 'TEXT', '', 0 ],
    [ 'ux_back', 'TEXT', '', 0 ],
    [ 'status', 'INT', 1, 1 ],
];

$pages_struct[] = [ 'content', $page_struct, 'content', 1 ];
$pages_struct[] = [ 'content_data', $page_data, 'cd', 1 ];
//$pages_struct[] = [ 'content_terms', $page_terms, 'pt', 1 ];
$pages_struct[] = [ 'widgets', $widgets, 'widget', 1 ];

$db = new DB();
$db->automate_tables( $pages_struct );