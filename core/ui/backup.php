<?php get_style( 'backup' ); ?>

<div id="backup">
    <div class="bk_head rel">
        <h1><?php E('DATABASE BACKUP'); ?></h1>
        <button class="backup abs r t"><?php E('Backup Now'); ?></button>
    </div>
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

        echo '<div class="b"><h4>'. $fn .'</h4><div class="dt">'.$ed.'</div><button class="res">'.$res.'</button><button class="del">'.$del.'</button></div>';

    } ?>

</div>

<?php get_script( 'backup' ); ?>