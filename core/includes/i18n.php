<?php

$db = new DB();
!defined( 'BASELANG' ) ? define( 'BASELANG', 'en' ) : '';

if( !empty( $_SESSION['lang'] ) && defined( 'BASELANG' ) && $_SESSION['lang'] !== BASELANG ) {

    $l = $_SESSION['lang'];
    //skel( $l );
    global $translated; // Already translated
    global $untranslated;

    // Load translations from database
    $db_trans = !is_array( $l ) ? $db->select( 'translations', 't_base, t_'.$l ) : [];
    $db_trans = !empty( $db_trans ) ? $db_trans : [];
    //skel( $db_trans );

    // Add translations to global variable $translated
    if( !empty( $db_trans ) && $l !== 'en' ){
        //$new_data = [];
        foreach( $db_trans as $t) {
            //skel( $t )
            // isset( $translated[ $t['t_base'] ] ) && $translated[ $t['t_base'] ] !== ''
            if( !empty( $t[ 't_' . $l ] ) ) {
                $translated[ $t['t_base'] ] = $t[ 't_' . $l ];
            }
            //$new_data[ $t['t_base'] ] = $t['t_'.$l];
        }
        //$translated = $new_data;
    }
    //skel( $translated );
    //echo count( $translated );
}


function save_untranslated( $string ): void {
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

/**
 * Updates as translation string to database
 * @param string $string Key base en string
 * @param string $language Language of this translation
 * @param string $translation Translated string
 * @param string $page Particular page of this translation
 * @return void
 */
function update_translation( string $string = '', string $language = '', string $translation = '', string $page = '' ): void {

    $string = $_POST['string'] ?? $string;
    $language = $_POST['language'] ?? $language;
    $translation = $_POST['translation'] ?? $translation;
    $page = $_POST['page'] ?? $page;

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

/**
 * AJAX Updates as translation string to database
 * @return void
 */
function update_translation_ajax(): void {

    $string = $_POST['string'] ?? '';
    $language = $_POST['language'] ?? '';
    $translation = $_POST['translation'] ?? '';
    $page = $_POST['page'] ?? '';

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

            $trans = $db->insert( 'translations', ['t_base','t_'.$language,'t_page'],[$string,$translation,$page]);

        }

    }

    isset( $trans ) && $trans ? ES('Translation Stored') : EF('Translation Not Added');

}

/**
 * Removes a translation string from database
 * @return void
 */
function remove_translation_ajax(): void {
    $id = $_POST['id'] ?? '';
    $appdebug = defined( 'APPDEBUG' ) && APPDEBUG;
    if( $id !== '' ){
        $cry = Encrypt::initiate();
        $db = new DB();
        $trans = $db->delete( 'translations', 't_id = "'.( $appdebug ? $id : $cry->decrypt( $id ) ).'"' );
        $trans ? ES('Translation Deleted') : EF('Could not delete Translation');
    }
}

// Language Translation Files
/**
 * Loads Translation Strings from database
 */
function get_translations() {
    $ln = isset( $_POST['ln'] ) && !empty( $_POST['ln'] ) ? $_POST['ln'] : 'en';

    $db = new DB();
    $trans = $db->select( 'translations', '', 'trans_ln = "'.$ln.'"' );

    if( !empty( $trans ) ){
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
        return [];
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

function app_languages(): array {
    $region = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
    global $options;
    $languages = $options[ $region . 'languages' ] ?? '';
    $languages = explode( ',', str_replace( ' ', '', $languages ) );

    if( !empty( $languages ) && is_array( $languages ) ) {
        $all_languages = get_languages();
        if( !empty( $all_languages ) ) {
            $data['en'] = 'English';
            foreach( $languages as $ln ) {
                if( isset( $all_languages[$ln] ) ) {
                    $data[$ln] = $all_languages[$ln];
                }
            }
            return array_unique( $data );
        } else {
            return [];
        }
    } else {
        return [];
    }
}

/**
 * Renders languages picker from preset languages in options
 * @param string $wrap_class Wrap class for languages
 * @param string $class Each language of the app
 * @param bool $full_title Toggle full title or half
 * @return void
 */
function language_picker( string $wrap_class = 'languages', string $class = '', bool $full_title = true ): void {
    $c = Encrypt::initiate();
    $callback = defined( 'APPDEBUG' ) && APPDEBUG ? 'set_language_ajax' : $c->encrypt('set_language_ajax');
    $i18ns = app_languages();
    if( !empty( $i18ns ) && count( $i18ns ) > 1 ) {
        _d( $wrap_class, '', 'data-language="'.$callback.'"' );
            $i18ns = array_unique($i18ns);
            foreach ($i18ns as $v => $t) {
                $lang = $_SESSION['lang'] ?? 'en';
                $c = $lang == $v ? 'on cp' : 'cp';
                $t = $full_title ? $t : $v;
                div( $c.' '.$class, $t, '', 'data-lang="'.$v.'"' );
                //echo '<div class="'.$c.' '.$class.'" data-lang="' . $v . '">' . $t . '</div>';
            }
        d_();
    }
}

/**
 * Set PHP Session of their preferred language choice
 * @param string $language
 * @return void
 */
function set_language( string $language = '' ): void {
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

function current_language(): string {
    return isset( $_SESSION['lang'] ) ? $_SESSION['lang'] : 'en';
}

/**
 * Set PHP Session of their preferred language choice by AJAX
 * @return void
 */
function set_language_ajax(): void {
    $language = !empty( $_POST['lang'] ) && !is_array( $_POST['lang'] ) ? $_POST['lang'] : 'en';
    $_SESSION['lang'] = $language;
    if( !empty( $_POST ) ) {
        es('Successfully changed Language');
    } else {
        ef('Failed to change language!');
    }
}

// Builds Translation Files

/**
 * Update translation strings to database in bulk
 * @return void
 */
function update_translations(): void {
    $ln = $_POST['language'];
    $trans = $_POST['translations'];
    if( !empty( $ln ) && is_array( $trans ) ){
        $passed = $failed = 0;
        foreach( $trans as $en => $tran ){
            $u = update_translation_ajax( $en, $ln, $tran );
            if( $u ){
                $passed++;
            } else {
                $failed++;
            }
        }
    }
}

/**
 * Returns array of untranslated strings from database
 * @return void
 */
function get_untranslated(): void {

    if( !empty( $_SESSION['lang'] ) ){

        $db = new DB();
        $words = $db->get_option( 'untranslated_' . $_SESSION['lang'] );
        echo !empty( $words ) ? json_encode( [1, unserialize($words)] ) : json_encode( [0, T('Fetching untranslated words failed')] );

    } else {

        echo json_encode( [0, T('Language is not selected, Please select Language')] );

    }

}

/**
 * Returns select options of languages
 * @return void
 */
function select_languages() : void {
    $langs = app_languages();
    if( is_array( $langs ) && !empty( $langs ) ) {
        $ln = $_SESSION['lang'] ?? 'en';
        foreach( $langs as $l => $n ){
            $c = $l == $ln ? 'selected' : '';
            echo '<option value="'.$l.'" '.$c.'>'.$n.'</option>';
        }
    }
}

function language_options(): void {
    $o = new OPTIONS();
    $db = new DB();
    $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';;
    //skel( $r );
    $all_languages = get_languages();
    unset( $all_languages['en'] );
    $languages = $db->get_option('languages');
    $form = [
        [ 'i' => $r.'languages', 'n' => 'Set Languages', 'p' => 'Choose Languages...', 'o' => $all_languages, 'v' => $languages, 'a' => 'data-al multiple', 'k' => 1, 't' => 'select2' ],
        [ 'i' => 'languages_updated', 'a' => 'data-al value="1"', 'c' => '.dn' ]
    ];
    $o->form( $form, 'row', 1, 'al', $o->region_flag().T('Save Language Options'), '', 'Successfully saved language options!', $r.'languages,languages_updated' );
    div( 'region_info', 'English is default, you can add additional languages.', '', 'style="text-align:center; font-size: .8rem"', 1 );
}