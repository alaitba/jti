<?php


namespace App\Http\Requests\Front;


/**
 * Class CustomerRequests
 * @package App\Http\Requests\Front
 */
class CustomerRequests
{

    public const PHONE_REQUEST = [
        'mobile_phone' => 'required|regex:/7[0-9]{10}/',
        'legal_age' => 'accepted'
    ];

    public const SMSCODE_REQUEST = [
        'mobile_phone' => 'required|regex:/7[0-9]{10}/',
        'sms_code' => 'required|regex:/[0-9]{4}/'
    ];

    public const CREATELEAD_REQUEST = [
        'mobile_phone' => 'required|regex:/7[0-9]{10}/',
        'firstname' => 'required_unless:self,1|string',
        'lastname' => 'required_unless:self,1|string',
        'birthdate' => 'required_unless:self,1|date',
        'product_code' => 'required_unless:self,1|string',
    ];

}
