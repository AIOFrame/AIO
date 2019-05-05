<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <?php get_styles( ['reset','aio_ui','backup','micro'] ); ?>
</head>
<body>

<header>
    <div class="one">
        <a href="" id="brand"><?php E('DATABASE BACKUP'); ?></a>
    </div>
    <div class="two tar">
        <button class="backup"><?php E('Backup Now'); ?></button>
    </div>
</header>

<div id="backup">
    <?php

    global $ui_params;
    $path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . '/storage/backups/*';

    foreach( glob( $path ) as $bk ) {

        $res = T('Restore');
        $del = T('Delete');

        $fa = explode( '/', $bk );
        $fn = $fa[count($fa)-1];

        $na = explode( '-', $fn );
        $t = $na[count($na)-1];

        $t = str_replace( '.sql', '', str_replace( '.sql.gz', '', $t ) );
        $ed = DateTime::createFromFormat( 'h_i_a_d_M_Y', $t )->format('h:i a d M, Y');

        echo '<div class="b"><h4>'. $fn .'</h4><div class="dt">'.$ed.'</div><button class="del">'.$del.'</button></div>';

    } ?>

</div>

<?php get_script( 'backup' ); ?>

</body>
</html>