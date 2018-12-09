<?php

// Reset Icons
if( APPDEBUG ) {
    $icon_css = fopen(APPPATH . 'assets/styles/icons.css', 'w') or die();
    fwrite($icon_css, '');
    fclose($icon_css);
}

function icon( $name, $size = 25, $thickness = 15, $color = '#000000', $stroke = true ) {
    global $icons;
    //$icon_css = fopen(APPPATH.'assets/icons.php','w') or die();
    if( file_exists( COREPATH . 'core/components/icons/'. $name . '.svg' ) || file_exists( APPPATH . 'assets/images/icons/' .$name . '.svg' ) ) {
        $icons[] = $name;
        $stroke = $stroke ? 'none' : $color;
        echo '<div class="ico '.str_replace(' ','_',$name).'" style="width:' . $size . 'px;height:' . $size . 'px;stroke-width:' . $thickness . ' !important;stroke:' . $color . ';fill:' . $stroke . ' !important">';
        //echo '<style>.</style>';
        /* $f = preg_replace('@<(style)\b.*?>.*?</\1>@si', '', $f); */
        //$f = remove_elements( $f, '<style', '</style>' );
        //echo $f;
        echo '</div>';
        if( APPDEBUG && !empty( $icons ) ){
            $f = file_get_contents( COREPATH . 'core/components/icons/' . $name . '.svg');
            $f = remove_elements( $f, '<style', '</style>' );
            $f = remove_elements( $f, '<?xml', '?>' );
            $f = remove_elements( $f, '<!-- Generator', ')  -->' );
            $f = '.'.$name.' { background:url(\'data:image/svg+xml;utf8,'.$f.'\'); }';
            $icon_css = file_put_contents(APPPATH.'assets/styles/icons.css',$f.' '.PHP_EOL,FILE_APPEND | LOCK_EX);
        }
    } else if( file_exists( APPPATH . 'assets/images/'.$name.'.jpg') || file_exists( APPPATH . 'assets/images/'.$name.'.png' ) ) {

    }
}