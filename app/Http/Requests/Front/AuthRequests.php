<?php


namespace App\Http\Requests\Front;


/**
 * Class AuthRequests
 * @package App\Http\Requests\Front
 */
class AuthRequests
{
    public const PHONE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'captcha' => 'required|captcha',
    ];

    public const SMSCODE_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'sms_code' => 'required|regex:/[0-9]{4}/'
    ];

    public const CREATE_PASSWORD_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        //'password' => 'required|regex:/.*^(?=.{6,})(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*\W).*$/',
        'password' => 'required|regex:/[\x20-\x7F]{6,}/',
        'password_check' => 'required|same:password'
    ];

    public const LOGIN_REQUEST = [
        'mobile_phone' => 'required|regex:/77[0-9]{9}/',
        'password' => 'required',
    ];

}
