<?php

$db = new DB();
$tables[] = [ 'translations', [
    [ 'base', 'VARCHAR', 4096, 1 ],
    [ 'page', 'VARCHAR', 512, 0 ],
], 't', 1 ];

global $options;
$i18ns = $options['languages'] ?? '';
$i18ns = !empty( $i18ns ) ? explode( ',', str_replace( ' ', '', $i18ns ) ) : [];
//$base = $os['base_language'] ?? 'English';
if( is_array( $i18ns ) && !empty( $i18ns ) ) {
    $i18ns = array_unique( $i18ns );
    foreach( $i18ns as $l ) {
        if( $l !== 'EN' && $l !== 'en' && $l !== 'English' && $l !== 'english' ) {
            $tables[0][1][] = [ $l, 'VARCHAR', 4999, 0 ];
        }
    }
}
if( !isset( $options['languages_updated'] ) || $options['languages_updated'] == 1 ) {
    skel( $tables );
    $db->create_tables( $tables );
    $db->update_option( 'languages_updated', 2, 0, 1 );
}
