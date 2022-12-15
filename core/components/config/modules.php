<?php
$f = new FORM();
$c = CONFIG;
$c = !empty( $c ) ? json_decode( $c, 1 ) : [];
$modules = $c['modules'] ?? '';
?>
<h3><?php E('Modules (External Dependencies)'); ?></h3>
<div class="row">
    <?php
    $modules_list = [
        'email' => 'Email System',
        'maps' => 'Google Maps System',
        'stripe' => 'Stripe Payment Gateway',
    ];
    $f->checkboxes('modules','Chosen Modules will enable loading of dependent classed functions and enables individual options',$modules_list,$modules,'data-c',0,12,'','',6);
    ?>
</div>
<div class="break"></div>