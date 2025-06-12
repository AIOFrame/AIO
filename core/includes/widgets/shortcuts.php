<?php
$shortcuts = [];
global $options;
$ic = ( $options['icon_class'] ?? 'mico' ) . ' ico';
$r = __r();
foreach( $shortcuts as $l ) {
    $r .= __pre( '', 'col-6 col-md-3', 'div', 'data-'.$l[3] )
    . __anchor( APPURL . 'admin/' . $l[2], 'card br8', T( 'Go to ' . $l[1] ) )
        . __r()
            . __c(3)
                . __el( 'i', $ic . ' ico xl grad-bg '.$l[0], $l[4] ?? '' )
            . c__()
            . __c( 9, 'tac' )
                . __h2( $l[3], 0 ) . __h5( $l[1], 0 )
            . c__()
        . r__()
    . anchor__()
    . __post();
}
$r .= r__();
return $r;