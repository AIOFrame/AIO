<?php

if( !defined( 'COREPATH' ) ) { exit(); }

// Returns a readable <title> for Header

function get_title() {
    if( !empty( PAGE ) ) {
        $p = PAGE == 'ROOT' ? 'Welcome' : PAGE;
        echo ucwords( str_replace('-',' ', str_replace('_',' ', $p )) ) . ' - ' . APPNAME;
    } else {
        echo APPNAME;
    }
}

// Overrides the <title> in Header from a page

function set_title( $title ){
    echo '<script>$(document).ready(function() { document.title = "' . $title . ' - ' . APPNAME . '"; });</script>';
}

// Returns a complete <link> for stylesheet if file exists either in app or in core, prioritizes minified file

global $universal;
$universal = [ 'styles' => [], 'scripts' => [] ];

function get_style( $f ) {
    global $universal;
    if( !in_array( $f, $universal['styles'] )) {
        $universal['styles'][] = $f;
        $af = APPPATH . '/assets/styles/' . $f;
        $cf = COREPATH . '/assets/styles/' . $f;
        $afl = APPURL . 'apps/' . APPDIR . '/assets/styles/' . $f;
        $cfl = APPURL . 'assets/styles/' . $f;
        $url = '';
        if (file_exists($af . '.min.css')) {
            $url = $afl . '.min.css';
        } else if (file_exists($af . '.css')) {
            $url = $afl . '.css';
        } else if (file_exists($cf . '.min.css')) {
            $url = $cfl . '.min.css';
        } else if (file_exists($cf . '.css')) {
            $url = $cfl . '.css';
        }
        echo $url !== '' ? '<link rel="stylesheet" href="' . $url . '">' : '';
    }
}

// Returns a complete <script> if file exists either in app or in core, prioritizes minified file

function get_script( $f ) {
    global $universal;
    if( !in_array( $f, $universal['scripts'] )) {
        $universal['scripts'][] = $f;
        $af = APPPATH . '/assets/scripts/' . $f;
        $cf = COREPATH . '/assets/scripts/' . $f;
        $afl = APPURL . 'apps/' . APPDIR . '/assets/scripts/' . $f;
        $cfl = APPURL . 'assets/scripts/' . $f;
        $url = '';
        if (file_exists($af . '.min.js')) {
            $url = $afl . '.min.js';
        } else if (file_exists($af . '.js')) {
            $url = $afl . '.js';
        } else if (file_exists($cf . '.min.js')) {
            $url = $cfl . '.min.js';
        } else if (file_exists($cf . '.js')) {
            $url = $cfl . '.js';
        }
        echo $url !== '' ? '<script src="' . $url . '"></script>' : '';
    }
}

/*
// Wrote this code to render background but later forgot where it helps but code looks usable
function bg_asset( $f ) {
    $supported_types = ['gif', 'jpg', 'jpeg', 'png', 'bmp', 'svg'];
    foreach( glob( APPPATH . '/assets/images/*' ) as $af ){
        $ft = pathinfo( $af, PATHINFO_EXTENSION );
        foreach( $supported_types as $type ){
            if( $ft == $type && strpos( $af, $f ) !== false ){
                echo 'background-image:url('.str_replace( COREPATH, APPURL, $af).');';
            }
        }
    }
} */

// Returns a reset style if it exists

function reset_styles() {
    get_style( 'reset' );
}

// Loops an array of file names for .min.css / .css extension and returns if exists

function get_styles( $ar = "" ) {
    if( !empty( $ar ) ){
        foreach( $ar as $f ){
            get_style( $f );
        }
    }
}

// Loop an array for file names for .min.js / .js extension and returns if exists

function get_scripts( $ar = "" ) {
    if( !empty( $ar ) ){
        foreach( $ar as $f ){
            get_script( $f );
        }
    }
}

// Detects if the user device is Mobile

function is_mobile() {
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            return 'true';
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false ) {
        return true;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) {
        return false;
    } else {
        return false;
    }
}

// Detects if the user device is Tablet

function is_tablet() {
    if ( empty($_SERVER['HTTP_USER_AGENT']) ) {
        return false;
    } elseif (
        strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Silk/') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Kindle') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'BlackBerry') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
        || strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false ) {
            return false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false && strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') == false ) {
        return false;
    } elseif ( strpos($_SERVER['HTTP_USER_AGENT'], 'iPad') !== false ) {
        return true;
    } else {
        return false;
    }
}

// Detects if the user device is Desktop

function is_desktop() {
    if( !is_mobile() && !is_tablet() ){
        return true;
    } else {
        return false;
    }
}

// Error Logs if APP is under DEBUG Mode

function elog( $line ){
    $line = is_array( $line ) ? implode( ', ', array_keys( $line ) ) . '<>' . implode( ', ', $line ) : $line;
    APPDEBUG ? error_log( $line ) : '';
}

// Gets any file from components folder

function get_comp( $n ){
    $ns = explode('/',str_replace('.php','',$n));
    $x = 1;
    $fl = APPPATH . '/components/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    file_exists( $fl ) ? include( $fl ) : '';
}

// Includes if page exists

function get_page( $n ){
    $ns = explode('/',$n);
    $x = 1;
    $fl = APPPATH . '/pages/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    file_exists( $fl ) ? include( $fl ) : '';
}

// Renders an array as <option> for <select> element

function select_options( $options = '', $selected = '', $placeholder = '' ) {
    $d = $options;
    $s = $selected;
    if( $placeholder !== '' ){
        echo empty($s) ? '<option disabled selected>'.$placeholder.'</option>' : '<option disabled>'.$placeholder.'</option>';
    }
    if( is_array($d) ){
        if (is_assoc($d)) {
            foreach ($d as $k => $t) {
                echo '<option value="' . $k . '" ' . ($k == $s ? "selected" : "") . '>' . $t . '</option>';
            }
        } else {
            foreach ($d as $t) {
                echo '<option value="' . $t . '" ' . ($t == $s ? "selected" : "") . '>' . $t . '</option>';
            }

        }
    } else if( is_numeric( $d ) ){
        for($x=0;$x<=$d;$x++){
            echo '<option value="' . $x . '" ' . ($x == $s ? "selected" : "") . '>' . $x . '</option>';
        }
    }
}

function render_options( $type = 'radio', $name, $values = [], $attr = '', $label_first = 0, $pre = '', $post = '' ) {
    if( is_array( $values ) ) {
        $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
        $valued = is_assoc( $values ) ? true : false; $x = 0;
        if( is_numeric( $pre ) ){
            $pre = '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        foreach( $values as $val => $title ){
            $k = $valued ? $val.$x : str_replace(' ','',$name).$x;
            $value = $valued ? $val : $title;
            if( $label_first ){
                echo $pre . '<label for="'.$k.'">'.$title.'</label><input '.$attr.' '.$type.' name="'.$name.'" id="'.$k.'" value="'.$value.'" >' . $post;
            } else {
                echo $pre . '<input '.$attr.' '.$type.' name="'.$name.'" id="'.$k.'" value="'.$value.'" ><label for="'.$k.'">'.$title.'</label>' . $post;
            }
            $x++;
        }
        /* if (is_assoc( $d )) {
            foreach ($d as $k => $t) {
                echo $before . '<label for="cb_' . $k . '" ><input ' . $attrs . '  id="cb_' . $k . '" type="' . $tp . '" value="' . $k . '" ' . (in_array($k, $s) ? "checked" : "") . '>' . $t . '</label>' . $after;
            }
        } else {
            foreach ($d as $t) {
                echo $before . '<label for="cb_' . str_replace(' ', '_', $t) . '" ><input' . $attrs . 'id="cb_' . str_replace(' ', '_', $t) . '" type="' . $tp . '" value="' . $t . '" ' . (in_array($t, $t) ? "checked" : "") . '>' . $t . '</label>' . $after;
            }
        } */
    }
}

function render_radios( $name, $values = [], $attr = '', $label_first = 0, $pre = '', $post = '' ){
    render_options( 'radio', $name, $values, $attr, $label_first, $pre, $post );
}

function render_checkboxs( $name, $values = [], $pre = '', $post = '' ){
    render_options( 'checkbox', $name, $values, $pre, $post );
}

// Render Input Elements

function render_input( $type = 'text', $id, $label, $placeholder = '', $value = '', $attrs = '', $pre = '', $post = '' ){
    if( is_numeric( $pre ) ){
        $pre = '<div class="col-12 col-lg-'.$pre.'">';
        $post = '</div>';
    }
    $ph = !empty( $placeholder ) ? ' placeholder="'.$placeholder.'"' : '';
    $at = !empty( $attrs ) ? ' '.$attrs : '';
    $va = !empty( $value ) ? ' value="'.$value.'"' : '';
    switch( $type ){
        case 'textarea':
            $input = '<textarea id="'.$id.'" name="'.$id.'" "'.$ph.$at.'">'.$va.'</textarea>';
            break;
        default:
            $input = '<input type="'.$type.'" id="'.$id.'" name="'.$id.'" '.$at.$ph.$va.'>';
            break;
    }
    echo $pre .'<label for="'.$id.'">'.$label.'</label>'.$input.$post;
}

function render_inputs( $type = 'text', $ids, $attrs = '', $pre = '', $post = '' ){
    if( is_array( $ids ) ){
        foreach( $ids as $id ){
            $slug = isset($id[0]) && !empty($id[0]) ? $id[0] : '';
            $label = isset($id[1]) && !empty($id[1]) ? $id[1] : '';
            $ph = isset($id[2]) && !empty($id[2]) ? $id[2] : '';
            $value = isset($id[3]) && !empty($id[3]) ? $id[3] : '';
            render_input( $type, $slug, $label, $ph, $value, $attrs, $pre, $post );
        }
    }
}

// Render Input type text Element

function in_text( $id, $label, $placeholder = '', $value = '', $attrs = '', $pre = '', $post = '' ) {
    render_input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
}

function in_texts( $array, $attrs, $pre = '', $post = '' ){
    if( is_array( $array ) ){
        if( is_assoc( $array ) ){
            foreach( $array as $k => $v ){
                render_input( 'text', $k, $v, $attrs, $pre, $post );
            }
        } else {
            render_inputs( 'texts', $array, $attrs, $pre, $post );
        }
    }
}

// Checks if current page is as param

function page_is( $p ) {
    return is_array( $p ) ? in_array(PAGEPATH,$p) ? true : false : PAGEPATH == $p ? true : false;
}

// Checks if current page is in set of page names

function page_of( $p ) {
    $pages = explode( '/', PAGEPATH );
    return in_array( $p, $pages ) ? true : false;
}

// Renders a full <ul><li><a></a></li></ul> based on an array (Also will add classes for current pages)

function render_menu( $array, $prefix = '' ) {
    if( is_array( $array ) ){
        echo '<ul>';
        foreach( $array as $first_row ){

            $title = !empty( $first_row[1] ) && !is_array( $first_row[1] ) ? $first_row[1] : ucwords( $first_row[0] );

            $url = explode( '/', PAGEPATH );

            $first_row_class = $url[0] == $first_row[0] ? 'class="on"' : '';
            $first_li_class = $url[0] == $first_row[0] ? 'class="open"' : '';

            $sec_rows = '';
            if( !empty( $first_row[2] ) && is_array( $first_row[2] ) ){
                $sec_rows = $first_row[2];
            } else if( !empty( $first_row[1] ) && is_array( $first_row[1] ) ) {
                $sec_rows = $first_row[1];
            }

            $sec = '';
            if( !empty( $sec_rows ) && is_array( $sec_rows ) ){
                $sec .= '<i></i><ul>';
                foreach( $sec_rows as $sec_row ){
                    $sec_row_class = $url[count($url)-1] == $sec_row[0] ? 'class="on"' : '';
                    if( $sec_row[0] == '' && $url[count($url)-1] == $url[0] ){
                        $sec_row_class = 'class="on"';
                    }
                    $sec_title = !empty( $sec_row[1] ) && !is_array( $sec_row[1] ) ? $sec_row[1] : ucwords( $sec_row[0] );
                    $sec .= '<li><a href="'.APPURL.$prefix.$first_row[0].'/'.$prefix.$sec_row[0].'" '.$sec_row_class.'>'.$sec_title.'</a></li>';
                }
                $sec .= '</ul>';
            }
            echo '<li '.$first_li_class.'><a href="'.APPURL.$prefix.$first_row[0].'" '.$first_row_class.'>'.$title.'</a>'.$sec.'</li>';
            /*    $title = ucfirst( $l );
                $sls .= '<ul>';
                foreach( $t as $sl => $st ){
                    $link = APPURL.$l.'/'.$prefix.$sl;
                    if( PAGEPATH == $sl ) { $sc = 'class="on"'; $c = 'on'; } else { $sc = ''; }
                    $sls .= '<li><a href="'.$link.'" '.$sc.'>'.$st.'</a></li>';
                }
                $sls .= '</ul>';
            }
            $s = !empty($sls) ? $c.' sub' : $c.'';
            echo '<li><a href="'.APPURL.$prefix.$l.'" class="'.$c.'">'.$title.'</a>'.$sls.'</li>'; */
        }
        echo '</ul>';

        !empty( $title ) ? define( 'PAGET', $title ) : '';
    }
}

function editable_data( $data = [] ) {
    $final = [];
    foreach( $data as $k => $v ){
        $final[ str_replace('_','',strstr($k,'_')) ] = $v;
    }
    echo json_encode($final);
}

// False user sessions for development mode

function dev_sessions( $logged_in = true ) {
    if( APPDEBUG && $logged_in ) {
        $_SESSION['username'] = 'Developer Name';
        $_SESSION['user_name'] = 'Developer Name';
        $_SESSION['user_pic'] = 'Developer Name';
        $_SESSION['user_id'] = '856';
        $_SESSION['user_login'] = 'developer';
        if (!isset($_SESSION)) {
            session_set_cookie_params(0, '/', str_replace(' ', '_', APPNAME), false, false);
            @session_regenerate_id(true);
        }
    }
}

// Remove HTML Elements by PHP

function remove_elements( $html, $start, $end ) {
    $exist = 1;
    do {
        $si = strpos( $html, $start );
        $ei = strpos( $html, $end )+strlen( $end );
        $html = str_replace( substr( $html, $si, $ei - $si ), '', $html);
        $exist = strpos( $html, $start ) > 0 ? 1 : 0;
    } while ( $exist > 0 );
    return $html;
}

/* File Uploader
function media_upload() {
    include_once( COREPATH . 'core/components/media.php' );
}



/* function icon_picker() {
    include_once( COREPATH.'core/components/icons.php' );
}

function access_permitter() {
    include_once( COREPATH . 'core/components/file_access.php' );
} */