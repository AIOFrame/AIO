<?php

$base = defined( 'BASELANG' ) ? BASELANG : 'en';

if( isset( $_POST['languages'] ) && is_array( $_POST['languages'] ) ) {
    $save_langs = update_option( 'app_languages', serialize( $_POST['languages'] ) );
}

$langs = get_languages();
$langs = is_array( $langs ) ? $langs : [];

$app_langs = get_option( 'app_languages' );
$app_langs = !empty( $app_langs ) ? unserialize( $app_langs ) : '';

$app_languages = [];
foreach( $app_langs as $al ) {
    $app_languages[ $al ] = $al !== BASELANG && isset( $langs[ $al ] ) ? $langs[ $al ] : $al;
}

$lang = isset( $_POST['lang'] ) ? $_POST['lang'] : '';
$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

$ts = select( 'translations' );
$ts = !empty( $ts ) && is_array( $ts ) ? $ts : [];

$pages = [];
foreach( $ts as $t ) {
    !empty( $t['t_page'] ) ? $pages[] = $t['t_page'] : '';
}
$pages = array_unique( $pages );
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo 'Translations - '.APPNAME; ?></title>
    <link rel="shortcut icon" href="<?php echo APPURL . 'assets/images/aio.png'; ?>">
    <?php get_styles( ['reset','select2','aio_ui','translations','micro'] ); font(['Lato','300,500']); ?>
</head>
<body <?php body_class(); ?>>

<header>
    <div class="one">
        <div id="back" onclick="window.history.back();"></div>
        <a href="" id="brand"><?php echo T('TRANSLATIONS'); ?></a>
    </div>
    <div class="two tar">
        <?php if( !empty( $lang ) && $lang !== 'add' ) { ?>
        <div class="search">
            <input type="text" placeholder="Search...">
        </div>
        <form method="post">
            <select name="lang" id="lang" onchange="this.form.submit()">
                <?php select_options( array_merge( ['add'=>'Add Language'], $app_languages ), $lang, 'Select Language' ); ?>
            </select>
            <select name="page" id="page" onchange="this.form.submit()">
                <?php select_options( array_merge( ['All'] , $pages ), $page, 'Select Page' ); ?>
            </select>
        </form>
        <?php } ?>
    </div>
</header>
<div id="progress"><span style="width:<?php echo count( $ts ); ?>"></span></div>
<article id="trans" data-save-scroll>
    <div id="backup">
        <?php

        //global $ui_params;
        //$path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . 'storage/backups/*';

        if( !empty( $ts ) && is_array( $ts ) && !empty( $lang ) && $lang !== 'add' ) {

            echo '<table id="translations">';

            foreach( $ts as $ts ){

                if( !empty( $page ) && !in_array( $page, ['All','Global'] ) && $ts['t_page'] !== $page ) { continue; }
                //$p = isset( $ts['t_page'] ) && !empty( $ts['t_page'] ) ?  '<span>'.$ts['t_page'].'</span>' : '';
                echo isset( $ts['t_base'] ) ? '<td>'.$ts['t_base'].'</td>' : '<td></td>';
                echo isset( $ts['t_'.$lang] ) ? '<td>'.$ts['t_'.$lang].'</td>' : '<td></td>';
                echo isset( $ts['t_page'] ) ? '<td>'.$ts['t_page'].'</td>' : '<td></td>';

                //echo '<td><i class="ico trash"></i></td>';
                echo '</tr>';

            }

            /*foreach ( $ts as $t ) { ?>

                <div class="b">
                    <div class="b">
                        <button class="res"><?php echo $res; ?></button>
                        <button class="del"><?php echo $del; ?></button>
                    </div>
                    <div class="l"><?php echo $fn; ?></div>
                    <div class="ft">
                        <div><?php echo 'Location - ' . $bk; ?></div>
                        <div class="dt"><?php echo 'Date - ' . $ed; ?></div>
                    </div>
                </div>

            <?php }*/
        } else if( $lang == 'add' ) { ?>
            <form method="post" style="width:50%;margin:0 auto">
                <h2 class="mb20">Please select your translatable languages</h2>
                <div class="mb20">
                    <select name="languages[]" id="languages[]" multiple class="select2">
                        <?php select_options( $langs, $app_langs, 'Select Languages' ); ?>
                    </select>
                </div>
                <div class="tar"><button onchange="this.form.submit()">Set Languages</button></div>
            </form>
        <?php } else { ?>
            <form method="post" style="width:50%;margin:0 auto">
                <h3>Please select a language to start managing translations</h3>
                <select name="lang" id="lang" onchange="this.form.submit()">
                    <?php select_options( array_merge( ['add'=>'Add Language'], $app_languages ), $lang, 'Select Language' ); ?>
                </select>
            </form>
        <?php } ?>

    </div>
    <?php if( !empty( $lang ) ) { ?>
    <div id="editor">
        <div class="close" data-on="#editor"></div>
        <div class="row">
            <div class="col">
                <label for="string"><?php E('Sentence'); ?></label>
                <button data-clipboard-target="#string" class="small">COPY</button>
                <textarea id="string" rows="2" tabindex="1"></textarea>
            </div>
            <div class="col ml20">
                <label for="translation"><?php echo !empty( $lang ) && isset( $langs[$lang] ) ? $langs[$lang].' ' : '';E('Translation'); ?></label>
                <button data-clipboard-target="#translation" class="small">COPY</button>
                <textarea id="translation" rows="2" tabindex="2"></textarea>
            </div>
            <div class="col">
                <button id="save" onclick="update_translation()"><?php E('Save'); ?></button>
            </div>
        </div>
    </div>
    <?php } ?>
</article>
<div id="notification">
    <h4 class="title"></h4>
    <div class="close"></div>
    <div class="message"></div>
</div>
<?php get_scripts(['jquery','clipboard','select2','core','translations']); ?>
</body>
</html>