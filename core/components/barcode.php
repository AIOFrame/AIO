<?php

/**
 * Class BARCODE
 */
class BARCODE {

    /**
     * Converts a string or int to Barcode
     * @param string $text Text to convert to Barcode
     * @param int $width Width of the barcode
     * @param int $height Height of the barcode
     * @param string $bgColor Background color hex of the barcode
     * @param string $lineColor Barcode lines color
     * @param string $textShow true or false to show code as text
     * @param string $textAlign left, right or center align text
     * @param string $textPosition top, center or bottom position text
     * @param int $textMargin Margin around the text
     * @param string $format Format of the barcode
     * @author Shaikh <hey@shaikh.dev>
     */
    function generate( string $text, int $width, int $height, string $bgColor, string $lineColor, string $textShow, string $textAlign, string $textPosition, int $textMargin, string $format ) {
        if( !defined( 'BARCODE_INCLUDED' ) ) {
            if (file_exists(ROOTPATH . 'core/components/ext/barcode/' . strtolower($format) . '.min.js')) {
                echo '<script src="' . APPURL . '/core/components/ext/barcode/' . strtolower($format) . '.min.js" type="text/javascript"></script>';
                define( 'BARCODE_INCLUDED', true );
            }
        }
        $r = rand(1,9999);
        $textShow = $textShow ? "true" : "false";
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
