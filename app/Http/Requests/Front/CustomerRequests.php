<?php


namespace App\Http\Requests\Front;


class CustomerRequests
{
    public const PHONE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'legal_age' => 'required|accepted'
    ];

    public const SMSCODE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'sms_code' => 'required|regex:/[0-9]{4}/'
    ];

    public const CREATELEAD_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'firstname' => 'required_unless:self,1|string',
        'lastname' => 'required_unless:self,1|string',
        'birthdate' => 'required_unless:self,1|date',
        'product_code' => 'required_unless:self,1|string',
        'signature' => 'required_unless:self,1|base64image'
    ];
}
