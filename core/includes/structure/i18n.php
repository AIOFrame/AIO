<?php

$db = new DB();
$tables[] = [ 'translations', [
    [ 'base', 'VARCHAR', 4096, 1 ],
    [ 'page', 'VARCHAR', 512, 0 ],
], 't', 1 ];

global $options;
//skel( $options );
$regions = $options['regions'] ?? '';
$regions = explode( ', ', $regions);
$i18ns = '';
if( !empty( $regions ) ) {
    foreach ($regions as $region) {
        $i18ns .= $options[ strtolower($region) . '_languages' ] . ', ';
        //$i18ns[] =
    }
} else {
    $i18ns = $options['languages'] ?? [];
}
$i18ns = !empty( $i18ns ) ? explode( ',', str_replace( ' ', '', $i18ns ) ) : [];

//skel( $i18ns );
//$base = $os['base_language'] ?? 'English';
if( is_array( $i18ns ) && !empty( $i18ns ) ) {
    $i18ns = array_unique( $i18ns );
    foreach( $i18ns as $l ) {
        if( $l !== '' && $l !== 'EN' && $l !== 'en' && $l !== 'English' && $l !== 'english' ) {
            $tables[0][1][] = [ $l, 'TEXT', '', 0 ];
        }
    }
}
//skel( $tables );
if( !isset( $options['languages_updated'] ) || $options['languages_updated'] == 1 ) {
    $db->create_tables( $tables );
    $db->update_option( 'languages_updated', 2, 0, 1 );
}
