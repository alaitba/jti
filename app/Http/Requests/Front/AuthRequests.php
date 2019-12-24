<?php


namespace App\Http\Requests\Front;


class AuthRequests
{
    public const PHONE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/'
    ];
}
