<?php

function favicon( $icon ){
    if( file_exists( APPPATH . 'assets/images/' . $icon . '.png' ) ) {
        $link = APPURL . 'apps/' . APPNAME . '/assets/images/'.$icon.'.png';
        echo '<link rel="shortcut icon" href="'.$link.'">';
        if (is_mobile()) {
            echo file_exists( APPPATH . 'assets/images/' . $icon . '.png' ) ? '<link rel="apple-touch-icon" href="'.$link.'"/>' : '';
            $sizes = [144,114,72,57];
            foreach( $sizes as $s ){
                echo file_exists( APPPATH . 'assets/images/' . $icon . '-' . $s . '.png' ) ? '<link rel="apple-touch-icon" href="'.APPURL . 'apps/' . APPNAME . 'assets/images/'.$icon.'-'.$s.'.png"/>' : '';
            }
        }
    }
}