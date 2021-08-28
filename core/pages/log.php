<?php
include_once(ROOTPATH . 'core/components/head.php' );
global $params;
$url = isset( $params['file'] ) && !empty( $params['file'] ) ? $params['file'] : ini_get('error_log');

if( $file = fopen( $url, 'r' ) ) {
    $f = new FORM();
    $cry = Crypto::initiate();
    ?>
    <header>
        <div class="logo"></div>
        <div class="title">LOG</div>
        <div class="options">
            <div id="back" class="ico back" onclick="window.history.back();"><i class="tip">Return to Previous Page</i></div>
            <div class="ico reload refresh" onclick="location.reload()"><i class="tip">Reload</i></div>
            <div onclick="process_data(this)" data-notify="2" data-reload="2" data-action="<?php $cry->enc('clear_log_ajax'); ?>" class="ico delete clear"><i class="tip">Clear all Log</i></div>
            <div class="ico dark" data-dark=""><i class="tip">Toggle Dark Mode</i></div>
        </div>
    </header>
    <article id="aio_log">
        <div class="df controls">
            <div class="filters">
                <label>Filter</label>
                <div class="df">
                    <i data-type="a" class="on">All</i>
                    <?php
                    $types = [ 'l' => 'Log', 'e' => 'Error', 'i' => 'Insert', 's' => 'Select', 'u' => 'Update', 'd' => 'Delete'];
                    foreach( $types as $tk => $tv ) {
                        echo '<i data-type="'.$tk.'">'.$tv.'</i>';
                    }
                    ?>
                </div>
            </div>
            <div class="search_wrap">
                <label for="search_log">Search</label>
                <input id="search_log" type="search" placeholder="Search">
            </div>
        </div>
        <div class="events_log">
            <?php
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
                            ?>
                            <div class="b <?php echo isset( $type[0] ) ? strtolower( $type )[0] : ''; ?>">
                                <div class="a">
                                    <div class="s"><?php echo isset( $type ) ? $type : ''; ?></div>
                                    <?php echo isset($target) && $target !== '' ? '<div class="t">' . $target . '</div>' : ''; ?>
                                </div>
                                <div class="c">
                                    <div class="co" data-clipboard-target="#l<?php echo $x; ?>">COPY</div>
                                    <div class="go">GOOGLE</div>
                                    <div class="so">STACK OF</div>
                                </div>
                                <div id="l<?php echo $x; ?>" class="l"><pre><?php echo is_array( $log ) || is_object( $log ) ? print_r( $log ) : $log; ?></pre></div>
                                <div class="ft">
                                    <div class="l"><?php echo !empty($line) ? 'Line - ' . $line : ''; ?></div>
                                    <div class="f"><?php echo isset( $doc ) ? $doc : ''; ?></div>
                                    <div class="d"><?php echo $date; ?></div>
                                </div>
                            </div>
                            <?php $x++;
                        }
                    }
                }
            } else {
                unlink( APPPATH . 'events.log' );
                ?>
                <div class="b a">
                    <div class="a"></div>
                </div>
                <?php
            }
            ?>
        </div>
    </article>
<?php
include_once(ROOTPATH . 'core/components/foot.php' );
fclose( $file ); }