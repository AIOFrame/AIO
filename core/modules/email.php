<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use \Mailjet\Resources;

class MAIL {

    private array $mail_providers = [
        'google' => [
            'text' => 'Google',
            'value' => 'smtp.gmail.com',
            'port' => 465
        ],
        'yahoo' => [
            'text' => 'Yahoo Mail',
            'value' => 'smtp.mail.yahoo.com'
        ],
        'hotmail' => [
            'text' => 'Hotmail',
            'value' => 'smtp-mail.outlook.com'
        ],
        'outlook' => [
            'text' => 'Outlook',
            'value' => 'smtp-mail.outlook.com'
        ],
        'live' => [
            'text' => 'Live Mail',
            'value' => 'smtp-mail.outlook.com'
        ],
        'mailjet' => [
            'text' => 'MailJet',
            'value' => 'in-v3.mailjet.com',
            'port' => 587
        ],
        'mailersend' => [
            'text' => 'MailerSend',
            'value' => 'smtp.mailersend.net',
            'port' => 587
        ],
        'sendgrid' => [
            'text' => 'SendGrid',
            'value' => 'smtp.sendgrid.net',
            'port' => 465
        ],
        'zepto' => [
            'text' => 'ZeptoMail',
            'value' => 'smtp.zeptomail.com',
            'port' => 465
        ],
        'sendinblue' => [
            'text' => 'SendInBlue',
            'value' => 'smtp-relay.sendinblue.com',
            'port' => 587
        ],
        'postmark' => [
            'text' => 'PostMark',
            'value' => 'smtp.postmarkapp.com',
            'port' => 587
        ],
        'mailgun' => [
            'text' => 'MailGun',
            'value' => 'smtp.mailgun.org',
            'port' => 587
        ],
        'elastic' => [
            'text' => 'Elastic Email',
            'value' => 'smtp.elasticemail.com',
            'port' => 587
        ],
        'pepipost' => [
            'text' => 'Pepipost',
            'value' => 'smtp.pepipost.com',
            'port' => 587
        ],
        'sparkpost' => [
            'text' => 'SparkPost',
            'value' => 'smtp.sparkpostmail.com',
            'port' => 587
        ],
        'mandrill' => [
            'text' => 'Mandrill',
            'value' => 'smtp.mandrillapp.com',
            'port' => 465
        ],
        'netcore' => [
            'text' => 'Net Core',
            'value' => 'smtp.netcorecloud.net',
            'port' => 465
        ],
        'sendpulse' => [
            'text' => 'SendPulse',
            'value' => 'smtp-pulse.com',
            'port' => 2525
        ],
        'kingmailer' => [
            'text' => 'KingMailer',
            'value' => 'kingmailer.org',
            'port' => 465
        ]
    ];

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
            elog( 'VIA SMTP: To:' . $to . ', From: ' . $from . ', Sub: ' . $subject );
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

        require VENDORLOAD;
        /* require 'path/to/PHPMailer/src/Exception.php';
        require 'path/to/PHPMailer/src/PHPMailer.php';
        require 'path/to/PHPMailer/src/SMTP.php'; */
        if( $auto_template ) {
            $head = $this->get_template('head');
            $foot = $this->get_template('foot');
            $content = $head . $content . $foot;
        }
        //$def = $this->mail_providers;
        $config = get_config( 'smtp' );
        if( is_array( $config ) ) {
            $smtp_server = !empty($smtp_server) ? $smtp_server : $config['server'];
            $smtp_port = !empty($smtp_port) ? $smtp_port : $config['port'];
            $username = !empty($username) ? $username : $config['username'];
            $password = !empty($password) ? $password : $config['password'];
        }
        if( class_exists( 'DB' ) ) {
            $db = new DB();
            $smtp_options = $db->get_options(['smtp_server','smtp_port','smtp_username','smtp_password']);
            $smtp_server = !empty( $smtp ) ? $smtp : ( $smtp_options['smtp_server'] ?? '' );
            $smtp_port = $smtp_options['smtp_port'] ?? '';
            $username = !empty( $username ) ? $username : ( $smtp_options['smtp_username'] ?? '' );
            $password = !empty( $password ) ? $password : ( $smtp_options['smtp_password'] ?? '' );
            $from = !empty( $from ) ? $from : ( $smtp_options['from_email'] ?? '' );
        }
        if( empty( $smtp_server ) ) {
            elog( 'Need to configure SMTP credentials!' );
            return 0;
        }
        elog( 'SMTP -> '. $smtp_server );
        /* $smtp = is_array( $smtp ) ? $smtp : ($def[$smtp] ?? []);
        elog( 'SMTP -> '. json_encode( $smtp ) );
        $from = !empty( $from ) ? $from : $username;
        $smtp['port'] = !empty( $smtp['port'] ) ? $smtp['port'] : 465; */
        $mail = new PHPMailer(true);

        $secure = $smtp_port == 587 ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;

        if( !empty( $smtp_server ) && !empty( $smtp_port ) ) {

            elog( 'To: ' . json_encode( $to ) . ', From: ' . json_encode( $from ) . ', Sub: ' . json_encode( $subject ), 'log', 182, ROOTPATH . 'core/modules/email.php' );
            elog( 'Server: ' . json_encode( $smtp ) );
            elog( 'Username: ' . $username . ', Password: ' . $password );

            elog('SMTP Host is '.$smtp_port);

            try {
                $mail->SMTPDebug = false;
                $mail->isSMTP();
                $mail->Host       = $smtp_server;
                $mail->SMTPAuth   = true;
                $mail->Username   = $username;
                $mail->Password   = $password;
                $mail->SMTPSecure = $secure;
                $mail->Port       = $smtp_port;

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

            require VENDORLOAD;

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

        if( empty( $key ) && defined( 'DB_TYPE' ) ) {
            $db = new DB();
            $key = $db->get_option( 'mailersend_key' );
        }
        $key = empty( $key ) ? get_config( 'mailersend_key' ) : $key;

        if( !empty( $key ) ) {
            require VENDORLOAD;
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

            require VENDORLOAD;

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

    /**
     * Renders email subject field and template editor
     * @param array $fields Email fields as assoc array [ 'welcome_student' => 'Welcome Student' ]
     * @param string $type
     * @param string|int $pre
     * @return void
     */
    function email_template_fields( array $fields = [], string $type = 'richtext', string|int $pre = 12 ): void {
        $f = new FORM();
        $db = new DB();
        foreach( $fields as $fk => $fv ) {
            _c( $pre );
            $options = $db->get_options([$fk,$fk.'_subject']);
            $subject_val = $options[ $fk.'_subject' ] ?? '';
            $template_val = $options[ $fk ] ?? '';
            $f->text( $fk.'_subject', $fv.' - '.T('Subject'), '', $subject_val, 'data-data class="subject"' );
            if( $type == 'richtext' ) {
                $f->richtext( $fk, $fv.' - '.T('Content'), $template_val, 'data-data class="editor"' );
            } else if( $type == 'code' ) {
                $f->code( $fk, $fv, $template_val, 'data-data class="editor"' );
            } else if( $type == 'textarea' ) {
                $f->textarea( $fk, $fv, '', $template_val, 'data-data class="editor"' );
            }
            c_();
        }
        ?>
        <script>
            document.addEventListener('DOMContentLoaded',function(){
                if( $('.template_preview').length ) {
                    $('.editor').trumbowyg({autogrow: true}).on('tbwchange tbwfocus', function (e) {
                        let f = $('iframe.template_preview');
                        let url = location.origin + $(f).data('url') + '?all&text=' + encodeURIComponent($(this).val());
                        console.log(url);
                        $('[data-key=test_content]').val($(e.currentTarget).val());
                        $(f).attr('src', url);
                        setTimeout(frame_height, 1000);
                    });
                }
            });
            function frame_height() {
                let f = $('iframe');
                $(f).height( $(f).contents().find('html').height() );
            }
        </script>
        <?php
    }

    function template_viewer( string $url = APPURL, string $wrap_class = '' ): void {
        echo '<div class="mail_view '.$wrap_class.'"><iframe style="width: 100%; margin-bottom: 30px;" class="template_preview" data-url="'.$url.'" src="'.$url.'" frameborder="0"></iframe></div>';
        global $template_strings;
        if( !empty( $template_strings ) && is_array( $template_strings ) ) {
            _d( 'template_strings' );
                h4( 'Template Strings', 1 );
                div( 'desc mb20', T('Copy the code within {{}} to replace with actual data!') );
            foreach( $template_strings as $code => $replace ) {
                _d( 'fz14 mb10' );
                    pre( $code . ' = ' . $replace );
                d_();
            }
            d_();
        }
    }

    /**
     * Renders Email Options
     * @param string $template_url
     * @return void
     */
    function options( string $template_url = '' ): void {
        $db = new DB();
        //$smtp_servers = [];
        $mail_providers = $this->mail_providers;
        /* $smtp_servers = [
            'google' => 'Google',
            'yahoo' => 'Yahoo',
            'hotmail' => 'Hotmail / Outlook / Live',
            'mailjet' => 'MailJet',
            'mailersend' => 'MailerSend',
            'sendgrid' => 'SendGrid',
            'zepto' => 'ZeptoMail'
        ];
        $email_fields = [
            'from_email' => 'From Email',
            'smtp' => 'SMTP Server',
            'smtp_username' => 'SMTP Username',
            'smtp_password' => 'SMTP Password',
        ]; */
        
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
        $this->template_viewer( $template_url );
        $f->pre_process('class="row"','','email');
        $os = $db->get_options(['from_email','smtp','smtp_server','smtp_port','smtp_username','smtp_password']);
        $attr = 'data-email';
        $f->text('test_content','','','',$attr.' class="dn"');
        //$f->text('autoload','','','from_email,smtp,smtp_username,smtp_password',$attr.' class="dn"');
        $f->input('email','test_email','Send Test Email','Ex: email@website.com', 'installer0001@gmail.com', $attr.' data-help', 10);
        $f->process_trigger('Send','l w r5 mt30','','send_test_email_ajax',2);
        $from = $os['from_email'] ?? '';
        $smtp = $os['smtp'] ?? '';
        $smtp_server = $os['smtp_server'] ?? '';
        $smtp_port = $os['smtp_port'] ?? '';
        $smtp_username = $os['smtp_username'] ?? '';
        $smtp_password = $os['smtp_password'] ?? '';
        $f->select('smtp','SMTP Gateway','Choose gateway...', $mail_providers, $smtp,$attr.' class="select2"',4,1,0);
        $f->texts([
            ['smtp_server','SMTP Server','',$smtp_server],
            ['smtp_port','SMTP Port','',$smtp_port],
            ['from_email','From (Sender) Email','',$from],
            ['smtp_username','SMTP Email','',$smtp_username],
        ],$attr,4);
        $f->input('password','smtp_password','SMTP Password','',$smtp_password,$attr,4);
        $f->process_trigger('Save API Details','store grad','','process_options_ajax','.col-12 tac');
        $f->post_process();
        pre( 'template', '', 'form', 'method="post"' );
            _r();
                _c(6);
                    $f->input( 'textarea','template_head','Email Header HTML','Ex: <html>',$head,'class="dn"' );
                    div( 'email_code', '', 'head_code' );
                c_();
                _c(6);
                    $f->input( 'textarea','template_foot','Email Footer HTML','Ex: </html>',$foot,'class="dn"' );
                    div( 'email_code', '', 'foot_code' );
                c_();
            r_();
            _d( 'tac' );
                b( 'store', T('Build Template') );
            d_();
        post( 'form' );
        get_style('https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.21.0/ui/trumbowyg.min.css');
        get_scripts(['ace','https://cdnjs.cloudflare.com/ajax/libs/Trumbowyg/2.21.0/trumbowyg.min.js']);
        ?>
        <script>
            window.onload = function () {
                $(document).ready(function () {
                    /* $('body').on('keyup focus','.editor', function(e){
                        let f = $('iframe');
                        let url = $(f).attr('src').split('?')[0]+'?all&text='+encodeURIComponent($(this).val());
                        $('#test_content').val($(this).val());
                        $(f).attr('src',url);
                        setTimeout(frame_height,1000);
                    }); */

                    $('body').on('change', '[data-key=smtp]', function () {
                        let d = $(this).find(':selected');
                        let port = d.data('port') !== undefined ? d.data('port') : 465;
                        $('[data-key=smtp_server]').val(d.val());
                        $('[data-key=smtp_port]').val(port);
                    });

                    $('.editor').trumbowyg({autogrow: true}).on('tbwchange tbwfocus', function (e) {
                        let f = $('iframe.template_preview');
                        let url = location.origin + '/' + $(f).data('url') + '?all&text=' + encodeURIComponent($(this).val());
                        $('[data-key=test_content]').val($(e.currentTarget).val());
                        $(f).attr('src', url);
                        setTimeout(frame_height, 1000);
                    });
                    let head_editor = ace.edit('head_code');
                    head_editor.session.setMode("ace/mode/html");
                    head_editor.session.setValue($('[data-key=template_head]').val(), -1);
                    head_editor.session.on('change', function (d) {
                        $('[data-key=template_head]').val(head_editor.getValue());
                    });
                    let foot_editor = ace.edit('foot_code');
                    foot_editor.session.setMode("ace/mode/html");
                    foot_editor.session.setValue($('[data-key=template_foot]').val(), -1);
                    foot_editor.session.on('change', function (d) {
                        $('[data-key=template_foot]').val(foot_editor.getValue());
                    });
                });
                ace.config.set("basePath", "<?php echo APPURL; ?>assets/ext/ace/" );
            }
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
    if( isset( $_POST['test_email'] ) ) {
        $m = new MAIL();
        $content = $_POST['test_content'] ?? '';
        $m->send( $_POST['test_email'], 'Test Email Template', $content );
        es('Test Email Send!');
    } else {
        ef('Test Email failed because of empty email or template content');
    }
}