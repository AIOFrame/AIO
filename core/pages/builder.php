<?php
get_comp('builder/head');
$p = $_POST;
$cry = Encrypt::initiate();
$form = new FORM();
?>
    <header>
        <div class="logo"></div>
        <div class="options_toggle"></div>
        <div class="options">
            <div class="ico" data-on=".languages"><div class="mat-ico">language</div><i class="tip">Change Language</i></div>
            <div class="ico dark" data-dark><div class="mat-ico">dark_mode</div><i class="tip">Toggle Dark Mode</i></div>
        </div>
    </header>
    <aside>
        <div class="languages scroll">
            <input type="search" class="filter_lang" placeholder="<?php E('Search'); ?>">
            <div class="list">
                <?php
                $langs = ['en'=>'English','ar'=>'العربية (Arabic)','zh'=>'中文 (Chinese Simplified)','fr'=>'Français (French)','hi'=>'हिंदी (Hindi)','in'=>'Bahasa Indonesian','ja'=>'日本語 (Japanese)','pr'=>'Português (Portuguese)','ru'=>'русский (Russian)','es'=>'Español (Spanish)'];
                if( is_array( $langs ) && !empty( $langs ) )
                    foreach( $langs as $k => $v )
                        echo '<div data-lang="' . $k . '">' . $v . '</div>';
                ?>
            </div>
        </div>
        <div class="filters"></div>
    </aside>
    <article data-off=".scroll">
        <div class="setup_wrap" data-tabs>
            <div class="setup one on">
                <div class="content">
                    <div class="head">
                        <h2><?php E('STEP 1 of 5'); ?></h2>
                        <h3><?php E('Basic Configuration'); ?></h3>
                    </div>
                    <div class="data">
                        <div class="row">
                            <?php get_comp('builder/one') ?>
                        </div>
                    </div>
                </div>
                <nav>
                    <div class="n" data-next><div class="mat-ico">arrow_circle_right</div></div>
                </nav>
            </div>
            <div class="setup two">
                <div class="content">
                    <div class="head">
                        <h2>STEP 2 OF 5</h2>
                        <h3>UI & UX</h3>
                    </div>
                    <div class="data">
                        <div class="q">
                            <div class="row">
                                <div class="col-4">
                                    <label for="cache"><span>Cache styles & scripts</span> <i class="tip">Caching styles and scripts will load fresh file after set minutes.</i></label>
                                    <select name="cache" id="cache">
                                        <option value="0" selected>Don't Cache</option>
                                        <option value="1">1 Min</option>
                                        <option value="2">2 Min</option>
                                        <option value="5">5 Min</option>
                                        <option value="10">10 Min</option>
                                        <option value="15">15 Min</option>
                                        <option value="20">20 Min</option>
                                        <option value="30">30 Min</option>
                                        <option value="60">1 Hour</option>
                                        <option value="120">2 Hours</option>
                                        <option value="240">4 Hours</option>
                                        <option value="360">6 Hours</option>
                                        <option value="480">8 Hours</option>
                                        <option value="600">10 Hours</option>
                                        <option value="1440">1 Day</option>
                                        <option value="2880">2 Days</option>
                                        <option value="5760">4 Days</option>
                                        <option value="10080">1 Week</option>
                                    </select>
                                </div>
                                <?php
                                $form->text('color_1','Primary Gradient Start','','','data-color-picker',4);
                                $form->text('color_2','Primary Gradient End','','','data-color-picker',4);
                                ?>
                            </div>
                        </div>
                        <div class="q">
                            <label for="fonts">Select Fonts</label>
                            <select name="fonts" id="fonts" class="select2" multiple data-close="false">
                                <?php
                                $weights = [ 'Hairline' => 100, 'Thin' => 100, 'ExtraLight' => 200, 'Light' => 300, 'Regular' => 400, 'Medium' => 500, 'SemiBold' => 600, 'Bold' => 700, 'Heavy' => 700, 'ExtraBold' => 800, 'Black' => 900 ];
                                foreach( glob( ROOTPATH . '/assets/fonts/*', GLOB_ONLYDIR ) as $f ){
                                    $fn = str_replace( ROOTPATH . '/assets/fonts/', '', $f );
                                    echo '<optgroup label="'.$fn.'">';
                                    $ws = [];
                                    foreach( glob( $f . '/*.ttf' ) as $fw ){
                                        $fwn = str_replace( ROOTPATH . '/assets/fonts/' . $fn . '/', '', $fw );
                                        $fwn = str_replace( $fn . '-', '', $fwn );
                                        $fwn = str_replace( '.ttf', '', $fwn );
                                        echo '<option value="'.$fn.'_'.$weights[$fwn].'">'.$fwn.'</option>';
                                    }
                                    //render_checkboxs('weights',$ws,'','',0,3);
                                    echo '</optgroup>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="q">
                            <label for="ints"><span>Include AIO styles / scripts</span> <i class="tip">Add scripts included by AIO to enrich your web app</i></label>
                            <div class="row">
                                <?php
                                $ints = [
                                    [ 'ui_reset', 'AIO Reset CSS', 'Stylesheet that has custom reset css to begin with' ],
                                    [ 'ux_core', 'AIO Core JS', 'Core features easy element manipulation, tabs, steps, input data fetching etc' ],
                                    [ 'ux_fps', 'AIO Full Page JS', 'Full page scrolling script' ],
                                    [ 'art()', 'AIO Art CSS', 'Styles tables, modals, notifications, steps, tabs, blocks, images, icons etc' ],
                                    [ 'ui_inputs', 'AIO Inputs CSS', 'Styles inputs, buttons, date pickers, color pickers etc' ],
                                    [ 'icons()', 'AIO Auto Icons CSS', 'Creates CSS for icons present in your assets/icons dir' ],
                                    [ 'ui_micro', 'AIO Micro CSS', 'Adds micro css overwrides, ex: add class dn on element to display:none' ],
                                ];
                                foreach( $ints as $i ) {
                                    //echo '<div class="col-12 col-lg-6"><input type="checkbox" id="'.$i[0].'" name="ints[]"><label for="'.$i[0].'">'.$i[1].'<i class="tip">'.$i[2].'</i></label></div>';
                                }
                                ?>
                                <select name="ints" id="ints" class="select" multiple>
                                    <?php
                                    foreach( $ints as $i ) {
                                        echo '<option value="'.$i[0].'">'.$i[1].'<span class="tip">'.$i[2].'</span></option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="q">
                            <label for="exts"><span>Include 3rd party styles / scripts</span> <i class="tip">Select the desired styles / scripts for your app from the web's most popular 3rd party enhancements</i></label>
                            <div>
                                <?php
                                $exts = [
                                    //'ui_bootstrap'=>'Bootstrap',
                                    'ui_bootstrap_grid'=>'Bootstrap Grid',
                                    'b_select2'=>'Select 2',
                                    'b_datepicker'=>'Air Datepicker',
                                    'ux_chart'=>'Chart JS',
                                    'ux_jquery'=>'jQuery',
                                    'ux_jquery_ui'=>'jQuery UI',
                                    'ux_clipboard'=>'Clipboard JS',
                                    'ux_moment'=>'Moment JS',
                                    'ux_tilt'=>'Tilt JS',
                                    'ux_bot_ui'=>'Bot UI JS',
                                ];
                                foreach( $exts as $k => $v ) {
                                    echo '<div><input type="checkbox" id="'.$k.'" name="exts[]"><label for="'.$k.'">'.$v.'</label></div>';
                                }
                                ?>
                            </div>
                        </div>
                        <div class="q">
                            <label for="styles"><span>Create Stylesheets</span> <i class="tip">Creates stylesheets with .scss extension, that will be auto linked on all your web app's pages</i></label>
                            <div><input type="text" id="styles" name="styles" placeholder="Ex: users, contacts etc."></div>
                        </div>
                        <div class="q">
                            <label for="scripts"><span>Create Scripts</span> <i class="tip">Creates scripts that will be auto linked on all your web app's pages in footer</i></label>
                            <div><input type="text" id="scripts" name="scripts" placeholder="Ex: users, contacts etc."></div>
                        </div>
                    </div>
                </div>
                <nav>
                    <div class="p"></div>
                    <div class="n"></div>
                </nav>
            </div>
            <div class="setup three">
                <div class="content">
                    <div class="head">
                        <h2>STEP 3 OF 5</h2>
                        <h3>Database</h3>
                    </div>
                    <div class="data">
                        <?php
                        foreach( $_POST as $k => $v ) {
                            echo !is_array( $v ) ? '<input type="hidden" name="'.$k.'" value="'.$v.'">' : '<input type="hidden" name="'.$k.'[]" value=\''.serialize($v).'\'">';
                        }
                        ?>
                        <div class="q">
                            <label for="type">Select Database Type</label>
                            <div class="row">
                                <?php
                                $bases = [
                                    ''=>'No Database Required',
                                    'mysql'=>'MySQL',
                                    'sql_lite'=>'SQL Lite',
                                    'mongodb'=>'MongoDB',
                                    'mssql'=>'Microsoft SQL Server',
                                    'firebase'=>'Firebase',
                                    'oracle'=>'Oracle',
                                    'pg_sql'=>'Post-gre SQL',
                                ];
                                $form->radios('type','',$bases,'','',0,4);
                                ?>
                            </div>
                        </div>
                        <div class="q">
                            <div class="row">
                                <div class="col-6">
                                    <label for="server"><span>Database Server Host / URL</span></label>
                                    <div><input type="text" id="server" name="server" placeholder="Ex: localhost"></div>
                                </div>
                                <div class="col-6">
                                    <label for="base"><span>Database Name</span></label>
                                    <div><input type="text" id="base" name="base" placeholder="Ex: <?php echo $appdir; ?>_db"></div>
                                </div>
                            </div>
                        </div>
                        <div class="q">
                            <div class="row">
                                <div class="col-6">
                                    <label for="user">Username</label>
                                    <input type="text" id="user" name="user" placeholder="Ex: <?php echo $appdir; ?>, admin etc.">
                                </div>
                                <div class="col-6">
                                    <?php $form->text('pass','Password'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <nav>
                    <div class="p"></div>
                    <div class="n"></div>
                </nav>
            </div>
            <div class="setup four">
                <div class="content">
                    <div class="head">
                        <h2>STEP 4 OF 5</h2>
                        <h3>Pages Structure</h3>
                    </div>
                    <div class="data">
                        <?php
                        foreach( $_POST as $k => $v ) {
                            echo !is_array( $v ) ? '<input type="hidden" name="'.$k.'" value="'.$v.'">' : '<input type="hidden" name="'.$k.'[]" value=\''.serialize($v).'\'">';
                        }
                        ?>
                        <div class="q">
                            <div>Setup function files for logged in users</div>
                            <div><input type="text" name="f_int" placeholder="Ex: contacts, invoices, payments, support etc."></div>
                        </div>
                        <div class="q">
                            <div>Setup function files for non logged in users</div>
                            <div><input type="text" name="f_ext" placeholder="Ex: login, register, support etc."></div>
                        </div>
                        <div class="q">
                            <label for="feats">Select your Web App Features </label>
                            <div class="row">
                                <?php $feats = [
                                    'cms'=>'AIO Content Management System',
                                    'commerce'=>'AIO Commerce',
                                ];
                                $form->checkboxes( 'feats', '', $feats, '', 'data-one', 0, 6 );
                                ?>
                            </div>
                        </div>
                        <div>
                            <div>Setup dynamic pages</div><br/>
                            <div><input type="text" name="pages" data-dynamic='<?php echo json_encode([['text','page','Page'],['div','url'],['checkbox','script','Custom Script'],['checkbox','style','Custom Stylesheet']]); ?>'></div>
                        </div>
                    </div>
                </div>
                <nav>
                    <div class="p"></div>
                    <div class="f"></div>
                </nav>
            </div>
        </div>
    </article>


    <?php
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
    ?>
</body>
<?php get_scripts(['select2','smooth-scrollbar','iro','basics','aio','setup']); ?>
</html>