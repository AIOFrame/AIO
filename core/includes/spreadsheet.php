<?php

class Excel {

    private static $instance;

    public static function initiate() {
        if (self::$instance === null) {
            self::$instance = new Excel();
        }
        return self::$instance;
    }

    private function __construct() {

        require_once(COREPATH . 'core/components/spreadsheet/vendor/autoload.php');

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

    public function import( $file ) {

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

            return $data;

        }

    }
}