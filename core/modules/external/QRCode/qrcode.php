<?php

class QRCODE {

    /**
     * Converts a string or int to Barcode
     * @param string $text Text to convert to Barcode
     * @param int $width Width of the barcode
     * @param int $height Height of the barcode
     * @param string $bgColor Background color hex of the barcode
     * @param string $lineColor Barcode lines color
     * @author Shaikh <hey@shaikh.dev>
     */
    function generate( string $text, int $width, int $height, string $bgColor, string $lineColor ): void {
        get_script('qrcode');
        $r = rand(1,9999);
        echo '<div id="bc_'.$r.'"></div>';
        echo '<script>';
        echo 'new QRCode(document.getElementById("bc_'.$r.'"), { useSVG: true,';
        echo 'text: "'.$text.'",';
        echo 'colorLight: "'.$bgColor.'",';
        echo 'colorDark: "'.$lineColor.'",';
        echo 'width: '.$width.',';
        echo 'height: '.$height.',';
        echo '});';
        echo '</script>';
    }
}
