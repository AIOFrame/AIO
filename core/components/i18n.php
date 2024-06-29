<?php

$db = new DB();
$f = new FORM();
global $options;
global $translated;
$icon_class = $options['icon_class'] ?? 'mico';
$go_icon = $options['ico_forward'] ?? 'arrow_forward';
$lang_icon = $options['ico_languages'] ?? 'language';
$ex_icon = $options['ico_download'] ?? 'download_for_offline';
$im_icon = $options['ico_file_upload'] ?? 'file_upload';
$icon_class = $options['icon_class'] ?? 'mico';
$close_ico = $options['ico_close'] ?? 'close';
$delete_ico = $options['ico_delete'] ?? 'delete';
$save_ico = $options['ico_save'] ?? 'save';
$search_ico = $options['ico_search'] ?? 'search';
//skel( $o0ptions );
$region = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
$languages = $options[ $region . 'languages' ] ?? '';
$languages = explode( ',', str_replace( ' ', '', $languages ) );
$app_debug = defined( 'APPDEBUG' ) && APPDEBUG;
//skel( $languages );
//skel(  $languages, [ 'English String' ] ) );

// Export Strings
//skel( $_POST );

// Import Strings

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

    $table_keys = isset( $_POST['export'] ) ? '' : 't_id,t_base,t_page,t_'.$editor_language;
    $query = !empty( $translate_url ) && $translate_url !== 'All' ? 't_page = "'.$translate_url.'"' : '';
    $query = isset( $_POST['export'] ) ? '' : $query;
    $strings = !empty( $_POST['editor_language'] ) ? $db->select( 'translations', $table_keys, $query ) : [];

    if( isset( $_POST['export'] ) ) {
        _d( 'tac' );
            h4( 'Your translation export spreadsheet file is ready to be downloaded!' );
            a( storage_url( '/storage/xlsx/Translations-' . date('d-m-y') . '.xlsx' ), 'Download Translations '.__i( $icon_class . ' ico xs ' . $ex_icon, $ex_icon ), 'btn down' );
        d_();
        $xls = SPREADSHEET::initiate();
        //skel( $strings );
        foreach( $strings as $ex_s ) {
            //skel( $ex_s );
            $base = $ex_s['t_base'];
            $body = [];
            foreach( $languages as $ex_l ) {
                $body[] = $ex_s[ 't_' . $ex_l ];
            }
            $ex_body[] = array_merge( [ $ex_s['t_id'], $base ], $body );
        }
        //skel( $ex_body );
        try {
            $xls->export('Translations', array_merge( [ 'ID', 'English String' ], $languages ), $ex_body, [], 1 );
        } catch (\PhpOffice\PhpSpreadsheet\Writer\Exception $e) {
            elog( $e );
        }
        //skel( $xls );
    }
    //skel( $query );
    $urls = $db->select( 'translations', 't_page' );
    $translation_urls = [];
    foreach( $urls as $url ) {
        !empty( $url['t_page'] ) ? $translation_urls[ $url['t_page'] ] = $url['t_page'] : '';
    }
    $translation_urls = array_unique( $translation_urls );
    $auto_post = 'onchange="this.form.submit()"';
    get_styles( ['bootstrap/css/bootstrap-grid','tagcomplete','i18n','micro'] );
    if( empty( $_POST['editor_language'] ) ) {
        pre( '', 'set_editor_language', 'form', 'method="post"' );
            h4( 'Choose Language to Edit' );
            _d( 'lang_grid' );
                foreach( $app_languages as $lk => $lv ) {
                    $f->checkboxes( 'editor_language', '', [ $lk => $lv . __i( $icon_class . ' ico s ' . $go_icon, $go_icon ) ], '', $auto_post );
                }
            d_();
            h4( 'Add / Edit App Languages' );
            _d( 'lang_grid' );
                $f->checkboxes( 'editor_language', '', [ 'add' => T('Language Settings')  . __i( $icon_class . ' ico s ' . $lang_icon, $lang_icon ) ], '', $auto_post );
            d_();
            //$f->select2( 'editor_language', 'Choose Language to start translating', 'Choose...', array_merge( [ 'add' => 'Add Language' ], $app_languages ), $editor_language, $auto_post, 12, 1 );
        post( 'form' );
    } else {
        $auto_post = 'onclick="document.forms[\'lang_form\'].submit();"';
        //skel( $_POST );
        pre( '', 'row', 'form', 'method="post" name="lang_form"' );
            $f->text( 'editor_language', '', '', $editor_language, '', '.dn' );
            _c(7,'rel');
                $f->input( 'search', 'lang_search', '', T('Search Strings...') );
                el( 'i', $icon_class . ' ico l ' . $search_ico, $search_ico );
            c_();
            $f->select2( 'translation_url', '', T('Select Page...'), array_merge( [ 'All' => T('Select Page...') ] , $translation_urls ), $translate_url, $auto_post, 3, 1 );
            _c(1);
                b( 'plain w py10 tac', __i( $icon_class . ' ico l ' . $ex_icon, $ex_icon ), '', 'name="export" type="submit" '.$auto_post );
            c_();
            _c(1);
                b( 'plain w py10 tac', __i( $icon_class . ' ico l ' . $im_icon, $im_icon ), '', 'value="import" type="submit" '.$auto_post );
            c_();
        post( 'form' );
        h2( T('Translations') . ' - ' . $app_languages[ $_POST['editor_language'] ], 0 );
        _d( '', 'i18n_wrap', 'data-save-scroll' );

            //skel( $options );
            //global $ui_params;
            //$path = !empty( $ui_params ) && isset( $ui_params['location'] ) ? $ui_params['location'] : APPPATH . 'storage/backups/*';

            if( !empty( $strings ) && is_array( $strings ) && !empty( $editor_language ) && $editor_language !== 'add' ) {
                //skel( $strings );
                $cry = Encrypt::initiate();
                _d( 'translations', 'aio_translations', 'data-update="'.( $app_debug ? 'update_translation_ajax' : $cry->encrypt('update_translation_ajax') ).'" data-remove="'.( APPDEBUG ? 'remove_translation_ajax' : $cry->encrypt('remove_translation_ajax') ).'"' );

                //skel( $strings );
                //$loop = array_slice( $strings, ( ( $p + 1 ) * $limit ), $limit );
                $page_group = array_group_by( $strings, 't_page' );
                //skel( $strings );
                foreach( $page_group as $page_url => $page_strings ){
                    _d( 'page_set mb20' );
                        h4( $page_url, 0, 'page_url' );
                        if( !empty( $page_strings ) ) {
                            foreach( $page_strings as $ps ) {
                                _d('r');
                                    //if( !empty( $page ) && !in_array( $page, ['All','Global'] ) && $ts['t_page'] !== $page ) { continue; }
                                    //$p = isset( $ts['t_page'] ) && !empty( $ts['t_page'] ) ?  '<span>'.$ts['t_page'].'</span>' : '';
                                    div( 'b', $ps['t_base'] ?? '' );
                                    div( 't', $ps['t_'.$editor_language] ?? '' );
                                    el( 'i', $icon_class . ' red', $delete_ico, '', 'data-trash-id="'.( $app_debug ? $ps['t_id'] : $cry->encrypt($ps['t_id']) ).'"' );
                                    //div( 'p', APPURL . ( $ps['t_page'] ?? '' ) );
                                d_();

                            }
                        }
                    d_();
                }

                // Add String Form
                $f->pre_process( 'class="new_string"', 'update_translation_ajax', 'lang', '', [ 'language' => $editor_language ] );
                    div( '', $f->__textarea( 'string', T('English String'), T('Write your english string...'), '', 'data-lang' ) );
                    div( '', $f->__textarea( 'translation', T('Translation'), T('Write your translation...'), '', 'data-lang' ) );
                    div( '', $f->__text( 'page', T('Page'), T('Write page path excluding domain...'), '', 'data-lang' ) );
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
                        el( 'label', '', T('English String'), '', 'for="string"' );
                        b( 'small mx-2', 'COPY', '', 'data-clipboard-target="[data-key=string]"' );
                    c_();
                    _c( 6 );
                        el( 'label', '', ( !empty( $lang ) && isset( $all_languages[$lang] ) ? $all_languages[$lang].' ' : '' ). T('Translation'), '', 'for="string"' );
                        b( 'small mx-2', 'COPY', '', 'data-clipboard-target="[data-key=translation]"' );
                    c_();
                r_();
                _r();
                    $f->textarea( 'edit_string', '', '', '', 'rows="2" tabindex="1"', 5 );
                    $f->textarea( 'edit_translation', '', '', '', 'rows="2" tabindex="2"', 6 );
                    _c( 1 );
                        b( '', 'Save', 'save', 'onclick="update_translation()"', 1 );
                    c_();
                r_();
            d_();
        d_();
    }
    get_scripts(['jquery','clipboard','tagcomplete','aio','i18n']);
}