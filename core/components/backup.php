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