<?php

class MAPS {

    function google_maps(): void {
        echo $this->__google_maps();
    }

    function __google_maps(): string {
        $db = new DB();
        $os = $db->get_options([ 'google_maps_key', 'default_map_marker' ]);
        $k = $os['google_maps_key'] ?? '';
        $k = empty( $k ) ? get_config('google_maps_key') : $k;
        $marker = $os['default_map_marker'] ?? APPURL.'assets/images/marker.png';
        $return = '<script>window.google_map_icon = \''.$marker.'\';window.google_maps_key = \''.$k.'\';</script>';
        if( !empty( $k ) ) {
            $return .= __get_script( 'https://maps.googleapis.com/maps/api/js', [ 'key' => $k, 'libraries' => 'places', 'callback' => 'render_google_maps' ], '', 'async', false );
            $return .= __get_script('google_maps',[],'','async');
        }
        return $return;
    }

    public array $options = [ 'default_map_lat', 'default_map_long', 'map_marker', 'show_map_search', 'show_map_full', 'default_map_zoom', 'show_map_zoom', 'default_map_type', 'show_map_type', 'map_style' ];

    /**
     * Renders Maps Options
     * @return void
     */
    function options(): void {
        $f = new FORM();
        $db = new DB();
        $o = new OPTIONS();
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $options_array = $this->options;
        $options_array = defined( 'REGION' ) ? prepare_values( $options_array, $r ) : $options_array;
        //skel( $options_array );
        $ops = $db->get_options( array_merge( $options_array, ['google_maps_key'] ) );
        //skel( $ops );
        $f->option_params_wrap('google-map', 2, 2);
        $attr = 'google-map';
        $fields = [
            [ 'i' => 'google_maps_key', 'n' => 'Google Maps - API Key', 'p' => 'Ex: AIvcDfDtd04QuAYdfgRN-aZBF5DuSFhMUnbdehD9', 'v' => $ops['google_maps_key'] ?? '', 'c' => 12 ],
            [ 'x' => '[data-key='.$r.'default_map_lat]', 'y' => '[data-key='.$r.'default_map_long]', 'xv' => $ops[$r.'default_map_lat'] ?? ( APPDEBUG ? 'fake_lat' : '' ), 'yv' => $ops[$r.'default_map_long'] ?? ( APPDEBUG ? 'fake_long' : '' ), 'z' => $ops[$r.'default_map_zoom'] ?? 13, 'mt' => $ops[$r.'default_map_type'] ?? 'terrain', 'sz' => $ops[$r.'show_map_zoom'] ?? 0, 'st' => $ops[$r.'show_map_type'] ?? 0, 'ss' => $ops[$r.'show_map_search'] ?? 0, 'sf' => $ops[$r.'show_map_full'] ?? 0, 't' => 'map', 'c' => 12 ],
            [ 'i' => $r.'default_map_lat', 'n' => 'Default Map Latitude', 'p' => 'Ex: 12.34233', 'v' => $ops[$r.'default_map_lat'] ?? ( APPDEBUG ? 'fake_lat' : '' ), 'c' => 4 ],
            [ 'i' => $r.'default_map_long', 'n' => 'Default Map Longitude', 'p' => 'Ex: 12.34233', 'v' => $ops[$r.'default_map_long'] ?? ( APPDEBUG ? 'fake_long' : '' ), 'c' => 4 ],
            [ 'i' => $r.'map_marker', 'n' => 'Map Marker Color', 'p' => 'Ex: red', 'v' => $ops[$r.'map_marker'] ?? '', 'c' => 4, 't' => 'color', 's' => .2 ],
            [ 'i' => $r.'default_map_zoom', 'n' => 'Default Zoom', 'p' => 'Select Level...', 'o' => range(0,19), 'v' => $ops[$r.'default_map_zoom'] ?? 13, 'c' => 2, 't' => 'select2' ],
            [ 'i' => $r.'default_map_type', 'n' => 'Map Type', 'p' => 'Select Type...', 'o' => ['roadmap','satellite','hybrid','terrain'], 'v' => $ops[$r.'default_map_type'] ?? 'terrain', 'c' => 2, 't' => 'select2' ],
            [ 'i' => $r.'show_map_zoom', 'n' => 'Zoom Controls', 'v' => $ops[$r.'show_map_zoom'] ?? 1, 'c' => 2, 't' => 'slide', 'on' => '', 'off' => '' ],
            [ 'i' => $r.'show_map_type', 'n' => 'Changeable Type', 'v' => $ops[$r.'show_map_type'] ?? 0, 'c' => 2, 't' => 'slide', 'on' => '', 'off' => '' ],
            [ 'i' => $r.'show_map_search', 'n' => 'Show Search', 'v' => $ops[$r.'show_map_search'] ?? 1, 'c' => 2, 't' => 'slide', 'on' => '', 'off' => '' ],
            [ 'i' => $r.'show_map_full', 'n' => 'Fullscreen', 'v' => $ops[$r.'show_map_full'] ?? 1, 'c' => 2, 't' => 'slide', 'on' => '', 'off' => '' ],
            [ 'i' => $r.'map_style', 'n' => 'Map Style Code', 'v' => $ops[$r.'map_style'] ?? '', 'c' => 12, 't' => 'textarea' ],
        ];
        _c();
            $f->form( $fields, 'row', $attr );
        c_();
        $f->process_options( $o->region_flag().'Save Map Options','store grad','','.tac');
        $o->region_notice();
        d_();
    }
}

/*

HOW TO USE

WRITE PHP CODE TO INITIATE CLASS AND FUNCTION:
render_maps(); // Will include everything necessary

HTML STRUCTURE
<div class="mb15 gmap" data-zoom="17" data-value="25.183365022185065, 55.34723626990683" id="branch_map" data-gps="#branch_gps" data-city="#branch_city" data-country="#branch_country" data-area="#branch_area" data-location="#branch_location"></div>

Set Location:
data-zoom // You can set a default level of zoom on initiation of map
data-value // You can pass latitude and longitude separated by comma to have marker to that position

Get Location: (On Marker Move)
data-gps // Sets lat and long separated by comma to given class or id
data-area // Sets area of the position
data-city // Sets city of position
data-country // Sets country of position
data-country_code // Sets country code Ex: AE
*/