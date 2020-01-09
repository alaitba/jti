<?php


namespace App\Providers;


use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class JtiApiProvider
{
    private const USER = 'P360Test';
    private const PASS = '123';
    private const BASE_URI = 'http://crmservices.jti.kz:8081/api/';

    private const SMS_URI = 'Contact/Send360Sms';
    private const CHECK_CONSUMER_URI = 'Contact/CheckContact';

    private static function makeUrl(string $uri): string
    {
        return self::BASE_URI . $uri;
    }

    /**
     * @param string $url
     * @param null $body
     * @param string $method
     * @return ResponseInterface
     */
    private static function executeQuery(string $url, $body = null, $method = 'POST')
    {
        return (new Client())->request(
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

    public static function checkConsumer(string $phone, string $sellerId)
    {
        $body = [
            'data' => [
                'mobilePhone' => $phone,
                'sellerId' => $sellerId
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::CHECK_CONSUMER_URI), $body);
    }
}
