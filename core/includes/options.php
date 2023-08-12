<?php
// TODO: Generic error messages, Data Missing, Data Insert Failure, Other Failure
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

    /**
     * Renders app branding options
     * @param bool $dark_mode_options Show Dark Mode Options
     * @return void
     */
    function brand_options( bool $dark_mode_options = true ): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $brands = defined( 'REGION' ) ? pre_keys( $this->brand_options, $r ) : $this->brand_options;
        $ops = $db->get_options( $brands );
        $f->option_params_wrap( 'brand', 2, 2, $brands );
        $attr = 'data-brand';
        $ext = 'jpg,svg,png';
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
        $this->region_notice();
        echo '</div>';
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
        $f->option_params_wrap( 'colors', 2, 2, $colors );
        foreach( $colors as $c => $cv ) {
            $f->color($c,ucwords(str_replace('_',' ',$c)),'Ex: F1F1F1',$ops[$c]??$cv,$attr,0,'','[data-key='.$c.']');
        }
        $f->process_options('Save Color Options','store grad','','.col-12 tac');
        echo '</div>';
        get_scripts('iro,color');
    }

    /**
     * Renders input and button design options
     * @param bool $dark_mode_options Show Dark Mode Options
     * @return void
     */
    function input_options( bool $dark_mode_options = true ): void {
        $f = new FORM();
        $db = new DB();
        $v = [];
        $options = $f->input_options;
        //skel( $options );
        $ops = $db->get_options( $options );
        $f->option_params_wrap( 'input', 2, 2, $options );
        $attr = 'data-input';
        foreach( $options as $iok => $iov ) {
            $v[ $iok ] = $ops[ $iok ] ?? $iov;
        }
        $f->input('number','input_radius','Corner Radius (Px)','Ex: 5',$v['input_radius'],$attr,4);
        $f->input('number','input_border_top','Border - Top (Px)','Ex: 2',$v['input_border_top'],$attr,2);
        $f->input('number','input_border_right','Border - Right (Px)','Ex: 2',$v['input_border_right'],$attr,2);
        $f->input('number','input_border_bottom','Border - Bottom (Px)','Ex: 2',$v['input_border_bottom'],$attr,2);
        $f->input('number','input_border_left','Border - Left (Px)','Ex: 2',$v['input_border_left'],$attr,2);
        echo '<div class="col-6"><h4 class="mt0 ttu">'.T('Padding').' (Px)</h4><div class="row">';
        $f->input('number','input_padding_top','Top','Ex: 2',$v['input_padding_top'],$attr,3);
        $f->input('number','input_padding_right','Right','Ex: 2',$v['input_padding_right'],$attr,3);
        $f->input('number','input_padding_bottom','Bottom','Ex: 2',$v['input_padding_bottom'],$attr,3);
        $f->input('number','input_padding_left','Left','Ex: 2',$v['input_padding_left'],$attr,3);
        echo '</div></div><div class="col-6"><h4 class="mt0 ttu">'.T('Margin').' (Px)</h4><div class="row">';
        $f->input('number','input_margin_top','Top','Ex: 2',$v['input_margin_top'],$attr,3);
        $f->input('number','input_margin_right','Right','Ex: 2',$v['input_margin_right'],$attr,3);
        $f->input('number','input_margin_bottom','Bottom','Ex: 2',$v['input_margin_bottom'],$attr,3);
        $f->input('number','input_margin_left','Left','Ex: 2',$v['input_margin_left'],$attr,3);
        echo '</div></div>';
        echo '<div class="col-12"><h4 class="mt0 ttu">'.T('Light Theme Options').'</h4></div>';
        $f->color('input_bg_light','Background','Ex: F1F1F1',$v['input_bg_light'],$attr,4,'','[data-key=input_bg_light]');
        $f->color('input_border_color_light','Border Color','Ex: F1F1F1',$v['input_border_color_light'],$attr,2,'','[data-key=input_border_color_light]');
        $f->color('input_border_color_active_light','Border Color - Active','Ex: F1F1F1',$v['input_border_color_active_light'],$attr,2,'','[data-key=input_border_color_active_light]');
        $f->color('input_color_light','Text Color','Ex: F1F1F1',$v['input_color_light'],$attr,2,'','[data-key=input_color_light]');
        $f->color('input_color_active_light','Text Color - Active','Ex: F1F1F1',$v['input_color_active_light'],$attr,2,'','[data-key=input_color_active_light]');
        if( $dark_mode_options ) {
            echo '<div class="col-12"><h4 class="mt0 ttu">'.T('Dark Theme Options').'</h4></div>';
            $f->color('input_bg_dark','Background','Ex: F1F1F1',$v['input_bg_dark'],$attr,4,'','[data-key=input_bg_dark]');
            $f->color('input_border_color_dark','Border Color','Ex: F1F1F1',$v['input_border_color_dark'],$attr,2,'','[data-key=input_border_color_dark]');
            $f->color('input_border_color_active_dark','Border Color - Active','Ex: F1F1F1',$v['input_border_color_active_dark'],$attr,2,'','[data-key=input_border_color_active_dark]');
            $f->color('input_color_dark','Text Color','Ex: F1F1F1',$v['input_color_dark'],$attr,2,'','[data-key=input_color_dark]');
            $f->color('input_color_active_dark','Text Color - Active','Ex: F1F1F1',$v['input_color_active_dark'],$attr,2,'','[data-key=input_color_active_dark]');
        }
        $f->process_options('Save Brand Options','store grad','','.col-12 tac');
        echo '</div>';
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
        $f->option_params_wrap('fonts', 2, 2, $options_array );
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
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $options_array = [ 'default_map_lat', 'default_map_long', 'default_map_zoom', 'default_map_type' ];
        $options_array = defined( 'REGION' ) ? prepare_values( $options_array, $r ) : $options_array;
        //skel( $options_array );
        $ops = $db->get_options( array_merge( $options_array, ['google_maps_key'] ) );
        //skel( $ops );
        $f->option_params_wrap('google-map', 2, 2 );
        $key = $ops['google_maps_key'] ?? '';
        $lat = !empty( $ops[$r.'default_map_lat'] ) ? $ops[$r.'default_map_lat'] : 'fake_lat';
        $v_lat = !empty( $ops[$r.'default_map_lat'] ) ? $ops[$r.'default_map_lat'] : '';
        $long = !empty( $ops[$r.'default_map_long'] ) ? $ops[$r.'default_map_long'] : 'fake_long';
        $v_long = !empty( $ops[$r.'default_map_long'] ) ? $ops[$r.'default_map_long'] : '';
        $zoom = $ops[$r.'default_map_zoom'] ?? 13;
        $type = !empty( $ops[$r.'default_map_type'] ) ? $ops[$r.'default_map_type'] : 'terrain';
        $attr = 'data-google-map';
        $f->text('google_maps_key','Google Maps - API Key','Ex: AIvcDfDtd04QuAYdfgRN-aZBF5DuSFhMUnbdehD9',$key,$attr,12);
        $f->map( '[data-key='.$r.'default_map_lat]', '[data-key='.$r.'default_map_long]', '', '', '', '', '', 12, $v_lat, $v_long, $zoom, $type );
        $f->text($r.'default_map_lat','Default Map Latitude','Ex: 12.34233',$lat,$attr,3);
        $f->text($r.'default_map_long','Default Map Longitude','Ex: 24.43555',$long,$attr,3);
        $f->select2($r.'default_map_zoom','Default Zoom Level','Select Level...',range(0,19),$zoom,$attr,3);
        $f->select2($r.'default_map_type','Default Map Type','Select Type...',['roadmap','satellite','hybrid','terrain'],$type,$attr,3);
        $f->process_options($this->region_flag().'Save Map Options','store grad','','.col-12 tac');
        $this->region_notice();
        echo '</div>';
    }

    function communication_options(): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $comm_options = defined( 'REGION' ) ? pre_keys( $this->communication_options, $r ) : $this->communication_options;
        //skel( $comm_options );
        $os = $db->get_options( $comm_options );
        //skel( $r );
        //skel( $os );
        $f->option_params_wrap('com',2,2);
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
        echo '</div>';
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
        $f->option_params_wrap('add',2,2);
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
        echo '</div>';
    }

    function finance_options(): void {
        $f = new FORM();
        $db = new DB();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $fin_ops = ['reg_name','reg','trn','tax','sign','rate','spot'];
        $fin_ops = defined( 'REGION' ) ? prepare_values( $fin_ops, $r ) : $fin_ops;
        $os = $db->get_options(array_merge($fin_ops,['primary_region']));
        $f->option_params_wrap('cd',2,2);
        $pr = $os['primary_region'] ?? 'US';
        $f->text($r.'reg_name','Registered Name','Ex: ABC Trading LLC.',$os[$r.'reg_name'] ?? '','data-cd',3);
        $f->text($r.'reg','Registration No.','Ex: 120-12565-132665',$os[$r.'reg'] ?? '','data-cd',3);
        $f->text($r.'trn','Tax Registration No.','Ex: 3562-2654-8954',$os[$r.'trn'] ?? '','data-cd',3);
        $f->input('number',$r.'tax','Tax%','Ex: 5',$os[$r.'tax'] ?? '','min="0" max="50" data-cd',3);
        $f->text($r.'rate','1 ['.$pr.'] = ? '.($os[$r.'sign'] ?? ''),'Ex: 3.67',$os[$r.'rate'] ?? '','data-cd',3);
        $f->text($r.'sign','Currency Symbol','Ex: $',$os[$r.'sign'] ?? '','data-cd',3);
        $f->slide($r.'spot','Currency Side','Left','Right',$os[$r.'spot'] ?? '','','data-cd',3);
        $f->process_options($this->region_flag().'Save Options','store grad','','.col-12 tac');
        $this->region_notice();
        echo '</div>';
    }

    function content_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['no_access_image','no_content_image']);
        $f->option_params_wrap('cn',2,2);
        $no_access_image = $os['no_access_image'] ?? '';
        $no_content_image = $os['no_content_image'] ?? '';
        $f->upload('no_access_image','Image to show when user has no access!','Upload',$no_access_image,1,0,'','data-cn','jpg,png,svg',.1,0,'',6);
        $f->upload('no_content_image','Image to show when there is no content!','Upload',$no_content_image,1,0,'','data-cn','jpg,png,svg',.1,0,'',6);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
    }

    /**
     * Renders options to set operating regions
     * @return void
     */
    function region_options(): void {
        //$c = !empty( CONFIG ) ? json_decode( CONFIG ) : [];
        //if( in_array( 'regions', $c->features ) || in_array( 'region', $c->features ) ) {
        if( defined( 'REGION' ) || defined( 'REGIONS' ) ) {
            $r = new REGION();
            $r->region_options();
        }
    }

    function social_options(): void {
        $options = $this->social_options;
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options( $options );
        $f->option_params_wrap('soc',2,2);
        foreach( $options as $ok => $ov ) {
            $val = $os[ $ok ] ?? '';
            $f->text( $ok, $ov, 'Ex: '.$ov.' URL', $val, 'data-soc', 3 );
        }
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
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
        $f->option_params_wrap('al',2,2,['languages','languages_updated']);
        $f->select2('languages','Set Languages','Choose Languages...',$all_languages,$languages,'data-al multiple',12,1);
        $f->text('languages_updated','','',1,'hidden data-al');
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '<div style="text-align:center; font-size: .8rem">English is default, you can add additional languages.</div></div>';
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
        $f->option_params_wrap( 'ei', 2, 2 );
        //$f->process_params('','ei','',2,2,[],'Successfully imported options!','','','','','','row');
        $f->textarea('export','Export Options','',$e->encrypt_array($data),'rows="5"',6);
        $f->textarea('import','Import Options','','','data-ei rows="5"',6);
        echo '<div class="col-6"></div>';
        $f->process_html('Import Options','store grad','','import_options_ajax','.col-12 tac');
        //$this->region_notice();
        echo '</div>';
    }

    /**
     * Renders ACCESS page options
     * @return void
     */
    function access_options(): void {
        $a = new ACCESS();
        $a->access_options();
    }

    /**
     * Renders Login page options
     * @return void
     */
    function login_options(): void {
        $a = new ACCESS();
        $a->access_options(2);
    }

    /**
     * Renders Register page options
     * @return void
     */
    function register_options(): void {
        $a = new ACCESS();
        $a->access_options(3);
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
            echo '<div class="tabs"><div class="tab_heads" data-store>';
            echo !empty( $enterprise ) ? '<div data-t="#aio_enterprise_options">'.T($enterprise).'</div>' : '';
            echo !empty( $brand ) ? '<div data-t="#aio_brand_options">'.T($brand).'</div>' : '';
            echo !empty( $input ) ? '<div data-t="#aio_input_options">'.T($input).'</div>' : '';
            echo !empty( $social ) ? '<div data-t="#aio_social_options">'.T($social).'</div>' : '';
            echo !empty( $languages ) ? '<div data-t="#aio_languages_options">'.T($languages).'</div>' : '';
            echo '</div></div>';
        }
        // Company Options
        // Brand Options
        // Input
        // Social
        // Languages
    }

    function region_notice(): void {
        $c = defined( 'CONFIG' ) ? json_decode( CONFIG, 1 ) : [];
        $r = defined( 'REGION' ) ? REGION : [];
        if( !empty( $r ) ) {
            $n = $r['name']['common'] ?? '';
            $f = $r['flag'] ?? '';
            echo '<div style="text-align:center; font-size: .8rem">' . T('Settings apply to region ') . $n . ' ' . $f . '</div>';
        } else if( in_array( 'regions', $c['features'] ) ) {
            echo '<div style="text-align:center; font-size: .8rem">' . T('Regions feature enabled! Please set a region on top and then save settings to be applicable!') . '</div>';
        }
    }

    function region_flag(): string {
        return defined( 'REGION' ) && isset( REGION['flag'] ) ? ' <i class="reg-flag">'.REGION['flag'].'</i> ' : '';
    }

}

function import_options_ajax(): void {
    if( isset( $_POST['import'] ) && !empty( $_POST['import'] ) ) {
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