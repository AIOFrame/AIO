<?php

$appdir = !empty( get_domain('sub') ) ? get_domain( 'sub' ) : get_domain();
// Functions

$p = $_POST;

// skel( $_POST );

if( ( isset( $p['setup'] ) && $p['setup'] == 'Yes' ) || isset( $p['step'] ) ) {

    echo '<h1>Welcome to AIO App Setup</h1>';

    if( !isset( $p['step'] ) ) { ?>

    <form class="setup one" method="post">

        <div class="head">
            <h2>STEP 1</h2>
            <h3>Basic Configuration</h3>
        </div>

        <div class="data">

            <div class="q">
                <div>What would you like to call your Application / Website ?</div>
                <div><input type="text" name="appname" placeholder="Ex: Food Delivery, AIO University, <?php echo ucfirst( $appdir ); ?> etc."></div>
            </div>

            <div class="q">
                <div>Set a key for one way encryption</div>
                <div><input type="text" name="key" placeholder="********"></div>
            </div>

            <div class="q">
                <div>Do you want to force SSL ?</div>
                <div><input type="checkbox" name="fssl" class="slide s"></div>
            </div>

            <div class="q">
                <div>Do you prefer debug mode ?</div>
                <div><input type="checkbox" name="debug" class="slide s" checked></div>
            </div>

            <div class="q">
                <div>Do you want to create a default .gitignore ?</div>
                <div><input type="checkbox" name="gitignore" class="slide s" checked></div>
            </div>

        </div>

        <div class="foot">
            <button class="to n" name="step" value="2"></button>
        </div>

    </form>

    <?php } else if( $p['step'] == '2' ) { ?>

    <form class="setup two" method="post">

        <div class="head">
            <h2>STEP 2</h2>
            <h3>UI & UX</h3>
        </div>

        <div class="data">

            <?php
                foreach( $_POST as $k => $v ) {
                    echo '<input type="hidden" name="'.$k.'" value="'.$v.'">';
                }
            ?>

            <div class="q">
                <div>How many minutes do you want to cache styles and scripts ?</div>
                <div><input type="number" name="cache" placeholder="Ex: 2" value="0"></div>
            </div>

            <div class="q">
                <div>Include AIO styles / scripts ?</div>
                <div>
                    <select name="ints[]" class="select2" multiple>
                        <?php $ints = [
                            'ui_reset'=>'AIO Reset CSS',
                            'ux_core'=>'AIO Core JS',
                            'ux_aio_full_page'=>'AIO Full Page JS',
                            'art()'=>'AIO Art CSS',
                            'ui_input'=>'AIO Inputs CSS',
                            'icons()'=>'AIO Auto Icons CSS',
                            'ui_micro'=>'AIO Micro CSS',
                        ];
                        select_options( $ints ); ?>
                    </select>
                </div>
            </div>

            <div class="q">
                <div>Include third party styles / scripts ?</div>
                <div>
                    <select name="exts[]" class="select2" multiple>
                        <?php $exts = [
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
                        select_options( $exts ); ?>
                    </select>
                </div>
            </div>

            <div class="q">
                <div>List the stylesheets you want to add globally</div>
                <div><input type="text" name="styles" placeholder="Ex: users, contacts etc."></div>
            </div>

            <div class="q">
                <div>And scripts you need to add globally</div>
                <div><input type="text" name="scripts" placeholder="Ex: users, contacts etc."></div>
            </div>

        </div>

        <div class="foot">
            <button type="button" class="to p" name="step" value="1" onclick="window.history.back()"></button>
            <button class="to n" name="step" value="3"></button>
        </div>

    </form>

    <?php } else if( $p['step'] == '3' ) { ?>

    <form class="setup three" method="post">

        <div class="head">
            <h2>STEP 3</h2>
            <h3>Data</h3>
        </div>

        <div class="data">

            <?php
            foreach( $_POST as $k => $v ) {
                echo !is_array( $v ) ? '<input type="hidden" name="'.$k.'" value="'.$v.'">' : '<input type="hidden" name="'.$k.'[]" value=\''.serialize($v).'\'">';
            }
            ?>

            <div class="q">
                <div>Which type of database does your app connect to ?</div>
                <div>
                    <select name="type" class="select2">
                        <?php $bases = [
                            'mysql'=>'MySQL',
                            'sql_lite'=>'SQL Lite',
                            'mongodb'=>'MongoDB',
                            'mssql'=>'Microsoft SQL Server',
                            'firebase'=>'Firebase',
                            'oracle'=>'Oracle',
                            'pg_sql'=>'Post-gre SQL',
                        ];
                        select_options( $bases ); ?>
                    </select>
                </div>
            </div>

            <div class="q">
                <div>Database Server Host / URL</div>
                <div><input type="text" name="server" placeholder="Ex: localhost"></div>
            </div>

            <div class="q">
                <div>Database Name</div>
                <div><input type="text" name="base" placeholder="Ex: <?php echo $appdir; ?>_db"></div>
            </div>

            <div class="q">
                <div>Authorized Username</div>
                <div><input type="text" name="user" placeholder="Ex: <?php echo $appdir; ?>, admin etc."></div>
            </div>

            <div class="q">
                <div>Password</div>
                <div><input type="text" name="pass"></div>
            </div>

        </div>

        <div class="foot">
            <button type="button" class="to p" name="step" value="1" onclick="window.history.back()"></button>
            <button class="to n" name="step" value="4"></button>
        </div>

    </form>

    <?php } else if( $p['step'] == '4' ) { ?>

    <form class="setup four" method="post">

        <div class="head">
            <h2>STEP 4</h2>
            <h3>Pages</h3>
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

            <div>
                <div>Setup dynamic pages</div><br/>
                <div><input type="text" name="pages" data-dynamic='<?php echo json_encode([['text','page','Page'],['div','url'],['checkbox','script','Custom Script'],['checkbox','style','Custom Stylesheet']]); ?>'></div>
            </div>

        </div>

        <div class="foot">
            <button type="button" class="to p" name="step" value="1" onclick="window.history.back()"></button>
            <button class="to f" name="step" value="5"></button>
        </div>

    </form>

    <?php } else if( $p['step'] == '5' ) {

        // Do Final
        unset( $p['step'] );
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
        !file_exists( COREPATH . 'apps/' . $appdir ) ? mkdir( COREPATH . 'apps/' . $appdir, 0777, true ) : '';

        // Make internal directories
        $dirs = [ 'assets' => [ 'icons', 'images', 'scripts', 'styles' ], 'components', 'functions', 'modals', 'pages', 'storage' ];
        foreach( $dirs as $dk => $dv ) {
            if( is_array( $dv ) ) {
                foreach( $dv as $d ) {
                    !file_exists( COREPATH . 'apps/' . $appdir . '/' . $dk . '/' . $d ) ? mkdir( COREPATH . 'apps/' . $appdir . '/' . $dk . '/' . $d, 0777, true ) : '';
                }
            } else {
                !file_exists( COREPATH . 'apps/' . $appdir . '/' . $dv ) ? mkdir( COREPATH . 'apps/' . $appdir . '/' . $dv, 0777, true ) : '';
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
                $style_file = fopen( COREPATH . 'apps/' . $appdir . '/assets/styles/' . $sf . '.css', 'w' );
                fclose( $style_file );
                $uix[] = 'ui_'.$sf;
            }
        }
        if( isset( $p['scripts'] ) && !empty( $p['scripts'] ) ) {
            $script_files = explode( ',', $p['scripts'] );
            $script_files = is_array( $script_files ) ? $script_files : [];
            foreach( $script_files as $sf ) {
                $script_file = fopen( COREPATH . 'apps/' . $appdir . '/assets/scripts/' . $sf . '.js', 'w' );
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

        $head = fopen( COREPATH . 'apps/' . $appdir . "/components/head.php", "w");
        if( $head ) {
            fwrite( $head, $html );
            fclose( $head );
        }

        // Make footer component
        $foot = fopen( COREPATH . 'apps/' . $appdir . "/components/foot.php", "w");
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
                    $script_file = fopen( COREPATH . 'apps/' . $appdir . '/assets/scripts/' . $p->page_1 . '.js', 'w' );
                    fclose( $script_file );
                }
                if( isset( $p->style_4 ) && $p->style_4 == 1 ) {
                    $style_file = fopen( COREPATH . 'apps/' . $appdir . '/assets/styles/' . $p->page_1 . '.css', 'w' );
                    fclose( $style_file );
                }
                $page_file = fopen( COREPATH . 'apps/' . $appdir . '/pages/' . $p->page_1 . '.php', 'w' );
                fwrite( $page_file, $page_html_pre . $p->page_1 . $page_html_post );
                fclose( $page_file );
            }
        }

        // Create function
        if( is_array( $funs ) ) {
            foreach( $funs as $f ) {
                $func_file = fopen( COREPATH . 'apps/' . $appdir . '/functions/' . $f . '.php', 'w' );
                fwrite( $func_file, '<?php'.PHP_EOL.$nl );
                fclose( $func_file );
            }
        }

        // Make config file
        $con = fopen( COREPATH . 'apps/' . $appdir . "/config.php", "w");
        if( $con && !empty( $c ) ) {
            fwrite( $con, $c );
            fclose( $con );
        }
        echo '<p>Setup Complete :)</p><br/><form method="post"><button>Reload</button></form>';
    }

} else if( !isset( $p['setup'] ) ) {
?>

<div class="setup zero">

    <div class="q">
        <div>Would you like to setup "<?php echo $appdir; ?>" app instead ?</div>
        <div>
            <form method="post">
                <button name="setup" value="Yes">Yes</button>
                <button name="setup" value="No">No</button>
            </form>
        </div>
    </div>

</div>

<?php } ?>