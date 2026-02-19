<?php
$errors = [
    '401' => 'Unauthorized',
    '403' => 'Page / Content Forbidden',
    '404' => 'Page Not Found',
    '408' => 'Request Timeout',
    '000' => 'Database Connectivity Failure',
    '00' => 'WebApp Not Found',
];
$title = !empty( $error ) && !empty( $errors[$error] ) ? $errors[ $error ] : 'What ? Error ??';
$id = !empty( $error ) ? $error : '0';
$page_title = $id . ' - ' . $title;
$appdir = !empty( get_domain('sub') ) ? get_domain( 'sub' ) : get_domain();
if( $id == '00' && ( isset( $_POST['setup'] ) ) ) {
    $deps = [ 'assets', 'config', 'functions', 'inputs' ];
    foreach($deps as $dep){
        $f = ROOTPATH . 'core/includes/' . $dep . '.php';
        file_exists( $f ) ? include_once( $f ) : '';
    }
    include_once(ROOTPATH . 'core/pages/builder.php');
    return;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $page_title; ?></title>
    <link rel="shortcut icon" href="<?php echo APPURL.'assets/images/fav_aio.png'; ?>">
    <link rel="stylesheet" href="<?php echo APPURL.'assets/fonts.php?error=Lato:300'; ?>">
    <link rel="stylesheet" href="<?php echo APPURL.'assets/styles/error.css'; ?>">
    <?php  ?>
</head>
<body>
    <?php

    if( ( !isset( $_POST['setup'] ) || $_POST['setup'] !== 'Yes' ) ) {
        el( 'h1', '', $id, 'id' );
        el( 'h2', '', $title, 'title' );
    }

    if( $id == '00' && !isset( $p['setup'] ) ) {
        _d( 'setup zero' );
            _d('aio_ai_suggestion');
                p( 'Would you like to build '.$appdir.' web app?' );
                _f('','post');
                    b('','Run Setup','','name="setup" value="Yes"');
                f_();
            d_();
        d_();
    } else {
        b( '', 'Go Back', 'back', 'onclick="history.back(-1);"' );
    } ?>
</body>
</html>