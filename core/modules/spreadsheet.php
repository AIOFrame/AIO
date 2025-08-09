<?php

class SPREADSHEET {

    private static $instance;

    public static function initiate(): SPREADSHEET {
        if (self::$instance === null) {
            self::$instance = new SPREADSHEET();
        }
        return self::$instance;
    }

    private function __construct() {
        require_once VENDORLOAD;
    }

    /**
     * Exports array as Spreadsheet
     * @param string $title Title
     * @param array $headers Header content array
     * @param array $body Body content array
     * @param array $footers Footer content array
     * @param bool $save_file True to save file on server or false to download (default false)
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export( string $title = 'Export', array $headers = [], array $body = [], array $footers = [], bool $save_file = false ) {

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
            $r = !empty($headers) ? 2 : 1;
            foreach ($body as $row) {
                //skel( $row );
                if (is_array($row)) {
                    $x = 0;
                    foreach ($row as $b) {
                        $f = $alphas[$x] . $r;
                        $b = $b !== null ? str_replace(',', '', $b) : $b;
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
        $file_name =  $title . '-' . date('d-m-y') . '.xlsx';

        ob_start();
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $file_name);
        header('Cache-Control: max-age=0');
        ob_end_clean();
        $writer = new PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        if ($save_file) {
            if (!file_exists(APPPATH . '/storage/xlsx/')) {
                mkdir(APPPATH . '/storage/xlsx/', 0777, true);
            }
            $writer->save( APPPATH . '/storage/xlsx/' . $file_name );
        } else {
            $writer->save('php://output');
        }
        die();
    }

    /**
     * Import a spreadsheet file as a data array
     * @param string $file_path
     * @param string $type
     * @return array
     */
    public function import( string $file_path, string $type = 'xlsx' ): array {
        $data = [];
        if( file_exists( $file_path ) ){
            try {
                elog( $type );
                elog( $file_path );
                $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader($type);
                $spreadsheet = $reader->load( $file_path );
                if( $type == 'xlsx' ) {
                    $sheet_names = $spreadsheet->getSheetNames();
                    if( is_array( $sheet_names ) ){
                        foreach( $sheet_names as $sn ) {
                            $data[$sn] = $spreadsheet->getSheetByName($sn)->toArray();
                        }
                    }
                } else if( $type == 'Csv' ) {
                    $worksheet = $spreadsheet->getActiveSheet();
                    $data = $worksheet->toArray();
                }
            } catch( Exception $e ) {
                elog( $e, 'error', 103, ROOTPATH . 'core/modules/spreadsheet.php' );
            }
        } else {
            skel( 'File doesnt exist!' );
        }
        return $data;
    }
}