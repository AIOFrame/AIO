<?php

// Reset Icons
if( APPDEBUG && file_exists( APPPATH . 'assets/styles/icons.css' ) ) {
    $icon_css = fopen(APPPATH . 'assets/styles/icons.css', 'w') or die();
    fwrite($icon_css, '');
    fclose($icon_css);
}

function _icon( $name, $size = 25, $thickness = 15, $color = '#000000', $stroke = true ) {
    global $icons;
    if( is_array( $name ) ){
        if( file_exists( COREPATH . 'core/components/icons/'. $name[0] . '.svg' ) || file_exists( APPPATH . 'assets/images/icons/' .$name[0] . '.svg' ) ) {
            $name = $name[0];
        } else {
            icon( $name[1], $size, $thickness, $color, $stroke );
            return;
        }
    }
    //$icon_css = fopen(APPPATH.'assets/icons.php','w') or die();
    if( file_exists( COREPATH . 'core/components/icons/'. $name . '.svg' ) || file_exists( APPPATH . 'assets/images/icons/' .$name . '.svg' ) ) {
        $icons[] = $name;
        $stroke = $stroke ? 'none' : $color;
        $data = '<div class="ico '.str_replace(' ','_',$name).'" style="width:' . $size . 'px;height:' . $size . 'px;stroke-width:' . $thickness . ' !important;stroke:' . $color . ' !important;fill:' . $stroke . ' !important">';
        $f = file_exists( COREPATH . 'core/components/icons/'. $name . '.svg' ) ? file_get_contents( COREPATH . 'core/components/icons/' . $name . '.svg') : file_get_contents( APPPATH . 'assets/images/icons/' . $name . '.svg');
        $f = remove_elements( $f, '<style', '</style>' );
        $f = remove_elements( $f, '<?xml', '?>' );
        $f = remove_elements( $f, '<!-- Generator', ')  -->' );
        //echo '<style>.</style>';
        /* $f = preg_replace('@<(style)\b.*?>.*?</\1>@si', '', $f); */
        //$f = remove_elements( $f, '<style', '</style>' );
        $data .= $f;
        $data .= '</div>';
        /* if( APPDEBUG && !empty( $icons ) ){
            $f = file_get_contents( COREPATH . 'core/components/icons/' . $name . '.svg');
            $f = remove_elements( $f, '<style', '</style>' );
            $f = remove_elements( $f, '<?xml', '?>' );
            $f = remove_elements( $f, '<!-- Generator', ')  -->' );
            $f = '.'.$name.' { background:url(\'data:image/svg+xml;utf8,'.$f.'\'); }';
            $icon_css = file_put_contents(APPPATH.'assets/styles/icons.css',$f.' '.PHP_EOL,FILE_APPEND | LOCK_EX);
        } */
        return $data;
    } else if( file_exists( APPPATH . 'assets/images/'.$name.'.jpg') || file_exists( APPPATH . 'assets/images/'.$name.'.png' ) ) {

    } else {

    }
}

function icon( $name, $size = 25, $thickness = 15, $color = '#000000', $stroke = true ) {
    echo _icon( $name, $size, $thickness, $color, $stroke );
}