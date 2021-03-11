<?php

/**
 * Class SMS
 */
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
    function vonage( string $no, string $msg, string $key, string $sec ): string {
        if( file_exists( COREPATH . 'core/components/ext/vonage/autoload.php' ) ){
            if( empty( $key ) ) {
                $con = new DB();
                $key = $con->get_option( 'vonage_key' );
            }
            $key = empty( $key ) ? get_config( 'vonage_key' ) : $key;
            include_once( COREPATH . 'core/components/ext/vonage/autoload.php' );
            $client = new Vonage\Client(new Vonage\Client\Credentials\Basic($key, $sec));
            $text = new \Vonage\SMS\Message\SMS(
                $no, // Receivers Number
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
            return "Sent message to " . $data->getTo() . ". Balance is now " . $bal . PHP_EOL;
        } else {
            return 'API Missing';
        }
    }
}