<?php


namespace App\Providers;


use App\Models\Partner;
use App\Models\TobaccoProduct;
use App\Services\SmsService\SmsService;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

/**
 * Class JtiApiProvider
 * @package App\Providers
 */
class JtiApiProvider
{
    /**
     * crm
     */
    private const USER = 'P360Test';
    private const PASS = '123';
    private const BASE_URI = 'http://crmservices.jti.kz:8081/api/';

    private const USER_PROD = 'Partner360User';
    private const PASS_PROD = 'u31Km7P>5Qe%';
    private const BASE_URI_PROD = 'https://crmservices.jti.kz:8444/api/';

    private const SMS_URI = 'Contact/Send360Sms';
    private const SMS_SELLER_URI = 'Seller/Send360Sms';
    private const CHECK_CONSUMER_URI = 'Contact/CheckContact';
    private const CREATE_LEAD_URI = 'Contact/Create360Lead';
    private const LEAD_HISTORY_URI = 'Seller/GetSellerLeadsHistory';
    private const GET_DICTIONARY_URI = 'Dictionary/Get';
    private const GET_BALANCE_URI = 'Seller/GetSellerPointAmount';
    private const GET_AVAILABLE_REWARDS_URI = 'Seller/GetSellerAvailableRewards';
    private const CREATE_REWARD_URI = 'Seller/CreateReward';
    private const CREATE_MONEY_REWARD_URI = 'Seller/CreateMoneyReward';
    private const REWARDS_HISTORY_URI = 'Seller/GetSellerRewardsHistory';

    /**
     * oos
     */

    private const OOS_URI = 'https://oos.jti.kz/api/';
    private const OOS_USER = 'partner360';
    private const OOS_PASS = 'cddb57047b352b8372c04c2a1600923c';


    /**
     * @param string $uri
     * @return string
     */
    private static function makeUrl(string $uri): string
    {
        return (app()->environment() === 'production' ? self::BASE_URI_PROD : self::BASE_URI) . $uri;
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
                'auth' => app()->environment() === 'production' ? [self::USER_PROD, self::PASS_PROD] : [self::USER, self::PASS],
                'json' => $body
            ]
        );
    }

    /**
     * @param string $phone
     * @param string $text
     * @param int $userType
     * @return ResponseInterface
     */
    public static function sendSms(string $phone, string $text, int $userType)
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
        return self::executeQuery(self::makeUrl($userType === SmsService::SELLER ? self::SMS_SELLER_URI : self::SMS_URI), $body);
    }

    /**
     * @param string $phone
     * @param string $sellerId
     * @return ResponseInterface
     */
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

    /**
     * @param array $data
     * @return ResponseInterface
     */
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

    /**
     * @param string $sellerId
     * @param int $perPage
     * @param int $page
     * @return ResponseInterface
     */
    public static function getLeadHistory(string $sellerId, int $perPage, int $page)
    {
        $body = [
            'data' => [
                'key' => $sellerId,
                'recordsPerPage' => $perPage,
                'pageNumber' => $page
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::LEAD_HISTORY_URI), $body);
    }

    /**
     * @param string $type
     * @return ResponseInterface
     */
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

    /**
     * @param string $sellerId
     * @return ResponseInterface
     */
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

    /**
     * @param string $sellerId
     * @return ResponseInterface
     */
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

    /**
     * @param string $sellerId
     * @param string $rewardId
     * @return ResponseInterface
     */
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

    /**
     * @param string $sellerId
     * @param int $amount
     * @param int $quizResultId
     * @return ResponseInterface
     */
    public static function createMoneyReward(string $sellerId, int $amount, int $quizResultId)
    {
        $body = [
            'data' => [
                'sellerId' => $sellerId,
                'amount' => $amount,
                'internalId' => $quizResultId
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::CREATE_MONEY_REWARD_URI), $body);
    }

    /**
     * @param string $sellerId
     * @param int $perPage
     * @param int $page
     * @return ResponseInterface
     */
    public static function getRewardsHistory(string $sellerId, int $perPage, int $page)
    {
        $body = [
            'data' => [
                'key' => $sellerId,
                'recordsPerPage' => $perPage,
                'pageNumber' => $page
            ],
            'identity' => [
                'locale' => 'ru-RU'
            ]
        ];
        return self::executeQuery(self::makeUrl(self::REWARDS_HISTORY_URI), $body);
    }

    public static function ordersOos()
    {
        $token = self::authOos();
        if (!$token)
        {
            return false;
        }

        /** @var Partner $partner */
        $partner = auth('partners')->user();
        $result = self::executeOosQuery(self::OOS_URI . 'orders', [
            'token' => $token,
            'orders' => [
                [
                    'shop_code' => $partner->current_tradepoint,
                    'seller_code' => $partner->current_uid,
                    'products' => [
                        [
                            'product_code' => TobaccoProduct::withoutTrashed()->first()->product_code ?? '',
                            'volume' => 1
                        ]
                    ]
                ]
            ]
        ]);
        return $result['success'];
    }


    /**
     * @param string $url
     * @param null $body
     * @param string $method
     * @return StreamInterface
     */
    private static function executeOosQuery(string $url, $body = null, $method = 'POST')
    {
        $result = (new Client())->request(
            $method,
            $url,
            [
                'json' => $body
            ]
        )->getBody();
        return json_decode($result, true);
    }

    /**
     * @return string|bool
     */
    private static function authOos()
    {
        $result = self::executeOosQuery(self::OOS_URI . 'authenticate', ['login' => self::OOS_USER, 'psw' => self::OOS_PASS]);
        return $result['token'] ?? false;
    }
}
