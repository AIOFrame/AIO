<?php

// global $options;
// skel( $options );

class OPTIONS {

    function brand_options(): void {
        $f = new FORM();
        $db = new DB();
        $brands = [ 'app_name', 'primary_color', 'secondary_color', 'logo_light', 'logo_dark', 'primary_color_dark', 'secondary_color_dark', 'fav' ];
        $ops = $db->get_options( $brands );
        echo '<div class="row"';
        $f->option_params( 'brand', 2, 2, $brands, [], [] );
        echo '>';
        $attr = 'data-brand';
        $ext = 'jpg,svg,png';
        $name = $ops['app_name'] ?? '';
        $c1 = $ops['primary_color'] ?? '';
        $c2 = $ops['secondary_color'] ?? '';
        $dc1 = $ops['primary_color_dark'] ?? '';
        $dc2 = $ops['secondary_color_dark'] ?? '';
        $light = $ops['logo_light'] ?? '';
        $dark = $ops['logo_dark'] ?? '';
        $fav = $ops['fav'] ?? '';
        $f->text('app_name','App Name','Ex: AIO University...',$name,$attr,8);
        $f->upload('fav','Fav Icon','Upload',$fav,0,0,'upload',$attr,'png',5,1,'',4);
        $f->upload('logo_light','Logo - Light Theme','Upload',$light,0,0,'upload',$attr,$ext,5,1,'',4);
        $f->color('primary_color','Primary Color','Ex: F1F1F1',$c1,$attr,4,'','#primary_color');
        $f->color('secondary_color','Secondary Color','Ex: A2A2A2',$c2,$attr,4,'','#secondary_color');
        $f->upload('logo_dark','Logo - Dark','Upload',$dark,0,0,'upload',$attr,$ext,5,1,'',4);
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

    /**
     * Renders Stripe API Options
     * @return void
     */
    function stripe_options(): void {
        $s = new STRIPE();
        $s->options();
    }

}