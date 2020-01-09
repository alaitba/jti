<?php


namespace App\Http\Requests\Front;


class CustomerRequests
{
    public const PHONE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/'
    ];

    public const SMSCODE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'sms_code' => 'required|regex:/[0-9]{4}/'
    ];

    public const CREATELEAD_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'firstname' => 'required|string',
        'lastname' => 'required|string',
        'birthdate' => 'required|date',
        'product_code' => 'required|string',
        'signature' => 'required|base64image'
    ];
}