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
    private const CREATE_LEAD_URI = 'Contact/Create360Lead';
    private const LEAD_HISTORY_URI = 'Seller/GetSellerLeadsHistory';
    private const GET_DICTIONARY_URI = 'Dictionary/Get';
    private const GET_BALANCE_URI = 'Seller/GetSellerPointAmount';
    private const GET_AVAILABLE_REWARDS_URI = 'Seller/GetSellerAvailableRewards';
    private const CREATE_REWARD_URI = 'Seller/CreateReward';

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

    public static function createLead(array $data)
    {
        $body = [
            'data' => $data,
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::CREATE_LEAD_URI), $body);
    }

    public static function getLeadHistory(string $sellerId, int $perPage, int $page)
    {
        $body = [
            'data' => [
                'key' => $sellerId,
                'recordsPerPage' => $perPage,
                'page' => $page
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::LEAD_HISTORY_URI), $body);
    }

    public static function getDictionary(string $type)
    {
        $body = [
            'data' => $type,
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::GET_DICTIONARY_URI), $body);
    }

    public static function getBalance(string $sellerId)
    {
        $body = [
            'data' => $sellerId,
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::GET_BALANCE_URI), $body);
    }

    public static function getAvailableRewards(string $sellerId)
    {
        $body = [
            'data' => $sellerId,
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::GET_AVAILABLE_REWARDS_URI), $body);
    }

    public static function createReward(string $sellerId, string $rewardId)
    {
        $body = [
            'data' => [
                'sellerId' => $sellerId,
                'rewardId' => $rewardId
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::CREATE_REWARD_URI), $body);
    }

}
