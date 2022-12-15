<?php
$f = new FORM();
$c = CONFIG;
$c = !empty( $c ) ? json_decode( $c, 1 ) : [];
$features = $c['features'] ?? '';
?>
<h3><?php E('Features'); ?></h3>
<div class="row">
    <?php
    $features_list = [
        'users' => 'User Management',
        'options' => 'Configurable Options DB',
        'alerts' => 'Notifications Features',
        'mobile' => 'Mobile REST Features',
        'lang' => 'Languages & Translation System',
        'portal' => 'Admin Portal Features',
        'upload' => 'File Uploader & Media Management',
        'data' => 'World Data (Countries, Cities, Currencies)',
        'backup' => 'Database Backup / Restore Feature',
        'region' => 'Regional Allowance / Restrictions',
        'cms' => 'Content Management System',
        'commerce' => 'E-Commerce System',
        'chat' => 'Chat System and Features',
    ];
    $f->checkboxes('features','Chosen features loads classed functions that enables respective features',$features_list,$features,'data-c',0,12,'','',6);
    ?>
</div>
<div class="break"></div>