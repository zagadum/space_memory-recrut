<?php


return [

    'driver' => env('MAIL_MAILER', 'smtp'),
    'host' => env('MAIL_HOST', 'mail.adm.tools'),
    'port' => env('MAIL_PORT', 2525),
    'from' => [
        'address' => env('MAIL_USERNAME', 'meet@soc.business'),
        'name' => env('MAIL_FROM_NAME', 'memoryForm'),
    ],
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username' => env('MAIL_USERNAME'),
    'password' => env('MAIL_PASSWORD'),
    'sendmail' => '/usr/sbin/sendmail -bs',
    'pretend' => false,
    'markdown' => [
        'theme' => 'default',
        'paths' => [
            resource_path('views/vendor/mail'),
        ],
    ],
    'log' => true,
    'stream' => [
        'ssl' => [
            'allow_self_signed' => true,
            'verify_peer' => false,
            'verify_peer_name' => false,
        ],
    ]
];

