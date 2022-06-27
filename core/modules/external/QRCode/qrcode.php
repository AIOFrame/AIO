<?php

class QRCODE {

    /**
     * Converts a string or int to QRCode
     * @param string $text Text to convert to QRCode
     * @param int $width Width of the QRCode
     * @param int $height Height of the QRCode
     * @param string $color Color hex of the QRCode
     * @author Shaikh <hey@shaikh.dev>
     */
    function generate( string $text, int $width, int $height, string $color ): void {
        get_script('qrcode');
        $r = rand(1,9999);
        echo '<div id="bc_'.$r.'"></div>';
        echo '<script>';
        echo 'new QRCode(document.getElementById("bc_'.$r.'"), { useSVG: true,';
        echo 'text: "'.$text.'",';
        echo 'colorLight: "#fff",';
        echo 'colorDark: "'.$color.'",';
        echo 'width: '.$width.',';
        echo 'height: '.$height.',';
        echo '});';
        echo '</script>';
    }
}
