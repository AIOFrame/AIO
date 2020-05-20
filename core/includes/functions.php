<?php

if( !defined( 'COREPATH' ) ) { exit(); }

// Returns a readable <title> for Header

function get_title() {
    if( !empty( PAGE ) ) {
        $p = PAGE == 'ROOT' ? 'Welcome' : PAGE;
        $pp = explode('/',PAGEPATH);
        $p = is_numeric( $p ) ? $pp[count($pp)-1] : $p;
        echo T( ucwords( str_replace('-',' ', str_replace('_',' ', $p )) ) ) . ' - ' . T( APPNAME );
    } else {
        E( APPNAME );
    }
}

// Overrides the <title> in Header from a page

function set_title( $title ){

    echo '<script>$(document).ready(function() { document.title = "' . $title . ' - ' . APPNAME . '"; });</script>';

}

// Fetches stylesheet with options

function art( $color1 = '000', $color2 = '000' ) {

    $cache = get_config( 'cache' );

    $v = $cache ? '&v=' . round( time() / ( $cache * 60 ) ) : '';

    echo '<link rel="stylesheet" href="'.APPURL.'assets/art.php?fc='.$color1.'&sc='.$color2. $v . '">';
}

// Returns a complete <link> for stylesheet if file exists either in app or in core, prioritizes minified file

global $universal;
$universal = [ 'styles' => [], 'scripts' => [] ];

function get_style( $f, $params = [], $page_of = '' ) {

    $cache = get_config( 'cache' );
    $v = !empty( $cache ) ? '?v=' . round( time() / ( $cache * 60 ) ) : '';

    $p = '';
    if( is_array( $params ) ) {
        foreach( $params as $pk => $pv ) {
            $p .= '&'.$pk.'='.$pv;
        }
    }
    $v = !empty( $v ) ? $v . $p : $p;

    global $universal;

    if( !empty($f) && !in_array( $f, $universal['styles'] )) {
        $universal['styles'][] = $f;
        $af = APPPATH . 'assets/styles/' . $f;
        $cf = COREPATH . 'assets/styles/' . $f;
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
        } else if (file_exists($af)) {
            $url = $afl;
        } else if (file_exists($cf)) {
            $url = $cfl;
        } else if( strpos( $f, 'http') !== false ) {
            $url = $f . '';
        } else {
            elog( $f. ' style file not found' );
        }
        if ($page_of !== '') {
            echo page_of($page_of) && $url !== '' ? '<link rel="stylesheet" href="' . $url . $v . '">' : '';
        } else {
            echo $url !== '' ? '<link rel="stylesheet" href="' . $url . $v . '">' : '';
        }
    }
}

// Returns a complete <script> if file exists either in app or in core, prioritizes minified file

function get_script( $f, $page_of = '' ) {

    $cache = get_config( 'cache' );

    $v = $cache ? '?v=' . round( time() / ( $cache * 60 ) ) : '';

    global $universal;
    if( !empty($f) && !in_array( $f, $universal['scripts'] )) {
        $universal['scripts'][] = $f;
        $af = APPPATH . 'assets/scripts/' . $f;
        $cf = COREPATH . 'assets/scripts/' . $f;
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
        } else if( strpos( $f, 'http') !== false ) {
            $url = $f . '';
        } else {
            elog( $f. ' script file not found' );
        }
        if( $f == 'recaptcha' ) {
            $site_key = get_config('recaptcha_site_key');
            if( !empty( $site_key ) ) {
                $url = 'https://www.google.com/recaptcha/api.js?render='.$site_key;
                echo '<script data-recaptcha src="' . $url . '"></script>';
                return;
            }
        }
        if( $page_of !== '' ){
            echo page_of( $page_of ) && $url !== '' ? '<script src="' . $url . $v . '"></script>' : '';
        } else {
            echo $url !== '' ? '<script src="' . $url . $v . '"></script>' : '';
        }

    }
}

// Includes Font

function font( $array = [] ){
    $weights = isset( $array[1] ) && !empty( $array[1] ) ? $array[1] : '400';
    $fonts[] = $array[0].':'.$weights;
    echo !empty( $fonts ) ? '<link rel="stylesheet" href="' . APPURL . 'assets/fonts.php?'.APPDIR.'='. implode( '|', $fonts ) .'">' : '';
}

function fonts( $array = [] ){
    /* $f = '';
    if( file_exists( COREPATH . 'assets/fonts/' . $name . '.min.css' ) ){
        $f = APPURL . 'assets/fonts/' . $name . '.min.css';
    } else if ( file_exists( COREPATH . 'assets/fonts/' . $name . '.css' ) ){
        $f = APPURL . 'assets/fonts/' . $name . '.css';
    } else if ( file_exists( COREPATH . 'apps/' . APPDIR . '/assets/fonts/' . $name . '.min.css' ) ){
        $f = APPURL . 'apps/' . APPDIR . '/assets/fonts/' . $name . '.min.css';
    } else if ( file_exists( COREPATH . 'apps/' . APPDIR . '/assets/fonts/' . $name . '.css' ) ) {
        $f = APPURL . 'apps/' . APPDIR . '/assets/fonts/' . $name . '.css';
    }
    echo $f !== '' ? '<link rel="stylesheet" href="' . $f . '">' : '';*/
    //?family=Titillium+Web:300,400
    $fonts = [];
    foreach( $array as $f ){
        $weights = isset( $f[1] ) && !empty( $f[1] ) ? $f[1] : '400';
        $fonts[] = $f[0].':'.$weights;
    }
    echo !empty( $fonts ) ? '<link rel="stylesheet" href="' . APPURL . 'assets/fonts.php?'.APPDIR.'='. implode( '|', $fonts ) .'">' : '';
}

//

function icons() {

    echo '<link rel="stylesheet" href="' .APPURL . 'assets/icons.php?'.APPDIR.'=1">';

}

function icons_old( ){
    echo '<link rel="stylesheet" href="' .APPURL . 'apps/' . APPNAME . '/assets/styles/icons.css">';
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

// Read Config

function get_config( $name = '' ) {

    if( file_exists( APPPATH . 'config.php' ) ) {

        $c = include( APPPATH . 'config.php' );

        if( is_array( $c ) && isset( $c[ $name ] ) ){

            return $c[ $name ];

        } else if ( is_array( $c ) && $name == '' ) {

            return $c;

        }

    }

}

// Write Config

function set_config( $name, $value ) {
    $c = [];
    if( file_exists( APPPATH . '/config.php' ) ) {
        $c = include( APPPATH . '/config.php' );
    };
    $c[ $name ] = $value;
    $config_php = fopen( APPPATH . '/config.php', 'w' );
    $config_text = '<?php'.PHP_EOL.'return ['.PHP_EOL;
    foreach( $c as $k => $v ) {
        if( !is_array( $v ) ) {
            $config_text .= is_numeric( $v ) ? '    "' . $k . '" => ' . $v . ',' . PHP_EOL : '    "' . $k . '" => "' . $v . '",' . PHP_EOL;
        } else {
            $config_text .= '    "' . $k . '" => [ ';
            foreach( $v as $sk => $sv ) {
                if( is_array( $sv ) ) {
                    if( is_assoc( $sv ) ) {
                        $config_text .= '"' . $sk . '" => [ "';
                        foreach( $sv as $tk => $tv ) {
                            $config_text .= '"' . $tk . '" => "' . $tv . '", ';
                        }
                        $config_text .= '" ],' . PHP_EOL;
                    } else {
                        $config_text .= '"' . $sk . '" => [ "' . implode( '", "', $sv ) . '" ],' . PHP_EOL;
                    }
                } else {
                    $config_text .= '"' . $sk . '" => "' . $sv . '", ';
                }
            }
            $config_text .= ' ],' . PHP_EOL;

            //$config_text .=
        }
    }
    $config_text .= '];';
    fwrite( $config_php, $config_text );
}

// Loops an array of file names for .min.css / .css extension and returns if exists

function get_styles( $ar = '', $page_of = '' ) {
    if( !empty( $ar ) ){
        foreach( $ar as $f ){
            if( $page_of !== '' ){
                page_of( $page_of ) ? get_style( $f ) : '';
            } else {
                get_style( $f );
            }
        }
    }
}

// Loop an array for file names for .min.js / .js extension and returns if exists

function get_scripts( $ar = '', $page_of = '' ) {
    if( !empty( $ar ) ){
        foreach( $ar as $f ){
            if( $page_of !== '' ){
                page_of( $page_of ) ? get_script( $f ) : '';
            } else {
                get_script( $f );
            }
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

//

function device() {

    if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'iPod' ) ) {

        return 'iPod';

    } else if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'iPhone' ) ) {

        return 'iPhone';

    } else if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'iPad' ) ) {

        return 'iPad';

    } else if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'Android' ) ) {

        return 'Android';

    } else if( stripos( $_SERVER['HTTP_USER_AGENT'] , 'webOS' ) ) {

        return 'webOS';

    } else {

        return '';

    }

}

//

function is_ios() {

    $d = device();

    if( $d == 'iPad' || $d == 'iPhone' || $d == 'iPod' ) {

        return true;

    } else {

        return false;

    }

}

// Error Logs if APP is under DEBUG Mode

function elog( $log, $type = 'log', $line = '', $file = '', $target = '' ){
    $log = is_array( $log ) ? json_encode( $log ) : $log;
    $log = is_object( $log ) ? var_dump( $log ) : $log;

    $data = $log . '<AIO>' . $type;
    $data .= $line !== '' ? '<AIO>' . $line : '';
    $data .= $file !== '' ? '<AIO>' . $file : '';
    $data .= $target !== '' ? '<AIO>' . $target : '';

    // Get dev users
    $devs = get_config( 'dev' );
    $devs = !empty( $dev ) ? explode( ',', $devs ) : [];
    $debug = isset( $_SESSION['user_id'] ) && is_array( $devs ) && in_array( $_SESSION['user_id'], $devs ) ? 1 : 0;

    // Log
    APPDEBUG || $debug ? error_log( $data . PHP_EOL ) : '';
}

// Gets any file from components folder

function comp_exists( $n, $dir ) {
    $ns = explode('/',str_replace('.php','',$n));
    $x = 1;
    $fl = APPPATH . '/' . $dir . '/';
    foreach($ns as $n){
        if($x == count($ns)){
            $fl .= $n . '.php';
        } else {
            $fl .= $n . '/';
        }
        $x++;
    }
    return file_exists( $fl ) ? $fl : false;
}

function get_comp( $n ){
    comp_exists( $n, 'components' ) ? include( comp_exists( $n, 'components' ) ) : '';
}

// Gets any file from modal

function modal( $n ){
    comp_exists( $n, 'modals' ) ? include( comp_exists( $n, 'modals' ) ) : '';
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

// APPURL echo

function APPURL( $link ) {
    echo APPURL.$link;
}

function back_link( $url = './' ) {
    echo '<a id="back" class="nico" href="'.$url.'"></a>';
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
                if( is_array( $s ) && in_array( $k, $s ) ) { $sel = 'selected'; } else if( $k == $s ) { $sel = 'selected'; } else { $sel = ''; }
                echo '<option value="' . $k . '" ' . $sel . '>' . T($t) . '</option>';
            }
            !empty($sel) ? elog($s) : '';
        } else {
            foreach ($d as $t) {
                if( is_array( $s ) && in_array( $t, $s ) ) { $sel = 'selected'; } else if( $t == $s ) { $sel = 'selected'; } else { $sel = ''; }
                echo '<option value="' . $t . '" ' . $sel . '>' . T($t) . '</option>';
            }

        }
    } else if( is_numeric( $d ) ){
        for($x=0;$x<=$d;$x++){
            echo '<option value="' . $x . '" ' . ($x == $s ? "selected" : "") . '>' . T($x) . '</option>';
        }
    }
}

function render_options( $type = 'radio', $name, $values = [], $checked = '', $attr = '', $label_first = 0, $pre = '', $post = '' ) {
    if( is_array( $values ) ) {
        $type = $type == 'radio' ? 'type="radio"' : 'type="checkbox"';
        $valued = is_assoc( $values ) ? true : false; $x = 0;
        if( is_numeric( $pre ) ){
            $pre = $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
            $post = '</div>';
        }
        $uq = rand(1,999);
        if( is_assoc( $values ) ) {
            foreach ($values as $val => $title) {
                $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                $value = $valued ? $val : $title;
                $c = $value == $checked ? 'checked' : '';
                if ($label_first) {
                    echo $pre . '<label for="' . $k . '">' . $title . '</label><input ' . $attr . ' ' . $type . ' name="' . $name . '" id="' . $k . '" value="' . $value . '" '. $c .' >' . $post;
                } else {
                    echo $pre . '<input ' . $attr . ' ' . $type . ' name="' . $name . '" id="' . $k . '" value="' . $value . '" '. $c .' ><label for="' . $k . '">' . $title . '</label>' . $post;
                }
                $x++;
            }
        } else {
            foreach ($values as $val) {
                $k = $valued ? $val . $x . '_' . $uq : str_replace(' ', '', $name) . $x;
                $title = is_array($val) && !empty($val[1]) ? $val[1] : $val;
                $value = is_array($val) ? $val[0] : $val;
                $c = $value == $checked ? 'checked' : '';
                if ($label_first) {
                    echo $pre . '<label for="' . $k . '">' . $title . '</label><input ' . $attr . ' ' . $type . ' name="' . $name . '" id="' . $k . '" value="' . $value . '" '.$c.'>' . $post;
                } else {
                    echo $pre . '<input ' . $attr . ' ' . $type . ' name="' . $name . '" id="' . $k . '" value="' . $value . '" '.$c.'><label for="' . $k . '">' . $title . '</label>' . $post;
                }
                $x++;
            }
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

function render_radios( $name, $values = [], $checked = '', $attr = '', $label_first = 0, $pre = '', $post = '' ){
    render_options( 'radio', $name, $values, $checked, $attr, $label_first, $pre, $post );
}

function render_checkboxs( $name, $values = [], $checked = '', $attr = '', $label_first = 0, $pre = '', $post = '' ){
    render_options( 'checkbox', $name, $values, $checked, $attr, $label_first, $pre, $post );
}

// Render Input Elements

function render_input( $type, $id, $label, $placeholder = '', $value = '', $attrs = '', $pre = '', $name = '', $post = '' ){
    $type = $type == '' ? 'text' : $type;
    if( is_numeric( $pre ) ){
        $pre =  $pre == 0 ? '<div class="col">' : '<div class="col-12 col-lg-'.$pre.'">';
        $post = '</div>';
    }
    $ph = $placeholder !== '' ? ' placeholder="'.$placeholder.'"' : '';
    $at = $attrs !== '' ? ' '.$attrs : '';
    $va = $value !== '' ? ' value="'.$value.'"' : '';
    $n = $name !== '' ? $name : $id;
    switch( $type ){
        case 'textarea':
            $input = '<textarea id="'.$id.'" name="'.$n.'" '.$ph.$at.'>'.$va.'</textarea>';
            break;
        case 'slide':
        case 'toggle':
            $input = '<div><input type="hidden" id="'.$id.'" name="'.$n.'" '.$at.$ph.$va.'>';
            $ch = $value == 'true' || $value == '1' ? 'checked' : '';
            $input .= '<input type="checkbox" data-check="#'.$id.'" class="slide m" '.$ch.'></div>';
            break;
        default:
            $input = '<input type="'.$type.'" id="'.$id.'" name="'.$n.'" '.$at.$ph.$va.'>';
            break;
    }
    echo $pre;
    echo !empty( $label ) ? '<label for="'.$id.'">'.T($label).'</label>' : '';
    echo $input.$post;
}

function render_inputs( $type = 'text', $array, $attrs = '', $pre = '', $post = '' ){
    if( is_array( $array ) ){
        foreach( $array as $id ){
            $slug = isset($id[0]) && $id[0] !== '' ? $id[0] : '';
            $label = isset($id[1]) && $id[1] !== '' ? $id[1] : '';
            $place = isset($id[2]) && $id[2] !== '' ? $id[2] : '';
            $value = isset($id[3]) && $id[3] !== '' ? $id[3] : '';
            $attr = isset($id[4]) && $id[4] !== '' ? $id[4] : '';
            render_input( $type, $slug, $label, $place, $value, $attr.' '.$attrs, $pre, $post );
        }
    }
}

// Render Input type text Element

function in_text( $id, $label, $placeholder = '', $value = '', $attrs = '', $pre = '', $post = '' ) {
    render_input( 'text', $id, $label, $placeholder, $value, $attrs, $pre, $post );
}

function in_texts( $array, $attrs = '', $pre = '', $post = '' ){
    if( is_array( $array ) ){
        if( is_assoc( $array ) ){
            foreach( $array as $k => $v ){
                render_input( 'text', $k, $v, $attrs, $pre, $post );
            }
        } else {
            render_inputs( 'text', $array, $attrs, $pre, $post );
        }
    }
}

// Checks if current page is as param

function page_is( $p ) {
    return is_array( $p ) ? ( in_array(PAGEPATH,$p) ? true : false ) : ( PAGEPATH == $p ? true : false );
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
            $page_class = strtolower(str_replace(' ','_',$title));
            $first_row_class = PAGEPATH == $first_row[0] ? 'class="on '.$page_class.'"' : 'class="'.$page_class.'"';
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
                    $sec_title = !empty( $sec_row[1] ) && !is_array( $sec_row[1] ) ? $sec_row[1] : ucwords( $sec_row[0] );
                    $sec_row_class = $url[count($url)-1] == $sec_row[0] ? 'class="on '.strtolower(str_replace(' ','_',$sec_title)).'"' : '';
                    if( $sec_row[0] == '' && $url[count($url)-1] == $url[0] ){
                        $sec_row_class = 'class="on '.strtolower(str_replace(' ','_',$sec_title)).'"';
                    }
                    $sec .= '<li><a href="'.APPURL.$prefix.$first_row[0].'/'.$prefix.$sec_row[0].'" '.$sec_row_class.'>'.T($sec_title).'</a></li>';
                }
                $sec .= '</ul>';
            }
            echo '<li '.$first_li_class.'><a href="'.APPURL.$prefix.$first_row[0].'" '.$first_row_class.'>'.T($title).'</a>'.$sec.'</li>';
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

        !empty( $title ) && !defined( 'PAGET' ) ? define( 'PAGET', $title ) : '';
    }
}

function editable_data( $data = [], $remove = '' ) {
    $final = [];
    $remove = explode( ',', $remove );
    foreach( $data as $k => $v ){
        $k = strpos( $k, '_') !== false ? ltrim( strstr($k,'_'), '_' ) : $k;
        if( $k == 'id' ) {
            $cry = CRYPTO::initiate();
            $final[ $k ] = $cry->encrypt( $v );
        } else if( !in_array( $k, $remove ) ){
            $final[ $k ] = $v;
        }
    }
    echo json_encode( $final );
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
        $html = str_replace( PHP_EOL, '', str_replace( substr( $html, $si, $ei - $si ), '', $html) );
        $exist = strpos( $html, $start ) > 0 ? 1 : 0;
    } while ( $exist > 0 );
    return $html;
}

function get_string_between( $string, $start, $end ){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

function encrypt_array( $array ){
    $cry = Crypto::initiate();
    return $cry->encrypt(serialize($array));
}

function eis( $post = '' ) {
    echo isset( $post ) && !empty( $post ) ? $post : '';
}

function P( $string ) {
    return isset( $_POST[$string] ) ? $_POST[$string] : 0;
}

function G( $string ) {
    return isset( $_GET[$string] ) ? $_GET[$string] : 0;
}

function notify( $message ) {
    echo '<script>$(document).ready(function(){ notify("' . $message . '"); });</script>';
}

function UI( $ui, $array = [] ) {

    $f = COREPATH . 'core/ui/' . $ui . '.php';

    if( file_exists( $f ) ) {

        if( !empty( $array ) ) {
            global $ui_params;
            $ui_params = $array;
        }
        include( $f );

    }

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