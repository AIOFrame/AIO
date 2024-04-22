<?php
$r = substr(md5(microtime()),rand(0,26),15);
$cache_ops = [ 0 => 'Don\'t Cache', 1 => '1 Minute', 5 => '5 Minutes', 10 => '10 Minutes', 15 => '15 Minutes', 20 => '20 Minutes', 30 => '30 Minutes', 60 => '1 Hour', 120 => '2 Hours', 360 => '6 Hours', 1440 => '1 Day', 2880 => '2 Days', 10080 => '1 Week', 42000 => '1 Month' ];
$font_ops = '';
$weights = [ 'Hairline' => 100, 'Thin' => 100, 'ExtraLight' => 200, 'Light' => 300, 'Regular' => 400, 'Medium' => 500, 'SemiBold' => 600, 'Bold' => 700, 'Heavy' => 700, 'ExtraBold' => 800, 'Black' => 900 ];
foreach( glob( ROOTPATH . '/assets/fonts/*', GLOB_ONLYDIR ) as $f ){
    $fn = str_replace( ROOTPATH . '/assets/fonts/', '', $f );
    $font_ops .= '<optgroup label="'.$fn.'">';
    $ws = [];
    foreach( glob( $f . '/*.ttf' ) as $fw ){
        $fwn = str_replace( ROOTPATH . '/assets/fonts/' . $fn . '/', '', $fw );
        $fwn = str_replace( $fn . '-', '', $fwn );
        $fwn = str_replace( '.ttf', '', $fwn );
        $font_ops .= '<option value="'.$fn.'_'.$weights[$fwn].'">'.$fwn.'</option>';
    }
    $font_ops .= '</optgroup>';
}
$arts = [ 'a11y', 'accordion', 'alerts', 'buttons', 'cards', 'icons', 'inputs', 'modal', 'sizes', 'table', 'tabs', 'tips' ];
$int_ops = [
    [ 'v' => 'a11y', 'n' => 'AIO A11y', 'd' => 'Accessibility scripts' ],
    [ 'v' => 'ui_reset', 'n' => 'AIO Reset CSS', 'd' => 'Stylesheet that has custom reset css to begin with' ],
    [ 'v' => 'ux_core', 'n' => 'AIO Core JS', 'd' => 'Scripts for user experience, like hide show div etc.' ],
    [ 'v' => 'fps', 'n' => 'AIO Full Page JS', 'd' => 'Full page scrolling script' ],
    [ 'v' => 'ui_micro', 'n' => 'AIO Micro CSS', 'd' => 'Minified css tags, micro css over-writes, ex: add class dn for display:none' ],
];
$ext_ops = [
    'bootstrap'=>'Bootstrap',
    'bootstrap_grid'=>'Bootstrap Grid',
    'select2'=>'Select 2',
    'datepicker'=>'Air Datepicker',
    'chart'=>'Chart JS',
    'jquery'=>'jQuery',
    'jquery_ui'=>'jQuery UI',
    'clipboard'=>'Clipboard JS',
    'moment'=>'Moment JS',
    'tilt'=>'Tilt JS',
    'bot_ui'=>'Bot UI JS',
];

// Steps Data
global $steps;
$steps = [
    [ 'title' => 'Basic Configuration', 'type' => 'settings', 'fields' => [
        [ 'i' => 'name', 'n' => 'Name your Web App', 'p' => 'Ex: Food Delivery, Events, CRM, '.ucfirst( APPDIR ).' App, '.ucfirst( APPDIR ).' etc.', 'v' => APPDIR, 'c' => 12, 'a' => 'required' ],
        [ 't' => 'slide', 'i' => 'force_ssl', 'n' => 'Do you want to force SSL ?', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 6 ],
        [ 't' => 'slide', 'i' => 'debug', 'n' => 'Do you prefer debug mode ?', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 6 ],
        [ 't' => 'slide', 'i' => 'git_ignore', 'n' => 'Create a default .gitignore ?', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 6 ],
        [ 'i' => 'key', 'n' => 'Set a key for basic encryption', 'p' => 'Ex: '.$r, 'v' => $r, 'c' => 6, 'a' => 'required' ],
        [ 'i' => 'log', 'n' => 'Error log page path', 'p' => 'Ex: /log', 'v' => '/log', 'c' => 6, 'a' => 'required' ],
    ] ],
    [ 'title' => 'UI & UX', 'fields' => [
        [ 't' => 'select2', 'i' => 'cache', 'n' => 'Cache styles & scripts', 'o' => $cache_ops, 'c' => 4, 'a' => 'required' ],
        [ 't' => 'select2', 'i' => 'fonts', 'n' => 'Select fonts & weights', 'o' => $font_ops, 'c' => 12, 'a' => 'required', 'm' => 1 ],
        [ 't' => 'select2', 'i' => 'internal', 'n' => 'Choose AIO styles / scripts', 'o' => $int_ops, 'c' => 6, 'a' => 'required', 'm' => 1 ],
        [ 't' => 'select2', 'i' => 'external', 'n' => 'Choose 3rd party styles / scripts', 'o' => $ext_ops, 'c' => 6, 'a' => 'required', 'm' => 1 ],
        [ 't' => 'color', 'i' => 'primary_color', 'n' => 'Primary color - Light', 'c' => 3, 'a' => 'required', 'view' => '[data-key=primary_color]' ],
        [ 't' => 'color', 'i' => 'secondary_color', 'n' => 'Secondary color - Light', 'c' => 3, 'a' => 'required', 'view' => '[data-key=secondary_color]' ],
        [ 't' => 'color', 'i' => 'primary_color_dark', 'n' => 'Primary color - Dark', 'c' => 3, 'a' => 'required', 'view' => '[data-key=primary_color_dark]' ],
        [ 't' => 'color', 'i' => 'secondary_color_dark', 'n' => 'Secondary color - Dark', 'c' => 3, 'a' => 'required', 'view' => '[data-key=secondary_color_dark]' ],
        [ 't' => 'color', 'i' => 'color_light', 'n' => 'Text color - Light', 'c' => 3, 'a' => 'required', 'view' => '[data-key=color_light]' ],
        [ 't' => 'color', 'i' => 'filled_color_light', 'n' => 'Text color on theme - Light', 'c' => 3, 'a' => 'required', 'view' => '[data-key=filled_color_light]' ],
        [ 't' => 'color', 'i' => 'color_dark', 'n' => 'Text color - Dark', 'c' => 3, 'a' => 'required', 'view' => '[data-key=color_dark]' ],
        [ 't' => 'color', 'i' => 'filled_color_dark', 'n' => 'Text color on theme - Dark', 'c' => 3, 'a' => 'required', 'view' => '[data-key=filled_color_dark]' ],
        [ 'i' => 'styles', 'n' => 'Create stylesheets', 'p' => 'Ex: users, contacts etc.', 'c' => 6 ],
        [ 'i' => 'scripts', 'n' => 'Create scripts', 'p' => 'Ex: users, contacts etc.', 'c' => 6 ],
    ] ],
    [ 'title' => 'Database', 'fields' => [

    ] ],
    [ 'title' => 'Features', 'fields' => [

    ] ],
    [ 'title' => 'Content Management', 'fields' => [

    ] ],
    [ 'title' => 'Data Designer', 'fields' => [

    ] ],
];
function builder_step( int $step, array $data ): void {
    global $steps;
    $nums = [ 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight' ];
    $step = $step + 1;
    $array_step = $step - 1;
    $num = $nums[ $array_step ] ?? '';
    //skel( $steps );
    echo '<div class="setup '.$num.' '.($step == 1 ? 'on': '').'"><div class="content"><div class="head"><h2>'.T( $data['title'] ?? '' ).'</h2><h4>'.T('STEP '.$step.' of 5').'</h4></div><div class="data">';
    $f = new FORM();
    $f->form( $data['fields'] ?? [], 'row', 'setup '. $num );
    echo '</div></div><nav>';
    echo $step !== 1 ? '<div class="p" data-prev><div class="mat-ico">arrow_circle_left</div></div>' : '';
    echo $step !== count( $steps ) ? '<div class="n" data-next><div class="mat-ico">arrow_circle_right</div></div>' : '';
    echo '</nav></div>';
}

function build_app( array $config ): void {
    // Do Final
    /*unset( $p['step'] );
    $nl = PHP_EOL.'    ';
    $c = "<?php
return [
'name' => '".$p['appname']."',".$nl;
    $c .= $p['debug'] == 'on' ? "'debug' => true,".$nl : "'debug' => false,".$nl;
    $c .= !empty( $p['cache'] ) ? "'cache' => ".$p['cache'].",".$nl : "";
    $c .= !empty( $p['key'] ) ? "'key' => '".$p['key']."',".$nl : "";

    $func = ''; $funs = [];
    if( !empty( $p['f_int'] ) ) {
        $ints = explode( ', ', $p['f_int'] );
        $funs = is_array( $ints ) ? array_merge( $funs, $ints ) : '';
        if( !empty( $ints ) && is_array( $ints ) ) {
            $func .= "    'logged_in' => ['".implode("','",$ints)."'],".$nl;
        }
    }
    if( !empty( $p['f_ext'] ) ) {
        $exts = explode( ', ', $p['f_ext'] );
        $funs = is_array( $exts ) ? array_merge( $funs, $exts ) : '';
        if( !empty( $exts ) && is_array( $exts ) ) {
            $func .= "    'logged_out' => ['".implode("','",$exts)."'],".$nl;
        }
    }
    if( !empty( $func ) ) {
        $c .= "'functions' => [ ".$nl.$func . "],".$nl;
    }

    if( !empty( $p['type'] ) && !empty( $p['server'] ) && !empty( $p['base'] ) && !empty( $p['user'] ) && !empty( $p['pass'] ) ) {
        $c .= "'data' => [ 'type' => '".$p['type']."', 'server' => '".$p['server']."', 'base' => '".$p['base']."', 'user' => '".$p['user']."', 'pass' => '".$p['pass']."' ]".PHP_EOL;
    }

    $c .= '];';

    // Make App directory
    !file_exists( ROOTPATH . 'apps/' . $appdir ) ? mkdir( ROOTPATH . 'apps/' . $appdir, 0777, true ) : '';

    // Make internal directories
    $dirs = [ 'assets' => [ 'icons', 'images', 'scripts', 'styles' ], 'components', 'functions', 'modals', 'pages', 'storage' ];
    foreach( $dirs as $dk => $dv ) {
        if( is_array( $dv ) ) {
            foreach( $dv as $d ) {
                !file_exists( ROOTPATH . 'apps/' . $appdir . '/' . $dk . '/' . $d ) ? mkdir( ROOTPATH . 'apps/' . $appdir . '/' . $dk . '/' . $d, 0777, true ) : '';
            }
        } else {
            !file_exists( ROOTPATH . 'apps/' . $appdir . '/' . $dv ) ? mkdir( ROOTPATH . 'apps/' . $appdir . '/' . $dv, 0777, true ) : '';
        }
    }

    // Sort styles and scripts
    $styles = $scripts = $header_func = '';
    $ins = !empty( $p['ints'] ) ? unserialize( unserialize( $p['ints'][0] )[0] ) : '';
    $ins = is_array( $ins ) ? $ins : [];
    $exs = !empty( $p['exts'] ) ? unserialize( unserialize( $p['exts'][0] )[0] ) : '';
    $exs = is_array( $exs ) ? $exs : [];
    $uix = array_merge( $ins, $exs );

    // Create custom styles and scripts
    if( isset( $p['styles'] ) && !empty( $p['styles'] ) ) {
        $style_files = explode( ',', $p['styles'] );
        $style_files = is_array( $style_files ) ? $style_files : [];
        foreach( $style_files as $sf ) {
            $style_file = fopen( ROOTPATH . 'apps/' . $appdir . '/assets/styles/' . $sf . '.css', 'w' );
            fclose( $style_file );
            $uix[] = 'ui_'.$sf;
        }
    }
    if( isset( $p['scripts'] ) && !empty( $p['scripts'] ) ) {
        $script_files = explode( ',', $p['scripts'] );
        $script_files = is_array( $script_files ) ? $script_files : [];
        foreach( $script_files as $sf ) {
            $script_file = fopen( ROOTPATH . 'apps/' . $appdir . '/assets/scripts/' . $sf . '.js', 'w' );
            fclose( $script_file );
            $uix[] = 'ux_'.$sf;
        }
    }

    // Create header component
    $html = '<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport"
      content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title><?php get_title(); ?></title>'.$nl;
    $jquery = in_array( 'ux_jquery', $uix ) ? 1 : 0;
    foreach( $uix as $i ) {
        $s = explode( '_', $i );
        if( $s[0] == 'ui' || $s[0] == 'b' ) {
            $styles .= "'".$s[1]."',";
        } else if( $s[0] == 'ux' || $s[0] == 'b' ) {
            $scripts .= $s[1] !== 'jquery' ? "'".$s[1]."'," : '';
        } else {
            $header_func .= $s[0].';'.$nl;
        }
    }
    $html .= '<?php' . $nl;
    $html .= !empty( $styles ) ? 'get_styles(['.$styles.'PAGEPATH]);' . $nl : '';
    $html .= isset( $jquery ) && $jquery ? "get_scripts(['jquery']);" . $nl : '';
    $html .= $header_func;
    $html .= '?>'.PHP_EOL.'</head>'.PHP_EOL.'<body <?php body_class(); ?>>'.$nl.'<header>'.$nl.$nl.'</header>';

    $head = fopen( ROOTPATH . 'apps/' . $appdir . "/components/head.php", "w");
    if( $head ) {
        fwrite( $head, $html );
        fclose( $head );
    }

    // Make footer component
    $foot = fopen( ROOTPATH . 'apps/' . $appdir . "/components/foot.php", "w");
    if( $foot ) {
        $scripts = !empty( $scripts ) ? '    <?php get_scripts(['.$scripts.'PAGEPATH]); ?>' . $nl : '';
        fwrite( $foot, $scripts.'</body>'.PHP_EOL.'</html>' );
        fclose( $foot );
    }

    // Create pages
    $pages = json_decode( $p['pages'] );
    $page_html_pre = "<?php get_comp('head'); ?>".$nl.'<h1>';
    $page_html_post = '</h1>'.PHP_EOL."<?php get_comp('foot'); ?>";
    $pages[] = (object) [ 'page_1' => 'index' ];
    if( is_array( $pages ) ) {
        foreach( $pages as $p ) {
            if( isset( $p->script_3 ) && $p->script_3 == 1 ) {
                $script_file = fopen( ROOTPATH . 'apps/' . $appdir . '/assets/scripts/' . $p->page_1 . '.js', 'w' );
                fclose( $script_file );
            }
            if( isset( $p->style_4 ) && $p->style_4 == 1 ) {
                $style_file = fopen( ROOTPATH . 'apps/' . $appdir . '/assets/styles/' . $p->page_1 . '.css', 'w' );
                fclose( $style_file );
            }
            $page_file = fopen( ROOTPATH . 'apps/' . $appdir . '/pages/' . $p->page_1 . '.php', 'w' );
            fwrite( $page_file, $page_html_pre . $p->page_1 . $page_html_post );
            fclose( $page_file );
        }
    }

    // Create function
    if( is_array( $funs ) ) {
        foreach( $funs as $f ) {
            $func_file = fopen( ROOTPATH . 'apps/' . $appdir . '/functions/' . $f . '.php', 'w' );
            fwrite( $func_file, '<?php'.PHP_EOL.$nl );
            fclose( $func_file );
        }
    }

    // Make config file
    $con = fopen( ROOTPATH . 'apps/' . $appdir . "/config.php", "w");
    if( $con && !empty( $c ) ) {
        fwrite( $con, $c );
        fclose( $con );
    }
    echo '<p>Setup Complete :)</p><br/><form method="post"><button>Reload</button></form>';*/
}