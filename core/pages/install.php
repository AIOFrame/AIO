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
                <div>Include internal styles / scripts ?</div>
                <div>
                    <select name="ints[]" class="select2" multiple>
                        <?php $ints = [
                            'reset'=>'AIO Reset CSS',
                            'core'=>'AIO Core JS',
                            'fullpage'=>'AIO Fullpage JS',
                            'art'=>'AIO Art CSS',
                            'input'=>'AIO Inputs CSS',
                            'icons'=>'AIO Auto Icons CSS',
                            'micro'=>'AIO Micro CSS',
                        ];
                        select_options( $ints ); ?>
                    </select>
                </div>
            </div>

            <div class="q">
                <div>Include external styles / scripts ?</div>
                <div>
                    <select name="exts[]" class="select2" multiple>
                        <?php $exts = [
                            'bootstrap'=>'Bootstrap',
                            'bootstrap_grid'=>'Bootstrap Grid',
                            'select2'=>'Select 2',
                            'datepicker'=>'Air Datepicker',
                            'chart'=>'Chart JS',
                            'jquery'=>'jQuery',
                            'jqueryui'=>'jQuery UI',
                            'clipboard'=>'Clipboard JS',
                            'moment'=>'Moment JS',
                            'tilt'=>'Tilt JS',
                            'botui'=>'Bot UI JS',
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
                    <select name="base[]" class="select2">
                        <?php $bases = [
                            'mysql'=>'MySQL',
                            'sqllite'=>'SQL Lite',
                            'mongodb'=>'MongoDB',
                            'mssql'=>'Microsoft SQL Server',
                            'firebase'=>'Firebase',
                            'oracle'=>'Oracle',
                            'pgsql'=>'PostgreSQL',
                        ];
                        select_options( $bases ); ?>
                    </select>
                </div>
            </div>

            <div class="q">
                <div>Database Server Host / URL</div>
                <div><input type="text" name="dbhost" placeholder="Ex: localhost"></div>
            </div>

            <div class="q">
                <div>Database Name</div>
                <div><input type="text" name="dbname" placeholder="Ex: <?php echo $appdir; ?>_db"></div>
            </div>

            <div class="q">
                <div>Authorized Username</div>
                <div><input type="text" name="dbuser" placeholder="Ex: <?php echo $appdir; ?>, admin etc."></div>
            </div>

            <div class="q">
                <div>Password</div>
                <div><input type="text" name="dbpass"></div>
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

            <div>
                <div>Setup dynamic pages</div><br/>
                <div><input type="text" name="pages" data-dynamic='<?php echo json_encode([['text','page','Page'],['checkbox','script','Custom Script'],['checkbox','style','Custom Stylesheet']]); ?>'></div>
            </div>

        </div>

        <div class="foot">
            <button type="button" class="to p" name="step" value="1" onclick="window.history.back()"></button>
            <button class="to f" name="step" value="5"></button>
        </div>

    </form>

    <?php } else if( $p['step'] == '5' ) {

        // Do Final
        echo 'Setup Complete :)';

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