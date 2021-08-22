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
     * @param bool $auto_template Auto wrap in Template
     * @return bool
     */
    function send( string $to, string $subject, string $content, string $from = '', string $cc = '', string $gate = '', string $key = '', bool $auto_template = true ): bool {
        if( $auto_template ) {
            $head = $this->get_template('head');
            $foot = $this->get_template('foot');
            $content = $head . $content . $foot;
        }
        $from = !empty( $from ) ? $from : 'no-reply@'.parse_url(APPURL, PHP_URL_HOST);
        elog( 'To: '.$to.', From: '.$from.', Sub: '.$subject.', Sender: '.$gate );
        if( $gate == 'sendgrid' ) {
            return $this->sendgrid($to, $subject, $content, $from, $cc, $key);
        } else if( $gate == 'mailersend' ) {
            return $this->mailersend($to, $subject, $content, $from, $cc, $key);
        } else if( class_exists( 'DB' ) ) {
            $db = new DB();
            $key = $db->get_option('sendgrid_key');
            if( !empty( $key ) ) {
                return $this->sendgrid( $to, $subject, $content, $from, $cc, $key );
            } else {
                $key = $db->get_option('mailersend_key');
                if( !empty( $key ) ) {
                    return $this->mailersend( $to, $subject, $content, $from, $cc, $key );
                } else {
                    $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
                    $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
                    return mail($to, $subject, $content, $headers);
                }
            }
        } else {
            $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
            $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
            return mail($to, $subject, $content, $headers);
        }
    }

    /**
     * Sends email thru SendGrid API
     * @param string $to Receiver's email addresses separated by ,
     * @param string $subject Subject of the email
     * @param string $content HTML content of the email
     * @param string $from Sender's email address
     * @param string $cc Carbon copy receivers addresses separated by ,
     * @param string $key SendGrid API Key (optional)
     * @return bool
     * @throws \SendGrid\Mail\TypeException
     */
    function sendgrid( string $to, string $subject, string $content, string $from = '', string $cc = '', string $key = '' ): bool {

        if( empty( $key ) ) {
            $con = new DB();
            $key = $con->get_option('sendgrid_key');
        }
        $key = empty( $key ) ? get_config( 'sendgrid_key' ) : $key;

        if( !empty( $key ) ) {

            require ROOTPATH . 'core/external/vendor/autoload.php';

            $email = new \SendGrid\Mail\Mail();
            $email->setFrom( $from );
            $email->setSubject( $subject );

            $to = explode( ',', $to );
            foreach( $to as $t ){
                $email->addTo( $t );
            }

            $email->addContent("text/html", $content);
            $sendgrid = new \SendGrid(getenv($key));
            try {
                $response = $sendgrid->send($email);
                //elog( $response );
                //$code = $response->statusCode();
                //$error = is_numeric( $code ) && isset( $errors[$code] ) ? $errors[$code] : $code;
                elog( $response->headers(), 'log', '88', __FILE__ );
                elog( $response->body(), 'log', '88', __FILE__ );
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

        if( empty( $key ) ) {
            $con = new DB();
            $key = $con->get_option( 'mailersend_key' );
        }
        $key = empty( $key ) ? get_config( 'mailersend_key' ) : $key;

        if( !empty( $key ) ) {
            require ROOTPATH . 'core/external/vendor/autoload.php';
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

    /**
     * Creates email template
     * @param string $type
     * @param string $content
     */
    function set_template( string $type = 'head', string $content = '' ) {

        // Create dir if not exists
        $template_path = APPPATH . 'storage/mail_templates/';
        if ( !file_exists( $template_path ) ) {
            mkdir( $template_path, 0777, true);
        }

        // Create template HTML file
        $file = fopen( $template_path . $type . '.html' , 'w' ) or die( 'Unable to open file!' );
        fwrite( $file, $content );
        fclose( $file );
    }

    /**
     * Fetches template content
     * @param string $template
     * @return string
     */
    function get_template( string $template = 'head' ): string {
        $template_path = APPPATH . 'storage/mail_templates/';
        return file_exists( $template_path . $template . '.html' ) ? file_get_contents( $template_path . $template . '.html' ) : '';
    }
}