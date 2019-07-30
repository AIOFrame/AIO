<?php

global $ui_params;

$file = isset( $ui_params['file'] ) && $ui_params['file'] !== '' ? $ui_params['file'] : ini_get('error_log');

    if( $file = fopen( $file, 'r' ) ) { ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport"
                  content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title><?php echo 'E Log - ' . APPNAME; ?></title>
            <link rel="shortcut icon" href="<?php echo APPURL . 'assets/images/aio.png'; ?>">
            <?php get_styles(['reset', 'aio_ui', 'aio_log', 'micro']);
            get_script('jquery');
            font(['Lato', '300,500']); ?>
        </head>
        <body <?php body_class('debug'); ?>>
        <header>
            <div class="one">
                <div id="back" onclick="window.history.back();"></div>
                <a href="" id="brand">AIO <?php echo strtoupper(APPNAME); ?> ERROR LOG</a>
            </div>
            <div class="two tar">
                <select name="type" id="type">
                    <?php select_options(['a' => 'All', 'l' => 'Log', 'e' => 'Error', 'i' => 'Insert', 's' => 'Select', 'u' => 'Update', 'd' => 'Delete']); ?>
                </select>
                <div class="search">
                    <input type="text" placeholder="Search...">
                    <button type="button" title="Search Stack Overflow">SO</button>
                </div>
                <button class="refresh">RELOAD</button>
                <button class="clear">CLEAR</button>
            </div>
        </header>
        <article id="aio_log" data-save-scroll>
        <div class="error_log">
        <?php
        $x = 0;
        $logger = '';
        while ($l = fgets($file)) {

            // Extract Date
            $date = get_string_between($l, '[', ']');
            $l = str_replace('[' . $date . '] ', '', $l);

            // Extract Log
            $lb = explode('<AIO>', $l);
            //if (is_array($lb) && count($lb) > 2) {
                $log = is_json( $lb[0] ) ? json_decode( $lb[0] ) : $lb[0];
                $type = isset($lb[1]) ? $lb[1] : 'LOG';
                $line = isset($lb[2]) ? $lb[2] : '';
                $doc = isset($lb[3]) ? $lb[3] : '';
                $target = isset($lb[4]) ? $lb[4] : '';
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
                        <div class="b">
                            <div class="co" data-clipboard-target="#l<?php echo $x; ?>">COPY LOG</div>
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
        ?>
        </div>
        <?php get_scripts(['core','clipboard','aio_ui','aio_log']); ?>
    </article>
    <div id="notification">
        <h4 class="title"></h4>
        <div class="close"></div>
        <div class="message"></div>
    </div>
</body>
</html>
<?php fclose( $file ); }