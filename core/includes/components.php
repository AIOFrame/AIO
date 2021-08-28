<?php

function clear_log_ajax() {
    unlink( APPPATH . 'events.log' ) ? es('Successfully removed log file!') : ef('Failed to remove log file!');
}