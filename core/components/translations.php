<?php get_style( 'translations' ); get_scripts(['clipboard','select2','core','translations']);

$ln = isset( $_POST['ln'] ) && !empty( $_POST['ln'] ) ? $_POST['ln'] : '';

if( isset( $_POST['set_languages'] ) && !empty( $_POST['set_languages'] ) ) {
    $set_langs = update_option( 'app_languages', serialize( $_POST['set_languages'] ) );
    echo $set_langs ? '<script>notify("Languages Set!");</script>' : '';
}

$lfs = get_language_files();

$langs = get_option( 'app_languages' );

?>
<div id="data">
    <table id="translations">
        <thead>
        <tr>
            <td>ENGLISH - DEFAULT</td>
            <td><form method='post'><select name="ln" id="ln" onchange="this.form.submit()">
                    <option selected disabled>Select Language</option>
                    <?php select_options( $lfs, $ln ); ?>
                </select></form></td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <?php

        $db_translation_strings = get_option( 'translation_strings' );

        $tstrings = !empty( $db_translation_strings ) && $db_translation_strings !== '' ? unserialize( $db_translation_strings ) : [];

        $trans = select( 'translations', '', 'trans_ln = "'.$ln.'"' );
        if( is_array( $trans ) ){
            $data = [];
            foreach( $trans as $tran ){
                $data[$tran['trans_base']] = $tran['trans_replace'];
            }
        }

        foreach( $tstrings as $ts ){

            echo '<tr><td>'.$ts.'</td>';

            echo isset( $data[$ts] ) ? '<td>'.$data[$ts].'</td>' : '<td></td>';

            echo '<td><i class="ico trash"></i></td></tr>';

        }
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
<!--                <button id="new_tran" onclick="add_row()">--><?php //__('New Sentence'); ?><!--</button>-->
<!--                <button id="get_untran" onclick="get_untranslations()">--><?php //__('Get Untranslated'); ?><!--</button>-->
                <button id="manage_lns" data-on="#modal_lang"><?php __('Manage Languages'); ?></button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div id="editor">
    <div class="row">
        <div class="col">
            <label for="english_string"><?php __('English Sentence'); ?></label><button data-clipboard-target="#english_string" class="small">COPY</button>
            <textarea id="english_string" rows="2" tabindex="1"></textarea>
        </div>
        <div class="col">
            <label for="translation"><?php __('Translation'); ?></label><button data-clipboard-target="#translation" class="small">COPY</button>
            <textarea id="translation" rows="2" tabindex="2"></textarea>
        </div>
        <div class="col">
            <button id="save" onclick="update_translation()"><?php __('Save'); ?></button>
        </div>
    </div>
    <div class="actions">

<!--        <button id="build" onclick="build_translations()">--><?php //__('Build'); ?><!--</button>-->
    </div>
</div>
<div class="modal s" id="modal_lang" data-fade="0">
    <div class="close"></div>
    <h2 class="mt0">Manage Languages</h2>
    <form method='post' style="position:relative; width:300px;">
        <label for="language_selector">Select Languages for Application</label>
        <select name="set_languages[]" id="language_selector" class="select2" multiple>
            <?php select_options( get_languages(), $langs ); ?>
        </select>
        <button id="set_languages"><?php __('SET LANGUAGES'); ?></button>
    </form>
</div>