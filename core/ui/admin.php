<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <?php
    get_title( 'AIO Admin', 0 );
    reset_styles('Lato,Helvetica Neue',300,5);
    favicon( APPURL.'assets/images/aio.png' );
    fonts([['Lato','300,500']]);
    get_styles( ['select2','bootstrap-grid'] );
    art('inputs','00A99D','047267');
    get_styles( ['aio','micro'] );
    get_script('jquery');
    $uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
    $url =  "//{$_SERVER['HTTP_HOST']}{$uri_parts[0]}";
    $url = htmlspecialchars( $url, ENT_QUOTES, 'UTF-8' );
    $page = isset( $_GET['page'] ) ? $_GET['page'] : 'overview';
    ?>
</head>
<body <?php body_class( $page ); ?>>
<header>
    <div class="logo"></div>
    <div class="options">
        <div class="ico nav" data-t=".menu"><i class="tip"><?php E('Toggle Menu'); ?></i></div>
        <div class="ico lang" data-t=".languages"><i class="tip"><?php E('Change Language'); ?></i></div>
        <div class="ico dark" data-dark><i class="tip"><?php E('Toggle Dark Mode'); ?></i></div>
    </div>
<!--    <div class="actions">-->
<!--        <a href="" id="brand">--><?php //E('DATABASE BACKUP'); ?><!--</a>-->
<!--    </div>-->
<!--    <div class="two tar">-->
<!--        <button class="backup">--><?php //E('BACKUP NOW'); ?><!--</button>-->
<!--    </div>-->
</header>
<aside>
    <div class="menu scroll">
        <div class="list">
            <?php
            $menu = [
                '' => 'Overview',
                'config' => 'App Configuration',
                'translations' => 'Translations',
                'backup' => 'Backup',
                'plugins' => 'Plugins',
                'database' => 'Database',
                'aio' => 'About'
            ];
            foreach( $menu as $mk => $mv ) {
                $on = ( $page == $mk ) || ( !isset( $_GET['page'] ) && $mk == '' ) ? 'class="on"' : '';
                $link = !empty( $mk ) ? '?page='.$mk : '';
                echo '<a href="'.$url.$link.'" '.$on.'>'.T($mv).'</a>';
            }
            ?>
        </div>
    </div>
    <div class="languages scroll">
        <input type="search" class="filter_lang" placeholder="<?php E('Search'); ?>">
        <div class="list">
            <?php
            $langs = ['en'=>'English','ar'=>'العربية (Arabic)','zh'=>'中文 (Chinese Simplified)','fr'=>'Français (French)','hi'=>'हिंदी (Hindi)','in'=>'Bahasa Indonesian','ja'=>'日本語 (Japanese)','pr'=>'Português (Portuguese)','ru'=>'русский (Russian)','es'=>'Español (Spanish)'];
            if( is_array( $langs ) && !empty( $langs ) )
                foreach( $langs as $k => $v )
                    echo '<div data-lang="' . $k . '">' . $v . '</div>';
            ?>
        </div>
    </div>
</aside>
<article>
    <?php !empty( $page ) ? UI( $page ) : ''; ?>
</article>
<?php
get_scripts(['select2','smooth-scrollbar','aio','admin']);
?>

</body>
</html>