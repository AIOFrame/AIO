<?php get_style( 'translations' ); get_scripts(['jquery','translations']); ?>
<div id="data">
    <table>
        <thead>
        <tr>
            <td>ENGLISH - DEFAULT</td>
            <td><select name="language_selector" id="language_selector">
                    <?php select_options( get_languages() ); ?>
                </select></td>
        </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
        <tr>
            <td colspan="100%" onclick="add_row()">ADD TRANSLATION</td>
        </tr>
        </tfoot>
    </table>
</div>
<div id="editor">
    <label for="en_translation">English Sentence</label>
    <textarea id="en_translation" rows="4">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequatur, quaerat?</textarea>
    <label for="translation">Translation</label>
    <textarea id="translation" rows="4"></textarea>
    <div class="actions">
        <div class="left">
            <button id="save" onclick="save_row()">Save</button>
            <button id="build">Build</button>
        </div>
        <div class="right">
            <button id="new_tran" onclick="add_row()">Add Translation</button>
            <button id="get_untran" onclick="get_untranslations()">Get Non Translated</button>
            <button id="new_lang">Add Language</button>
        </div>
    </div>
</div>
<div class="modal s" id="modal_lang">
    <div class="close"></div>
    <h4>Translate New Language</h4>
    <div>
        <select name="language_selector" id="language_selector">
            <?php select_options( get_languages() ); ?>
        </select>
        <button id="add_lang" onclick="add_lang()">ADD LANGUAGE</button>
    </div>
</div>