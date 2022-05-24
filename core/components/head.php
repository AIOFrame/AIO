<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo 'E Log - ' . APPNAME; ?></title>
    <link rel="icon" href="<?php echo APPURL; ?>assets/images/fav_aio.png" type="image/png" >
    <?php
    reset_styles('Lato','300',5);
    get_styles(['aio']);
    get_scripts('jquery');
    art('inputs,alerts','00A99D','047267');
    get_styles(['bootstrap/grid',PAGENAME,'micro']);
    font('Lato','300,500');
    ?>
</head>
<body <?php body_class(); ?>>