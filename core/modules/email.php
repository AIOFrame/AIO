<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Mailjet\Resources;

class MAIL {

    /**
     * @param string $to Receivers email address
     * @param string $subject Email subject
     * @param string $content Email content
     * @param string $from From email address
     * @param string $cc Carbon-copy email address
     * @param string|array $gate Email gateway or SMTP array (sendgrid,mandrill,default php) or (google,yahoo,live,outlook)
     * @param string $key Email gateway API key
     * @param bool $auto_template Auto wrap in Template
     * @return bool
     */
    function send( string $to, string $subject, string $content, string $from = '', string $cc = '', string|array $gate = '', string $key = '', bool $auto_template = true ): bool {
        elog( 'To:' . $to . ', From: ' . $from . ', Sub: ' . $subject );
        if( $auto_template ) {
            $head = $this->get_template('head');
            $foot = $this->get_template('foot');
            $content = $head . $content . $foot;
        }
        if( class_exists( 'DB' ) ) {
            $db = new DB();
            $from = !empty( $from ) ? $from : $db->get_option('from_email');
            $gate = !empty( $gate ) ? $gate : $db->get_option('mail_gateway');
        }
        $gate = !empty( $gate ) ? $gate : get_config('mail_gateway');
        $from = !empty( $from ) ? $from : get_config('from_email');
        if( $gate == 'sendgrid' ) {
            elog( 'To: '.$to.', From: '.$from.', Sub: '.$subject.', Server: SendGrid' );
            return $this->sendgrid($to, $subject, $content, $from, $cc, $key);
        } else if( $gate == 'mailersend' ) {
            elog( 'To: '.$to.', From: '.$from.', Sub: '.$subject.', Server: MailerSend' );
            return $this->mailersend($to, $subject, $content, $from, $cc, $key);
        } else if( is_array( $gate ) || $gate == 'google' || $gate == 'outlook' || $gate == 'yahoo' || $gate == 'live' ) {
            return $this->smtp( $to, $subject, $content, $from, $gate, '', '', $cc, 0 );
        } else if( class_exists( 'DB' ) ) {
            $db = new DB();
            $key = $db->get_option('sendgrid_key');
            if( !empty( $key ) ) {
                elog( 'To: '.$to.', From: '.$from.', Sub: '.$subject.', Server: SendGrid' );
                return $this->sendgrid( $to, $subject, $content, $from, $cc, $key );
            } else {
                $key = $db->get_option('mailersend_key');
                if( !empty( $key ) ) {
                    elog( 'To: '.$to.', From: '.$from.', Sub: '.$subject.', Server: MailerSend' );
                    return $this->mailersend( $to, $subject, $content, $from, $cc, $key );
                } else {
                    $smtp = $this->smtp( $to, $subject, $content, $from, '', '', '', '', 0 );
                    if( !$smtp ) {
                        $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
                        $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
                        return mail($to, $subject, $content, $headers);
                    } else {
                        return $smtp;
                    }
                }
            }
        } else {
            $smtp = $this->smtp( $to, $subject, $content, $from, '', '', '', '', 0 );
            if( !$smtp ) {
                $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
                $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
                return mail($to, $subject, $content, $headers);
            } else {
                return $smtp;
            }
        }
    }

    /**
     * @param string|array $to Receivers email address
     * @param string $subject Email subject
     * @param string $content Email content
     * @param string $from From email address
     * @param array|string $smtp SMTP Server Details [ 'host', 'port' ] or 'google'|'yahoo'...
     * @param string $username SMTP Username
     * @param string $password SMTP Password
     * @param string $cc Carbon-copy email addresses
     * @param bool $auto_template Auto wrap in Template
     * @return bool
     */
    function smtp( string|array $to, string $subject, string $content, string $from = '', array|string $smtp = '', string $username = '', string $password = '', string $cc = '', bool $auto_template = true ): bool {

        elog('Email initiated thru SMTP');

        require ROOTPATH . 'core/external/vendor/autoload.php';
        /* require 'path/to/PHPMailer/src/Exception.php';
        require 'path/to/PHPMailer/src/PHPMailer.php';
        require 'path/to/PHPMailer/src/SMTP.php'; */
        if( $auto_template ) {
            $head = $this->get_template('head');
            $foot = $this->get_template('foot');
            $content = $head . $content . $foot;
        }
        $def = [
            'google' => [
                'host' => 'smtp.gmail.com',
                'port' => 465
            ],
            'yahoo' => [
                'host' => 'smtp.mail.yahoo.com'
            ],
            'hotmail' => [
                'host' => 'smtp-mail.outlook.com'
            ],
            'outlook' => [
                'host' => 'smtp-mail.outlook.com'
            ],
            'live' => [
                'host' => 'smtp-mail.outlook.com'
            ],
            'mailjet' => [
                'host' => 'in-v3.mailjet.com',
                'port' => 587
            ],
            'mailersend' => [
                'host' => 'smtp.mailersend.net',
                'port' => 587
            ],
            'sendgrid' => [
                'host' => 'smtp.sendgrid.net',
                'port' => 465
            ]
        ];
        if( class_exists( 'DB' ) ) {
            $db = new DB();
            $smtp = !empty( $smtp ) ? $smtp : $db->get_option('smtp');
            $username = !empty( $username ) ? $username : $db->get_option('smtp_username');
            $password = !empty( $password ) ? $password : $db->get_option('smtp_password');
            $from = !empty( $from ) ? $from : $db->get_option('from_email');
        }
        $smtp = !empty( $smtp ) ? $smtp : get_config('smtp');
        $username = !empty( $username ) ? $username : get_config('smtp_username');
        $password = !empty( $password ) ? $password : get_config('smtp_password');
        elog( 'SMTP -> '. $smtp );
        $smtp = is_array( $smtp ) ? $smtp : ($def[$smtp] ?? []);
        elog( 'SMTP -> '. json_encode( $smtp ) );
        $from = !empty( $from ) ? $from : $username;
        $smtp['port'] = !empty( $smtp['port'] ) ? $smtp['port'] : 465;
        $mail = new PHPMailer(true);

        $secure = $smtp['port'] == 587 ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;

        if( !empty( $smtp['host'] ) && !empty( $smtp['port'] ) ) {

            elog( 'To: ' . json_encode( $to ) . ', From: ' . json_encode( $from ) . ', Sub: ' . json_encode( $subject ), 'log', 182, ROOTPATH . 'core/modules/email.php' );
            elog( 'Server: ' . json_encode( $smtp ) );
            elog( 'Username: ' . $username . ', Password: ' . $password );

            elog('SMTP Host is '.$smtp['host']);

            try {
                $mail->SMTPDebug = false;
                $mail->isSMTP();
                $mail->Host       = $smtp['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = $username;
                $mail->Password   = $password;
                $mail->SMTPSecure = $secure;
                $mail->Port       = $smtp['port'];

                //Recipients
                if( is_array( $to ) ) {
                    foreach( $to as $t ) {
                        is_array( $t ) ? $mail->addAddress( $t[0], $t[1] ) : $mail->addAddress( $t );;
                    }
                } else {
                    $to = explode( ',', $to );
                    foreach( $to as $t ){
                        $mail->addAddress( $t );
                    }
                }
                $mail->setFrom( $from, APPNAME );
                $mail->addReplyTo( $from, APPNAME );

                // Carbon-copy
                if( is_array( $cc ) ) {
                    foreach( $cc as $c ) {
                        is_array( $c ) ? $mail->addCC( $c[0], $c[1] ) : $mail->addCC( $c );;
                    }
                } else if( !empty( $cc ) ) {
                    $cc = explode( ',', $cc );
                    foreach( $cc as $c ){
                        $mail->addCC( $c );
                    }
                }

                //Attachments
                //$mail->addAttachment('/var/tmp/file.tar.gz');
                //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');

                //Content
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body    = $content;

                $mail->send();
                return 1;
            } catch (Exception $e) {
                elog( $mail->ErrorInfo, 'error', 170, ROOTPATH . 'core/modules/email.php' );
                return 0;
            }
        } else {
            elog( 'SMTP Details are not set!' );
            return 0;
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

        elog('Email initiated thru SendGrid');

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
                return $e->getMessage();
            }
        } else {
            elog('SendGrid Key is Empty! Please add key to config or option.');
            return 0;
        }
    }

    function mailersend( $to, $subject, $content, $from, $cc = '', $key = '' ): bool {

        elog('Email initiated thru MailerSend');

        if( empty( $key ) ) {
            $db = new DB();
            $key = $db->get_option( 'mailersend_key' );
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

    function mailjet( string $to, string $subject, string $content, string $from = '', string $public_key = '', string $private_key = '' ): bool {

        elog('Email initiated thru MailJet');

        if( empty( $public_key ) || empty( $private_key ) ) {
            $db = new DB();
            $public_key = !empty( $public_key ) ? $public_key : $db->get_option( 'mailjet_public_key' );
            $private_key = !empty( $private_key ) ? $private_key : $db->get_option( 'mailjet_private_key' );
        }
        $public_key = !empty( $public_key ) ? $public_key : get_config( 'mailjet_public_key' );
        $private_key = !empty( $private_key ) ? $private_key : get_config( 'mailjet_private_key' );
        if( !empty( $public_key ) || !empty( $private_key ) ) {

            require ROOTPATH . 'core/external/vendor/autoload.php';

            // Use your saved credentials, specify that you are using Send API v3.1
            $mj = new \Mailjet\Client(getenv( $public_key ), getenv( $private_key ), true, ['version' => 'v3.1']);

            // Define your request body
            $body = [
                'Messages' => [
                    [
                        'From' => [
                            'Email' => $from,
                            //'Name' => "Me"
                        ],
                        'To' => [
                            [
                                'Email' => $to,
                                //'Name' => "You"
                            ]
                        ],
                        'Subject' => $subject,
                        //'TextPart' => "Greetings from Mailjet!",
                        'HTMLPart' => $content
                    ]
                ]
            ];

            // All resources are located in the Resources class
            $response = $mj->post(Resources::$Email, ['body' => $body]);

            // Read the response
            elog( $response->getData() );
            var_dump( $response->getData() );
            if( $response->success() ) {
                elog( $response->success() );
                return 1;
            } else {
                return 0;
            }
        } else {
            return 0;
        }
    }

    /**
     * Creates email template
     * @param string $type
     * @param string $content
     */
    function set_template( string $type = 'head', string $content = '' ): void {

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

    function email_template_fields( array $fields = [], array $options = [], string|int $pre = 12 ): void {
        $f = new FORM();
        foreach( $fields as $fk => $fv ) {
            $val = $options[ $fk ] ?? '';
            $f->textarea( $fk, $fv, '', $val, 'data-data class="editor"', $pre );
        }
    }

    /**
     * Renders Email Options
     * @param string $template_url
     * @return void
     */
    function options( string $template_url = '' ): void {
        $db = new DB();
        $email_fields = [
            'from_email' => 'From Email',
            'smtp' => 'SMTP Server',
            'smtp_username' => 'SMTP Username',
            'smtp_password' => 'SMTP Password',
        ];
        $m = new MAIL();
        if( isset( $_POST['template_head'] ) ) {
            $m->set_template( 'head', $_POST['template_head'] );
        }
        $head = $m->get_template('head');
        if( isset( $_POST['template_foot'] ) ) {
            $m->set_template( 'foot', $_POST['template_foot'] );
        }
        $foot = $m->get_template('foot');
        $f = new FORM();
        ?>
        <div class="mail_view">
            <iframe class="template_preview" src="<?php echo $template_url; ?>" frameborder="0"></iframe>
        </div>
        <div class="row" <?php $f->process_params('','email','',3,0,[],'Successfully Saved Settings'); ?>>
            <?php
            $os = $db->get_options(['from_email','smtp','smtp_username','smtp_password']);
            $attr = 'data-email';
            $f->text('test_content','','','',$attr.' class="dn"');
            //$f->text('autoload','','','from_email,smtp,smtp_username,smtp_password',$attr.' class="dn"');
            $f->input('email','test_email','Send Test Email','Ex: email@website.com', 'installer0001@gmail.com', $attr.' data-help', 10);
            $f->process_html('Send','l w r5 mt30','','send_test_email_ajax',2);
            $from = $os['from_email'] ?? '';
            $smtp = $os['smtp'] ?? '';
            $smtp_username = $os['smtp_username'] ?? '';
            $smtp_password = $os['smtp_password'] ?? '';
            $f->text('from_email','From (Sender) Email','',$from,$attr,3);
            $f->select('smtp','SMTP Gateway','Choose gateway...',[ 'google' => 'Google', 'yahoo' => 'Yahoo', 'hotmail' => 'Hotmail / Outlook / Live', 'mailjet' => 'MailJet', 'mailersend' => 'MailerSend', 'sendgrid' => 'SendGrid' ],$smtp,$attr.' class="select2"',3,1);
            $f->text('smtp_username','SMTP Email','',$smtp_username,$attr,3);
            $f->input('password','smtp_password','SMTP Password','',$smtp_password,$attr,3);
            $f->process_html('Save API Details','store grad','','process_options_ajax','col-12 tac');
            ?>
        </div>
        <form id="template" method="post">
            <div class="row">
                <?php
                echo '<div class="col-12 col-md-6">';
                $f->input( 'textarea','template_head','Email Header HTML','Ex: <html>',$head,'class="dn"' );
                echo '<div id="head_code" class="email_code"></div>';
                echo '</div>';
                echo '<div class="col-12 col-md-6">';
                $f->input( 'textarea','template_foot','Email Footer HTML','Ex: </html>',$foot,'class="dn"' );
                echo '<div id="foot_code" class="email_code"></div>';
                echo '</div>';
                ?>
            </div>
            <div class="tac"><button class="store"><?php E('Build Template'); ?></button></div>
        </form>

        <script>
            $(document).ready(function(){
                /* $('body').on('keyup focus','.editor', function(e){
                    let f = $('iframe');
                    let url = $(f).attr('src').split('?')[0]+'?all&text='+encodeURIComponent($(this).val());
                    $('#test_content').val($(this).val());
                    $(f).attr('src',url);
                    setTimeout(frame_height,1000);
                }); */
                $('.editor').trumbowyg({ autogrow: true }).on('tbwchange tbwfocus', function(e){
                    let f = $('iframe');
                    let url = $(f).attr('src').split('?')[0]+'?all&text='+encodeURIComponent($(this).val());
                    $('#test_content').val( $( e.currentTarget ).val() );
                    $(f).attr('src',url);
                    setTimeout( frame_height, 1000 );
                });
                let head_editor = ace.edit( 'head_code' );
                head_editor.session.setMode("ace/mode/html");
                head_editor.session.setValue($('#template_head').val(),-1);
                head_editor.session.on('change', function(d) {
                    $('#template_head').val( head_editor.getValue() );
                });
                let foot_editor = ace.edit( 'foot_code' );
                foot_editor.session.setMode("ace/mode/html");
                foot_editor.session.setValue($('#template_foot').val(),-1);
                foot_editor.session.on('change', function(d) {
                    $('#template_foot').val( foot_editor.getValue() );
                });
            });
            function frame_height() {
                let f = $('iframe');
                //console.log( $(f).contents().find('html').height() );
                $(f).height( $(f).contents().find('html').height() );
            }
        </script>
        <style>
            #head_code, #foot_code {
                position: relative;
                width: 100%;
                height: 220px;
            }
        </style>
        <?php
        get_style('https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.21.0/ui/trumbowyg.min.css');
        get_scripts(['ace','https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.21.0/trumbowyg.min.js']);
        echo '<script>ace.config.set("basePath", "'. APPURL . 'assets/ext/ace/" );</script>';
    }

    function render_template(): void {
        $m = new MAIL();
        $head = $m->get_template('head');
        $foot = $m->get_template('foot');

        $text = isset( $_GET['text'] ) ? '<div style="text-align:center">'.urldecode( $_GET['text'] ).'</div>' : '';
        echo isset( $_GET['head'] ) ? $head . $text : '';
        echo isset( $_GET['foot'] ) ? $text . $foot : '';
        echo isset( $_GET['all'] ) ? $head.$text.$foot : '';
    }

}

function send_test_email_ajax(): void {
    elog( $_POST );
    if( isset( $_POST['test_email'] ) && $_POST['test_content'] ) {
        $m = new MAIL();
        $m->send( $_POST['test_email'], 'Test Email Template', $_POST['test_content'] );
        es('Test Email Send!');
    } else {
        ef('Test Email failed because of empty email or template content');
    }
}