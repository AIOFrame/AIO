<?php get_style( 'backup' ); ?>
<div id="backup">
    <?php foreach( glob( APPPATH . '/storage/backups/*.zip' ) as $bk ) {
        echo '<div class="bk">'. $bk .'</div>';
    } ?>
</div>
<?php get_script( 'backup' ); ?>