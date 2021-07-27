<?php
$c = get_config();
!isset( $c['name'] ) ? $c['name'] = '' : '';
!isset( $c['debug'] ) ? $c['debug'] = '' : '';
!isset( $c['timezone'] ) ? $c['timezone'] = '' : '';
!isset( $c['ssl'] ) ? $c['ssl'] = '' : '';
!isset( $c['cache'] ) ? $c['cache'] = '' : '';
!isset( $c['data']['type'] ) ? $c['data']['type'] = '' : '';
!isset( $c['data']['host'] ) ? $c['data']['host'] = '' : '';
!isset( $c['data']['base'] ) ? $c['data']['base'] = '' : '';
!isset( $c['data']['user'] ) ? $c['data']['user'] = '' : '';
!isset( $c['data']['pass'] ) ? $c['data']['pass'] = '' : '';
!isset( $c['functions']['logged_in'] ) ? $c['functions']['logged_in'] = '' : '';
!isset( $c['functions']['logged_out'] ) ? $c['functions']['logged_out'] = '' : '';
?>
<div class="page_head">
    <div class="title"><?php E('App Configuration'); ?></div>
</div>
<div class="config_form mb60">
    <h3><?php E('General Settings'); ?></h3>
    <div class="row">
        <?php
        text('name','Web App Name','Ex: Food Delivery, Events, CRM, '.ucfirst( APPNAME ).', App etc.',$c['name'],'data-c',6);
        input('text','key','Encryption Key','',$c['key'],'data-c',6);
        select('timezone','Time Zone','Select default timezone',timezone_identifiers_list(),$c['timezone'],'data-c class="select2"',6);
        text('cache','Cache (Minutes)','(Minutes) Ex: 5',$c['cache'],'data-c',2);
        input('checkbox','ssl','Force SSL','',$c['ssl'],'class="slide" data-c',2);
        input('checkbox','debug','Debug Mode','',$c['debug'],'class="slide" data-c',2);
        ?>
    </div>
    <div class="break"></div>
    <h3><?php E('Features'); ?></h3>
    <div class="row">
        <?php
        checkboxes('features',['users'=>'User Management','languages'=>'Translations','upload'=>'File Uploads','data'=>'World Cities & Countries'],$c['features'],'data-c',0,3);
        ?>
    </div>
    <div class="break"></div>
    <h3><?php E('Database'); ?></h3>
    <div class="row">
        <?php
        select('type','Database Type','Select database type',['mysql'=>'MySQL','mssql'=>'MS SQL Server','postgre'=>'Postgre SQL','oracle'=>'Oracle','sqlite'=>'SQLite'],$c['data']['type'],'data-c class="select2"',12);
        texts([['host','Database Server','Ex: localhost',$c['data']['host']],['base','Database Name','Ex: myapp_db',$c['data']['base']],['user','Database Username','Ex: app_user',$c['data']['user']],['pass','Password','Ex: *********',$c['data']['pass']]],'data-c data-key="data" data-array',3)
        ?>
    </div>
    <div class="break"></div>
    <h3><?php E('Functions Access'); ?></h3>
    <div class="row">
        <?php
        $fun = [];
        foreach( glob( APPPATH . '/functions/*.php' ) as $f ){
            $f = explode('/',$f);
            $fun[] = str_replace('.php','',$f[count($f) - 1]);
        }
        select('logged_in','For Logged-in users','Select function files for logged in users',$fun,$c['functions']['logged_in'],'data-c multiple class="select2"',12);
        select('logged_out','For Logged-out users','Select function files for logged out users',$fun,$c['functions']['logged_out'],'data-c multiple class="select2"',12);
        ?>
    </div>
    <div class="break"></div>
    <h3><?php E('Optional API'); ?></h3>
    <div class="row">
        <?php
        $gm_link = '<a class="link" href="https://console.cloud.google.com/project/_/apiui/credential" target="_blank">'.T('Link').'</a>';
        $sg_link = '<a class="link" href="https://app.sendgrid.com/settings/api_keys" target="_blank">'.T('Link').'</a>';
        $ms_link = '<a class="link" href="https://app.mailersend.com/domains" target="_blank">'.T('Link').'</a>';
        $vo_link = '<a class="link" href="https://dashboard.nexmo.com/settings" target="_blank">'.T('Link').'</a>';
        texts([['google_maps','Google Maps Key '.$gm_link],['sendgrid','SendGrid Key '.$sg_link],['mailersend','MailerSend Key '.$ms_link],['vonage','Vonage Key '.$vo_link]],'data-array data-key="keys" data-c',3)
        ?>
    </div>
    <button class="main">Build App Config</button>
</div>
<?php
skel($c);