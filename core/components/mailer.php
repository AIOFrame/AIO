<?php

class MAIL {

    function send_email($to, $subject, $content, $from, $cc = '') {
        $headers = "MIME-Version: 1.0" . "\r\n" . "Content-type:text/html;charset=UTF-8" . "\r\n" . "From: " . $from . "\r\n" . "Reply-To: " . $from;
        $headers .= !empty($c) ? "\r\n" . "CC: " . $cc : '';
        return mail($to, $subject, $content, $headers);
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