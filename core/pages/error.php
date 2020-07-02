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
$appdir = !empty( get_domain('sub') ) ? get_domain( 'sub' ) : get_domain();
if( $id == '00' && ( isset( $_POST['setup'] ) || isset( $_POST['step'] ) ) ) {
    include_once(COREPATH . 'core/pages/install.php');
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
    <link rel="shortcut icon" href="<?php echo APPURL.'assets/images/aio.png'; ?>">
    <link rel="stylesheet" href="<?php echo APPURL.'assets/styles/error.css'; ?>">
    <?php  ?>
</head>
<body>
    <?php

    if( ( !isset( $_POST['setup'] ) || $_POST['setup'] !== 'Yes' ) && !isset( $_POST['step'] ) ) {
        echo '<h1 id="id">'.$id.'</h1><h2 id="title">'.$title.'</h2>';
    }

    if( $id == '00' && !isset( $p['setup'] ) ) { ?>

            <div class="setup zero">

                <div class="q">
                    <div>Would you like to setup "<?php echo $appdir; ?>" app instead ?</div>
                    <div>
                        <form method="post">
                            <button name="setup" value="Yes">Yes</button>
                            <button name="setup" value="No">No</button>
                        </form>
                    </div>
                </div>

            </div>

        <?php
    } else {

        echo '<button id="back" onclick="window.history.back()">GO BACK</button>';

    } ?>
</body>
</html>