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

    private $smsableItem;
    private $codeLength = 4;
    private $codeType = self::DIGITS;
    private $code;
    private $userType = self::SELLER;

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
     * @return JsonResponse
     */
    public function sendSms()
    {
        $this->generateCode();
        /**
         * Send sms if in production
         */
        $inProd = app()->environment() === 'production';
        if ($inProd && !$this->codeType == self::TEST) {
            try {
                $result = JtiApiProvider::sendSms($this->smsableItem->mobile_phone, $this->code, $this->userType)->getBody();
                $result = json_decode($result, true);
                if (!$result['result'])
                {
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
         * Return OTP if not in prod
         */
        if (!$inProd) {
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
}
