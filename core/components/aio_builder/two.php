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