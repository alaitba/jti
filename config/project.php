<?php

return [
    'default_locale' => 'ru',
    'locales' => ['ru', 'kz'],
    'admin_prefix' => '',

    'views' => [
        'defaults' => [
            'admin_navigation' => 'common.navigation',
            'admin_header_nav' => 'common.header_nav'
        ]
    ],
    'sms_code_lifetime' => 3,
    'sms_code_resend_time' => 3,
    'create_password_lifetime' => 3,
    'failed_auth_block_time' => 10,

    'push_logo' => config('FRONT_URL', 'https://partner360.kz') . '/icons/logo.png',
    'front_url' => config('FRONT_URL', 'https://partner360.kz'),
];
