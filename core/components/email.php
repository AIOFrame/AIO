<?php

class MAIL {

    /**
     * @param string $to Receivers email address
     * @param string $subject Email subject
     * @param string $content Email content
     * @param string $from From email address
     * @param string $cc Carbon-copy email address
     * @param string $gate Email gateway (sendgrid,mandrill,default php)
     * @param string $key Email gateway API key
     * @return bool
     */
    function send( string $to, string $subject, string $content, string $from, string $cc = '', string $gate = '', string $key = '' ) {
        if( $gate == 'sendgrid' ) {
            return $this->sendgrid($to, $subject, $content, $from, $cc, $key);
        } else if( $gate == 'mailersend' ) {
            return $this->mailersend($to, $subject, $content, $from, $cc, $key);
        } else {
            $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
            $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
            return mail($to, $subject, $content, $headers);
        }
    }

    function sendgrid( $to, $subject, $content, $from, $cc = '', $key = '' ): bool {

        $key = empty( $key ) ? get_config( 'sendgrid_key' ) : '';
        global $con;
        if( empty( $key ) && $con ) {
            $key = get_option( 'sendgrid_key' );
        }

        if( !empty( $key ) ) {

            require COREPATH . 'core/components/ext/sendgrid/autoload.php';

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom( $from );
            $email->setSubject( $subject );

            if( is_array( $to ) ){
                foreach( $to as $k => $v ){
                    $email->addTo($v, $k);
                }
            } else {
                $email->addTo($to);
            }

            $email->addContent("text/html", $content);
            $sendgrid = new \SendGrid(getenv($key));
            try {
                $response = $sendgrid->send($email);
                elog( $response->statusCode() );
                return 1;
            } catch (Exception $e) {
                elog( $e->getMessage() );
                return 0;
            }
        } else {
            elog('SendGrid Key is Empty! Please add key to config or option.');
            return 0;
        }
    }

    function mailersend( $to, $subject, $content, $from, $cc = '', $key = '' ): bool {
        $key = empty( $key ) ? get_config( 'mailersend_key' ) : '';
        global $con;
        if( empty( $key ) && $con ) {
            $key = get_option( 'mailersend_key' );
        }

        if( !empty( $key ) ) {
            require COREPATH . 'core/components/ext/guzzle/autoload.php';
            $client = new GuzzleHttp\Client(['base_uri' => 'https://api.mailersend.com/v1/']);
            $options = [
                'headers' => [
                    'Authorization' => 'Bearer '.$key
                ],
                'form_params' => [
                    'from' => [
                        'email' => $from,
                        'name' => 'Email from '.APPNAME
                    ],
                    'to' => [ [
                        'email' => $to,
                    ] ],
                    'subject' => $subject,
                    'html' => $content,
                    'text' => 'Email from '.APPNAME,
                ]
            ];
            $response = $client->request( 'POST', 'email', $options);
            return $response->getStatusCode() == 202 ? 1 : 0;
        } else {
            elog('MailerSend Key is Empty! Please add key to config or option named "mailersend_key".','error');
            return 0;
        }
    }

}