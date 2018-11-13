<?php get_style( 'translations' ); get_scripts(['clipboard','translations']); ?>
<div id="data">
    <table>
        <thead>
        <tr>
            <td>ENGLISH - DEFAULT</td>
            <td><select name="language_selector" id="language_selector">
                    <?php $lfs = get_language_files(); unset($lfs['en']); select_options( $lfs ); ?>
                </select></td>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <td colspan="100%">
                <button id="new_tran" onclick="add_row()"><?php __('New Sentence'); ?></button>
                <button id="get_untran" onclick="get_untranslations()"><?php __('Get Untranslated'); ?></button>
                <button id="new_lang"><?php __('Add Language'); ?></button>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<div id="editor">
    <div class="row">
        <div class="col">
            <label for="en_translation"><?php __('English Sentence'); ?></label><button data-clipboard-target="#en_translation" class="small">COPY</button>
            <textarea id="en_translation" rows="2"></textarea>
        </div>
        <div class="col">
            <label for="translation"><?php __('Translation'); ?></label><button data-clipboard-target="#translation" class="small">COPY</button>
            <textarea id="translation" rows="2"></textarea>
        </div>
    </div>
    <div class="actions">
        <button id="save" onclick="save_row()"><?php __('Save'); ?></button>
        <button id="build" onclick="build_translations()"><?php __('Build'); ?></button>
    </div>
</div>
<div class="modal s" id="modal_lang">
    <div class="close"></div>
    <h4>Translate New Language</h4>
    <div>
        <select name="language_selector" id="language_selector">
            <?php select_options( get_languages() ); ?>
        </select>
        <button id="add_lang" onclick="add_lang()"><?php __('ADD LANGUAGE'); ?></button>
    </div>
</div>