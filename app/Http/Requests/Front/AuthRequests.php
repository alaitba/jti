<?php


namespace App\Http\Requests\Front;


class AuthRequests
{
    public const PHONE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/'
    ];

    public const SMSCODE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'sms_code' => 'required|regex:/[0-9]{4}/'
    ];
}
