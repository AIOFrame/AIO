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
        $brands = $this->brand_options;
        $ops = $db->get_options( $brands );
        $f->option_params_wrap( 'brand', 2, 2, $brands );
        $attr = 'data-brand';
        $ext = 'jpg,svg,png';
        $name = !empty( $ops['app_name'] ) ? $ops['app_name'] : 'fake_name';
        $desc = !empty( $ops['app_desc'] ) ? $ops['app_desc'] : 'fake_text';
        $theme = !empty( $ops['default_theme'] ) ? $ops['default_theme'] : '';
        $c1 = !empty( $ops['primary_color'] ) ? $ops['primary_color'] : 'fake_hex';
        $c2 = !empty( $ops['secondary_color'] ) ? $ops['secondary_color'] : 'fake_hex';
        $c3 = !empty( $ops['color_light'] ) ? $ops['color_light'] : 'fake_hex';
        $c4 = !empty( $ops['filled_color_light'] ) ? $ops['filled_color_light'] : 'fake_hex';
        $dc1 = !empty( $ops['primary_color_dark'] ) ? $ops['primary_color_dark'] : 'fake_hex';
        $dc2 = !empty( $ops['secondary_color_dark'] ) ? $ops['secondary_color_dark'] : 'fake_hex';
        $dc3 = !empty( $ops['color_dark'] ) ? $ops['color_dark'] : 'fake_hex';
        $dc4 = !empty( $ops['filled_color_dark'] ) ? $ops['filled_color_dark'] : 'fake_hex';
        $light = !empty( $ops['logo_light'] ) ? $ops['logo_light'] : '';
        $dark = !empty( $ops['logo_dark'] ) ? $ops['logo_dark'] : '';
        $fav = !empty( $ops['fav'] ) ? $ops['fav'] : '';
        $uis = [ 'default' => 'Default - Light' ];
        $ui_list = scandir( ROOTPATH . 'assets/styles/portal/ui' );
        foreach( $ui_list as $ui ) {
            if( str_contains( $ui, '.scss' ) ) {
                $s = str_replace( '.scss', '', $ui );
                $uis[ $s ] = ucwords( str_replace( '-', ' ', $s ) );
            }
        }
        $f->text('app_name','Web App / Site Name','Ex: AIO University...',$name,$attr,8);
        $f->upload('fav','Fav Icon','Upload',$fav,0,0,'upload',$attr,'png',.1,1,'',4);
        $f->textarea('app_desc','Web App / Site Description','Ex: We provide...',$desc,$attr,12);
        $f->select2( 'default_theme', 'Default Admin Theme', 'Select Theme...', $uis, $theme, $attr, 12, 1 );
        //$f->select( 'input_theme', 'Input Style', 'Select Theme...', [], '', 'data-data class="select2"', 6, 1 );
        echo '<h3 class="col-12">'.T('Light Color Options').'</h3>';
        $f->upload('logo_light','Logo','Upload',$light,0,0,'upload',$attr,$ext,.2,1,'',4);
        $f->color('primary_color','Primary','Ex: F1F1F1',$c1,$attr,2,'','[data-key=primary_color]');
        $f->color('secondary_color','Secondary','Ex: A2A2A2',$c2,$attr,2,'','[data-key=secondary_color]');
        $f->color('color_light','Content Color','Ex: A2A2A2',$c3,$attr,2,'','[data-key=color_light]');
        $f->color('filled_color_light','Filled Content Color','Ex: A2A2A2',$c4,$attr,2,'','[data-key=filled_color_light]');
        if( $dark_mode_options ) {
            echo '<h3 class="col-12">'.T('Dark Color Options').'</h3>';
            $f->upload('logo_dark','Logo','Upload',$dark,0,0,'upload',$attr,$ext,.2,1,'',4);
            $f->color('primary_color_dark','Primary','Ex: F1F1F1',$dc1,$attr,2,'','[data-key=primary_color_dark]');
            $f->color('secondary_color_dark','Secondary','Ex: A2A2A2',$dc2,$attr,2,'','[data-key=secondary_color_dark]');
            $f->color('color_dark','Content Color','Ex: A2A2A2',$dc3,$attr,2,'','[data-key=color_dark]');
            $f->color('filled_color_dark','Filled Content Color','Ex: A2A2A2',$dc4,$attr,2,'','[data-key=filled_color_dark]');
        }
        $f->process_options('Save Brand Options','store grad','','.col-12 tac');
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
        $options_array = [ 'font_1', 'font_1_weights', 'font_weight', 'font_2', 'font_2_weights' ];
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
        $f->select2('font_1','Primary Font','Select Font...',$fonts,$font_1,$attr,6);
        $f->select2('font_1_weights','Font Weights','Select Weights...',$weights,$font_1_weights,$attr.' multiple',4);
        $f->select2('font_weight','Default Weight','Select...',$weights,$font_weight,$attr,2);
        $f->select2('font_2','Secondary Font','Select Font...',$fonts,$font_2,$attr,6);
        $f->select2('font_2_weights','Secondary Font Weights','Select Weights...',$weights,$font_2_weights,$attr.' multiple',4);
        //$f->select2('font_2_weight','Default Weight','Select...',$weights,$font_2_weight,$attr,2);
        $f->process_options('Save Map Options','store grad','','.col-12 tac');
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
        $f->option_params_wrap('google-map', 2, 2 );
        $key = $ops['google_maps_key'] ?? '';
        $lat = !empty( $ops['default_map_lat'] ) ? $ops['default_map_lat'] : 'fake_lat';
        $long = !empty( $ops['default_map_long'] ) ? $ops['default_map_long'] : 'fake_long';
        $zoom = $ops['default_map_zoom'] ?? 13;
        $type = !empty( $ops['default_map_type'] ) ? $ops['default_map_type'] : 'terrain';
        $attr = 'data-google-map';
        $f->text('google_maps_key','Google Maps - API Key','Ex: AIvcDfDtd04QuAYdfgRN-aZBF5DuSFhMUnbdehD9',$key,$attr,12);
        $f->map( '[data-key=default_map_lat]', '[data-key=default_map_long]', '', '', '', '', '', 12 );
        $f->text('default_map_lat','Default Map Latitude','Ex: 12.34233',$lat,$attr,3);
        $f->text('default_map_long','Default Map Longitude','Ex: 24.43555',$long,$attr,3);
        $f->select2('default_map_zoom','Default Zoom Level','Select Level...',range(0,19),$zoom,$attr,3);
        $f->select2('default_map_type','Default Map Type','Select Type...',['roadmap','satellite','hybrid','terrain'],$type,$attr,3);
        $f->process_options('Save Map Options','store grad','','.col-12 tac');
        echo '</div>';
    }

    function communication_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options( $this->communication_options );
        //skel( $os );
        $f->option_params_wrap('com',2,2);
        $phone = !empty( $os['phone'] ) ? $os['phone'] : 'fake_phone';
        $mobile = !empty( $os['mobile'] ) ? $os['mobile'] : 'fake_phone';
        $email = !empty( $os['email'] ) ? $os['email'] : 'fake_email';
        $website = !empty( $os['website'] ) ? $os['website'] : 'fake_site';
        $f->text('phone','Official Phone No.','Ex: 403334444',$phone,'data-com',3);
        $f->text('mobile','Mobile No. for Social Media','Ex: 503334444',$mobile,'data-com',3);
        $f->text('email','Official Email','Ex: hello@company.com',$email,'data-com',3);
        $f->text('website','Official Website','Ex: company.com',$website,'data-com',3);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
    }

    function address_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['address','city','state','post','country']);
        $f->option_params_wrap('add',2,2);
        $address = !empty( $os['address'] ) ? $os['address'] : 'fake_address';
        $city = !empty( $os['city'] ) ? $os['city'] : 'fake_city';
        $state = !empty( $os['state'] ) ? $os['state'] : 'fake_state';
        $post = !empty( $os['post'] ) ? $os['post'] : 'fake_po';
        $country = $os['country'] ?? 'United Arab Emirates';
        $countries = get_countries();
        $f->text('address','Address','Ex: Office 1100, Building Name, Street Name...',$address,'data-add',12);
        $f->text('city','City','Ex: Burlington',$city,'data-add',3);
        $f->text('state','State','Ex: Burlington',$state,'data-add',3);
        $f->text('postal','Postal Code','Ex: 110250',$post,'data-add',3);
        $f->select2('country','Country','Choose Country...',$countries,$country,'data-add',3);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
    }

    function tax_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['reg_name','reg','trn','tax']);
        $f->option_params_wrap('cd',2,2);
        $reg_name = $os['reg_name'] ?? '';
        $reg = $os['reg'] ?? '';
        $trn = $os['trn'] ?? '';
        $tax = $os['tax'] ?? '';
        $f->text('reg_name','Registered Name','Ex: ABC Trading LLC.',$reg_name,'data-cd',4);
        $f->text('reg','Registration No.','Ex: 120-12565-132665',$reg,'data-cd',3);
        $f->text('trn','TRN No.','Ex: 3562-2654-8954',$trn,'data-cd',3);
        $f->input('number','tax','Tax%','Ex: 5',$tax,'min="0" max="50" data-cd',2);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
        // TODO : Add Regional Options [ 'Country', 'Timezone', 'Currency', 'Currency Symbol', 'Tax', 'Date Format', 'Time Format' ]
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

    function region_options(): void {
        $c = defined( 'CONFIG' ) && !empty( CONFIG ) ? json_decode( CONFIG, 1 ) : [];
        if( !empty( $c['features'] ) && in_array( 'region', $c['features'] ) ) {
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
            $f->text( $ok, $ov, 'Ex: '.$ov.' Details', $val, 'data-soc', 3 );
        }
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
    }

    function region_switch( string $label = '', string $load_text = 'Go' ): void {
        $f = new FORM();
        $d = new DB();
        $regions = $d->select( 'regions' );
        $regions = !empty( $regions ) ? array_to_assoc( $regions, 'reg_id', 'reg_country' ) : [];
        echo '<div data-t class="row">';
        $f->select2( 'option_region', $label, 'Select...', $regions, $_SESSION['option_region'] ?? '', '', 8, 1 );
        $f->process_html( $load_text, 'store grad', '', 'set_option_region_ajax', 4 );
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
        $f->select2('languages','Select Languages for Translations','Choose Languages...',$all_languages,$languages,'data-al multiple',12,1);
        $f->text('languages_updated','','',1,'hidden data-al');
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '</div>';
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
        $f->process_html('Import Options','store grad','','import_options_ajax','.col-6 tac');
        echo '</div>';
    }

    /**
     * Renders ACCESS page options
     * @param string $default_background Default repeating background
     * @param bool $show_register Show register link
     * @param int $reload_seconds Seconds to reload page after login / register
     * @param int $hours Default hours to remember access
     * @param bool $show_remember Show remember options
     * @param string $login_button_text Login button text
     * @param string $username_text Username label text
     * @param string $password_text Password label text
     * @param string $forgot_password_text Forgot password text
     * @param string $reset_pass_text Reset password button text
     * @param string $return_to_login_text Return to Log in link text
     * @return void
     */
    function access_options( string $default_background = '', bool $show_register = false, int $reload_seconds = 2, int $hours = 24, bool $show_remember = false, string $login_button_text = 'LOGIN', string $username_text = 'User Login / Email', string $password_text = 'Password', string $forgot_password_text = 'Forgot Password ?', string $reset_pass_text = 'RESET PASSWORD', string $return_to_login_text = 'Return to Login' ): void {
        $a = new ACCESS();
        $a->access_options();
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

}

function set_option_region_ajax(): void {
    if( isset( $_POST['option_region'] ) && !empty( $_POST['option_region'] ) ) {
        $_SESSION['option_region'] = $_POST['option_region'];
        es('Successfully changed option region! Now you can change settings!!');
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