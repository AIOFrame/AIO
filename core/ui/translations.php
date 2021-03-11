<?php

$db = new DB();
$form = new FORM();

$base = defined( 'BASELANG' ) ? BASELANG : 'en';

if( isset( $_POST['languages'] ) && is_array( $_POST['languages'] ) ) {
    $save_langs = $db->update_option( 'languages', serialize( $_POST['languages'] ) );
}

$all_languages = get_languages();
$all_languages = is_array( $all_languages ) ? $all_languages : [];

$app_langs = $db->get_option( 'languages' );
$app_langs = !empty( $app_langs ) ? unserialize( $app_langs ) : [];

$app_languages = [];
foreach( $app_langs as $al ) {
    $app_languages[ $al ] = $al !== BASELANG && isset( $all_languages[ $al ] ) ? $all_languages[ $al ] : $al;
}

$lang = isset( $_POST['lang_select'] ) ? $_POST['lang_select'] : '';
$page = isset( $_POST['page'] ) ? $_POST['page'] : '';

$ts = $db->select( 'translations' );
$ts = !empty( $ts ) && is_array( $ts ) ? $ts : [];

$pages = [];
foreach( $ts as $t ) {
    !empty( $t['t_page'] ) ? $pages[] = $t['t_page'] : '';
}
$pages = array_unique( $pages );
reset_styles('Lato','300',5);
get_styles( ['select2','bootstrap-grid','translations','micro'] );
font(['Lato','300,500']);
?>
<div class="row">
    <div class="col-12 col-lg-4">
        <form method="post">
            <label for="lang_select"><?php E('Select Language'); ?></label>
            <select name="lang_select" id="lang_select" onchange="this.form.submit()">
                <?php $form->select_options( array_merge( ['add'=>'Add Language'], $app_languages ), $lang, 'Select Language' ); ?>
            </select>
        </form>
    </div>
    <div class="col-12 col-lg-4 search">
        <label for="lang_search"><?php E('Search strings...'); ?></label>
        <input type="text" id="lang_search" name="lang_search" placeholder="Search...">
    </div>
    <div class="col-12 col-lg-4">
        <form method="post">
            <label for="lang_page"><?php E('Select page to translate'); ?></label>
            <select name="lang_page" id="lang_page" onchange="this.form.submit()">
                <?php $form->select_options( array_merge( ['All'] , $pages ), $page, 'Select Page' ); ?>
            </select>
        </form>
    </div>
</div>
<div id="trans" data-save-scroll>
    <?php

    //global $ui_params;
    //$path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . 'storage/backups/*';

    if( !empty( $ts ) && is_array( $ts ) && !empty( $lang ) && $lang !== 'add' ) {

        $cry = Crypto::initiate();
        echo '<div id="aio_translations" class="translations">';

        foreach( $ts as $ts ){

            echo '<div>';
            if( !empty( $page ) && !in_array( $page, ['All','Global'] ) && $ts['t_page'] !== $page ) { continue; }
            //$p = isset( $ts['t_page'] ) && !empty( $ts['t_page'] ) ?  '<span>'.$ts['t_page'].'</span>' : '';
            echo isset( $ts['t_base'] ) ? '<div>'.$ts['t_base'].'</div>' : '<div></div>';
            echo isset( $ts['t_'.$lang] ) ? '<div>'.$ts['t_'.$lang].'</div>' : '<div></div>';
            echo isset( $ts['t_page'] ) ? '<div>'.APPURL.$ts['t_page'].'</div>' : '<div></div>';

            echo '<i data-id="'.$cry->encrypt($ts['t_id']).'" class="ico trash"></i>';
            echo '</div>';

        }

        echo '</div>';

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
    } else if( $lang == 'add' ) {
        unset( $all_languages['en'] ); ?>
        <form method="post">
            <div class="mb20">
                <?php
                $form->select( 'languages[]', 'Select Languages', 'Select Languages', $all_languages, $app_langs, 'multiple class="select2"' );
                ?>
            </div>
            <div class="tar"><button onchange="this.form.submit()">Set Languages</button></div>
        </form>
    <?php } else { ?>
<!--            <form method="post">-->
<!--                <h3>Please select a language to start managing translations</h3>-->
<!--                <select name="lang" id="lang" onchange="this.form.submit()">-->
<!--                    --><?php //$form->select_options( array_merge( ['add'=>'Add Language'], $app_languages ), $lang, 'Select Language' ); ?>
<!--                </select>-->
<!--            </form>-->
    <?php }
    if( !empty( $lang ) && $lang !== 'add' ) { ?>
    <div id="aio_lang_editor">
        <div class="close" data-on="#editor"></div>
        <div class="row">
            <div class="col-12 col-lg-5">
                <label for="string"><?php E('Sentence'); ?></label>
                <button data-clipboard-target="#string" class="small">COPY</button>
                <textarea id="string" rows="2" tabindex="1"></textarea>
            </div>
            <div class="col-12 col-lg-6">
                <label for="translation"><?php echo !empty( $lang ) && isset( $all_languages[$lang] ) ? $all_languages[$lang].' ' : '';E('Translation'); ?></label>
                <button data-clipboard-target="#translation" class="small">COPY</button>
                <textarea id="translation" rows="2" tabindex="2"></textarea>
            </div>
            <div class="col-12 col-lg-1">
                <button id="save" onclick="update_translation()"><?php E('Save'); ?></button>
            </div>
        </div>
    </div>
    <?php } ?>
</div>
<?php get_scripts(['jquery','clipboard','select2','aio','translations']); ?>