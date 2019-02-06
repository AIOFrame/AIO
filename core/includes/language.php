<?php

// Sets a User preferred language as global

if( !empty( $_SESSION['lang'] ) ){
    global $langs; global $untranslated;
    if( file_exists( COREPATH . 'core/languages/en.php' ) ){
        $clangs[] = include( COREPATH . 'core/languages/en.php' );
    }
    if( file_exists( COREPATH . 'core/languages/' . $_SESSION['lang'] . '.php' ) ){
        $clangs[] = include( COREPATH . 'core/languages/' . $_SESSION['lang'] . '.php' );
    }
    if( file_exists( APPPATH . 'languages/en.php' ) ){
        $langs[] = include( APPPATH . 'languages/en.php' );
    }
    if( file_exists( APPPATH . 'languages/' . $_SESSION['lang'] . '.php' ) ){
        $langs[] = include( APPPATH . 'languages/' . $_SESSION['lang'] . '.php' );
    }
    $clangs = isset( $clangs[0] ) && isset( $clangs[1] ) && !is_integer( $clangs[0] ) && !is_integer( $clangs[1] ) ? array_combine( $clangs[0], $clangs[1] ) : [];
    $langs = isset( $langs[0] ) && isset( $langs[1] ) && !is_integer( $langs[0] ) && !is_integer( $langs[1] ) && count($langs[0]) == count($langs[1]) ? array_combine( $langs[0], $langs[1] ) : [];
    $langs = array_merge( $clangs, $langs );
    //skel($langs);
}

// Changes and echo the word as per set language

function __( $string ) {
    global $langs; global $untranslated;
    !isset( $langs[$string] ) || $langs[$string] == '' ? save_untranslated( $string ) : '';
    //!isset( $langs[$string] ) ? $untranslated[] = $string : '';
    echo isset( $langs[$string] ) && $langs[$string] !== '' ? $langs[$string] : $string;
}

// Change and return the word as per set language

function _t( $string ) {
    global $langs; global $untranslated;
    !isset( $langs[$string] ) || $langs[$string] == '' ? save_untranslated( $string ) : '';
    //!isset( $langs[$string] ) ? $untranslated[] = $string : '';
    return isset( $langs[$string] ) && $langs[$string] !== '' ? $langs[$string] : $string;
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

function update_translation() {

    $english_string = isset( $_POST['english_string'] ) ? $_POST['english_string'] : '';
    $language = isset( $_POST['language'] ) ? $_POST['language'] : '';
    $translation = isset( $_POST['translation'] ) ? $_POST['translation'] : '';
    $page = isset( $_POST['page'] ) ? $_POST['page'] : '';

    $exist = select( 'translations', '', 'trans_base = "'.$english_string.'" AND trans_ln = "'.$language.'"' );

    if( $exist ) {

        $keys = ['trans_base','trans_ln'];
        $vals = [$english_string,$language];
        if( $translation !== '' ){
            $keys[] = 'trans_replace';
            $vals[] = $translation;
        }
        if( $page !== '' ){
            $keys[] = 'trans_page';
            $vals[] = $page;
        }
        $trans = update( 'translations', $keys, $vals, 'trans_base = "'.$english_string.'"');

    } else {

        $trans = insert( 'translations', ['trans_base','trans_replace','trans_ln','trans_page'],[$english_string,$translation,$language,$page]);

    }

    echo isset( $trans ) && $trans ? json_encode([1,'Translation Stored']) : json_encode([0,'Translation Not Added']);

}

// Language Translation Files

function get_translations() {

    $ln = isset( $_POST['lang'] ) && !empty( $_POST['lang'] ) ? $_POST['lang'] : 'en';

    $trans = select( 'translations', '', 'trans_ln = "'.$ln.'"' );

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
        echo json_encode([0,'No Translations Found']);
    }
}

function translations_transfer() {

    $languages = get_language_files();
    foreach( $languages as $ln => $name ){
        if( file_exists( APPPATH . 'languages/' . $ln . '.php' ) ){
            $langs[$ln] = include( APPPATH . 'languages/' . $ln . '.php' );
        }
    }
    if( is_array( $langs ) ){
        foreach( $langs as $lang => $trans ) {
            if( $lang == 'hi' || $lang == 'ru' ) {
                foreach( $trans as $k => $tran ){
                    skel($langs['en'][$k] . ' - ' . $tran);
                    $i = insert_translation($langs['en'][$k], $lang, $tran);
                    echo $i ? ' - Done<br/>' : ' - Failed<br/>';
                }
            }
        }
    }

}

// Returns list of language files present in App

function get_language_files() {
    $final_languages = [];
    if( file_exists( APPPATH . 'languages' ) ){
        $languages = get_languages();
        foreach( glob( APPPATH . 'languages/*.php' ) as $file ){
            $lang = str_replace( APPPATH . 'languages/', '', str_replace( '.php', '', $file ));
            isset( $languages[$lang] ) ? $final_languages[$lang] = $languages[$lang] : '';
        }
    }
    return $final_languages;
}

// Set User Session of their preferred language choice

function set_language( $language = '' ) {
    unset($_POST['action']);
    $language = !empty( $language ) ? $language : !empty( $_POST['lang'] ) ? $_POST['lang'] : 'en';
    //elog($language);
    if( !empty( $language ) ){
        $_SESSION['lang'] = $language;
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

        $words = get_option( 'untranslated_' . $_SESSION['lang'] );
        echo !empty( $words ) ? json_encode( [1, unserialize($words)] ) : json_encode( [0, _t('Fetching untranslated words failed')] );

    } else {

        echo json_encode( [0, _t('Language is not selected, Please select Language')] );

    }

}

function select_languages() {
    $langs = get_language_files();
    if( is_array( $langs ) && !empty( $langs ) ) {
        $ln = isset( $_SESSION['lang'] ) ? $_SESSION['lang'] : 'en';
        foreach( $langs as $l => $n ){
            $c = $l == $ln ? 'selected' : '';
            echo '<option value="'.$l.'" '.$c.'>'.$n.'</option>';
        }
    }
}