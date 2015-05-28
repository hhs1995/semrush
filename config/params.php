<?php

return [
    'beanstalkd' => [
        'host' =>  '127.0.0.1',
        'port' =>  11300,
        'tube' => [
            'notifications:combine' => 'notificationsCombine',
            'notifications:push'    => 'notificationsPush'
        ]
    ],
    'memcached' => [
        'host' =>  '127.0.0.1',
        'port' =>  11211
    ],
    'db' => require(__DIR__.'/db.php'),
];