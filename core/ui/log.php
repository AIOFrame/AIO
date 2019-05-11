<?php

global $ui_params;

$file = isset( $ui_params['file'] ) && $ui_params['file'] !== '' ? $ui_params['file'] : ini_get('error_log');

    if( $file = fopen( $file, 'r' ) ) { ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo 'E Log - '.APPNAME; ?></title>
    <?php get_styles(['reset','aio_ui','elog','micro']);
    get_script('jquery');
    font(['Lato','300,500']); ?>
</head><body>
    <header>
        <div class="one">
            <a href="<?php APPURL; ?>" id="brand">TO ERR IS HUMAN</a>
        </div>
        <div class="two tar">
            <div class="search">
                <input type="text" placeholder="Search...">
                <button type="stack" title="Search Stack Overflow">SO</button>
            </div>
            <button class="refresh">RELOAD</button>
            <button class="clear">CLEAR</button>
        </div>
    </header>
    <div class="log" data-save-scroll>
<?php

        while ( $l = fgets( $file )) {

            // Extract Date
            $date = get_string_between($l, '[', ']');
            $edate = easy_date( $date, 'h:i:s a d M, Y' );

            // Extract Type
            $type = get_string_between($l, '|', '|');
            $type = !empty( $type ) ? $type : 'LOG';

            if( $type == 'UPDATE' ) {
                $table = get_string_between( $l, 'UPDATE ', ' ' );
            } else if ( $type == 'SELECT' ) {
                $table = get_string_between( $l, 'FROM ', ' ' );
            }

            // Extract Line
            $line = get_string_between($l, '<<', '>>');

            // Extract Document
            $doc = get_string_between($l, '{', '}');

            $log = str_replace( '[' . $date . '] ', '', str_replace( '|' . $type . '| ', '', str_replace( '<<' . $line . '>> ', '', str_replace( '{' . $doc . '}', '', $l ) ) ) );

            if( !empty( $date ) && !empty( $log ) ) { ?>
                <div class="b <?php echo strtolower( $type ); ?>">
                    <div class="a">
                        <div class="s"><?php echo $type; ?></div>
                        <?php echo isset( $table ) ? '<div class="t">' . strtoupper( $table ) . '</div>' : ''; ?>
                    </div>
                    <div class="dt"><?php echo $edate; ?></div>
                    <div class="c"><?php echo $log; ?></div>
                    <div class="f">
                        <div class="l"><?php echo !empty( $line ) ? 'Line - '.$line : ''; ?></div>
                        <div class="d"><?php echo $doc; ?></div>
                    </div>
                </div>
            <?php }
        }

        get_scripts(['core','elog']);

        echo '</div><div id="notification"><h4 class="title"></h4><div class="close"></div><div class="message"></div></div></body></html>';

        fclose( $file );

    }