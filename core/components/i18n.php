<?php

$db = new DB();
$f = new FORM();
global $options;
$icon_class = $options['icon_class'] ?? 'mico';
$go_icon = $options['ico_forward'] ?? 'arrow_forward';
$lang_icon = $options['ico_languages'] ?? 'language';
//skel( $options );
$region = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
$languages = $options[ $region . 'languages' ] ?? '';
$languages = explode( ',', str_replace( ' ', '', $languages ) );
$app_debug = defined( 'APPDEBUG' ) && APPDEBUG;

// Select Languages to Translate
if( ( isset( $_POST['editor_language'] ) && $_POST['editor_language'] == 'add' ) || empty( $languages ) ) {
    $o = new OPTIONS();
    $o->language_options();
} else {

    $all_languages = get_languages();

    $base = $options['base_language'] ?? 'English';

    $app_languages = [];
    foreach( $languages as $al ) {
        $app_languages[ $al ] = $all_languages[$al] ?? $al;
    }
    $p = $_GET['page'] ?? 0;
    $limit = 10;

    $editor_language = $_POST['editor_language'] ?? '';
    $translate_url = $_POST['translation_url'] ?? '';


    $query = !empty( $translate_url ) && $translate_url !== 'All' ? 't_page = "'.$translate_url.'"' : '';
    $strings = !empty( $_POST['editor_language'] ) ? $db->select( 'translations', 't_id,t_base,t_page,t_'.$_POST['editor_language'], $query ) : [];
    //skel( $strings );
    $urls = $db->select( 'translations', 't_page' );
    $translation_urls = [];
    foreach( $urls as $url ) {
        !empty( $url['t_page'] ) ? $translation_urls[] = $url['t_page'] : '';
    }
    $translation_urls = array_unique( $translation_urls );
    get_styles( ['bootstrap/css/bootstrap-grid','tagcomplete','i18n','micro'] );
    if( empty( $_POST['editor_language'] ) ) {
        pre( '', 'set_editor_language', 'form', 'method="post"' );
            h4( 'Choose Language to Edit' );
            _d( 'lang_grid' );
                foreach( $app_languages as $lk => $lv ) {
                    $f->checkboxes( 'editor_language', '', [ $lk => $lv . __i( $icon_class . ' ico s ' . $go_icon, $go_icon ) ], '', 'onchange="this.form.submit()"' );
                }
            d_();
            h4( 'Add / Edit App Languages' );
            _d( 'lang_grid' );
                $f->checkboxes( 'editor_language', '', [ 'add' => T('Language Settings')  . __i( $icon_class . ' ico s ' . $lang_icon, $lang_icon ) ], '', 'onchange="this.form.submit()"' );
            d_();
            //$f->select2( 'editor_language', 'Choose Language to start translating', 'Choose...', array_merge( [ 'add' => 'Add Language' ], $app_languages ), $editor_language, 'onchange="this.form.submit()"', 12, 1 );
        post( 'form' );
    } else {
        pre( '', 'row', 'form', 'method="post"' );
            $f->text( 'editor_language', '', '', $editor_language, 'style="display:none"' );
            $f->input( 'search', 'lang_search', '', 'Search Strings...', '', '', 8 );
            $f->select2( 'translation_url', '', 'Select Page...', array_merge( [ 'Select Page...' ] , $translation_urls ), $translate_url, 'onchange="this.form.submit()"', 4 );
        post( 'form' );
        h2( 'Translations for ' . $app_languages[ $_POST['editor_language'] ] );
        _d( '', 'i18n_wrap', 'data-save-scroll' );

            $icon_class = $options['icon_class'] ?? 'mico';
            $close_ico = $options['ico_close'] ?? 'close';
            $delete_ico = $options['ico_delete'] ?? 'delete';
            $save_ico = $options['ico_save'] ?? 'save';
            //skel( $options );
            //global $ui_params;
            //$path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . 'storage/backups/*';

            if( !empty( $strings ) && is_array( $strings ) && !empty( $editor_language ) && $editor_language !== 'add' ) {
                //skel( $strings );
                $cry = Encrypt::initiate();
                _d( 'translations', 'aio_translations', 'data-update="'.( $app_debug ? 'update_translation_ajax' : $cry->encrypt('update_translation_ajax') ).'" data-remove="'.( APPDEBUG ? 'remove_translation_ajax' : $cry->encrypt('remove_translation_ajax') ).'"' );

                //skel( $strings );
                //$loop = array_slice( $strings, ( ( $p + 1 ) * $limit ), $limit );
                foreach( $strings as $ts ){

                    _d();
                        //if( !empty( $page ) && !in_array( $page, ['All','Global'] ) && $ts['t_page'] !== $page ) { continue; }
                        //$p = isset( $ts['t_page'] ) && !empty( $ts['t_page'] ) ?  '<span>'.$ts['t_page'].'</span>' : '';
                        div( '', $ts['t_base'] ?? '' );
                        div( '', $ts['t_'.$editor_language] ?? '' );
                        div( '', APPURL . ( $ts['t_page'] ?? '' ) );
                        el( 'i', $icon_class . ' red', $delete_ico, '', 'data-trash-id="'.( $app_debug ? $ts['t_id'] : $cry->encrypt($ts['t_id']) ).'"' );
                    d_();

                }

                // Add String Form
                $f->pre_process( 'class="new_string"', 'update_translation_ajax', 'lang', '', [ 'language' => $editor_language ] );
                    div( '', $f->__textarea( 'string', 'English String', 'Write your english string...', '', 'data-lang' ) );
                    div( '', $f->__textarea( 'translation', 'Translation', 'Write your translation...', '', 'data-lang' ) );
                    div( '', $f->__text( 'page', 'Page', 'Write page path excl. domain...', '', 'data-lang' ) );
                    $f->process_trigger( '', 'px-2', '', '', '', '', 'div' );
                    //el( 'i', $icon_class . ' green', $save_ico );
                d_();

                d_();

                //pagination( ( $p + 1 ), count( $strings ), $limit, 'mt20', 'page_link', '?page=' );

                //skel( $strings );
            }
            _d( '', 'aio_lang_editor' );
                div( $icon_class . ' ' . $close_ico, $close_ico, '', 'data-on="#editor"' );
                _r();
                    _c( 5 );
                        //el( 'label', '', T('Sentence'), '', 'for="string"' );
                        b( 'small mx-2', 'COPY', '', 'data-clipboard-target="[data-key=string]"' );
                        $f->textarea( 'string', 'Sentence', '', '', 'rows="2" tabindex="1"' );
                    c_();
                    _c( 6 );
                        b( 'small mx-2', 'COPY', '', 'data-clipboard-target="[data-key=translation]"' );
                        $f->textarea( 'translation', ( !empty( $lang ) && isset( $all_languages[$lang] ) ? $all_languages[$lang].' ' : '' ). T('Translation'), '', '', 'rows="2" tabindex="2"' );
                    c_();
                    _c( 1 );
                        b( '', 'Save', 'save', 'onclick="update_translation()"', 1 );
                    c_();
                r_();
            d_();
        d_();
    }
    get_scripts(['jquery','clipboard','tagcomplete','aio','i18n']);
}