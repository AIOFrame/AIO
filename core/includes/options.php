<?php

global $options;
//skel( $options );

class OPTIONS {

    function brand_options(): void {
        $f = new FORM();
        $db = new DB();
        $brands = [ 'app_name', 'default_theme', 'primary_color', 'secondary_color', 'logo_light', 'logo_dark', 'primary_color_dark', 'secondary_color_dark', 'fav' ];
        $ops = $db->get_options( $brands );
        echo '<div class="row"';
        $f->option_params( 'brand', 2, 2, $brands );
        echo '>';
        $attr = 'data-brand';
        $ext = 'jpg,svg,png';
        $name = $ops['app_name'] ?? '';
        $theme = $ops['default_theme'] ?? '';
        $c1 = $ops['primary_color'] ?? '';
        $c2 = $ops['secondary_color'] ?? '';
        $dc1 = $ops['primary_color_dark'] ?? '';
        $dc2 = $ops['secondary_color_dark'] ?? '';
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
        $f->upload('fav','Fav Icon','Upload',$fav,0,0,'upload',$attr,'png',5,1,'',4);
        $f->select2( 'default_theme', 'Default Admin Theme', 'Select Theme...', $uis, $theme, $attr, 12, 1 );
        //$f->select( 'input_theme', 'Input Style', 'Select Theme...', [], '', 'data-data class="select2"', 6, 1 );
        $f->upload('logo_light','Logo - Light Theme','Upload',$light,0,0,'upload',$attr,$ext,5,1,'',4);
        $f->color('primary_color','Primary Color - Light','Ex: F1F1F1',$c1,$attr,4,'','#primary_color');
        $f->color('secondary_color','Secondary Color - Light','Ex: A2A2A2',$c2,$attr,4,'','#secondary_color');
        $f->upload('logo_dark','Logo - Dark Theme','Upload',$dark,0,0,'upload',$attr,$ext,5,1,'',4);
        $f->color('primary_color_dark','Primary Color - Dark','Ex: F1F1F1',$dc1,$attr,4,'','#primary_color_dark');
        $f->color('secondary_color_dark','Secondary Color - Dark','Ex: A2A2A2',$dc2,$attr,4,'','#secondary_color_dark');
        $f->process_options('Save Brand Options','store grad','','col-12 tac');
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
        $os = $db->get_options(['phone','mobile','email','website']);
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
        $f->text('reg_name','Registered Name','Ex: ABC Trading LLC.',$reg_name,'data-cd',3);
        $f->text('reg','Registration No.','Ex: 120-12565-132665',$reg,'data-cd',3);
        $f->text('trn','TRN No.','Ex: 3562-2654-8954',$trn,'data-cd',3);
        $f->input('number','tax','Generic Tax Percent (%)','Ex: 5',$tax,'min="0" max="50" data-cd',3);
        $f->process_options('Save Options','store grad','','col-12 tac');
        echo '</div>';
    }

    function social_options(): void {
        $f = new FORM();
        $db = new DB();
        $os = $db->get_options(['fb','tw','ig','yt','wa','tg','pin','sk','ln','mob','ll']);
        echo '<div class="row"';
        $f->option_params('soc',2,2);
        echo '>';
        $fb = $os['fb'] ?? '';
        $tw = $os['tw'] ?? '';
        $ig = $os['ig'] ?? '';
        $yt = $os['yt'] ?? '';
        $wa = $os['wa'] ?? '';
        $tg = $os['tg'] ?? '';
        $pin = $os['pin'] ?? '';
        $sk = $os['sk'] ?? '';
        $ln = $os['ln'] ?? '';
        $mob = $os['mob'] ?? '';
        $ll = $os['ll'] ?? '';
        $f->texts([
            ['fb','Facebook Link','',$fb],
            ['tw','Twitter Link','',$tw],
            ['ig','Instagram Link','',$ig],
            ['yt','Youtube Link','',$yt],
            ['wa','Whatsapp Link','',$wa],
            ['tg','Telegram Link','',$tg],
            ['pin','Pinterest Link','',$pin],
            ['sk','Pinterest Link','',$sk],
            ['ln','LinkedIn Link','',$ln],
            ['mob','Mobile Number','',$mob],
            ['ll','Mobile Number','',$ll],
        ],'data-soc',3);
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