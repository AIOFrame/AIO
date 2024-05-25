<?php
global $params;
$url = !empty( $params['file'] ) ? $params['file'] : ini_get('error_log');
if( $file = fopen( $url, 'r' ) ) {
    include_once(ROOTPATH . 'core/components/head.php' );
    $f = new FORM();
    $cry = Encrypt::initiate();
    pre( '', '', 'header' );
        div( 'logo' );
        div( 'title', T( 'LOG' ) );
        _d( 'options' );
            div( 'ico', __div( 'mat-ico', 'arrow_back' ) . __el( 'i', 'tip', T('Return to Previous Page') ), 'back', 'onclick="window.history.back()"' );
            div( 'ico', __div( 'mat-ico', 'refresh' ) . __el( 'i', 'tip', T('Reload') ), '', 'onclick="location.reload()"' );
            $f->process_trigger( __div( 'mat-ico', 'delete_sweep' ) . __el( 'i', 'tip', T('Clear Log') ), 'ico clear', '', 'clear_log_ajax', '', '', 'div', '', 2, 2 );
            div( 'ico dark', __div( 'mat-ico', 'dark_mode' ) . __el( 'i', 'tip', T('Toggle Dark Mode') ), '', 'data-dark' );
        d_();
    post( 'header' );
    _article( '', '', 'aio_log' );
        _d( 'df controls' );
            _d( 'filters' );
                _d( 'df' );
                    div( 'on', __div( 'mat-ico', 'format_align_justify' ) . ' ' . T('All'), '', 'data-type="a"' );
                    $types = [
                        [ 'l', 'Log', 'view_list' ],
                        [ 'e', 'Error', 'warning' ],
                        [ 'i', 'Insert', 'add_box' ],
                        [ 's', 'Select', 'storage' ],
                        [ 'u', 'Update', 'cloud_upload' ],
                        [ 'd', 'Delete', 'remove_circle' ]
                    ];
                    foreach( $types as $t ) {
                        div( '', __div( 'mat-ico', $t[2] ) . ' ' . T( $t[1] ), '', 'data-type="'.$t[0].'"' );
                    }
                d_();
            d_();
            _d( 'search_wrap' );
                echo '<input id="search_log" type="search" placeholder="Search">';
            d_();
        d_();
        _d( 'events_log' );
            $x = 0;
            $logger = '';
            $size = filesize($url) / 1000;
            if( $size < 512 ) {
                while ($l = fgets($file)) {

                    // Extract Date
                    $date = get_string_between($l, '[', ']');
                    $l = str_replace('[' . $date . '] ', '', $l);

                    // Extract Log
                    $lb = explode('<AIO>', $l);
                    //if (is_array($lb) && count($lb) > 2) {
                    $log = is_json( $lb[0] ) ? json_decode( $lb[0] ) : $lb[0];
                    $type = $lb[1] ?? 'LOG';
                    $line = $lb[2] ?? '';
                    $doc = $lb[3] ?? '';
                    $target = $lb[4] ?? '';
                    //}
                    //skel( $type );

                    //$edate = easy_date( $date, 'h:i:s a d M, Y' );

                    // Extract Type
                    //$type = get_string_between($l, '|', '|');
                    //$type = !empty( $type ) ? $type : 'LOG';

                    /*if( $type == 'UPDATE' ) {
                        $table = get_string_between( $l, 'UPDATE ', ' ' );
                    } else if ( $type == 'SELECT' ) {
                        $table = get_string_between( $l, 'FROM ', ' ' );
                    }*/

                    // Extract Line
                    //$line = get_string_between($l, '<<', '>>');

                    // Extract Document
                    //$doc = get_string_between($l, '{', '}');
                    //$doc = $log = $line = $table = $type = 'test';
                    //
                    //$php_errors = ['PHP Notice:','PHP 1.','PHP 2.','PHP 3.','PHP 4.','PHP 5.','PHP 6.','PHP 7.',]
                    //if(  )
                    //$log = str_replace( '[' . $date . '] ', '', str_replace( '|' . $type . '| ', '', str_replace( '<<' . $line . '>> ', '', str_replace( '{' . $doc . '}', '', $l ) ) ) );
                    if( !empty( $date ) && !empty( $l ) ) {

                        if( strpos( $l, 'PHP ' ) !== false ) {

                            $logger .= $l . '<br/>';

                        } else {

                            if( $logger !== '' ) {
                                $log = $logger;
                                $logger = '';
                                $type = 'ERROR';
                                $target = get_string_between( $log, ': ', ':' );
                                $line = get_string_between( $log, 'line ', '<' );
                                $doc = get_string_between( $log, 'in ', ' on' );
                            }
                            _d( 'b '. ( isset( $type[0] ) ? strtolower( $type )[0] : '' ) );
                                _d( 'a' );
                                    div( 's', $type ?? '' );
                                    isset($target) && $target !== '' ? div( 't', $target ) : '';
                                d_();
                                _d( 'c' );
                                    div( 'co', '', '', 'data-clipboard-target="#l'.$x.'" title="'.T('Copy syntax').'"' );
                                    div( 'go', '', '', 'title="'.T('Google this error!').'"' );
                                    div( 'so', '', '', 'title="'.T('Search on Stackoverflow').'"' );
                                d_();
                                div( 'l', __el( 'pre', '', is_array( $log ) || is_object( $log ) ? print_r( $log ) : $log ), 'l'.$x );
                                _d( 'ft' );
                                    div( 'l', !empty($line) ? 'Line - ' . $line : '' );
                                    div( 'f', $doc ?? '' );
                                    div( 'd', $date ?? '' );
                                d_();
                            d_();
                            $x++;
                        }
                    }
                }
            } else {
                unlink( APPPATH . 'events.log' );
                div( 'b a', __div( 'a' ) );
            }
        d_();
    article_();
    include_once(ROOTPATH . 'core/components/foot.php' );
    fclose( $file );
}