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
     * @param string $number
     * @param string $message
     * @param string $key
     * @param string $secret
     * @return string
     */
    function vonage( string $number, string $message, string $key = '', string $secret = '' ): string {

        // Gathering API Key and API Secret
        if( class_exists( 'DB' ) ) {
            $db = new DB();
            $key = !empty( $key ) ? $key : $db->get_option('vonage_key');
            $secret = !empty( $secret ) ? $secret : $db->get_option('vonage_secret');
        }
        $key = !empty( $key ) ? $key : get_config('vonage_key');
        $secret = !empty( $secret ) ? $secret : get_config('vonage_secret');

        // Validations
        $number = preg_replace('/[^0-9]/', '', $number);
        if( !is_numeric( $number ) || $number < 4 ) {
            elog('Phone number '.$number.' is very short!','error',44,ROOTPATH.'core/modules/sms.php');
            return 0;
        }
        if( empty( $message ) ) {
            elog('SMS Message is empty!','error',48,ROOTPATH.'core/modules/sms.php');
        }

        if( !empty( $key ) && !empty( $secret ) ) {
            include_once( ROOTPATH . 'core/external/vendor/autoload.php' );
            $basic  = new \Vonage\Client\Credentials\Basic($key, $secret);
            $client = new \Vonage\Client($basic);

            // Trigger the SMS send request
            $response = $client->sms()->send(
                new \Vonage\SMS\Message\SMS(
                    $number,
                    APPNAME,
                    $message
                )
            );
            $message = $response->current();

            if ($message->getStatus() == 0) {
                elog('SMS Message sent to '.$number.' successfully!','log',67,ROOTPATH.'core/modules/sms.php');
                return 1;
            } else {
                elog('Vonage Message failed to Deliver! '.$message->getStatus(),'error',48,ROOTPATH.'core/modules/sms.php');
                return 0;
            }

            /*$bal = 0;
            if( is_array( $response ) ) {
                foreach($response as $index => $data){
                    $bal += (int)$data->getRemainingBalance();
                }
            } else {
                $data = $response->current();
                $bal += (int)$data->getRemainingBalance();
            }*/
            //elog("Sent message to " . $data->getTo() . ". Balance is now " . $bal . PHP_EOL);
        } else {
            elog('Vonage API Credentials are missing','log',85,ROOTPATH.'core/modules/sms.php');
            return 0;
        }
    }
}