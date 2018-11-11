<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Language Editor</title>
    <?php get_styles( ['reset','languages'] ); get_scripts(['jquery','languages']); ?>
</head>
<body>
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
            <button id="save" onclick="save_row()">Save</button>
            <button id="build">Build</button>
            <button id="new" onclick="add_row()">New</button>
        </div>
    </div>
</body>
</html>