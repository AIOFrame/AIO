<?php
$f = new FORM();
$c = CONFIG;
$c = !empty( $c ) ? json_decode( $c, 1 ) : [];
$name = $c['name'] ?? '';
$key = $c['key'] ?? '';
$debug = $c['debug'] ?? '';
$zone = $c['timezone'] ?? '';
$date = $c['date_format'] ?? '';
$ssl = $c['ssl'] ?? '';
$cache = $c['cache'] ?? 0;
?>
<h3><?php E('General Settings'); ?></h3>
<div class="row">
    <?php
    $f->text('name','Web App Name','Ex: Food Delivery, Events, CRM, '.ucfirst( APPNAME ).', App etc.',$name,'data-c',6);
    $f->input('text','key','Encryption Key','',$key,'data-c',6);
    $f->select('timezone','Time Zone','Select default timezone',timezone_identifiers_list(),$zone,'data-c class="select2"',3);
    $f->text('date_format','Date Format','d-M-Y',$date,'data-c',3);
    $f->input('number','cache','Cache (Minutes)','(Minutes) Ex: 5',$cache,'data-c min="0"',2);
    $f->slide('ssl','Force SSL','','',$ssl,'m','data-c',2);
    $f->slide('debug','Debug Mode','','',$debug,'m','data-c',2);
    ?>
</div>
<div class="break"></div>