<?php

return [
    'default' => env('SMS_DRIVER', 'mts'),
    'connections' => [
        'mts' => [
            'driver' => 'mts',
            'login' => env('MTS_SMS_LOGIN'),
            'password' => env('MTS_SMS_PASSWORD'),
            'user_group' => null
        ],
        'smsaero' => [
            'driver' => 'smsaero',
            'login' => env('SMSAERO_SMS_LOGIN'),
            'password' => env('SMSAERO_SMS_PASSWORD'),
            'default_sign' => ''
        ]
    ]
];