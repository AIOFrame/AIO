<?php

// Set autoload user options as session
$options = select( 'options', 'option_name,option_value', 'option_scope = "'.$_SESSION['user_id'].'" AND option_load = 1' );
if( is_array( $options ) ) {
    foreach( $options as $opt ) {
        $_SESSION[$opt['option_name']] = $opt['option_value'];
    }
}