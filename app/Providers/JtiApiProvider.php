<?php


namespace App\Providers;


use App\Services\LogService\LogService;
use GuzzleHttp\Client;

class JtiApiProvider
{
    private const USER = 'P360Test';
    private const PASS = '123';
    private const BASE_URI = 'http://crmservices.jti.kz:8081/';

    private const SMS_URI = 'api/Contact/Send360Sms';

    private static function makeUrl(string $uri): string
    {
        return self::BASE_URI . $uri;
    }

    /**
     * @param string $url
     * @param null $body
     * @param string $method
     */
    private static function executeQuery(string $url, $body = null, $method = 'POST')
    {
        (new Client())->request(
            $method,
            $url,
            [
                'auth' => [self::USER, self::PASS],
                'json' => $body
            ]
        );
    }

    /**
     * @param string $phone
     * @param string $text
     */
    public static function sendSms(string $phone, string $text)
    {
        $body = [
            'data' => [
                'mobilePhone' => $phone,
                'smsText' => $text
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        self::executeQuery(self::makeUrl(self::SMS_URI), $body);
    }
}
