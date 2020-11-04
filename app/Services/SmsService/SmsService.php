<?php


namespace App\Services\SmsService;


use App\Models\CustomerPhoneVerification;
use App\Models\Partner;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

/**
 * Class SmsService
 * @package App\Services\SmsService
 */
class SmsService
{
    const DIGITS = 0;
    const STRING = 1;
    const TEST = 2;

    const SELLER = 0;
    const CUSTOMER = 1;

    /** @var CustomerPhoneVerification|Partner $smsableItem */
    private $smsableItem;
    private $codeLength = 4;
    private $codeType = self::DIGITS;
    private $code;
    private $userType = self::SELLER;
    private $userData = null;

    /**
     * SmsService constructor.
     * @param Partner|CustomerPhoneVerification|Model $smsableItem
     */
    public function __construct($smsableItem)
    {
        $this->smsableItem = $smsableItem;
    }

    /**
     * @param int $codeLength
     * @return SmsService
     */
    public function setCodeLength(int $codeLength): SmsService
    {
        $this->codeLength = $codeLength;
        return $this;
    }

    /**
     * @param int $codeType
     * @return SmsService
     */
    public function setCodeType(int $codeType): SmsService
    {
        $this->codeType = $codeType;
        return $this;
    }

    private function generateCode()
    {
        switch ($this->codeType)
        {
            case self::DIGITS:
                $this->code = '' . rand(0, pow(10, $this->codeLength) - 1);
                while (strlen($this->code) < $this->codeLength) {
                    $this->code = '0' . $this->code;
                }
                break;
            case self::STRING:
                $this->code = Str::random($this->codeLength);
                break;
            case self::TEST:
                $this->code = '7777';
                break;
            default:
                $this->code = '';
        }
        $this->smsableItem->sms_code = $this->code;
    }

    /**
     * @return bool|JsonResponse
     */
    public function checkLimit()
    {
        if (!$this->smsableItem->sms_code_sent_at)
        {
            return true;
        }
        $nextSend = Carbon::parse($this->smsableItem->sms_code_sent_at)->addMinutes(config('project.sms_code_resend_time', 2));
        if ($nextSend->isFuture())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'sms_send_limit',
                'can_send_at' => $nextSend
            ], 403);
        }
        return true;
    }

    /**
     * @param $legal_age
     * @return JsonResponse
     */
    public function sendSms($legal_age)
    {
        $this->generateCode();
        /**
         * Send sms if in production
         */
        $inProd = app()->environment() === 'production';
        if ($inProd && !$this->codeType == self::TEST) {
            try {
                $disclaimer = 'Ваш одноразовый пароль: ' . $this->code . ' Передавая пароль, Вы даёте ТОО «Джей Ти Ай Казахстан» согласие на получение Вами информации, призов, продукции и иного имущества, а также на сбор, обработку Ваших персональных данных. Полный текст согласия: http://terms.jti.kz:8020/?d=D002';

                $generatedCode = 'Код: ' . $this->code;

                if ($legal_age) {
                    $result = JtiApiProvider::sendSms('+' . $this->smsableItem->mobile_phone, $disclaimer, $this->userType)->getBody();
                } else {
                    $result = JtiApiProvider::sendSms('+' . $this->smsableItem->mobile_phone, $generatedCode, $this->userType)->getBody();
                }
                $result = json_decode($result, true);
                if (!$result['result'])
                {
                    LogService::logInfo($result);
                    return response()->json([
                        'status' => 'error',
                        'message' => 'sms_not_sent',
                        'crm_message' => $result['message']['messageText'] ?? ''
                    ], 403);
                }
            } catch (Exception $e) {
                LogService::logException($e);
                return response()->json([
                    'status' => 'error',
                    'message' => 'sms_not_sent'
                ], 500);
            }
        }
        $this->smsableItem->sms_code_sent_at = now();
        $this->smsableItem->save();
        $responseData = [
            'status' => 'ok',
            'message' => 'need_otp',
            'mobile_phone' => $this->smsableItem->mobile_phone,
            'sms_code_sent_at' => $this->smsableItem->sms_code_sent_at
        ];

        /**
         * Return customer data
         */
        if ($this->userType == self::CUSTOMER)
        {
            $responseData['client_data'] = $this->userData;
        }

        /**
         * Return OTP if not in prod
         */
        if (!$inProd) {
            $responseData['sms_code'] = $this->code;
        }

        if ($inProd && $this->smsableItem->mobile_phone == '+77777777771') {
            $responseData['sms_code'] = $this->code;
        }

        return response()->json($responseData);
    }

    /**
     * @param int $userType
     */
    public function setUserType(int $userType): void
    {
        $this->userType = $userType;
    }

    /**
     * @param null $userData
     */
    public function setUserData($userData): void
    {
        $this->userData = $userData;
    }
}
