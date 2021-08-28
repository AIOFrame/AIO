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

function render_maps() {
    include_once( ROOTPATH . 'core/modules/maps.php' );
    global $google_maps_loaded;
    if( !$google_maps_loaded ){
        $gmaps = new Maps();
        $gmaps->google_maps();
        $google_maps_loaded = 1;
    }
}

// Include and Render various BarCode

function barcode( $text, $width = '100', $height = '36', $textShow = 0, $bgColor = '#ffffff', $lineColor = '#000000', $textAlign = 'center', $textPosition = 'bottom', $textMargin = '0', $format = 'CODE128' ) {
    include_once( ROOTPATH . 'core/modules/external/JsBarcode/barcode.php' );
    global $barcode_loaded;
    if( !$barcode_loaded ) {
        $barcode = new BARCODE();
        $barcode->generate($text, $width, $height, $bgColor, $lineColor, $textShow, $textAlign, $textPosition, $textMargin, $format);
        $barcode_loaded = 1;
    }
}

// File Uploader and Stored URL Processor

function file_upload() {
    include_once( ROOTPATH . 'core/modules/file_uploader.php' );
    global $file_upload_loaded;
    if( !$file_upload_loaded ){
        $file = new FUP();
        $file->file_uploader();
        $file_upload_loaded = 1;
    }
}

function render_image( string $logo = '', string $name = '', string $class = '' ) {
    //skel( !file_exists( APPPATH . $logo ) );
    $n = !file_exists( APPPATH . $logo ) && !empty( $name ) ? '<span>'.$name[0].'</span>' : '';
    $logo = !empty( $logo ) ? ' style="background-image:url(\''.storage_url( $logo ).'\')"' : '';
    echo '<div class="image '.$class.'"'.$logo.'>'.$n.'</div>';
}

function storage_url( $url = '' ): string {
    return mb_substr( $url, 1, 7) == 'storage' || mb_substr( $url, 1, 6) == 'assets' ? APPURL . 'apps/' . APPDIR . $url : $url;
}

function asset_url( $url = '' ): string {
    return $url !== '' ? APPURL . 'apps/' . APPDIR . '/assets/' . $url : '';
}

function file_url( $url ): string {
    return $url !== '' ? APPPATH . $url : '';
}

// Emailer by Modules

function email( $to, $subject, $content, $from, $cc = '' ){
    include_once( ROOTPATH . 'core/modules/email.php' );
    $mail = new MAIL();
    return $mail->send( $to, $subject, $content, $from, $cc );
}

// Language Editor

function language_editor(){
    language_editor();
}
function manage_translations() {
    include_once( ROOTPATH . 'core/modules/translations.php' );
}

