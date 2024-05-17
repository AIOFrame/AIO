<?php
// TODO: Fix auto load not saving issue
global $options;
//skel( $options );

class OPTIONS {

    public array $brand_options = [
        'app_name',
        'app_desc',
        'default_theme',
        'primary_color',
        'secondary_color',
        'color_light',
        'filled_color_light',
        'logo_light',
        'logo_dark',
        'primary_color_dark',
        'secondary_color_dark',
        'color_dark',
        'filled_color_dark',
        'fav'
    ];
    public array $colors = [
        'disabled_color' => '#e6e6e6',
        'progress_color' => '#ff6b00',
        'warning_color' => '#fb0000',
        'error_color' => '#a70000',
        'success_color' => '#03a700',
    ];
    public array $social_options = [
        'fb' => 'Facebook',
        'tw' => 'Twitter',
        'ig' => 'Instagram',
        'yt' => 'YouTube',
        'wa' => 'WhatsApp',
        'tg' => 'Telegram',
        'pin' => 'Pinterest',
        'sk' => 'Skype',
        'ln' => 'LinkedIn',
        'ps' => 'Play Store',
        'as' => 'App Store',
        //'mob' => 'Mobile Number',
        //'ll' => 'Land Line'
    ];
    public array $communication_options = [
        'phone' => 'Phone',
        'mobile' => 'Mobile',
        'email' => 'Email',
        'website' => 'Website'
    ];
    public array $finance_options = [
        'trade_name' => 'Registered Name',
        'trade_no' => 'Registered No.',
        'trn' => 'Tax Reg. No.',
        'tax' => 'Tax%',
        'currency' => 'Currency Name',
        'symbol' => 'Currency Symbol',
        'sign' => 'Currency Symbol',
        'side' => 'Currency Side',
        'rate' => 'Rate',
    ];
    public array $dimension_fields = [
        [ 'i' => 'all', 'n' => ' (px)', 't' => 'range', 'c' => 12 ],
        [ 'i' => '', 't' => 'col', 'c' => 3 ],
        [ 'i' => 't', 'n' => 'Top ↑', 't' => 'range', 'c' => 6 ],
        [ 'i' => '', 't' => 'col', 'c' => 3 ],
        [ 'i' => 'l', 'n' => 'Left ←', 't' => 'range', 'c' => 6 ],
        [ 'i' => 'r', 'n' => 'Right →', 't' => 'range', 'c' => 6 ],
        [ 'i' => '', 't' => 'col', 'c' => 3 ],
        [ 'i' => 'b', 'n' => 'Bottom ↓', 't' => 'range', 'c' => 6 ],
        [ 'i' => '', 't' => 'col', 'c' => 3 ],
    ];
    public array $colors_fields = [
        [ 'i' => 'bg_c1', 'n' => 'BG Primary', 't' => 'color', 'c' => 4 ],
        [ 'i' => 'bg_c2', 'n' => 'BG Secondary', 't' => 'color', 'c' => 4 ],
        [ 'i' => 'text_color', 'n' => 'Text', 't' => 'color', 'c' => 4 ],
    ];
    public array $shadow_fields = [
        [ 'i' => '_sx', 'n' => 'Shadow X', 't' => 'r', 'min' => 0, 'max' => 50, 'c' => 2 ],
        [ 'i' => '_sy', 'n' => 'Shadow Y', 't' => 'r', 'min' => 0, 'max' => 50, 'c' => 2 ],
        [ 'i' => '_sb', 'n' => 'Shadow Blur', 't' => 'r', 'min' => 0, 'max' => 50, 'c' => 2 ],
        [ 'i' => '_sc', 'n' => 'Shadow Color', 't' => 'c', 'c' => 3 ],
        [ 'i' => '_sa', 'n' => 'Shadow Angle', 't' => 'c', 'min' => 0, 'max' => 365, 'c' => 3 ],
    ];
    public array $events = [ 'normal', 'hover', 'click' ];
    public array $icon_options = [
        'accordion_view' => 'view_day',
        'calendar' => 'calendar_month',
        'search' => 'search',
        'file_upload' => 'file_upload',
        'file_size' => 'straighten',
        'file_type' => 'description',
        'file_limit' => 'content_copy',
        'modal_expand' => 'open_in_full',
        'modal_contract' => 'close_fullscreen',
    ];
    public array $option_elements = [
        'input' => 'Input Fields',
        'card' => 'Cards',
        'btn' => 'Generic Buttons',
        'modal' => 'Modal Popup',
        'alert' => 'Alert'
    ];
    public array $option_types = [
        'p' => 'padding',
        'b' => 'border',
        'm' => 'margin',
    ];

    /**
     * Renders app branding options
     * @param bool $dark_mode_options Show Dark Mode Options
     * @return void
     */
    function brand_options( bool $dark_mode_options = true ): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $brands = defined( 'REGION' ) ? prepare_values( $this->brand_options, $r ) : $this->brand_options;
        $ops = $db->get_options( $brands );
        //skel( prepare_values( $this->brand_options, $r ) );
        $f->option_params_wrap( 'brand', 'row', $brands );
            $attr = 'data-brand';
            $ext = 'jpg,svg,png,gif';
            $name = !empty( $ops[$r.'app_name'] ) ? $ops[$r.'app_name'] : 'fake_name';
            $desc = !empty( $ops[$r.'app_desc'] ) ? $ops[$r.'app_desc'] : 'fake_text';
            $theme = !empty( $ops[$r.'default_theme'] ) ? $ops[$r.'default_theme'] : '';
            $c1 = !empty( $ops[$r.'primary_color'] ) ? $ops[$r.'primary_color'] : 'fake_hex';
            $c2 = !empty( $ops[$r.'secondary_color'] ) ? $ops[$r.'secondary_color'] : 'fake_hex';
            $c3 = !empty( $ops[$r.'color_light'] ) ? $ops[$r.'color_light'] : 'fake_hex';
            $c4 = !empty( $ops[$r.'filled_color_light'] ) ? $ops[$r.'filled_color_light'] : 'fake_hex';
            $dc1 = !empty( $ops[$r.'primary_color_dark'] ) ? $ops[$r.'primary_color_dark'] : 'fake_hex';
            $dc2 = !empty( $ops[$r.'secondary_color_dark'] ) ? $ops[$r.'secondary_color_dark'] : 'fake_hex';
            $dc3 = !empty( $ops[$r.'color_dark'] ) ? $ops[$r.'color_dark'] : 'fake_hex';
            $dc4 = !empty( $ops[$r.'filled_color_dark'] ) ? $ops[$r.'filled_color_dark'] : 'fake_hex';
            $light = !empty( $ops[$r.'logo_light'] ) ? $ops[$r.'logo_light'] : '';
            $dark = !empty( $ops[$r.'logo_dark'] ) ? $ops[$r.'logo_dark'] : '';
            $fav = !empty( $ops[$r.'fav'] ) ? $ops[$r.'fav'] : '';
            $uis = [ 'default' => 'Default - Light' ];
            $ui_list = scandir( ROOTPATH . 'assets/styles/portal/ui' );
            foreach( $ui_list as $ui ) {
                if( str_contains( $ui, '.scss' ) ) {
                    $s = str_replace( '.scss', '', $ui );
                    $uis[ $s ] = ucwords( str_replace( '-', ' ', $s ) );
                }
            }
            $f->text($r.'app_name','Web App / Site Name','Ex: AIO University...',$name,$attr,8);
            $f->upload($r.'fav','Fav Icon','Upload',$fav,0,0,'upload',$attr,'png',.1,1,'',4);
            $f->textarea($r.'app_desc','Web App / Site Description','Ex: We provide...',$desc,$attr,12);
            $f->select2( $r.'default_theme', 'Default Admin Theme', 'Select Theme...', $uis, $theme, $attr, 12, 1 );
            //$f->select( 'input_theme', 'Input Style', 'Select Theme...', [], '', 'data-data class="select2"', 6, 1 );
            echo '<h3 class="col-12">'.T('Light Color Options').'</h3>';
            $f->upload($r.'logo_light','Logo','Upload',$light,0,0,'upload',$attr,$ext,.2,1,'',4);
            $f->color($r.'primary_color','Primary','Ex: F1F1F1',$c1,$attr,2,'','[data-key=primary_color]');
            $f->color($r.'secondary_color','Secondary','Ex: A2A2A2',$c2,$attr,2,'','[data-key=secondary_color]');
            $f->color($r.'color_light','Text Color','Ex: A2A2A2',$c3,$attr,2,'','[data-key=color_light]');
            $f->color($r.'filled_color_light','Text on Theme BG','Ex: A2A2A2',$c4,$attr,2,'','[data-key=filled_color_light]');
            if( $dark_mode_options ) {
                echo '<h3 class="col-12">'.T('Dark Color Options').'</h3>';
                $f->upload($r.'logo_dark','Logo','Upload',$dark,0,0,'upload',$attr,$ext,.2,1,'',4);
                $f->color($r.'primary_color_dark','Primary','Ex: F1F1F1',$dc1,$attr,2,'','[data-key=primary_color_dark]');
                $f->color($r.'secondary_color_dark','Secondary','Ex: A2A2A2',$dc2,$attr,2,'','[data-key=secondary_color_dark]');
                $f->color($r.'color_dark','Text Color','Ex: A2A2A2',$dc3,$attr,2,'','[data-key=color_dark]');
                $f->color($r.'filled_color_dark','Text on Theme BG','Ex: A2A2A2',$dc4,$attr,2,'','[data-key=filled_color_dark]');
            }
            $f->process_options($this->region_flag().'Save Brand Options','store grad','','.col-12 tac');
        $f->post_process();
            $this->region_notice();
        //post();
        file_upload();
        get_scripts('iro,color');
    }

    /**
     * Renders Color Options
     * @return void
     */
    function color_options(): void {
        $f = new FORM();
        $db = new DB();
        $attr = 'data-colors';
        $colors = $this->colors;
        $ops = $db->get_options( $colors );
        $f->option_params_wrap(  'colors', 'row', $colors );
        foreach( $colors as $c => $cv ) {
            $f->color($c,ucwords(str_replace('_',' ',$c)),'Ex: F1F1F1',$ops[$c]??$cv,$attr,0,'','[data-key='.$c.']');
        }
        $f->process_options('Save Color Options','store grad','','.col-12 tac');
        post();
        get_scripts('iro,color');
    }

    function icon_options(): void {
        global $options;
        $icons = $this->icon_options;
        $a = 'data-ico';
        if( class_exists( 'PORTAL' ) ) {
            $p = new PORTAL();
            $icons = array_merge( $icons, $p->icon_options );
        }
        $form = [
            [ 'i' => 'icon_font', 'n' => 'Icon Fonts', 'v' => $options['icon_font'] ?? 'MaterialIcons', 'o' => [ 'MaterialIcons' => 'Material Icons', 'BootstrapIcons' => 'Bootstrap Icons' ], 'c' => 12, 't' => 's', 'k' => 1, 'm' => 1, 'a' => $a ],
            [ 'i' => 'icon_class', 'n' => 'Icon Class', 'v' => $options['icon_class'] ?? 'mico', 'c' => 6, 'a' => $a ],
            [ 'i' => 'icon_after_class', 'n' => 'Icon After Class', 'v' => $options['icon_after_class'] ?? 'mica', 'c' => 6, 'a' => $a ],
        ];
        $autoload = [ 'icon_font', 'icon_class', 'icon_after_class' ];
        foreach( $icons as $o => $v ) {
            $ico = 'ico_'.$o;
            //$idc = 'port_ico_'.$o.'_class';
            $autoload[] = $ico;
            //$autoload[] = $idc;
            $form[] = [ 'i' => $ico, 'n' => ucwords( str_replace( '_', ' ', $o ) ). ' Ico', 'v' => $options[$ico] ?? $v, 'c' => 2, 'a' => $a ];
            //$form[] = [ 'i' => $idc, 'n' => ucwords( $o ). ' Ico Class', 'v' => $v, 'c' => 2 ];
        }
        $o = new OPTIONS();
        $o->form( $form, 'row', 1, 'ico', 'Successfully saved icon settings!', 2, 2, $autoload );
    }

    /**
     * Renders input and button design options
     * @param bool $dark_mode_options Show Dark Mode Options
     * @return void
     */
    function design_options( bool $dark_mode_options = true ): void {
        $f = new FORM();
        //$db = new DB();
        //$v = [];
        //$options = $f->design_options;
        //skel( $options );
        $ops = [];
        //$ops = $db->get_options( $options );
        //skel( $ops );
        $dimensions_form = $this->dimension_fields;
        $colors_form = $this->colors_fields;
        $shadow_form = $this->shadow_fields;
        $option_elements = $this->option_elements;
        $option_types = $this->option_types;
        get_script('design_options');
        // Loop option elements for tab heads
        pre_tabs( 'material mb20' );
        foreach( $option_elements as $x => $oe ) {
            //skel( $x );
            tab( T( ucwords( $oe ) ), $x == 'input', '#'.$x.'_options' );
        }
        post_tabs();
        // Loop option elements
        _d( 'design_options_data' );
            foreach( $option_elements as $x => $oe ) {
                _d( $x == 'input' ? '' : 'dn', $x.'_options' );
                    // Loop option types
                    $autoload = [];
                    _r();
                    foreach( $option_types as $ok => $ot ) {
                        $type_options = [];
                        // h4( T( ucfirst( $ot ) ), 0, 'mt0 ttu' );
                        _c( 4 );
                            //skel( $dimensions_form );
                            ;
                            foreach( $dimensions_form as $dk => $dd ) {
                                if( ( $ot == 'border' ) ) {
                                    $dd['max'] = 5;
                                    if( $dk == 0 ) {
                                        $dd['c'] = 6;
                                    } else if( $dk == 1 ) {
                                        $type_options[] = [ 'i' => $x . '_' . 'radius_all', 'n' => 'Radius', 't' => 'range', 'min' => 0, 'max' => 50, 'c' => 6, 'a' => 'data-unified_trigger' ];
                                    }
                                }
                                //skel( $dk );
                                $radius_titles = [ 1 => '◜', 3 => '◝', 6 => '◟', 8 => '◞' ];
                                if( $ot == 'border' && $dd['c'] == 3 ) {
                                    $dd['t'] = 'range';
                                    $dd['n'] = $radius_titles[ $dk ];
                                    $dd['i'] = $x . '_' . $ot . '_' . $radius_titles[ $dk ];
                                    $dd['a'] = 'data-override_radius';
                                    $dd['max'] = 50;
                                } else {
                                    $dd['i'] == 'all' ? $dd['n'] = ucwords( $ot ) . $dd['n'] : '';
                                    $dd['a'] = $dd['i'] == 'all' ? 'data-unified_trigger' : 'data-override_'.$ot;
                                    $dd['i'] = $x . '_' . $ot . '_' . $dd['i'];
                                }
                                //skel( $dd['i'] );
                                $type_options[] = $dd;
                                $autoload[] = $dd['i'];
                                //skel( $ob );
                            }
                                $f->form( $type_options );
                        c_();
                    }
                    r_();
                    $f->option_params_wrap( 'input', 'row', $autoload );
                        //skel( $autoload );
                        //$f->form( $type_options, 'row' );
                        $f->process_options('Save ' . $x . ' Styling Options','store grad','','.col-12 tac');
                    post();
                    h4( 'Color Options' );
                    $autoload = $color_options = [];
                    foreach( $colors_form as $cc ) {
                        $d = [];
                        $d['t'] = $cc['t'];
                        $d['i'] = $x . '_' . $cc['i'];
                        $d['n'] = $cc['n'];
                        $d['p'] = 'Ex: #000000';
                        $d['c'] = $cc['c'];
                        //$d['v'] = $ops[ $ck ] ?? '';
                        $color_options[] = $d;
                    }
                    $f->option_params_wrap( 'input', 'row', $autoload );
                        $f->form( $color_options );
                        $f->process_options('Save Color Options','store grad','','.col-12 tac');
                    post();
                d_();
            }
        d_();

    }

    /**
     * Renders Maps Options
     * @return void
     */
    function font_options(): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $options_array = [ 'font_1', 'font_1_weights', 'font_weight', 'font_2', 'font_2_weights' ];
        $options_array = defined( 'REGION' ) ? prepare_values( $options_array, $r ) : $options_array;
        $ops = $db->get_options( $options_array );
        $f->option_params_wrap( 'fonts', 'row', $options_array );
        $font_1 = $ops['font_1'] ?? 'Lato';
        $font_1_weights = $ops['font_1_weights'] ?? '400';
        $font_weight = $ops['font_weight'] ?? '400';
        $font_2 = $ops['font_2'] ?? '';
        $font_2_weights = $ops['font_2_weights'] ?? '';
        $attr = 'data-fonts';
        $core_fonts_list = new DirectoryIterator( ROOTPATH . 'assets/fonts' );
        $app_fonts_list = file_exists( ROOTPATH . 'apps/'. APPDIR . '/assets/fonts' ) ? new DirectoryIterator( ROOTPATH . 'apps/'. APPDIR . '/assets/fonts' ) : [];
        $fonts = [];
        $weights = [ 100, 200, 300, 400, 500, 600, 700, 800, 900 ];
        foreach( $core_fonts_list as $font ) {
            if ( !$font->isDot() && $font->getFilename() !== '.DS_Store' ) {
                $fonts[] = $font->getFilename();
            }
        }
        foreach( $app_fonts_list as $font ) {
            if ( !$font->isDot() && !in_array( $font->getFilename(), [ '.DS_Store', 'MaterialIcons' ] ) ) {
                $fonts[] = $font->getFilename();
            }
        }
        $f->select2($r.'font_1','Primary Font','Select Font...',$fonts,$font_1,$attr,6);
        $f->select2($r.'font_1_weights','Font Weights','Select Weights...',$weights,$font_1_weights,$attr.' multiple',4);
        $f->select2($r.'font_weight','Default Weight','Select...',$weights,$font_weight,$attr,2);
        $f->select2($r.'font_2','Secondary Font','Select Font...',$fonts,$font_2,$attr,6);
        $f->select2($r.'font_2_weights','Secondary Font Weights','Select Weights...',$weights,$font_2_weights,$attr.' multiple',4);
        //$f->select2('font_2_weight','Default Weight','Select...',$weights,$font_2_weight,$attr,2);
        $f->process_options($this->region_flag().'Save Map Options','store grad','','.col-12 tac');
        $this->region_notice();
        echo '</div>';
    }

    /**
     * Renders Maps Options
     * @return void
     */
    function map_options(): void {
        $m = new MAPS();
        $m->options();
    }

    function communication_options(): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $comm_options = defined( 'REGION' ) ? prepare_values( $this->communication_options, $r ) : $this->communication_options;
        //skel( $comm_options );
        $os = $db->get_options( $comm_options );
        //skel( $r );
        //skel( $os );
        $f->option_params_wrap('com','row');
            $phone = !empty( $os[$r.'phone'] ) ? $os[$r.'phone'] : 'fake_phone';
            $mobile = !empty( $os[$r.'mobile'] ) ? $os[$r.'mobile'] : 'fake_phone';
            $email = !empty( $os[$r.'email'] ) ? $os[$r.'email'] : 'fake_email';
            $website = !empty( $os[$r.'website'] ) ? $os[$r.'website'] : 'fake_site';
            $f->text($r.'phone','Official Phone No.','Ex: 403334444',$phone,'data-com',3);
            $f->text($r.'mobile','Mobile No. for Social Media','Ex: 503334444',$mobile,'data-com',3);
            $f->text($r.'email','Official Email','Ex: hello@company.com',$email,'data-com',3);
            $f->text($r.'website','Official Website','Ex: company.com',$website,'data-com',3);
            $f->process_options($this->region_flag().'Save Options','store grad','','.col-12 tac');
            $this->region_notice();
        post();
    }

    function geo_options(): void {
        $f = new FORM();
        $db = new DB();
        $zones = get_timezones();
        //skel( $zones );
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $add_ops = ['address','add_name','city','state','post','country','date_format','time_format','zone'];
        $add_ops = defined( 'REGION' ) ? prepare_values( $add_ops, $r ) : $add_ops;
        $os = $db->get_options($add_ops);
        $f->option_params_wrap('add','row');
        $address = !empty( $os[$r.'address'] ) ? $os[$r.'address'] : 'fake_address';
        $city = !empty( $os[$r.'city'] ) ? $os[$r.'city'] : 'fake_city';
        $state = !empty( $os[$r.'state'] ) ? $os[$r.'state'] : 'fake_state';
        $post = !empty( $os[$r.'post'] ) ? $os[$r.'post'] : 'fake_po';
        $country = $os[$r.'country'] ?? 'AE';
        $countries = get_countries( 'iso2', 'flag name' );
        $f->text($r.'address','Address','Ex: Office 1100, Building Name, Street Name...',$address,'data-add',9);
        $f->text($r.'add_name','Name','Ex: Dubai Branch',$os[$r.'add_name'] ?? '','data-add',3);
        $f->text($r.'city','City','Ex: Burlington',$city,'data-add',3);
        $f->text($r.'state','State','Ex: Burlington',$state,'data-add',3);
        $f->text($r.'postal','Postal Code','Ex: 110250',$post,'data-add',3);
        $f->select2($r.'country','Country','Choose Country...',$countries,$country,'data-add',3,1);
        $f->text($r.'date_format','Date Format','Ex: d M,Y',$os[$r.'date_format'] ?? '','data-add',3);
        $f->text($r.'time_format','Time Format','Ex: H:i a',$os[$r.'time_format'] ?? '','data-add',3);
        $f->select2($r.'zone','Timezone','Choose Zone...',$zones,$os[$r.'zone'] ?? '','data-add',3);
        $f->process_options($this->region_flag().'Save Options','store grad','','.col-12 tac');
        $this->region_notice();
        post();
    }

    //private array $finance_options = ['reg_name','reg','trn','tax','sign','rate','spot'];

    function finance_options(): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $fin_ops = $this->finance_options;
        $fin_ops = defined( 'REGION' ) ? prepare_keys( $fin_ops, $r ) : $fin_ops;
        $finance_keys = array_merge( $fin_ops, [ 'base_region' ] );
        $os = $db->get_options( $finance_keys );
        $f->option_params_wrap('cd','row');
        //skel( REGIONS );
        $base = defined('REGIONS') && isset( REGIONS['base'] ) ? REGIONS['base']['cca2'] . ' - ' .REGIONS['base']['symbol'] : 'US';
        $now = defined('REGIONS') && isset( REGIONS['now'] ) ? REGIONS['now']['cca2'] . ' - ' .REGIONS['now']['symbol'] : 'US';
        $rate_place = $os[$r.'rate'] ?? '__';
        $rate_val = $os[$r.'rate'] ?? 1;
        $f->text($r.'trade_name','Registered Name','Ex: ABC Trading LLC.',$os[$r.'trade_name'] ?? '','data-cd',3);
        $f->text($r.'trade_no','Registration No.','Ex: 120-12565-132665',$os[$r.'trade_no'] ?? '','data-cd',3);
        $f->text($r.'trn','Tax Reg. No.','Ex: 3562-2654-8954',$os[$r.'trn'] ?? '','data-cd',3);
        $f->input('number',$r.'tax','Tax%','Ex: 5',$os[$r.'tax'] ?? '','min="0" max="50" data-cd',3);
        $base !== $now ? $f->text($r.'rate','Rate, 1 ('.$base.') = '.$rate_place.' ('.$now.')?','Ex: 3.67',$rate_val,'data-cd',3) : '';
        $f->text($r.'sign','Currency Symbol','Ex: $',$os[$r.'sign'] ?? '','data-cd',3);
        $f->slide($r.'spot','Currency Side','Left','Right',$os[$r.'spot'] ?? '','','data-cd',3);
        $f->process_options($this->region_flag().'Save Options','store grad','','.col-12 tac');
        $this->region_notice();
        post();
    }

    function content_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['no_access_image','no_content_image']);
        $f->option_params_wrap('cn','row');
        $no_access_image = $os['no_access_image'] ?? '';
        $no_content_image = $os['no_content_image'] ?? '';
        $f->upload('no_access_image','Image to show when user has no access!','Upload',$no_access_image,0,1,'','data-cn','jpg,png,svg',.1,0,'',6);
        $f->upload('no_content_image','Image to show when there is no content!','Upload',$no_content_image,0,1,'','data-cn','jpg,png,svg',.1,0,'',6);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        $f->post_process();
    }

    /**
     * Renders options to set operating regions
     * @return void
     */
    function region_options(): void {
        //skel( FEATURES );
        //$c = !empty( CONFIG ) ? json_decode( CONFIG ) : [];
        //if( in_array( 'regions', $c->features ) || in_array( 'region', $c->features ) ) {
        if( in_array_any( [ 'region', 'regions' ], FEATURES ) ) {
            $r = new REGION();
            $r->region_options();
        }
    }

    function social_options(): void {
        $options = $this->social_options;
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options( $options );
        $f->option_params_wrap('soc','settings');
        $social_form = [];
        foreach( $options as $ok => $ov ) {
            $val = $os[ $ok ] ?? '';
            $social_form[] = [ 'i' => $ok, 'n' => $ov, 'p' => 'Ex: '.$ov.' URL', 'v' => $val ];
        }
        $f->form( $social_form, 'settings', 'soc' );
        $f->process_options('Save Options','store grad','','.col-12 tac');
        $f->post_process();
    }

    function all_options( string $table_class = '' ): void {
        $d = new DB();
        $f = new FORM();
        $options = $d->select( 'options' );
        $table[] = [ 'head' => [ 'ID', 'Key', 'Value', 'Scope', 'Autoload' ] ];
        foreach( $options as $o ) {
            $table[] = [ 'body' => [
                $o['option_id'],
                $f->_text( 'option_name_'.$o['option_id'], '', '', $o['option_name'], 'data-ao' ),
                $f->_text( 'option_value'.$o['option_id'], '', '', $o['option_value'], 'data-ao' ),
                $f->_text( 'option_scope'.$o['option_id'], '', '', $o['option_scope'], 'data-ao' ),
                $f->_slide( 'option_load'.$o['option_id'], '', '', '', $o['option_load'], 'data-ao' ),
            ] ];
        }
        table( $table, $table_class );
    }

    /**
     * Returns saved social option values
     * @return array
     */
    function get_social_options(): array {
        $keys = $this->social_options;
        $db = new DB();
        return $db->get_options( $keys );
    }

    /**
     * Renders Stripe API Options
     * @return void
     */
    function stripe_options(): void {
        $s = new STRIPE();
        $s->options();
    }

    function language_options(): void {
        // TODO: Move language options to languages php
        $f = new FORM();
        $db = new DB();
        $all_languages = get_languages();
        unset( $all_languages['en'] );
        $languages = $db->get_option('languages');
        $f->option_params_wrap('al','settings','languages,languages_updated');
        $f->select2('languages','Set Languages','Choose Languages...',$all_languages,$languages,'data-al multiple',12,1);
        $f->text('languages_updated','','',1,'hidden data-al');
        $f->process_options('Save Options','store grad','','.col-12 tac');
        div( 'region_info', 'English is default, you can add additional languages.', '', 'style="text-align:center; font-size: .8rem"', 1 );
    }

    function export_import_options(): void {
        $f = new FORM();
        $db = new DB();
        $e = Encrypt::initiate();
        $data = [];
        $options = $db->select('options','option_name,option_value','option_scope = \'0\' && option_load = \'1\'');
        if( !empty( $options ) ) {
            foreach( $options as $o ) {
                $data[ $o['option_name'] ] = $o['option_value'];
            }
        }
        $f->option_params_wrap( 'ei', 'settings' );
        //$f->process_params('','ei','',2,2,[],'Successfully imported options!','','','','','','row');
        $f->textarea('export','Export Options','',$e->encrypt_array($data),'rows="5"',6);
        $f->textarea('import','Import Options','','','data-ei rows="5"',6);
        _c('6');
        c_();
        $f->process_trigger('Import Options','store grad','','import_options_ajax','.col-12 tac');
        //$this->region_notice();
        $f->post_process();
    }

    /**
     * Renders ACCESS page options
     * @param bool $show_access Show access page options (Default true)
     * @param bool $show_login Show login form options (Default true)
     * @param bool $show_register Show register form options (Default true)
     * @param string $form_type 'row' or 'settings' type to render inputs
     * @return void
     */
    function access_options( bool $show_access = true, bool $show_login = true, bool $show_register = true, string $form_type = 'row' ): void {
        $a = new ACCESS();
        $a->options( $show_access, $show_login, $show_register, $form_type );
    }

    /**
     * Renders Login page options
     * @return void
     */
    function login_options(): void {
        $a = new ACCESS();
        $a->options( 0, 1, 0 );
    }

    /**
     * Renders Register page options
     * @return void
     */
    function register_options(): void {
        $a = new ACCESS();
        $a->options( 0, 0, 1 );
    }

    /**
     * Returns Query statement to fetch multiple options
     * @param array $options_array
     * @param string $relation
     * @return string
     */
    function options_query( array $options_array = [], string $relation = 'OR' ): string {
        return 'option_name = \'' . implode( '\' ' . $relation . ' option_name = \'', $options_array ) . '\'';
    }

    function form( array $form = [], string $type = '', bool $get_options = false, string $data = '', string $button_text = 'Save Options', string $button_class = 'store grad', string $success = '', array|string $auto_load = [], array|string $unique = [], array|string $encrypt = '', string $confirm = '', string $callback = '' ): void {
        if( $get_options ) {
            $db = new DB();
            // Fetch Options
            $os = [];
            if( !empty( $form ) ) {
                foreach( $form as $f ) {
                    $id = $f['identity'] ?? ( $f['id'] ?? ( $f['i'] ?? '' ) );
                    if( !empty( $id ) ) {
                        $os[] = $id;
                    }
                }
            }
            $os = $db->get_options( $os );
            //skel( $os );
            // Add values to fields
            if( !empty( $form ) && !empty( $os ) ) {
                foreach( $form as $fk => $f ) {
                    $id = $f['identity'] ?? ( $f['id'] ?? ( $f['i'] ?? '' ) );
                    $value = $os[ $id ] ?? ( $f['value'] ?? ( $f['val'] ?? ( $f['v'] ?? '' ) ) );
                    unset( $f['value'] );
                    unset( $f['val'] );
                    if( !empty( $id ) ) {
                        $form[ $fk ]['v'] = $value;
                        //$f['v'] = $value;
                    }
                }
            }
        }
        //skel( $form );
        $f = new FORM();
        $f->option_params_wrap( $data, '', $auto_load, $unique, $encrypt, $success, $callback, $confirm );
            $f->form( $form, $type );
            $f->process_trigger( $button_text, $button_class, '', '', '.col-12 tac' );
        post();
    }

    function data_options(): void {
        $url = str_replace( '/', '', str_replace( '://', '', str_replace( 'http', '', str_replace( 'https', '', APPURL ) ) ) );
        $form = [
            [ 'i' => 'success_message', 'p' => 'Ex: Stored successfully!', 'n' => 'Data saved success Message', 'c' => 6 ],
            [ 'i' => 'failure_message', 'p' => 'Ex: Failed to store data, please mail at support@'.$url, 'n' => 'Data save failure Message', 'c' => 6 ],
            [ 'i' => 'notify_time', 't' => 'number', 'p' => 'Ex: 2', 'n' => 'Notification Display Time (seconds)', 'c' => 3 ],
            [ 'i' => 'reload_time', 't' => 'number', 'p' => 'Ex: 2', 'n' => 'Page Reload Time (seconds)', 'c' => 3 ],
            [ 'i' => 'save_class', 'p' => 'Ex: save_button', 'n' => 'Save button class', 'c' => 6 ],
            //[ 't' => 'hidden', 'i' => 'autoload', 'v' => 'notify_time,reload_time,success_message,failure_message' ],
        ];
        $f = new FORM();
        $f->option_params_wrap( '', '', 'notify_time,reload_time,success_message,failure_message,save_class' );
            $f->form( $form, 'row' );
            $f->process_trigger( 'Save Options', 'store grad', '', '', '.col-12 tac' );
        post();
    }

    /**
     * @param int $display_type 1 = Tabbed, 2 = Accordion, 3 = Normal
     * @param string $enterprise
     * @param string $brand
     * @param string $input
     * @param string $social
     * @param string $languages
     * @return void
     */
    function render_options( int $display_type = 1, string $enterprise = '', string $brand = '', string $input = '', string $social = '', string $languages = '' ): void {
        if( $display_type == 1 ) {
            pre_tabs();
            !empty( $enterprise ) ? tab( T( $enterprise ), 0, 'aio_enterprise_options' ) : '';
            !empty( $brand ) ? tab( T( $brand ), 0, 'aio_brand_options' ) : '';
            !empty( $input ) ? tab( T( $input ), 0, 'aio_input_options' ) : '';
            !empty( $social ) ? tab( T( $social ), 0, 'aio_social_options' ) : '';
            !empty( $languages ) ? tab( T( $languages ), 0, 'aio_languages_options' ) : '';
            post_tabs();
        }
        // Company Options
        // Brand Options
        // Input
        // Social
        // Languages
    }

    function region_notice(): void {
        $c = defined( 'CONFIG' ) ? CONFIG : [];
        $r = defined( 'REGION' ) ? REGION : [];
        if( !empty( $r ) ) {
            $n = $r['name']['common'] ?? '';
            $f = $r['flag'] ?? '';
            div( 'region_info', T('Settings apply to region ') . $n . ' ' . $f, '', 'style="text-align:center; font-size: .8rem"' );
        } else if( in_array( 'regions', $c['features'] ) ) {
            div( 'region_info', T('Regions feature enabled! Please select a region in header and then save settings to apply to selected region!'), '', 'style="text-align:center; font-size: .8rem"' );
        }
    }

    function region_flag(): string {
        return defined( 'REGION' ) && isset( REGION['flag'] ) ? ' <i class="reg-flag">'.REGION['flag'].'</i> ' : '';
    }

}

function import_options_ajax(): void {
    if(!empty( $_POST['import'] )) {
        // Parse Import Data
        $e = Encrypt::initiate();
        $options = $e->decrypt_array( $_POST['import'] );

        // Import
        if( !empty( $options ) ) {
            $db = new DB();
            $x = 0;
            foreach( $options as $ok => $ov ) {
                $update = $db->update_option( $ok, $ov, 0, 1 );
                $update ? $x++ : '';
            }
            es('Successfully updated '.$x.' options!');
        } else {
            ef('Import data could not be parsed! Please try again or consult support!!');
        }
    } else {
        ef('Received no data to import! Please try again or consult support!!');
    }

}