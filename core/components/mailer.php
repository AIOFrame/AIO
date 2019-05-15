<?php

class MAIL {

    function send_email($to, $subject, $content, $from, $cc = '') {

        // Get preferred mailer
        $mailer = get_option( 'mailer' );
        $mailer = empty( $mailer ) ? get_config( 'mailer' ) : $mailer;

        $mail_log = 'Mailer found to be '.$mailer;

        // Send mail via mailer
        switch( $mailer ) {
            case 'sendgrid':

                // Get Send Grid key
                $key = get_option('send_grid_key');
                $key = empty( $key ) ? get_config( 'send_grid_key' ) : $key;
                $mail_log .= '. SendGrid key is '.$key;

                if( !empty( $key ) ) {

                    require( COREPATH . 'core/components/emailers/sendgrid/sendgrid-php.php' );

                    $email = new SendGrid\Mail\Mail();
                    if( is_assoc( $from ) ) {
                        $keys = array_keys( $from );
                        $email->setFrom( $keys[0], $from[$keys[0]] );
                    } else {
                        $email->setFrom( $from );
                    }
                    $email->setSubject( $subject );
                    if( is_array( $to ) ) {
                        if( is_assoc( $to ) ) {
                            foreach( $to as $tk => $tv ) {
                                $email->addTo( $tk, $tv );
                            }
                        } else {
                            foreach( $to as $t ) {
                                $email->addTo( $t );
                            }
                        }
                    } else {
                        $email->addTo( $to );
                    }
                    $email->addContent( "text/html", $content );
                    $sendgrid = new \SendGrid( $key );
                    try {
                        $response = $sendgrid->send($email);
                        $mail_log .= '. Response: '. json_encode($response->headers()) . '. ';
                        $mail_log .= json_encode($response->body());
                        elog( $mail_log );
                        return $response;
                    } catch (Exception $e) {
                        $mail_log .= '. Caught exception: '. $e->getMessage();
                        elog( $mail_log );
                    }
                } else {
                    $mail_log .= '. KEY NOT FOUND';
                    elog( $mail_log );
                }

                break;
            case 'mandrill':
                break;
            default:
                $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
                $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
                return mail($to, $subject, $content, $headers);
        }

    }

    function mandrill_send($to, $to_name, $subject, $content, $from, $from_name, $cc = '') {
        require('Mandrill.php');

        $mandrill = new Mandrill('CNQfDLttNFDZ9Wq7D_ai8w');

        $message = array();

        $message['html'] = $content;
        $message['subject'] = $subject;
        $message['from_email'] = $from;
        $message['from_name'] = $from_name;

// instantiate a Recipient object and add details

        $message['to'] = array(
            array(
                'email' => $to,
                'name' => $to_name,
                'type' => "to"
            )
        );
// send the message
        $response = $mandrill->messages->send($message);
        return $response;
    }

}