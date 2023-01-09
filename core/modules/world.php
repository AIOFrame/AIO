<?php

class WORLD {

    public array $countries = [];
    public array $languages = [];

    function __construct() {
        $c_json = ROOTPATH . 'core/external/countries.json';
        if( file_exists( $c_json ) ) {
            $data = file_get_contents( $c_json );
            if( !empty( $data ) )
                $this->countries = json_decode( $data, 1 );
        }
        $l_json = ROOTPATH . 'core/external/languages.json';
        if( file_exists( $l_json ) ) {
            $data = file_get_contents( $l_json );
            if( !empty( $data ) )
                $this->languages = json_decode( $data, 1 );
        }
    }

    /**
     * Returns array of countries
     * @param string $key Choose key 'name','iso2','iso3','calling_code','currency','currency_name','currency_symbol','currency_code','capital','flag','languages','gps','lat','long'
     * @param string $value Choose value 'name','iso2','iso3','calling_code','currency','currency_name','currency_symbol','currency_code','capital','flag','languages','gps','lat','long'
     * @param string $logic iso2=AE
     * @return array
     */
    function countries( string $key = 'id', string $value = 'name', string $logic = '' ): array {
        $r = [];
        //$logic = !empty( $logic ) ? explode( '=', $logic ) : [];
        $data = $this->countries;
        if( !empty( $data ) ){
            $x = 0;
            foreach( $data as $c ){
                $k = $key !== 'id' ? $this->get_property( $c, $key ) : $x;
                if( str_contains( $value, ' ' ) ) {
                    $values = explode(' ',$value);
                    $v = '';
                    foreach( $values as $vs ) {
                        $v .= $this->get_property( $c, $vs ).' ';
                    }
                    $v = rtrim( $v );
                } else {
                    $v = $this->get_property( $c, $value );
                }
                if( !empty( $k ) && !empty( $v ) ) {
                    $r[$k] = $v;
                }
                $x++;
            }
        }
        return $r;
    }


    function languages(): array {
        $r = [];
        $data = $this->languages;
        if( !empty( $data ) && is_array( $data ) ){
            foreach( $data as $l ){
                $r[ $l['code'] ] = $l['name'];
            }
        }
        return $r;
    }

    /**
     * Country Property Finder
     * @param array $c Country Array
     * @param string $p 'name','iso2','iso3','calling_code','currency','currency_name','currency_symbol','currency_code','capital','flag','languages','gps','lat','long'
     */
    private function get_property( array $c, string $p ) {
        if( $p == 'name' )
            $r = $c['name']['common'];
        elseif( $p == 'official_name' )
            $r = $c['name']['official'];
        else if( $p == 'iso2' || $p == 'ISO2' )
            $r = $c['cca2'];
        else if( $p == 'iso3' || $p == 'ISO3' )
            $r = !empty( $c['cioc'] ) ? $c['cioc'] : $c['cca3'];
        else if( $p == 'calling_code')
            $r = $c['callingCodes'][0] ?? '';
        else if( $p == 'currency' || $p == 'currency_name' )
            $r = isset($c['currencies'][array_key_first($c['currencies'])]['name']) ? $c['currencies'][array_key_first($c['currencies'])]['name'] : '';
        else if( $p == 'currency_code')
            $r = array_key_first($c['currencies']);
        else if( $p == 'currency_symbol')
            $r = array_key_first( $c['currencies'] ) !== null ? $c['currencies'][array_key_first($c['currencies'])]['symbol'] : '';
        else if( $p == 'capital')
            $r = $c['capital'][0] ?? '';
        else if( $p == 'gps' )
            $r = $c['lat'].','.$c['long'];
        else
            $r = $c[$p] ?? '';
        return $r;
    }

    /**
     * Returns states of a country
     * @param string $country Country to get states
     * @return array
     */
    /* function states( string $country = 'AE' ): array {
        return [];
    } */

    /**
     * Returns cities of a given state and country
     * @param string $state_name Name of the state
     * @param string $country_code ISO2 code of the country Ex: AE
     * @return array
     */
    /* function cities( string $state_name = '', string $country_code = 'AE' ): array {
        /* Use input instead of dropdown for now */
        /* return [];
    } */

    /**
     * Returns array of currencies
     * @param string $key Choose key 'name','symbol','code'
     * @param string $value Choose value 'name','symbol','code'
     * @param string $country Name
     * @return array
     */
    function currencies( string $key = 'symbol', string $value = 'name', string $country = '' ): array {
        $key = !empty( $key ) ? 'currency_'.$key : $key;
        $value = !empty( $value ) ? 'currency_'.$value : $value;

        $r = [];
        $countries_data = $this->countries;
        if( !empty( $countries_data ) ){
            foreach( $countries_data as $c ){
                $k = $this->get_property( $c, $key );
                $v = $this->get_property( $c, $value );
                if( !empty( $k ) && !empty( $v ) )
                    $r[ $k ] = $v;
            }
        }
        return $r;
    }

    /**
     * Returns array of calling codes
     * @param string $key Choose key 'calling_code','flag'
     * @param string $value Choose value 'calling_code','flag'
     * @param string $country Name
     * @return array
     */
    function calling_codes( string $key = 'calling_code', string $value = 'calling_code', string $country = '' ): array {

        $r = [];
        $countries_data = $this->countries;
        if( !empty( $countries_data ) ){
            foreach( $countries_data as $c ){
                $k = $this->get_property( $c, $key );
                $v = $this->get_property( $c, $value );
                if( !empty( $k ) && !empty( $v ) )
                    $r[ $k ] = $v;
            }
        }
        return $r;
    }

    function get_country( string $country = '' ): array {
        $logic = explode( '=', $country );
        if( count( $logic ) > 1 ) {
            $key = $logic[0];
            $val = $logic[1];
        } else {
            $val = $country;
            if( is_numeric( $country ) ) {
                $key = 'id';
            } else if( strlen( $country ) == 2 ) {
                $key = 'cca2';
            } else if( strlen( $country ) === 3 ) {
                $key = 'cioc';
            } else {
                $key = 'name';
            }
        }
        $r = [];
        $data = $this->countries;
        if( !empty( $data ) ){
            foreach( $data as $c ){
                if( $c[ $key ] == $val )
                    $r = $c;
            }
        }
        return $r;
    }

}