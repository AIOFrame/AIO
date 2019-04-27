<?php

// THIS FILE FETCHES THIRD PARTY DEPENDENCIES THAT ARE OPTIONAL

// Include VUE JS

function get_vue( $type = '' ) {
    if( !empty( $type ) ) {
        echo '<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>';
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/vue.js"></script>';
    }
}

// Include SMS Module and Send SMS

function send_sms( $number, $message, $gateway, $key = '', $secret = '' ) {
    include_once( COREPATH . 'core/components/sms.php' );
    global $sms_loaded;
    if( !$sms_loaded ){
        $sms = new SMS;
        return $sms->send_sms( $number, $message, $gateway, $key, $secret );
        $sms_loaded = 1;
    }
}

function nsms( $no, $msg ) {
    $key = get_option( 'sms_key' );
    $secret = get_option( 'sms_secret' );
    send_sms( '971'.$no, $msg, 'nexmo', $key, $secret );
}

// Include Google Maps Module and Render Map

function render_maps() {
    include_once( COREPATH . 'core/components/google_maps.php' );
    global $google_maps_loaded;
    if( !$google_maps_loaded ){
        $gmaps = new GMaps();
        $gmaps->google_maps();
        $google_maps_loaded = 1;
    }
}

// Include and Render various BarCode

function barcode( $text, $width = '100', $height = '36', $textShow = 0, $bgColor = '#ffffff', $lineColor = '#000000', $textAlign = 'center', $textPosition = 'bottom', $textMargin = '0', $format = 'CODE128' ) {
    include_once( COREPATH . 'core/components/barcode.php' );
    global $barcode_loaded;
    if( !$barcode_loaded ) {
        $brcd = new BRCD;
        return $brcd->generate($text, $width, $height, $bgColor, $lineColor, $textShow, $textAlign, $textPosition, $textMargin, $format);
        $barcode_loaded = 1;
    }
}

// File Uploader and Stored URL Processor

function file_upload() {
    include_once( COREPATH . 'core/components/file_uploader.php' );
    global $file_upload_loaded;
    if( !$file_upload_loaded ){
        $file = new FUP();
        $file->file_uploader();
        $file_upload_loaded = 1;
    }
}
function storage_url( $url ) {
    return $url !== '' ? APPURL . 'apps/' . APPDIR . str_replace( ' ', '%20', $url) : '';
}

function file_url( $url ) {
    return $url !== '' ? APPPATH . $url : '';
}

// Emailer by Modules

function email( $to, $subject, $content, $from, $cc = '' ){
    include_once( COREPATH . 'core/components/mailer.php' );
    $mail = new MAIL();
    return $mail->send_email( $to, $subject, $content, $from, $cc );
}

function sendgrid( $to, $subject, $content, $from, $cc = '' ) {

    $key = get_option( 'sendgrid_key' );

    if( !empty( $key ) ) {

        require COREPATH . 'core/components/emailers/sendgrid/sendgrid-php.php';

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom( $from );
        $email->setSubject( $subject );

        if( is_array( $to ) ){
            foreach( $to as $k => $v ){
                $email->addTo($k, $v);
            }
        } else {
            $email->addTo($to);
        }

        $email->addContent("text/html", $content);
        $sendgrid = new \SendGrid( $key );
        try {
            $response = $sendgrid->send($email);
            elog( $response->statusCode() );
        } catch (Exception $e) {
            elog( $e->getMessage() );
            return 0;
        }
    } else {
        return 1;
    }
}

function mandrill( $to, $to_name ,$subject, $content, $from , $from_name, $cc = '' ){
    include_once( COREPATH . 'core/components/mailer.php' );
    global $mailer_loaded;
    if( !$mailer_loaded ){
        $mailer = new Mailer;
        return $mailer->mandrill_send( $to,$to_name, $subject, $content, $from,$from_name, $cc );
        $mailer_loaded = 1;
    }
}

// Language Editor

function language_editor(){
    language_editor();
}
function manage_translations() {
    include_once( COREPATH . 'core/components/translations.php' );
}