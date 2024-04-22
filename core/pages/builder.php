<?php
get_comp('aio_builder/head');
pre( '', '', 'aside' );
    get_comp('aio/aside/languages');
post( 'aside' );
_article( '', 'data-off=".scroll"' );
    global $steps;
    if( !empty( $steps ) ) {
        _d( 'setup_wrap', '', 'data-tabs' );
        foreach( $steps as $k => $d ) {
            builder_step( $k, $d );
        }
        d_();
    }
article_();
get_comp('aio_builder/foot');