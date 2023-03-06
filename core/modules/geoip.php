<?php
require ROOTPATH . 'core/external/vendor/autoload.php';
use GeoIp2\Database\Reader;

class REGION {

    public $countryReader;
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
            $reader = new Reader(ROOTPATH . 'core/external/GeoLite2-Country.mmdb');
            $this->countryReader = $reader->country( $this->ip );
            return $this->countryReader->country->isoCode;
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

}
