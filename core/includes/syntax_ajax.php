<?php

if( isset( $_POST['action'] ) && !empty( $_POST['action'] ) ){
    $process_ajax = $_POST['action'];
    $process_ajax( $_POST );
}

?>