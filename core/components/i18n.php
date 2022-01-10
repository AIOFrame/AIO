<?php

$db = new DB();
$f = new FORM();
$w = new WORLD();

$os = $db->get_options(['base_language','languages']);
$i18ns = $os['languages'] ?? '';
$base = $os['base_language'] ?? 'English';

$app_languages = !empty( $i18ns ) ? explode( ',', str_replace( ' ', '', $i18ns ) ) : [];
/* if( !empty( $i18ns ) ) {
    $array_langs = $i18ns !== '' ? unserialize( $app_langs ) : [];
    foreach( $array_langs as $al ) {
        $app_languages[ $al ] = $al !== BASELANG && isset( $all_languages[ $al ] ) ? $all_languages[ $al ] : $al;
    }
} */

$lang = $_POST['lang_select'] ?? '';
$page = $_POST['page'] ?? '';

$strings = $db->select( 'translations' );

$pages = [];
foreach( $strings as $t ) {
    !empty( $t['t_page'] ) ? $pages[] = $t['t_page'] : '';
}
$pages = array_unique( $pages );
reset_styles('Lato','300',5);
get_styles( ['bootstrap-grid','tagcomplete','i18n','micro'] );
font(['Lato','300,500']);
?>
<form class="row" method="post">
    <?php
    $f->select( 'lang_select', 'Select Language', 'Select Language', array_merge( ['' => '', 'add'=>'Add Language'], $app_languages ), $lang, 'onchange="this.form.submit()" class="select2"', 4, '', 1, 0 );
    $f->text( 'lang_search', 'Search Strings...', 'Search...', '', '', 4 );
    $f->select( 'lang_page', 'Select Page...', 'Select Page...', array_merge( ['All'] , $pages ), $page, 'onchange="this.form.submit()" class="select2"', 4, '', 1, 0 );
    ?>
</form>
<div id="i18n_wrap" data-save-scroll>
    <?php

    //global $ui_params;
    //$path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . 'storage/backups/*';

    if( !empty( $strings ) && is_array( $strings ) && !empty( $lang ) && $lang !== 'add' ) {

        $cry = Crypto::initiate();
        echo '<div id="aio_translations" class="translations" data-update="'.$cry->encrypt('update_translation_ajax').'" data-remove="'.$cry->encrypt('remove_translation_ajax').'">';

        foreach( $strings as $ts ){

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
    } else if( $lang == 'add' ) { ?>
        <div <?php $f->process_params('','i18ns','',3,3,[],'Successfully updated Languages!'); ?>>
            <?php
            $f->text('base_language','Default Programmed Language', 'Ex: English', $base, 'data-i18ns', 12 );
            $f->textarea( 'languages', 'Add Languages to be Translated (Separate by ,)', 'Ex: العربية, English, Español, 普通话 etc.', $i18ns, 'data-i18ns', 12 );
            ?>
            <div class="tac">
                <?php
                $f->process_button_html('Save Languages','save_i18ns','','process_options_ajax');
                ?>
            </div>
        </div>
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
<?php get_scripts(['jquery','clipboard','tagcomplete','aio','i18n']); ?>