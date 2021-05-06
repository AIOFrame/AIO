<?php

class WORLD {

    public array $countries = [];

    function __construct() {
        $json = ROOTPATH . 'core/external/countries.json';
        if( file_exists( $json ) ) {
            $data = file_get_contents( $json );
            if( !empty( $data ) )
                $this->countries = json_decode( $data, 1 );
        }
    }

    /**
     * Returns array of countries
     * @param string $key Choose key 'id','name','iso2'-AE,'iso3'-UAE,'phonecode','currency'
     * @param string $value Choose value 'id','name','iso2'-AE,'iso3'-UAE,'phonecode','capital','currency','symbol','currency_name'
     * @param string $country_code
     * @return array
     */
    function countries( string $key = 'iso2', string $value = 'name', $country_code = '' ): array {

        $db = new DB();
        $q = $country_code && $country_code !== '' ? 'iso2 = "'.$country_code.'"' : '';

        if( $key == 'phone' || $key == 'phonecode' )
            $c1 = 'phonecode';
        else if( $key == 'iso3' )
            $c1 = 'iso3';
        else if( $key == 'currency' )
            $c1 = 'currency';
        else
            $c1 = $key;

        if( $value == 'iso2' )
            $c2 = 'iso2';
        else if( $value == 'iso3' )
            $c2 = 'iso3';
        else if( $value == 'phone' || $value == 'phonecode' )
            $c2 = 'phonecode';
        else if( $value == 'capital' )
            $c2 = 'capital';
        else if( $value == 'currency' )
            $c2 = 'currency';
        else if( $value == 'symbol' )
            $c2 = 'symbol';
        else if( $value == 'currency_name' )
            $c2 = 'currency_name';
        else
            $c2 = $value;

        $data = $db->select( 'countries', $c1.','.$c2, $q );
        if( !empty( $data ) && is_array( $data ) ){
            $countries = [];
            foreach( $data as $c ){
                $countries[ $c[$c1] ] = $c[$c2];
            }
            return $countries;
        } else {
            return [];
        }
    }

    /**
     * Returns states of a country
     * @param string $country_iso2 ISO2 code of country. Ex: AE
     * @return array
     */
    function states( string $country_iso2 = 'AE' ): array {
        $db = new DB();
        if( is_numeric( $country_iso2 ) ){
            $id['id'] = $country_iso2;
        } else {
            $id = $db->select( 'countries', 'id', 'iso2 = "'.$country_iso2.'"', 1 );
        }

        $q = isset( $id['id'] ) ? 'country_id = "'.$id['id'].'"' : '';
        if( $q !== '' ) {
            $data = $db->select( 'states', 'name', $q );
            if( is_array( $data ) ){
                $states = [];
                foreach( $data as $d ){
                    $states[] = $d['name'];
                }
                if( isset( $_POST['id'] ) ) { echo json_encode($states); } else { return $states; }
            } else {
                return [];
            }
        } else {
            return [];
        }

    }

    /**
     * Returns cities of a given state and country
     * @param string $state_name Name of the state
     * @param string $country_code ISO2 code of the country Ex: AE
     * @return array
     */
    function cities( string $state_name = '', string $country_code = 'AE' ): array {

        $db = new DB();
        if( $state_name != '' ){
            $id = $db->select( 'states', 'id', 'name = "'.$state_name.'"', 1 );
            $q = isset( $id['id'] ) ? 'state_id = "'.$id['id'].'"' : '';
        } else if( $country_code != '' ){
            $id = $db->select( 'countries', 'id', 'iso2 = "'.$country_code.'"', 1 );
            $q = isset( $id['id'] ) ? 'country_id = "'.$id['id'].'"' : '';
        }

        if( $q !== '' ) {
            $data = $db->select( 'cities', 'name', $q );
            if( is_array( $data ) ){
                $cities = [];
                foreach( $data as $d ){
                    $cities[] = $d['name'];
                }
                return $cities;
            } else {
                return [];
            }
        } else {
            return [];
        }

    }

    /**
     * Returns array of currencies
     * @param string $value Choose value 'id','name','symbol','code'
     * @param string $key Choose key 'id','name','symbol','code'
     * @param bool $country_code ISO2 code of country, Ex: AE if to get currency of UAE
     * @return array
     */
    function currencies( string $value = 'name', string $key = 'code', bool $country_code = false ): array {

        $db = new DB();
        $q = $country_code && $country_code !== '' ? 'iso2 = "'.$country_code.'"' : '';

        if( $key == 'symbol' )
            $c1 = 'currency_symbol';
        else if( $key == 'name' )
            $c1 = 'currency_name';
        else if( $key == 'code' )
            $c1 = 'currency';
        else
            $c1 = $key;

        if( $value == 'symbol' )
            $c2 = 'currency_symbol';
        else if( $value == 'code' )
            $c2 = 'currency';
        else if( $value == 'name' )
            $c2 = 'currency_name';
        else
            $c2 = $value;

        $cdata = $db->select( 'countries', $c1.','.$c2, $q );
        if( !empty( $cdata ) && is_array( $cdata ) ){
            $currencies = [];
            foreach( $cdata as $c ){
                $currencies[ $c[$c1] ] = $c[$c2];
            }
            return array_filter( $currencies );
        } else {
            return [];
        }

        //return $currencies;
    }

}