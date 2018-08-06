<?php

class SMS {

    function send_sms( $no, $msg, $gate, $key, $sec ) {
        if( $gate == 'nexmo' ){
            return $this->nexmo_send_sms( $no, $msg, $key, $sec );
        }
    }

    function nexmo_send_sms( $no, $msg, $key, $sec ){
        if( file_exists( COREPATH . 'core/components/sms_api/nexmo/autoload.php' ) ){
            include_once( COREPATH . 'core/components/sms_api/nexmo/autoload.php' );
            $client = new Nexmo\Client(new Nexmo\Client\Credentials\Basic( $key, $sec ));
            $message = $client->message()->send([
                'to' => $no,
                'from' => APPNAME,
                'text' => $msg
            ]);
            return $message;
        } else {
            return 'API Missing';
        }
    }
}