<?php
$errors = [
    '401' => 'Unauthorized',
    '403' => 'Page / Content Forbidden',
    '404' => 'Page Not Found',
    '408' => 'Request Timeout',
    '000' => 'Database Connectivity Failure',
    '00' => 'App Not Found',
];
$title = !empty( $error ) && !empty( $errors[$error] ) ? $errors[ $error ] : 'What ? Error ??';
$id = !empty( $error ) ? $error : '0';
$page_title = $id . ' - ' . $title;
if( $id == '00' ) {
    include_once( COREPATH . 'core/includes/setup.php' );
    include_once( COREPATH . 'core/includes/arrays.php' );
    include_once( COREPATH . 'core/includes/functions.php' );
    $page_title = 'Welcome to AIO Setup';
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
    <link rel="shortcut icon" href="<?php echo APPURL.'assets/images/aio.png'; ?>">
    <link rel="stylesheet" href="<?php echo APPURL.'assets/styles/error.min.css'; ?>">
    <?php if( $id == '00' && ( isset( $_POST['setup'] ) || isset( $_POST['step'] ) ) ) {
        echo '<link rel="stylesheet" href="'.APPURL.'assets/styles/reset.min.css">';
        echo '<link rel="stylesheet" href="'.APPURL.'assets/styles/select2.min.css">';
        echo '<link rel="stylesheet" href="'.APPURL.'assets/styles/inputs.min.css">';
        echo '<link rel="stylesheet" href="'.APPURL.'assets/styles/install.min.css">';
    } ?>
</head>
<body>
    <?php

    if( ( !isset( $_POST['setup'] ) || $_POST['setup'] !== 'Yes' ) && !isset( $_POST['step'] ) ) {
        echo '<h1 id="id">'.$id.'</h1><h2 id="title">'.$title.'</h2>';
    }

    if( $id == '00' && get_user_ip() == '127.0.0.1' ) {

        include_once(COREPATH . 'core/pages/install.php');

    } else {

        echo '<button id="back" onclick="window.history.back()">GO BACK</button>';

    } ?>
    <?php if( $id == '00' && ( isset( $_POST['setup'] ) || isset( $_POST['step'] ) ) ) {
        echo '<script src="'.APPURL.'assets/scripts/jquery.min.js"></script>';
        echo '<script src="'.APPURL.'assets/scripts/select2.min.js"></script>';
        echo '<script src="'.APPURL.'assets/scripts/core.min.js"></script>';
        echo '<script src="'.APPURL.'assets/scripts/aio_install.min.js"></script>';
    } ?>
</body>
</html>