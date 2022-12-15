<?php
$f = new FORM();
$c = CONFIG;
$c = !empty( $c ) ? json_decode( $c, 1 ) : [];
$name = $c['name'] ?? '';
$key = $c['key'] ?? '';
$debug = $c['debug'] ?? '';
$zone = $c['timezone'] ?? '';
$ssl = $c['ssl'] ?? '';
$cache = $c['cache'] ?? 0;
?>
<h3><?php E('APIs'); ?></h3>
<div class="row">
    <?php
    $gm_link = '<a class="link" href="https://console.cloud.google.com/project/_/apiui/credential" target="_blank">'.T('Link').'</a>';
    $sg_link = '<a class="link" href="https://app.sendgrid.com/settings/api_keys" target="_blank">'.T('Link').'</a>';
    $ms_link = '<a class="link" href="https://app.mailersend.com/domains" target="_blank">'.T('Link').'</a>';
    $vo_link = '<a class="link" href="https://dashboard.nexmo.com/settings" target="_blank">'.T('Link').'</a>';
    $f->texts([['google_maps','Google Maps Key '.$gm_link],['sendgrid','SendGrid Key '.$sg_link],['mailersend','MailerSend Key '.$ms_link],['vonage','Vonage Key '.$vo_link]],'data-array data-key="keys" data-c',3)
    ?>
</div>