<?php
require VENDORLOAD;
use GeoIp2\Database\Reader;

// Set Region Params
global $options;
//skel( $options );
if( isset( $options['regions'] ) || isset( $options['region'] ) || isset( $options['primary_region'] ) ) {
//skel( $options );
    if( !empty( $options['regions'] ) ) {
        !defined( 'REGIONS' ) ? define( 'REGIONS', $options['regions'] ) : '';
    }
    require_once( ROOTPATH . 'core/modules/world.php' );
    $set_countries = array_map( 'trim', explode( ',', $options['regions'] ) );
    $r = $options['region'] ?? ( !empty( $options['primary_region'] ) ? $options['primary_region'] : $set_countries[0] );
    //skel( $r );
    $w = new WORLD();
    $region = $w->get_country( $r );
    if( !empty( $region ) ) {
        //$options['region_data'] = $region;
        !defined( 'REGION' ) ? define( 'REGION', $region ) : '';
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
        $f = new FORM();
        $db = new DB();
        $countries = get_countries('iso2');
        $regions = $db->get_options(['primary_region','regions']);

        // Limit primary region
        $limit_regions = [];
        if( !empty( $regions['regions'] ) ) {
            $rs = array_map( 'trim', explode( ',', $regions['regions'] ) );
            if( !empty( $rs ) ) {
                foreach( $rs as $sr ) {
                    $limit_regions[ $sr ] = $countries[ $sr ];
                }
            }
        }
        $f->option_params_wrap('reg',2,2,'primary_region,regions');
        $f->select2('regions','Set Operating Regions','Choose countries...',$countries,$regions['regions']??'','multiple data-reg',12,1);
        $f->select2('primary_region','Set Primary Region','Choose country...',$limit_regions,$regions['primary_region']??'','data-reg',12,1);
        $f->process_options('Save Options','store grad','','.col-12 tac');
        echo '<div style="text-align:center; font-size: .8rem">'.T('Please set and save operating regions, then set primary region.').'</div></div>';
    }

}

function set_region_ajax(): void {
    if( isset( $_POST['iso2'] ) ) {
        $db = new DB();
        $u = $db->update_option( 'region', $_POST['iso2'], get_user_id(), 1 );
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
