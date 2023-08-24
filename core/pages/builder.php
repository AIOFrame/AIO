<?php
get_comp('aio_builder/head');
$p = $_POST;
$cry = Encrypt::initiate();
$form = new FORM();
!defined( 'APP_NAME' ) ? define( 'APP_NAME', 'AIO App Builder' ) : '';
?>
    <header>
        <div class="logo"></div>
        <div class="options_toggle"></div>
        <div class="options">
            <div class="ico" data-on=".languages"><div class="mat-ico">language</div><i class="tip">Change Language</i></div>
            <div class="ico dark" data-dark><div class="mat-ico">dark_mode</div><i class="tip">Toggle Dark Mode</i></div>
        </div>
    </header>
    <aside>
        <?php get_comp('aio/aside/languages'); ?>
    </aside>
    <article data-off=".scroll">
        <?php
        global $steps;
        if( !empty( $steps ) ) {
            echo '<div class="setup_wrap" data-tabs>';
            foreach( $steps as $k => $d ) {
                builder_step( $k, $d );
            }
            echo '</div>';
        }
        ?>
    </article>
<?php
get_comp('aio_builder/foot');