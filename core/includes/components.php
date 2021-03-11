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
    $db = new DB();
    $key = $db->get_option( 'sms_key' );
    $secret = $db->get_option( 'sms_secret' );
    send_sms( $no, $msg, 'nexmo', $key, $secret );
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
function storage_url( $url = '' ) {
    elog(mb_substr( $url, 1, 7));
    return mb_substr( $url, 1, 7) == 'storage' || mb_substr( $url, 1, 6) == 'assets' ? APPURL . 'apps/' . APPDIR . $url : $url;
}

function asset_url( $url = '' ) {
    return $url !== '' ? APPURL . 'apps/' . APPDIR . '/assets/' . $url : '';
}

function file_url( $url ) {
    return $url !== '' ? APPPATH . $url : '';
}

// Emailer by Modules

function email( $to, $subject, $content, $from, $cc = '' ){
    include_once( COREPATH . 'core/components/email.php' );
    $mail = new MAIL();
    return $mail->send( $to, $subject, $content, $from, $cc );
}

// Language Editor

function language_editor(){
    language_editor();
}
function manage_translations() {
    include_once( COREPATH . 'core/components/translations.php' );
}

class Excel {

    private static $instance;

    public static function initiate() {
        if (self::$instance === null) {
            self::$instance = new Excel();
        }
        return self::$instance;
    }

    private function __construct() {
        require_once COREPATH . 'core/components/ext/phpspreadsheet/autoload.php';
    }

    // Export single spreadsheet
    public function export($title = 'Export', $headers = [], $body = [], $footers = [], $save_file = false) {

        $spreadsheet = new PhpOffice\PhpSpreadsheet\Spreadsheet();
        //$format = new PhpOffice\PhpSpreadsheet\Style\NumberFormat();
        $spreadsheet->setActiveSheetIndex(0);
        $sheet = $spreadsheet->getActiveSheet();

        // ADDING HEADERS
        if (!empty($headers)) {
            $alphas = range('A', 'Z');
            $x = 0;
            foreach ($headers as $h) {
                $f = $alphas[$x] . '1';
                $sheet->setCellValue($f, $h)->getStyle($f)->getFont()->setBold(true);
                $x++;
            }
        }

        // ADDING BODY
        if (!empty($body)) {
            $alphas = range('A', 'Z');
            $r = !empty($headers) ? 2 : 0;
            foreach ($body as $bds) {
                if (is_array($bds)) {
                    $x = 0;
                    foreach ($bds as $b) {
                        $f = $alphas[$x] . $r;
                        $b = str_replace(',', '', $b);
                        /*if( is_float( $b ) ){
                            $sheet->setCellValueExplicit( $f, $b, $format::FORMAT_NUMBER );
                        } else if( is_numeric( $b ) ) {
                            $sheet->setCellValueExplicit( $f, $b, $format::FORMAT_NUMBER );
                        } else {
                        }*/
                        $sheet->setCellValue($f, $b);
                        $x++;
                    }
                }
                $r++;
            }
        }

        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $title . '-' . date('d-m-y') . '.xlsx"');
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if ($save_file) {
            $writer->save(APPPATH . '/storage/xlsx/');
        } else {
            $writer->save('php://output');
        }
        die();
    }

    public function import( $file ): array {
        $data = [];
        if( file_exists( $file['tmp_name'] ) ){
            $e = explode(".", $file['name']);
            $type = is_array($e) ? ucfirst( $e[count($e)-1] ) : 'Xlsx';
            $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
            $spreadsheet = $reader->load( $file['tmp_name'] );
            $sheetnames = $spreadsheet->getSheetNames();
            if( is_array( $sheetnames ) ){
                foreach( $sheetnames as $sn ) {
                    $data[$sn] = $spreadsheet->getSheetByName($sn)->toArray();
                }
            }
        }
        return $data;
    }
}