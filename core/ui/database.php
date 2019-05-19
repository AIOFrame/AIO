<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo 'Database - '.APPNAME; ?></title>
    <link rel="shortcut icon" href="<?php echo APPURL . 'assets/images/aio.png'; ?>">
    <?php get_styles( ['reset','aio_ui','backup','micro'] ); font(['Lato','300,500']); ?>
</head>
<body <?php body_class(); ?>>

<header>
    <div class="one">
        <div id="back" onclick="window.history.back();"></div>
        <a href="" id="brand"><?php E('DATABASE BACKUP'); ?></a>
    </div>
    <div class="two tar">
        <button class="backup"><?php E('BACKUP NOW'); ?></button>
    </div>
</header>
<article>
    <div id="backup">
        <?php

        global $ui_params;
        $path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . 'storage/backups/*';

        foreach( glob( $path ) as $bk ) {

            $res = T('RESTORE');
            $del = T('DELETE');

            $fa = explode( '/', $bk );
            $fn = $fa[count($fa)-1];

            $na = explode( '-', $fn );
            $t = $na[count($na)-1];

            $t = str_replace( '.sql', '', str_replace( '.sql.gz', '', $t ) );
            $ed = DateTime::createFromFormat( 'h_i_a_d_M_Y', $t )->format('h:i a d M, Y'); ?>

            <div class="b">
                <div class="b">
                    <button class="res"><?php echo $res; ?></button>
                    <button class="del"><?php echo $del; ?></button>
                </div>
                <div class="l"><?php echo $fn; ?></div>
                <div class="ft">
                    <div><?php echo 'Location - ' . $bk; ?></div>
                    <div class="dt"><?php echo 'Date - ' . $ed; ?></div>
                </div>
            </div>

        <?php } ?>

    </div>
</article>
<?php get_script( 'backup' ); ?>

</body>
</html>