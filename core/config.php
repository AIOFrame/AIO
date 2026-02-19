<?php

return [

    'data' => [
        // Set your Database type
        'type' => 'mysql',
        // Set your global database server
        'host' => 'localhost',
        // Set your global privileged username
        'username' => 'mosques',
        // Set your global privileged password
        'password' => 'tA*9nK2.Vfk44)[0',
    ],

    'sample' => [
        [
            'name' => '',
            'debug' => 1,
            'cache' => 0,
            'portal' => 1,
            'autofill' => 1,
            'key' => '',
            'timezone' => '',
            'data' => [
                'type' => 'mysql',
                'host' => 'localhost',
                'database' => '',
                'username' => '',
                'password' => '',
            ],
            'functions' => [
                'logged_out' => [ 'fun' ],
            ],
            'gemini_key' => '',
            'google_maps_key' => '',
            'features' => [ 'users', 'languages', 'data', 'cms', 'region', 'storage', 'portal' ],
            'modules' => [ 'email', 'maps', 'spreadsheet' ],
            'users' => [
                [ 'login' => 'developer', 'password' => 'passwords' ],
            ]
        ]
    ]
];

//CREATE USER 'manydoors'@'localhost' IDENTIFIED WITH mysql_native_password AS '***';GRANT USAGE ON *.* TO 'manydoors'@'localhost' REQUIRE NONE WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0;CREATE DATABASE IF NOT EXISTS `manydoors`;GRANT ALL PRIVILEGES ON `manydoors`.* TO 'manydoors'@'localhost';