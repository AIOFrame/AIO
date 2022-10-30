<?php

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
        'logo_light',
        'logo_dark',
        'primary_color_dark',
        'secondary_color_dark',
        'color_dark',
        'fav'
    ];
    public array $input_options = [
        'input_radius',
        'input_border_top',
        'input_border_right',
        'input_border_bottom',
        'input_border_left',
        'input_margin_top',
        'input_margin_right',
        'input_margin_bottom',
        'input_margin_left',
        'input_padding_top',
        'input_padding_right',
        'input_padding_bottom',
        'input_padding_left',
        'input_bg_light',
        'input_border_color_light',
        'input_border_color_active_light',
        'input_color_light',
        'input_color_active_light',
        'input_bg_dark',
        'input_border_color_dark',
        'input_border_color_active_dark',
        'input_color_dark',
        'input_color_active_dark',
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
     * @param bool $dark_mode_options Show Dark Mode Options
     * @return void
     */
    function brand_options( bool $dark_mode_options = true ): void {
        $f = new FORM();
        $db = new DB();
        $brands = $this->brand_options;
        $ops = $db->get_options( $brands );
        echo '<div class="row"';
        $f->option_params( 'brand', 2, 2, $brands );
        echo '>';
        $attr = 'data-brand';
        $ext = 'jpg,svg,png';
        $name = $ops['app_name'] ?? '';
        $desc = $ops['app_desc'] ?? '';
        $theme = $ops['default_theme'] ?? '';
        $c1 = $ops['primary_color'] ?? '';
        $c2 = $ops['secondary_color'] ?? '';
        $c3 = $ops['color_light'] ?? '';
        $dc1 = $ops['primary_color_dark'] ?? '';
        $dc2 = $ops['secondary_color_dark'] ?? '';
        $dc3 = $ops['color_dark'] ?? '';
        $light = $ops['logo_light'] ?? '';
        $dark = $ops['logo_dark'] ?? '';
        $fav = $ops['fav'] ?? '';$uis = [ 'default' => 'Default - Light' ];
        $ui_list = scandir( ROOTPATH . 'assets/styles/portal/ui' );
        foreach( $ui_list as $ui ) {
            if( str_contains( $ui, '.scss' ) ) {
                $s = str_replace( '.scss', '', $ui );
                $uis[ $s ] = ucwords( str_replace( '-', ' ', $s ) );
            }
        }
        $f->text('app_name','Web App / Site Name','Ex: AIO University...',$name,$attr,8);
        $f->upload('fav','Fav Icon','Upload',$fav,0,0,'upload',$attr,'png,jpg,doc',5,1,'',4);
        $f->textarea('app_desc','Web App / Site Description','Ex: We provide...',$desc,$attr,12);
        $f->select2( 'default_theme', 'Default Admin Theme', 'Select Theme...', $uis, $theme, $attr, 12, 1 );
        //$f->select( 'input_theme', 'Input Style', 'Select Theme...', [], '', 'data-data class="select2"', 6, 1 );
        $f->upload('logo_light','Logo - Light Theme','Upload',$light,0,0,'upload',$attr,$ext,5,1,'',3);
        $f->color('primary_color','Primary Color - Light','Ex: F1F1F1',$c1,$attr,3,'','[data-key=primary_color]');
        $f->color('secondary_color','Secondary Color - Light','Ex: A2A2A2',$c2,$attr,3,'','[data-key=secondary_color]');
        $f->color('color_light','Content Color on Gradient - Light','Ex: A2A2A2',$c3,$attr,3,'','[data-key=color_light]');
        if( $dark_mode_options ) {
            $f->upload('logo_dark','Logo - Dark Theme','Upload',$dark,0,0,'upload',$attr,$ext,5,1,'',3);
            $f->color('primary_color_dark','Primary Color - Dark','Ex: F1F1F1',$dc1,$attr,3,'','[data-key=primary_color_dark]');
            $f->color('secondary_color_dark','Secondary Color - Dark','Ex: A2A2A2',$dc2,$attr,3,'','[data-key=secondary_color_dark]');
            $f->color('color_dark','Content Color on Gradient - Dark','Ex: A2A2A2',$dc3,$attr,3,'','[data-key=color_dark]');
        }
        $f->process_options('Save Brand Options','store grad','','col-12 tac');
        file_upload();
        get_scripts('iro,color');
        echo '</div>';
    }

    /**
     * @param bool $dark_mode_options Show Dark Mode Options
     * @return void
     */
    function input_options( bool $dark_mode_options = true ): void {
        $f = new FORM();
        $db = new DB();
        $options = $this->input_options;
        $ops = $db->get_options( $options );
        echo '<div class="row"';
        $f->option_params( 'input', 2, 2, $options );
        echo '>';
        $attr = 'data-input';
        $radius = $ops['input_radius'] ?? 8;
        $bt = $ops['input_border_top'] ?? 2;
        $br = $ops['input_border_right'] ?? 2;
        $bb = $ops['input_border_bottom'] ?? 2;
        $bl = $ops['input_border_left'] ?? 2;
        $mt = $ops['input_margin_top'] ?? 0;
        $mr = $ops['input_margin_right'] ?? 0;
        $mb = $ops['input_margin_bottom'] ?? 15;
        $ml = $ops['input_margin_left'] ?? 0;
        $pt = $ops['input_padding_top'] ?? 12;
        $pr = $ops['input_padding_right'] ?? 12;
        $pb = $ops['input_padding_bottom'] ?? 12;
        $pl = $ops['input_padding_left'] ?? 12;
        $bgl = $ops['input_bg_light'] ?? '#ffffff';
        $bcl = $ops['input_border_color_light'] ?? '#eeeeee';
        $bal = $ops['input_border_color_active_light'] ?? '#eeeeee';
        $cl = $ops['input_color_light'] ?? '#000000';
        $cal = $ops['input_color_active_light'] ?? '#000000';
        $bgd = $ops['input_bg_dark'] ?? '';
        $bcd = $ops['input_border_color_dark'] ?? '';
        $bad = $ops['input_border_color_active_dark'] ?? '';
        $cd = $ops['input_color_dark'] ?? '';
        $cad = $ops['input_color_active_dark'] ?? '';
        $f->input('number','input_radius','Corner Radius (Px)','Ex: 5',$radius,$attr,4);
        $f->input('number','input_border_top','Border - Top (Px)','Ex: 2',$bt,$attr,2);
        $f->input('number','input_border_right','Border - Right (Px)','Ex: 2',$br,$attr,2);
        $f->input('number','input_border_bottom','Border - Bottom (Px)','Ex: 2',$bb,$attr,2);
        $f->input('number','input_border_left','Border - Left (Px)','Ex: 2',$bl,$attr,2);
        echo '<div class="col-6"><h4 class="mt0 ttu">'.T('Padding').' (Px)</h4><div class="row">';
        $f->input('number','input_padding_top','Top','Ex: 2',$pt,$attr,3);
        $f->input('number','input_padding_right','Right','Ex: 2',$pr,$attr,3);
        $f->input('number','input_padding_bottom','Bottom','Ex: 2',$pb,$attr,3);
        $f->input('number','input_padding_left','Left','Ex: 2',$pl,$attr,3);
        echo '</div></div><div class="col-6"><h4 class="mt0 ttu">'.T('Margin').' (Px)</h4><div class="row">';
        $f->input('number','input_margin_top','Top','Ex: 2',$mt,$attr,3);
        $f->input('number','input_margin_right','Right','Ex: 2',$mr,$attr,3);
        $f->input('number','input_margin_bottom','Bottom','Ex: 2',$mb,$attr,3);
        $f->input('number','input_margin_left','Left','Ex: 2',$ml,$attr,3);
        echo '</div></div>';
        echo '<div class="col-12"><h4 class="mt0 ttu">'.T('Light Theme Options').'</h4></div>';
        $f->color('input_bg_light','Background','Ex: F1F1F1',$bgl,$attr,4,'','[data-key=input_bg_light]');
        $f->color('input_border_color_light','Border Color','Ex: F1F1F1',$bcl,$attr,2,'','[data-key=input_border_color_light]');
        $f->color('input_border_color_active_light','Border Color - Active','Ex: F1F1F1',$bal,$attr,2,'','[data-key=input_border_color_active_light]');
        $f->color('input_color_light','Text Color','Ex: F1F1F1',$cl,$attr,2,'','[data-key=input_color_light]');
        $f->color('input_color_active_light','Text Color - Active','Ex: F1F1F1',$cal,$attr,2,'','[data-key=input_color_active_light]');
        if( $dark_mode_options ) {
            echo '<div class="col-12"><h4 class="mt0 ttu">'.T('Dark Theme Options').'</h4></div>';
            $f->color('input_bg_dark','Background','Ex: F1F1F1',$bgd,$attr,4,'','[data-key=input_bg_dark]');
            $f->color('input_border_color_dark','Border Color','Ex: F1F1F1',$bcd,$attr,2,'','[data-key=input_border_color_dark]');
            $f->color('input_border_color_active_dark','Border Color - Active','Ex: F1F1F1',$bad,$attr,2,'','[data-key=input_border_color_active_dark]');
            $f->color('input_color_dark','Text Color','Ex: F1F1F1',$cd,$attr,2,'','[data-key=input_color_dark]');
            $f->color('input_color_active_dark','Text Color - Active','Ex: F1F1F1',$cad,$attr,2,'','[data-key=input_color_active_dark]');
        }
        $f->process_options('Save Brand Options','store grad','','col-12 tac');
        echo '</div>';
    }

    /**
     * Renders Maps Options
     * @return void
     */
    function font_options(): void {
        $f = new FORM();
        $db = new DB();
        $options_array = [ 'font_1', 'font_1_weights', 'font_2', 'font_2_weights' ];
        $ops = $db->get_options( $options_array );
        echo '<div class="row"';
        $f->option_params('fonts', 2, 2, $options_array );
        echo '>';
        $font_1 = $ops['font_1'] ?? 'Lato';
        $font_1_weights = $ops['font_1_weights'] ?? '400';
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
        $f->select2('font_1','Primary Font','Select Font...',$fonts,$font_1,$attr,6);
        $f->select2('font_1_weights','Primary Font Weights','Select Weights...',$weights,$font_1_weights,$attr.' multiple',6);
        $f->select2('font_2','Primary Font','Select Font...',$fonts,$font_2,$attr,6);
        $f->select2('font_2_weights','Primary Font Weights','Select Weights...',$weights,$font_2_weights,$attr.' multiple',6);
        $f->process_options('Save Map Options','store grad','','col-12 tac');
        echo '</div>';
    }

    /**
     * Renders Maps Options
     * @return void
     */
    function map_options(): void {
        $f = new FORM();
        $db = new DB();
        $options_array = [ 'google_maps_key', 'default_map_lat', 'default_map_long', 'default_map_zoom', 'default_map_type' ];
        $ops = $db->get_options( $options_array );
        echo '<div class="row"';
        $f->option_params('google-map', 2, 2 );
        echo '>';
        $key = $ops['google_maps_key'] ?? '';
        $lat = $ops['default_map_lat'] ?? '';
        $long = $ops['default_map_long'] ?? '';
        $zoom = $ops['default_map_zoom'] ?? 13;
        $type = $ops['default_map_type'] ?? 'terrain';
        $attr = 'data-google-map';
        $f->text('google_maps_key','Google Maps - API Key','Ex: AIvcDfDtd04QuAYdfgRN-aZBF5DuSFhMUnbdehD9',$key,$attr,12);
        $f->map( '[data-key=default_map_lat]', '[data-key=default_map_long]', '', '', '', '', '', 12 );
        $f->text('default_map_lat','Default Map Latitude','Ex: 12.34233',$lat,$attr,3);
        $f->text('default_map_long','Default Map Longitude','Ex: 24.43555',$long,$attr,3);
        $f->select2('default_map_zoom','Default Zoom Level','Select Level...',range(0,19),$zoom,$attr,3);
        $f->select2('default_map_type','Default Map Type','Select Type...',['roadmap','satellite','hybrid','terrain'],$type,$attr,3);
        $f->process_options('Save Map Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function communication_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options( $this->communication_options );
        echo '<div class="row"';
        $f->option_params('com',2,2);
        echo '>';
        $phone = $os['phone'] ?? '';
        $mobile = $os['mobile'] ?? '';
        $email = $os['email'] ?? '';
        $website = $os['website'] ?? '';
        $f->text('phone','Official Phone No.','Ex: 403334444',$phone,'data-com',3);
        $f->text('mobile','Mobile No. for Social Media','Ex: 503334444',$mobile,'data-com',3);
        $f->text('email','Official Email','Ex: hello@company.com',$email,'data-com',3);
        $f->text('website','Official Website','Ex: company.com',$website,'data-com',3);
        $f->process_options('Save Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function address_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['address','city','state','post','country']);
        echo '<div class="row"';
        $f->option_params('add',2,2);
        echo '>';
        $address = $os['address'] ?? '';
        $city = $os['city'] ?? '';
        $state = $os['state'] ?? '';
        $post = $os['post'] ?? '';
        $country = $os['country'] ?? 'United Arab Emirates';
        $countries = get_countries();
        $f->text('address','Address','Ex: Office 1100, Building Name, Street Name...',$address,'data-add',12);
        $f->text('city','City','Ex: Burlington',$city,'data-add',3);
        $f->text('state','State','Ex: Burlington',$state,'data-add',3);
        $f->text('postal','Postal Code','Ex: 110250',$post,'data-add',3);
        $f->select('country','Country','Choose Country...',$countries,$country,'class="select2" data-add',3,'',0,0);
        $f->process_options('Save Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function tax_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['reg_name','reg','trn','tax']);
        echo '<div class="row"';
        $f->option_params('cd',2,2);
        echo '>';
        $reg_name = $os['reg_name'] ?? '';
        $reg = $os['reg'] ?? '';
        $trn = $os['trn'] ?? '';
        $tax = $os['tax'] ?? '';
        $f->text('reg_name','Registered Name','Ex: ABC Trading LLC.',$reg_name,'data-cd',5);
        $f->text('reg','Registration No.','Ex: 120-12565-132665',$reg,'data-cd',3);
        $f->text('trn','TRN No.','Ex: 3562-2654-8954',$trn,'data-cd',3);
        $f->input('number','tax','Tax%','Ex: 5',$tax,'min="0" max="50" data-cd',1);
        $f->process_options('Save Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function social_options(): void {
        $options = $this->social_options;
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options( $options );
        echo '<div class="row"';
        $f->option_params('soc',2,2);
        echo '>';
        foreach( $options as $ok => $ov ) {
            $val = $os[ $ok ] ?? '';
            $f->text( $ok, $ov, 'Ex: '.$ov.' Details', $val, 'data-soc', 3 );
        }
        $f->process_options('Save Options','store grad','','col-12 tac');
        echo '</div>';
    }

    /**
     * Renders Stripe API Options
     * @return void
     */
    function stripe_options(): void {
        $s = new STRIPE();
        $s->options();
    }

}