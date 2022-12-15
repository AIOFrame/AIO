<?php
$f = new FORM();
$c = CONFIG;
$c = !empty( $c ) ? json_decode( $c, 1 ) : [];
$dbs = $c['data']['type'] ?? '';
$host = $c['data']['host'] ?? '';
$base = $c['data']['base'] ?? '';
$user = $c['data']['user'] ?? '';
$pass = $c['data']['pass'] ?? '';
$databases = [
    'mysql' => 'MySQL',
    'mssql' => 'MS SQL Server',
    'postgre' => 'Postgre SQL',
    'oracle' => 'Oracle',
    'sqlite' => 'SQLite'
];
?>
<h3><?php E('Database'); ?></h3>
<div class="row">
    <?php
    $f->select2('type','Database Type','Select database type',$databases,$dbs,'data-c',12);
    $f->texts([
        ['host','Database Server','Ex: localhost',$host],
        ['base','Database Name','Ex: myapp_db',$base],
        ['user','Database Username','Ex: app_user',$user],
        ['pass','Password','Ex: *********',$pass]
    ],'data-c data-key="data" data-array',3);
    ?>
</div>
<div class="break"></div>