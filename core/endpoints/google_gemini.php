<?php
global $options;
$key = !empty( $options['gemini_key'] ) ? $options['gemini_key'] : ( !empty( CONFIG['gemini_key'] ) ? CONFIG['gemini_key'] : '' );
if( !empty( $key ) && !empty( $_POST['prompt'] ) ) {
    $c = new CURL();
    $data = [ "contents" => [ [ "parts" => [ [ "text" => $_POST['prompt'] ] ] ] ] ];
    $r = $c->post(
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key='.$key,
        [ 'Content-Type: application/json' ],
        json_encode($data)
    );
    $r = json_decode( $r, 1 );
    if( !empty( $r['candidates'][0]['content']['parts'][0]['text'] ) ) {
        echo str_replace( '**', '', $r['candidates'][0]['content']['parts'][0]['text'] );
    }
}