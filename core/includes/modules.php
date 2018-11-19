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

function barcode( $text, $width = '100', $height = '36', $textShow = 'false', $bgColor = '#ffffff', $lineColor = '#000000', $textAlign = 'center', $textPosition = 'bottom', $textMargin = '0', $format = 'CODE128' ) {
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
    return !empty( $url ) ? APPURL . 'apps/' . APPDIR . $url : '';
}

// Emailer

function email( $to, $to_name ,$subject, $content, $from , $from_name, $cc = '' ){
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
    include_once( COREPATH . 'core/components/translations.php' );
}
function manage_translations() {
    language_editor();
}

// Export to excel
function export_xlsx( $title = 'Export', $headers = [], $body = [], $footers = [], $save_file = false ) {

    require_once(COREPATH.'core/components/spreadsheet/vendor/autoload.php');

    $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
    //$format = new PhpOffice\PhpSpreadsheet\Style\NumberFormat();
    $spreadsheet->setActiveSheetIndex(0);
    $sheet = $spreadsheet->getActiveSheet();

    // ADDING HEADERS
    if( !empty( $headers ) ){
        $alphas = range( 'A', 'Z' );
        $x = 0;
        foreach( $headers as $h ){
            $f = $alphas[$x].'1';
            $sheet->setCellValue( $f, $h )->getStyle( $f )->getFont()->setBold(true);
            $x++;
        }
    }

    // ADDING BODY
    if( !empty( $body ) ){
        $alphas = range( 'A', 'Z' );
        $r = !empty( $headers ) ? 2 : 0;
        foreach( $body as $bds ){
            if( is_array( $bds ) ){
                $x = 0;
                foreach( $bds as $b ){
                    $f = $alphas[$x].$r;
                    $b = str_replace(',','',$b);
                    /*if( is_float( $b ) ){
                        $sheet->setCellValueExplicit( $f, $b, $format::FORMAT_NUMBER );
                    } else if( is_numeric( $b ) ) {
                        $sheet->setCellValueExplicit( $f, $b, $format::FORMAT_NUMBER );
                    } else {
                    }*/
                    $sheet->setCellValue( $f, $b );
                    $x++;
                }
            }
            $r++;
        }
    }

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="'.$title.'-'.date('d-m-y').'.xlsx"');
    header('Cache-Control: max-age=0');
    ob_end_clean();
    $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    if( $save_file ){
        $writer->save(APPPATH.'/storage/xlsx/');
    } else {
        $writer->save('php://output');
    }
    die();
}