<?php

function icon( $name, $size = 25, $thickness = 15, $color = '#000000', $stroke = true ) {
    global $icon;
    if( file_exists( COREPATH . 'core/components/icons/'. $name . '.svg' ) ) {
        $icon++;
        $stroke = $stroke ? 'none' : $color;
        echo '<div class="ico" style="width:' . $size . 'px;height:' . $size . 'px;stroke-width:' . $thickness . ' !important;stroke:' . $color . ';fill:' . $stroke . ' !important">';
        echo '<style>.</style>';
        $f = file_get_contents( COREPATH . 'core/components/icons/' . $name . '.svg');
        /* $f = preg_replace('@<(style)\b.*?>.*?</\1>@si', '', $f); */
        $f = remove_elements( $f, '<style', '</style>' );
        echo $f;
        echo '</div>';
    }
}