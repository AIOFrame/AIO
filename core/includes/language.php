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

function insert_translation( $english_word, $language, $translation = '', $page = '' ) {

    $exist = select( 'translations', '', 'trans_base = "'.$english_word.'"' );

    if( !$exist ) {

        $trans = insert( 'translations', ['trans_base','trans_replace','trans_ln','trans_page'],[$english_word,$translation,$language,$page]);

        echo $trans ? json_encode([1,'Translation Added']) : json_encode([0,'Translation Not Added']);

    }

}

// Language Translation Files

function get_translations() {
    if( !empty( $_POST['languages'] ) && is_array( $_POST['languages'] ) ){
        foreach( $_POST['languages'] as $ln ){
            if( file_exists( APPPATH . 'languages/' . $ln . '.php' ) ){
                $langs[] = include( APPPATH . 'languages/' . $ln . '.php' );
            }
        }
        if( !empty( $langs ) ){
            if( !empty( $_POST['method'] ) && $_POST['method'] == 'json' ){
                echo json_encode( $langs );
            } else {
                return include( $langs );
            }
        }
    }
    if( !empty( $_POST['lang'] ) ){
        $ln = isset( $_POST['lang'] ) && !empty( $_POST['lang'] ) ? $_POST['lang'] : 'en';
        if( file_exists( APPPATH . 'languages/' . $ln . '.php' ) ){
            if( !empty( $_POST['method'] ) && $_POST['method'] == 'json' ){
                echo json_encode( include( APPPATH . 'languages/' . $ln . '.php' ) );
            } else {
                return include( APPPATH . 'languages/' . $ln . '.php' );
            }
        } else {
            echo 0;
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

function build_translations() {
    $ls = $_POST['languages'];
    $ts = $_POST['translations'];
    $o = [];
    if( is_array( $ls ) && is_array( $ts ) && count( $ls ) == count( $ts ) ){
        foreach( $ls as $i => $l ){
            //if( $first_file = fopen( APPPATH . 'languages/' . $l . '.php', 'w' ) ){
                //$trans = '<?php return [';
                foreach( $ts[$i] as $t ){
                    //$trans .= '"'.$t.'",';
                }
                //$trans .= '];';
                //fwrite( $first_file, $trans );
                //fclose( $first_file );
            //}
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