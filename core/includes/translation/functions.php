<?php

$db = new DB();
$lang = get_config('lang');
$lang = empty( $lang ) ? 'en' : $lang;
!defined( 'BASELANG' ) ? define( 'BASELANG', $lang ) : '';

if( !empty( $_SESSION['lang'] ) && defined( 'BASELANG' ) && $_SESSION['lang'] !== BASELANG ) {

    $l = $_SESSION['lang'];
    global $translated; // Already translated
    global $untranslated;

    // Load translations from database
    $db_trans = !is_array( $l ) ? $db->select( 'translations', 't_base, t_'.$l ) : [];
    $db_trans = !empty( $db_trans ) && is_array( $db_trans ) ? $db_trans : [];

    // Add translations to global variable $translated
    if( !empty( $db_trans ) && is_array( $db_trans ) ){

        $new_data = [];
        foreach( $db_trans as $t) {

            $new_data[ $t['t_base'] ] = $t['t_'.$l];

        }
        $translated = $new_data;

    }
    //skel( $translated );
    //echo count( $translated );
}






function save_untranslated( $string ){
    if( isset( $_SESSION['lang'] ) ) {
        global $untranslated_words;
        $untranslated_words[] = $string;
        /*elog( 'some' );
        $untranslated_words = !isset( $untranslated_words ) ? get_option('untranslated_' . $_SESSION['lang']) : $untranslated_words;
        $untranslated_words = !is_array( $untranslated_words ) ? unserialize( $untranslated_words ) : $untranslated_words;
        if ( $untranslated_words ) {
            if( !in_array( $string, $untranslated_words ) ){
                $no = update_option('untranslated_' . $_SESSION['lang'], array_merge( $untranslated_words, [ $string ] ) );
            }
        } else {
            $no = update_option('untranslated_' . $_SESSION['lang'], serialize([$string]) );
        }*/
    }
}

function update_translation( $string = '', $language = '', $translation = '', $page = '' ) {

    $string = isset( $_POST['string'] ) ? $_POST['string'] : $string;
    $language = isset( $_POST['language'] ) ? $_POST['language'] : $language;
    $translation = isset( $_POST['translation'] ) ? $_POST['translation'] : $translation;
    $page = isset( $_POST['page'] ) ? $_POST['page'] : $page;

    $db = new DB();
    $exist = $db->select( 'translations', '', 'BINARY t_base = "'.$string.'"' );

    if( !empty( $language ) && !empty( $translation ) && !empty( $string ) ) {

        if( $exist ) {

            $keys = ['t_base'];
            $vals = [$string];
            if( $translation !== '' ){
                $keys[] = 't_'.$language;
                $vals[] = $translation;
            }
            if( $page !== '' ){
                $keys[] = 't_page';
                $vals[] = $page;
            }
            $trans = $db->update( 'translations', $keys, $vals, 't_base = "'.$string.'"');

        } else {

            $trans = $db->insert( 'translations', ['t_base','t_'.$language,'trans_page'],[$string,$translation,$page]);

        }

    }

    isset( $trans ) && $trans ? ES('Translation Stored') : EF('Translation Not Added');

}

function remove_translation() {
    $id = isset( $_POST['id'] ) ? $_POST['id'] : '';
    if( $id !== '' ){
        $cry = Crypto::initiate();
        $db = new DB();
        $trans = $db->delete( 'translations', 't_id = "'.$cry->decrypt( $id ).'"' );
        $trans ? ES('Translation Deleted') : EF('Could not delete Translation');
    }
}

// Language Translation Files

function get_translations() {

    $ln = isset( $_POST['ln'] ) && !empty( $_POST['ln'] ) ? $_POST['ln'] : 'en';

    $db = new DB();
    $trans = $db->select( 'translations', '', 'trans_ln = "'.$ln.'"' );

    if( is_array( $trans ) ){
        $data = [];
        foreach( $trans as $tran ){
            $data[$tran['trans_base']] = $tran['trans_replace'];
        }
        if( !empty( $_POST['method'] ) && $_POST['method'] == 'json' ){
            echo json_encode( $data );
        } else {
            return $data;
        }
    } else {
        EF('No Translations Found');
    }
}

/* function translations_transfer() {

    $languages = get_language_files();
    foreach( $languages as $ln => $name ){
        if( file_exists( APPPATH . 'languages/' . $ln . '.php' ) ){
            $langs[$ln] = include( APPPATH . 'languages/' . $ln . '.php' );
        }
    }
    if( is_array( $langs ) ){
        foreach( $langs as $lang => $trans ) {
            if( $lang !== 'en' ) {
                foreach( $trans as $k => $tran ){
                    skel($langs['en'][$k] . ' - ' . $tran);
                    $i = update_translation($langs['en'][$k], $lang, $tran);
                    echo $i ? ' - Done<br/>' : ' - Failed<br/>';
                }
            }
        }
    }

} */

// Returns list of language files present in App

function app_languages() {
    /* $final_languages = [];
    if( file_exists( APPPATH . 'languages' ) ){
        $languages = get_languages();
        foreach( glob( APPPATH . 'languages/*.php' ) as $file ){
            $lang = str_replace( APPPATH . 'languages/', '', str_replace( '.php', '', $file ));
            isset( $languages[$lang] ) ? $final_languages[$lang] = $languages[$lang] : '';
        }
    }
    return $final_languages; */
    $db = new DB();
    $app_langs = $db->get_option( 'app_languages' );

    if( !empty( $app_langs ) ) {
        $n = unserialize( $app_langs );
        if( is_array( $n ) ) {
            $langs = get_languages();
            if (is_array($langs)) {
                $data['en'] = 'English';
                foreach ($n as $ln) {
                    $data[$ln] = $langs[$ln];
                }
                return array_unique( $data );
            } else {
                return [];
            }
        }
    } else {
        return [];
    }
}

// Set User Session of their preferred language choice

function set_language( $language = '' ) {
    if( !empty( $_POST ) ) {
        unset( $_POST['action'] );
        $language = !empty( $_POST['lang'] ) && !is_array( $_POST['lang'] ) ? $_POST['lang'] : 'en';
    } else {
        $language = !empty( $language ) && !is_array( $language ) ? $language : 'en';
    }
    if( !empty( $language ) && !is_array( $language ) ){
        $_SESSION['lang'] = $language;
    }
    if( !empty( $_POST ) ) {
        es('Successfully changed Language');
    } else {
        ef('Failed to change language!');
    }
}

// Builds Translation Files

function update_translations() {
    $ln = $_POST['language'];
    $trans = $_POST['translations'];
    if( !empty( $ln ) && is_array( $trans ) ){
        $passed = $failed = 0;
        foreach( $trans as $en => $tran ){
            $u = update_translation( $en, $ln, $tran );
            if( $u ){
                $passed++;
            } else {
                $failed++;
            }
        }
    }
}

function get_untranslated() {

    if( !empty( $_SESSION['lang'] ) ){

        $db = new DB();
        $words = $db->get_option( 'untranslated_' . $_SESSION['lang'] );
        echo !empty( $words ) ? json_encode( [1, unserialize($words)] ) : json_encode( [0, T('Fetching untranslated words failed')] );

    } else {

        echo json_encode( [0, T('Language is not selected, Please select Language')] );

    }

}

function select_languages() {
    $langs = app_languages();
    if( is_array( $langs ) && !empty( $langs ) ) {
        $ln = isset( $_SESSION['lang'] ) ? $_SESSION['lang'] : 'en';
        foreach( $langs as $l => $n ){
            $c = $l == $ln ? 'selected' : '';
            echo '<option value="'.$l.'" '.$c.'>'.$n.'</option>';
        }
    }
}