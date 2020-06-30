<?php

return [
    'enabled' => env('RECAPTCHA_ENABLED', true),
    'key'     => env('INVISIBLE_RECAPTCHA_SITEKEY'),
    'secret'  => env('INVISIBLE_RECAPTCHA_SECRETKEY'),
];
