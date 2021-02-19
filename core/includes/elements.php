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

function html_class( $class = '', $extras = 1 ) {
    // Is Debug
    $dc = APPDEBUG ? 'debug ' : '';

    // Is RTL
    $dir = isset( $_SESSION['lang'] ) && in_array( $_SESSION['lang'], ['ar','iw','ku','fa','ur'] ) ? 'dir="rtl" lang="'.$_SESSION['lang'].'"' : '';

    // Custom class
    $ec = !empty( $class ) ? 'class="'.$class.' '.$dc.'"' : '';

    $ex = '';
    if( $extras ) {
        global $access;
        // Get Browser
        $browser = $access::get_user_browser();
        $ex = 'browser="'.str_replace(' ','_',strtolower($browser)).'"';

        // Get OS
        $os = $access::get_user_os();
        $ex .= ' client="'.str_replace(' ','_',strtolower($os)).'"';
    }

    // Final Output
    echo $dir.' '.$ec.' '.$ex;
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

    // Get Client Info
    $client = new CLIENT();
    $dev = strtolower(str_replace(' ','_',$client->get_device_type()));
    $os = strtolower(str_replace(' ','_',$client->get_os()));
    $brow = strtolower(str_replace(' ','_',$client->get_browser()));

    $dd = 'data-device="'.$dev.'" data-os="'.$os.'" data-browser="'.$brow.'"';

    // Final output
    echo 'class="'.$dc.$pc.$ec.$dm.'" '.$dd;
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