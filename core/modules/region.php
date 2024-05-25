<?php
require VENDORLOAD;
use GeoIp2\Database\Reader;

// Set Region Params
global $options;
//skel( $options );
if( isset( $options['regions'] ) || isset( $options['region'] ) || isset( $options['base_region'] ) ) {
    require_once( ROOTPATH . 'core/modules/world.php' );
    $w = new WORLD();
    $set_regions = array_map( 'trim', explode( ',', $options['regions'] ) );
    $now_region = $options['region'] ?? ( !empty( $options['base_region'] ) ? $options['base_region'] : $set_regions[0] );
    if( !empty( $options['regions'] ) && !defined( 'REGIONS' ) ) {
        $regions = [];
        $d = new DB();

        // Set Base
        $regions['base'] = !empty( $options['base_region'] ) ? get_region( $options['base_region'] ) : get_region( 'US' );
        $base_regional_options = $d->select( 'options', 'option_name,option_value', 'option_name LIKE \''.$regions['base']['cca2'].'%\'' );
        if( !empty( $base_regional_options ) ) {
            foreach( $base_regional_options as $dro ) {
                $regions['base'][ str_replace( strtolower($regions['base']['cca2']).'_', '', $dro['option_name'] ) ] = $dro['option_value'];
            }
        }

        // Set Current Region
        if( !empty( $regions['base']['cca2'] ) && $regions['base']['cca2'] == $now_region ) {
            $regions['now'] = $regions['base'];
        } else {
            $regions['now'] = get_region( $now_region );
        }
        $dynamic_regional_options = $d->select( 'options', 'option_name,option_value', 'option_name LIKE \''.$regions['now']['cca2'].'%\'' );
        if( !empty( $dynamic_regional_options ) ) {
            foreach( $dynamic_regional_options as $dro ) {
                $regions['now'][ str_replace( strtolower($regions['now']['cca2']).'_', '', $dro['option_name'] ) ] = $dro['option_value'];
            }
        }

        // Set all regions
        if( !empty( $set_regions ) ){
            foreach( $set_regions as $r ) {
                $regions['regions'][$r] = get_region( $r );
            }
        }
        define( 'REGIONS', $regions );
        //!defined( 'REGIONS' ) ? define( 'REGIONS', $options['regions'] ) : '';
    }
    if( !defined( 'REGION' ) ) {
        $region = REGIONS['now'] ?? get_region( $now_region );
        define( 'REGION', $region );
    }
}

class REGION {

    private string $ip;

    function __construct() {
        $this->ip = $this->get_user_ip();
    }

    function get_country_name(): ?string {
        return $this->countryReader->country->name;
    }

    function get_country_flag(): ?string {
        return $this->countryReader->country->flag;
    }

    function get_city_name(): ?string {
        return $this->countryReader->city->name;
    }

    function get_lat(): ?string {
        return $this->countryReader->location->latitude;
    }

    function get_long(): ?string {
        return $this->countryReader->location->longitude;
    }

    function get_country_code(): ?string {
        try {
            $reader = new Reader( EXTPATH . 'GeoLite2-Country.mmdb');
            if( $this->ip !== '127.0.0.1' ) {
                $this->countryReader = $reader->country( $this->ip );
                return $this->countryReader->country->isoCode;
            } else {
                return false;
            }
        } catch (\MaxMind\Db\Reader\InvalidDatabaseException|\GeoIp2\Exception\AddressNotFoundException $e) {
            elog($e);
            return false;
        }
    }

    function get_user_ip() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip == '::1' ? '127.0.0.1' : $ip;
    }

    /**
     * Renders options to set operating regions
     * @return void
     */
    function region_options(): void {
        // Regional Options
            // Region
                // Address
                // Primary Language
                // Currency Symbol
                // Currency Rate
                // Serving Countries
                // Tax Options
                // Payment Options
        $o = new OPTIONS();
        $db = new DB();
        $countries = get_countries('iso2');
        $r = defined( 'REGION' ) && isset( REGION['cca2'] ) ? strtolower( REGION['cca2'] ).'_' : '';
        $regions = $db->get_options('base_region,regions,'.$r.'serving_regions');

        // Limit base region
        $operating_regions = [];
        if( !empty( $regions['regions'] ) ) {
            $rs = array_map( 'trim', explode( ',', $regions['regions'] ) );
            if( !empty( $rs ) ) {
                foreach( $rs as $sr ) {
                    $operating_regions[ $sr ] = $countries[ $sr ];
                }
            }
        }
        $regions_form = [
            [ 'i' => 'regions', 't' => 'select2', 'n' => 'Choose countries of Management', 'p' => 'Choose country...', 'o' => $countries, 'v' => ( $regions['regions'] ?? '' ), 'k' => 1, 'a' => 'multiple data-region' ]
        ];
        !empty( $regions['regions'] ) ? $regions_form[] = [ 'i' => 'base_region', 't' => 'select2', 'n' => 'Set Primary Country', 'p' => 'Choose country...', 'o' => $operating_regions, 'k' => 1, 'v' => ( $regions['base_region'] ?? '' ), 'a' => 'data-region' ] : '';
        $o->form( $regions_form, 'row', 0, 'region', 'Save Region Options', '', 'Regional settings saved successfully!', 'base_region,regions' );
        //div( '', 'Please set and save operating regions, then set primary region.', '', 'style="text-align:center; font-size: .8rem"', 1 );
        //$f->option_params_wrap('reg','row','base_region,regions');
        //$f->select2('regions','Set Operating Regions','Choose countries...',$countries,$regions['regions']??'','multiple data-reg',12,1);
        if( !empty( $regions['regions'] ) ) {
            $region_options_form = [
                [ 'i' => $r.'serving_regions', 't' => 'select2', 'n' => 'Serving countries', 'p' => 'Choose country...', 'o' => $countries, 'k' => 1, 'v' => ( $regions[ $r.'serving_regions' ] ?? '' ), 'a' => 'multiple data-region' ],
            ];
            $o->form( $region_options_form, 'row', 0, 'region', $this->flag() . ' Save Restrictions' );
            //$f->select2('base_region','Set Base Region','Choose country...',$limit_regions,$regions['base_region']??'','data-reg',12,1);
        }
        //$f->process_options('Save Options','store grad','','.col-12 tac');
        //d_();
    }

    function __notice(): string {
        $c = defined( 'CONFIG' ) ? CONFIG : [];
        $r = defined( 'REGION' ) ? REGION : [];
        if( !empty( $r ) ) {
            $n = $r['name']['common'] ?? '';
            $f = $r['flag'] ?? '';
            return __div( 'region_info', T('Settings apply to region ') . $n . ' ' . $f, '', 'style="text-align:center; font-size: .8rem"' );
        } else if( in_array( 'regions', $c['features'] ) ) {
            return __div( 'region_info', T('Regions feature enabled! Please select a region in header and then save settings to apply to selected region!'), '', 'style="text-align:center; font-size: .8rem"' );
        } else {
            return __div( 'region_info', T('') );
        }
    }

    function notice(): void {
        echo $this->__notice();
    }

    function flag(): string {
        return defined( 'REGION' ) && isset( REGION['flag'] ) ? ' '.__i( 'reg-flag', REGION['flag'] ).' ' : '';
    }

    /**
     * @param string $title_format
     * @param int $reload_time
     * @param string $active_class
     * @param string $class
     * @param string $element
     * @return void
     */
    function region_changer( string $title_format = 'country - symbol', int $reload_time = 1, string $active_class = 'on', string $class = '', string $element = 'div', ): void {
        /* $db = new DB();
        global $options;
        $set_countries = array_map( 'trim', explode( ',', $options['regions'] ) );
        if( !empty( $set_countries ) ) {
            $my_region = $db->get_option('region',get_user_id());
            $cr = !empty( $my_region ) ? $my_region : ( !empty( $options['base_region'] ) ? $options['base_region'] : $set_countries[ 0 ] );
            foreach( $set_countries as $r ){
                $t = $countries[$r] ?? '';
                if( !empty( $cr ) && $cr == $r  ) {
                    echo '<li class="ln list on">'.$t.'</li>';
                } else {
                    echo '<li class="ln list" data-set-region="'.$r.'">'.$t.'</li>';
                }
            }
        } */
        //skel( REGIONS );
        if( defined( 'REGIONS' ) && is_array( REGIONS ) ) {
            if( !empty( REGIONS['regions'] ) && is_array( REGIONS['regions'] ) ) {
                $base = REGIONS['now']['cca2'] ?? 'US';
                $replaces = [ 'cca2', 'cca3', 'country', 'flag', 'currency', 'symbol', 'call' ];
                $f = new FORM();
                $wrap = $element == 'li' || $element == 'ul' ? 'ul' : $element;
                pre( 'aio_language_changer', 'aio_language_changer_wrap', $wrap, $f->_pre_process( '', 'set_region_ajax' ) );
                foreach( REGIONS['regions'] as $r ){
                    $title = $title_format;
                    foreach( $replaces as $rep ) {
                        $title = str_replace( $rep, $r[ $rep ] ?? '', $title );
                    }
                    $field = $f->_radios('cca2','',[ $r['cca2'] => $title ],$base == $r['cca2']?[ $r['cca2'] ]:'','auto-post');
                    echo '<'.$element.' class="ln cp list '.($base == $r['cca2']?'on':'').'">'.$field.'</'.$element.'>';
                }
                $f->process_trigger('','dn','');
                post( $wrap );
            }
        }
    }

}

function region_flag( string $element = 'div', string $class = '' ): void {
    echo _region_flag( $element, $class );
}
function _region_flag( string $element = 'div', string $class = '' ): string {
    return defined( 'REGION' ) && isset( REGION['flag'] ) ? __el( $element, $class, REGION['flag'] ) : '';
}

function set_region_ajax(): void {
    if( isset( $_POST['cca2'] ) ) {
        $db = new DB();
        $u = $db->update_option( 'region', $_POST['cca2'], get_user_id(), 1 );
        $u ? es('Successfully changed current region!') : ef('Failed to change current region!');
    } else {
        ef('Failed to change current region! Set country not received in the backend, please consult developer!');
    }
}

function get_region_countries( string $key = 'id', string $value = 'name flag' ): array {
    $d = new DB();
    $w = new WORLD();
    $regions = $d->select( 'regions', 'reg_country' );
    $data = [];
    //skel( $regions );
    if( !empty( $regions ) ) {
        foreach( $regions as $r ) {
            $country = $w->get_country( $r['reg_country'] );
            //skel( $country );
            $k = $w->get_property( $country, $key );
            $v = $w->get_property( $country, $value );
            $data[ $k ] = $v;
        }
    }
    //skel( $data );
    return $data;
}

//skel( REGION );

function get_region( string $cca2 ): array {
    require_once( ROOTPATH . 'core/modules/world.php' );
    $w = new WORLD();
    $c = $w->get_country( $cca2 );
    //skel( $c );
    $cs = $c['currencies'];
    $ck = is_array( $cs ) ? array_key_first( $cs ) : 0;
    $region = [
        'cca2' => $c['cca2'],
        'cca3' => $c['cca3'],
        'country' => $c['name']['common'],
        'flag' => $c['flag'],
        'currency' => !empty( $c['currencies'][ $ck ] ) ? $c['currencies'][ $ck ]['name'] : '',
        'symbol' => !empty( $c['currencies'][ $ck ] ) ? $c['currencies'][ $ck ]['symbol'] : '',
        'call' => $c['callingCodes'][0],
    ];
    return $region;
    /* $o = new OPTIONS();
    $fos = $o->finance_options;
    $d = new DB();
    $regional_options = $d->get_options( prepare_keys( $fos, strtolower( $cca2 ) . '_' ) );
    //skel( prepare_keys( $fos, $cca2.'_' ) );
    return array_merge( $region, $regional_options ); */
}
