<?php

class CURL {

    function get( string $url = '', array $headers = [], array $params = [] ): bool|string {
        $c = curl_init();
        if( empty( $headers ) ) {
            $headers = [
                'Accept: application/json',
                'Content-Type: application/json',
            ];
        }
        curl_setopt( $c, CURLOPT_URL, $url );
        curl_setopt( $c, CURLOPT_HTTPHEADER, $headers);
        curl_setopt( $c, CURLOPT_HEADER, 0);
        $body = '{}';

        curl_setopt( $c, CURLOPT_CUSTOMREQUEST, "GET" );
        curl_setopt( $c, CURLOPT_POSTFIELDS, $body );
        curl_setopt( $c, CURLOPT_RETURNTRANSFER, true );

        // Timeout in seconds
        curl_setopt( $c, CURLOPT_TIMEOUT, 30);

        $server_output = curl_exec( $c );

        curl_close( $c );

        return $server_output;
    }

    function post( string $url = '', array $headers = [], array|string $post_params = [], string $username = '', string $password = '' ): bool|string {

        $ch = curl_init();

        // Prepare Data
        /* $post_query_params = [];
        if( !empty( $post_params ) ) {
            foreach( $post_params as $pk => $pv ) {
                $post_query_params[] = $pk.'='.$pv;
            }
        }
        $post_query = implode( '&', $post_query_params ); */

        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, $headers );
        curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
        if( !empty( $username ) && !empty( $password ) ) {
            curl_setopt( $ch, CURLOPT_USERPWD, $username . ':' . $password );
        }
        curl_setopt( $ch, CURLOPT_POSTFIELDS, ( is_array( $post_params ) ? http_build_query( $post_params ) : $post_params ) );

        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $server_output = curl_exec($ch);

        curl_close($ch);

        return $server_output;
    }

}