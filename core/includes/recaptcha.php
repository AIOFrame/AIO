<?php

function recaptcha_verify() {
    $secret_key = get_config('recaptcha_secret_key');
    if( !empty( $secret_key ) ) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['recaptcha_token'])) {
            $recaptcha = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['recaptcha_token']);
            $recaptcha = json_decode($recaptcha);
            return $recaptcha->score >= 0.5 ? 1 : 0;
        } else {
            return 0;
        }
    } else {
        elog('Recaptcha secret key not found!');
        return 0;
    }
}