<?php get_style( 'translations' ); get_scripts(['clipboard','select2','core','translations']);

$con = new DB();
$form = new FORM();
$ln = isset( $_POST['ln'] ) && !empty( $_POST['ln'] ) ? $_POST['ln'] : '';

if( isset( $_POST['set_languages'] ) && !empty( $_POST['set_languages'] ) ) {
    $set_langs = $con->update_option( 'app_languages', serialize( $_POST['set_languages'] ) );
    echo $set_langs ? '<script>notify("Languages Set!");</script>' : '';
}

$lfs = get_language_files();

$langs = $con->get_option( 'app_languages' );
$langs = !empty( $langs ) ? unserialize( $langs ) : '';
//skel( $langs );
if( isset( $_POST['ln'] ) ) {
?>
<div id="data">
    <table id="translations">
        <thead>
        <tr>
            <td>ENGLISH - DEFAULT</td>
            <td><form method='post'><select name="ln" id="ln" onchange="this.form.submit()">
                    <option selected disabled>Select Language</option>
                    <?php $form->select_options( $lfs, $ln ); ?>
                </select></form></td>
            <td></td>
        </tr>
        </thead>
        <tbody>
        <?php

        $db_translation_strings = $con->get_option( 'translation_strings' );

        $tstrings = !empty( $db_translation_strings ) && $db_translation_strings !== '' ? unserialize( $db_translation_strings ) : [];

        $trans = $con->select( 'translations', '', 'trans_ln = "'.$ln.'"' );
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

        $browser = ACCESS::get_user_browser();
        ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
<!--                <button id="new_tran" onclick="add_row()">--><?php //E('New Sentence'); ?><!--</button>-->
<!--                <button id="get_untran" onclick="get_untranslations()">--><?php //E('Get Untranslated'); ?><!--</button>-->
                <button id="manage_lns" data-on="#modal_lang"><?php E('Manage Languages'); ?></button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div id="editor">
    <div class="row">
        <div class="col">
            <label for="english_string"><?php E('English Sentence'); ?></label>
            <button data-clipboard-target="#english_string" class="small">COPY</button>
            <?php echo $browser == 'Chrome' ? '<button data-paste="#english_string" class="small">PASTE</button>' : ''; ?>
            <textarea id="english_string" rows="2" tabindex="1"></textarea>
        </div>
        <div class="col">
            <label for="translation"><?php E('Translation'); ?></label>
            <button data-clipboard-target="#translation" class="small">COPY</button>
            <?php echo $browser == 'Chrome' ? '<button data-paste="#translation" class="small">PASTE</button>' : ''; ?>
            <textarea id="translation" rows="2" tabindex="2"></textarea>
        </div>
        <div class="col">
            <button id="save" onclick="update_translation()"><?php E('Save'); ?></button>
        </div>
    </div>
    <div class="actions">

<!--        <button id="build" onclick="build_translations()">--><?php //E('Build'); ?><!--</button>-->
    </div>
</div>
<div class="modal s" id="modal_lang" data-fade="0">
    <div class="close"></div>
    <h2 class="mt0">Manage Languages</h2>
    <form method='post' style="position:relative; width:300px;">
        <label for="language_selector">Select Languages for Application</label>
        <select name="set_languages[]" id="language_selector" class="select2" multiple>
            <?php $form->select_options( get_languages(), $langs ); ?>
        </select>
        <button id="set_languages"><?php E('SET LANGUAGES'); ?></button>
    </form>
</div>
<?php } else { ?>
<form method='post'><select name="ln" id="ln" onchange="this.form.submit()">
        <option selected disabled>Select Language</option>
        <?php $form->select_options( $lfs, $ln ); ?>
    </select></form>
<?php }