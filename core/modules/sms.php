<?php

class SMS {

    /**
     * Sends SMS
     * @param string $no Phone number to receive the SMS
     * @param string $msg Content of the Message
     * @param string $gate SMS API gateway name. "vonage"
     * @param string $key Primary API Key
     * @param string $sec Secondary (Secret) API Key
     * @return string
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \Vonage\Client\Exception\Exception
     * @author Shaikh <hey@shaikh.dev>
     */
    function send( string $no, string $msg, string $gate, string $key, string $sec ): string {
        if( $gate == 'vonage' ){
            return $this->vonage( $no, $msg, $key, $sec );
        } else {
            return 'Payment gateway incorrect!';
        }
    }

    /**
     * @param string $no
     * @param string $msg
     * @param string $key
     * @param string $sec
     * @return string
     * @throws \Psr\Http\Client\ClientExceptionInterface
     * @throws \Vonage\Client\Exception\Exception
     */
    function vonage( string $no, string $msg, string $key = '', string $secret = '' ): string {
        if( file_exists( ROOTPATH . 'core/external/vendor/autoload.php' ) ){
            $db = new DB();
            if( empty( $key ) ) {
                $key = $db->get_option( 'vonage_key' );
            }
            if( empty( $secret ) ) {
                $secret = $db->get_option( 'vonage_secret' );
            }
            $key = empty( $key ) ? get_config( 'vonage_key' ) : $key;
            $secret = empty( $secret ) ? get_config( 'vonage_secret' ) : $secret;
            include_once( ROOTPATH . 'core/external/vendor/autoload.php' );
            $client = new Vonage\Client(new Vonage\Client\Credentials\Basic($key, $secret));
            $text = new \Vonage\SMS\Message\SMS(
                str_replace( '+', '', $no), // Receivers Number
                APPNAME, // Senders Name
                $msg // Message
            );
            $text->setClientRef('test-message');
            $response = $client->sms()->send($text);
            $bal = 0;
            if( is_array( $response ) ) {
                foreach($response as $index => $data){
                    $bal += (int)$data->getRemainingBalance();
                }
            } else {
                $data = $response->current();
                $bal += (int)$data->getRemainingBalance();
            }
            elog("Sent message to " . $data->getTo() . ". Balance is now " . $bal . PHP_EOL);
            return 1;
        } else {
            elog('Vonage API Missing');
            return 0;
        }
    }
}