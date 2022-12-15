<?php
$c = CONFIG;
$c = !empty( $c ) ? json_decode( $c, 1 ) : [];
$in = $c['functions']['logged_in'] ?? '';
$out = $c['functions']['logged_out'] ?? '';
$functions = [];
foreach( glob( APPPATH . '/functions/*.php' ) as $f ){
    $f = explode('/',$f);
    $functions[] = str_replace('.php','',$f[count($f) - 1]);
}
$f = new FORM();
?>
<h3><?php E('Functions Access'); ?></h3>
<div class="row">
    <?php
    $f->select2('logged_in','User Logged-in functions files','Select files...',[],'','data-c multiple',12);
    $f->select2('logged_out','User Logged-out functions files','Select files...',[],'','data-c multiple',12);
    ?>
</div>
<div class="break"></div>