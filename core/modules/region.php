<?php
require VENDORLOAD;
use GeoIp2\Database\Reader;

class REGION {

    private string $ip;

    function __construct() {
        $this->ip = $this->get_user_ip();
    }

    function get_country_name(): ?string {
        return $this->countryReader->country->name;
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

    function region_options(): void {
        $db = new DB();
        //$c = json_decode( CONFIG, 1 );
        $regions = $db->select( 'regions' );
        $countries = get_countries('iso2');
        $zones = timezone_identifiers_list();
        $currencies = get_currencies('code','name code');
        $languages = get_languages();
        $this->region_options_form( [], $countries, $zones, $currencies, $languages );
        if( !empty( $regions ) ) {
            foreach( $regions as $i => $r ) {
                echo '<div class="accordion"><div class="accordion_head">'.T( $r['reg_country'] ).'</div><div class="accordion_body">';
                $this->region_options_form( $r, $countries, $zones, $currencies, $languages );
                echo '</div></div><hr>';
            }
        }
        echo '<div class="help tac"><a href="https://www.php.net/manual/en/datetime.formats.date.php" target="_blank">'.T('Date Format Help').'</a> <a href="https://www.php.net/manual/en/datetime.formats.time.php" target="_blank">'.T('Time Format Help').'</a></div>';
    }

    function region_options_form( $r, $countries, $zones, $currencies, $languages ): void {
        $f = new FORM();
        $h = [];
        if( !empty( $r ) ) {
            $r = replace_in_keys( $r, 'reg_' );
            $h['id'] = $r['id'];
        }
        echo '<div';
        $f->process_params('regions','reg','reg_',6,2,$h);
        echo '>';
        $a = 'data-reg';
        echo '<div class="row">';
        $f->text('domain','Domains','Ex: website.com, website.net',$r['domain']??'',$a.' required',3);
        $f->select2('country','Default Country','Select Region...',$countries,$r['country']??'',$a.' required',3,1);
        $f->select2('language','Default Language','Select Language...',$languages,$r['language']??'',$a.' required',3,1);
        $f->select2('timezone','Timezone','Select zone...',$zones,$r['timezone']??'',$a,3);
        $f->select2('currency_code','Currency','Select currency...',$currencies,$r['currency_code']??'',$a,3,1);
        $f->text('currency_symbol','Currency Symbol','Ex: ₹',$r['currency_symbol']??'',$a,2);
        $f->text('currency_rate','Currency Rate','Ex: 20.45',$r['currency_rate']??1,$a,2);
        $f->text('date_format',"Date Format",'d M, Y',$r['date_format']??'',$a,2);
        $f->text('time_format',"Time Format",'H:i:s',$r['time_format']??'',$a,2);
        $f->slide('status','Status','','',$r['status']??1,'m',$a,1);
        /*$f->text('name','Registered Name','Ex: ABC Trading LLC.','',$a,3);
        $f->text('tax','Tax %','Ex: 5','',$a,1);
        $f->text('company_code','Registration No.','Ex: 1202-1256-1326','',$a,0);
        $f->upload('company_doc','Reg. Doc.','Browse','',0,0,'',$a,'jpg,jpeg,png,pdf',2,1,'',0);
        $f->text('tax_code','TRN No.','Ex: 3562-2654-8954','',$a,0);
        $f->upload('tax_doc','TRN Doc.','Browse','',0,0,'',$a,'jpg,jpeg,png,pdf',2,1,'',0);*/
        echo '<div class="col-12 tac">';
        if( !empty( $r ) ) {
            $f->trash_html('regions','reg_id = \''.$r['id'].'\'','button','Remove Region','store grad red');
        }
        $f->process_html('Save Region','store grad');
        echo '</div></div></div>';
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
