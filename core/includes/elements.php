<?php

function favicon( $icon ){
    if( file_exists( APPPATH . 'assets/images/' . $icon . '.png' ) ) {
        $link = APPURL . 'apps/' . APPDIR . '/assets/images/'.$icon.'.png';
        echo '<link rel="shortcut icon" href="'.$link.'">';
        if (is_mobile()) {
            echo file_exists( APPPATH . 'assets/images/' . $icon . '.png' ) ? '<link rel="apple-touch-icon" href="'.$link.'"/>' : '';
            $sizes = [144,114,72,57];
            foreach( $sizes as $s ){
                echo file_exists( APPPATH . 'assets/images/' . $icon . '-' . $s . '.png' ) ? '<link rel="apple-touch-icon" href="'.APPURL . 'apps/' . APPDIR . 'assets/images/'.$icon.'-'.$s.'.png"/>' : '';
            }
        }
    }
}

function body_class( $class = '' ) {

    // Is Debug
    $dc = APPDEBUG ? 'debug ' : '';

    // Page path
    $pc = str_replace('/',' ',PAGEPATH);

    // Custom class
    $ec = !empty( $class ) ? ' '.$class : '';

    // Dark mode
    $dm = isset($_SESSION['dark_mode']) && $_SESSION['dark_mode'] == 'true';
    $dm = !empty( $dm ) ? ' d' : '';

    // Final output
    echo 'class="'.$dc.$pc.$ec.$dm.'"';
}

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