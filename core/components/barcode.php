<?php

class BRCD {

    function generate( $text, $width, $height, $bgColor, $lineColor, $textShow, $textAlign, $textPosition, $textMargin, $format ) {
        if( !defined( 'BARCODE_INCLUDED' ) ) {
            if (file_exists(COREPATH . 'core/components/barcode/' . strtolower($format) . '.min.js')) {
                echo '<script src="' . APPURL . '/core/components/barcode/' . strtolower($format) . '.min.js" type="text/javascript"></script>';
                define( 'BARCODE_INCLUDED', true );
            }
        }
        $r = rand(1,9999);
        echo '<svg id="brcd_'.$r.'"></svg>';
        echo '<script>';
            echo 'JsBarcode("#brcd_'.$r.'", "'.$text.'", {';
                echo 'format: "'.$format.'",';
                echo 'lineColor: "'.$lineColor.'",';
                echo 'background: "'.$bgColor.'",';
                echo 'width: '.($width/100).',';
                echo 'height: '.$height.',';
                echo 'displayValue: '.$textShow.',';
                echo 'textAlign: "'.$textAlign.'",';
                echo 'textPosition: "'.$textPosition.'",';
                echo 'textMargin:"'.$textMargin.'",';
                echo 'fontSize:12';
            echo '});';
        echo '</script>';
    }
}
