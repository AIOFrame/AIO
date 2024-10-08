<?php

// THIS FILE FETCHES THIRD PARTY DEPENDENCIES THAT ARE OPTIONAL

function get_module( $mod = '' ) {
    if( file_exists( ROOTPATH . 'core/modules/'. $mod . '.php' ) )
        include_once( ROOTPATH . 'core/modules/'. $mod . '.php' );
}

// Include SMS Module and Send SMS

function send_sms( $number, $message, $gateway, $key = '', $secret = '' ) {
    include_once( ROOTPATH . 'core/modules/sms.php' );
    global $sms_loaded;
    if( !$sms_loaded ){
        $sms = new SMS;
        return $sms->send_sms( $number, $message, $gateway, $key, $secret );
        $sms_loaded = 1;
    }
}

function nsms( $no, $msg ) {
    $db = new DB();
    $key = $db->get_option( 'sms_key' );
    $secret = $db->get_option( 'sms_secret' );
    send_sms( $no, $msg, 'nexmo', $key, $secret );
}

// Include Google Maps Module and Render Map

function render_maps(): void {
    include_once( ROOTPATH . 'core/modules/maps.php' );
    global $google_maps_loaded;
    if( !$google_maps_loaded ){
        $gmaps = new Maps();
        $gmaps->google_maps();
        $google_maps_loaded = 1;
    }
}

// Include and Render various BarCode

function barcode( $text, $width = '100', $height = '36', $textShow = 0, $bgColor = '#ffffff', $lineColor = '#000000', $textAlign = 'center', $textPosition = 'bottom', $textMargin = '0', $format = 'CODE128' ) : void {
    include_once( ROOTPATH . 'core/modules/external/JsBarcode/barcode.php' );
    //global $barcode_loaded;
    //if( !$barcode_loaded ) {
        $barcode = new BARCODE();
        $barcode->generate($text, $width, $height, $bgColor, $lineColor, $textShow, $textAlign, $textPosition, $textMargin, $format);
        $barcode_loaded = 1;
    //}
}

function qrcode( $text, $width = '100', $height = '100', $color = '#000000' ): void {
    include_once( ROOTPATH . 'core/modules/external/QRCode/qrcode.php' );
    $qrcode = new QRCODE();
    $qrcode->generate( $text, $width, $height, $color );
}

// File Uploader and Stored URL Processor

function file_upload(): void {
    include_once( ROOTPATH . 'core/modules/file_uploader.php' );
    global $file_upload_loaded;
    if( !$file_upload_loaded ){
        $file = new FUP();
        $file->file_uploader();
        $file_upload_loaded = 1;
    }
}

function storage_url( $url = '' ): string {
    if( !empty( $url ) ) {
        return mb_substr( $url, 1, 7) == 'storage' || mb_substr( $url, 1, 6) == 'assets' ? APPURL . 'apps/' . APPDIR . $url : $url;
    } else {
        return '';
    }
}

function storage_path( $url = '' ): string {
    if( !empty( $url ) ) {
        return mb_substr( $url, 1, 7) == 'storage' || mb_substr( $url, 1, 6) == 'assets' ? APPPATH . $url : $url;
    } else {
        return '';
    }
}

function get_file_info( $url ): array {
    return [];
}

function asset_url( string $url = '' ): void {
    echo __asset_url( $url );
}

function __asset_url( string $url = '' ): string {
    return $url !== '' ? APPURL . 'apps/' . APPDIR . '/assets/' . $url : '';
}

function file_url( $url ): string {
    return $url !== '' ? APPPATH . $url : '';
}

// Emailer by Modules

function email( $to, $subject, $content, $from, $cc = '' ) {
    include_once( ROOTPATH . 'core/modules/email.php' );
    $mail = new MAIL();
    return $mail->send( $to, $subject, $content, $from, $cc );
}

// Language Editor

