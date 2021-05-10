<?php

function ace( $element_id, $mode = 'html' ) {
    echo '<script src="'.APPURL.'core/components/ace/ace.min.js" type="text/javascript" charset="utf-8"></script>'.
    '<script src="'.APPURL.'core/components/ace/emmet.min.js"></script>'.
    '<script src="'.APPURL.'core/components/ace/ext-language_tools.min.js"></script>'.
    '<script>
        document.addEventListener("DOMContentLoaded", function(e) {
            
            // Load Scripts
            ace.config.set("basePath", "'.APPURL.'core/components/ace");
            
            var editor = ace.edit("' . $element_id . '");
            editor.setTheme("ace/theme/twilight");
            
            editor.session.setMode("ace/mode/'.$mode.'");
            editor.setOptions({
                enableBasicAutocompletion: true,
                enableSnippets: true,
                enableLiveAutocompletion: false
            });
        });
    </script>';
}