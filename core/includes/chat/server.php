<?php
require '../../external/vendor/autoload.php';
require 'chat.php';

//print_r( $_SESSION );
// Run the server application through the WebSocket protocol on port 8080
$app = new Ratchet\App('localhost', 8080);
$app->route('/chat', new Chat, array('*'));
//$app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
$app->run();