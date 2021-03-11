<?php

if( !defined( 'COREPATH' ) ) { exit(); }

// Returns a readable <title> for Header

function get_title( string $title = '', int $join_app_name = 1 ) {
    $add = $join_app_name ? ' - '.T( APPNAME ) : '';
    if( !empty( $title ) ) {
        echo '<title>'.$title.$add.'</title>';
        return;
    }
    if( !empty( PAGE ) ) {
        $p = PAGE == 'index' ? 'Welcome' : PAGE;
        $pp = explode('/',PAGEPATH);
        $p = is_numeric( $p ) ? $pp[count($pp)-1] : $p;
        echo '<title>'.T( ucwords( str_replace('-',' ', str_replace('_',' ', $p )) ) ) .$add.'</title>';
    } else {
        echo '<title>'.T( APPNAME ).'</title>';
    }
}

// Overrides the <title> in Header from a page

function set_title( $title ){
    echo '<script>$(document).ready(function() { document.title = "' . $title . ' - ' . APPNAME . '"; });</script>';
}


/**
 * AIO Error Log
 * @param string $log Log message
 * @param string $type Log type 'log', 'error'
 * @param string $line The link where the log is logged
 * @param string $file The file path which initiates the log
 * @param string $target Target
 * @author Shaikh <hey@shaikh.dev>
 */
function elog( $log, $type = 'log', $line = '', $file = '', $target = '' ){
    $log = is_array( $log ) ? json_encode( $log ) : $log;
    $log = is_object( $log ) ? var_dump( $log ) : $log;

    $data = $log . '<AIO>' . $type;
    $data .= $line !== '' ? '<AIO>' . $line : '';
    $data .= $file !== '' ? '<AIO>' . $file : '';
    $data .= $target !== '' ? '<AIO>' . $target : '';

    // Get dev users
    $debug = 0;
    if( defined( 'CONFIG' ) ) {
        $devs = get_config('dev');
        $devs = !empty($dev) ? explode(',', $devs) : [];
        $debug = isset($_SESSION['user_id']) && is_array($devs) && in_array($_SESSION['user_id'], $devs) ? 1 : 0;
    }

    // Log
    APPDEBUG || $debug ? error_log( $data . PHP_EOL ) : '';
}


// APPURL echo

function APPURL( $link ) {
    echo APPURL.$link;
}

function back_link( $url = './', $title = '' ) {
    echo '<a id="back" class="nico" href="'.$url.'">'.$title.'</a>';
}

/**
 * Check if current page is given page name
 * @param string $p page name
 * @return bool
 */
function page_is( string $p ): bool {
    return is_array( $p ) ? in_array(PAGEPATH,$p) : PAGEPATH == $p;
}

// Checks if current page is in set of page names

/**
 * Check if current page is or child of given page name
 * @param string $p page name
 * @return bool
 */
function page_of( string $p ): bool {
    $results = [];
    $page_ofs = explode( ',', $p );
    $pages = explode( '/', PAGEPATH );
    foreach( $page_ofs as $of ) {
        $results[] = in_array( $of, $pages );
    }
    return in_array( true, $results );
}


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