<?php

function clear_log_ajax() {
    unlink( APPPATH . 'events.log' );
}