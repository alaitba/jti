<?php

return [
    'default_locale' => 'ru',
    'locales' => ['ru', 'kz'],
    'admin_prefix' => 'admin',

    'views' => [
        'defaults' => [
            'admin_navigation' => 'common.navigation',
            'admin_header_nav' => 'common.header_nav'
        ]
    ],
    'sms_code_lifetime' => 2,
    'sms_code_resend_time' => 2,
    'create_password_lifetime' => 2,
];
