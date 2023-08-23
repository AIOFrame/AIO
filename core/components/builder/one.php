<?php
$f = new FORM();
$r = substr(md5(microtime()),rand(0,26),15);
$fields = [
    [ 'i' => 'name', 'n' => 'Name your Web App', 'p' => 'Ex: Food Delivery, Events, CRM, '.ucfirst( APPDIR ).' App, '.ucfirst( APPDIR ).' etc.', 'c' => 12 ],
    [ 't' => 'slide', 'i' => 'force_ssl', 'n' => 'Do you want to force SSL ?', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 6 ],
    [ 't' => 'slide', 'i' => 'debug', 'n' => 'Do you prefer debug mode ?', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 6 ],
    [ 't' => 'slide', 'i' => 'git_ignore', 'n' => 'Create a default .gitignore ?', 'off' => 'No', 'on' => 'Yes', 'v' => 1, 'c' => 6 ],
    [ 'i' => 'key', 'n' => 'Set a key for basic encryption', 'p' => 'Ex: '.$r, 'c' => 6 ],
];
$f->form( $fields, 'row', 'setup one' );