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
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo $id . ' - ' . $title; ?></title>
    <style>
        body {
            text-align: center;
        }
        h1, h2, button {
            font-family:'Segoe UI','Helvetica Neue',sans-serif;
            font-weight: lighter;
            letter-spacing: 2px;
        }
        h1 {
            font-size: 160px;
            margin:10vh auto 30px;
        }
        h2 {
            font-size: 40px;
            text-transform: uppercase;
        }
        button {
            border:0;
            padding:12px 25px;
            font-size: 20px;
            cursor:pointer;
            outline: 0;
        }
    </style>
</head>
<body>
    <h1><?php echo $id; ?></h1>
    <h2><?php echo $title; ?></h2>
    <button onclick="window.history.back()">GO BACK</button>
</body>
</html>