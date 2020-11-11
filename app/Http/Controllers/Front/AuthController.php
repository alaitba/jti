<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\PartnerAuth;
use App\Models\TradePointContact;
use App\Services\SmsService\SmsService;
use App\Services\ValidatorService\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Browser;

/**
 * Class AuthController
 * @package App\Http\Controllers\Front
 */
class AuthController extends Controller
{

    /**
     * @param Partner $partner
     * @return JsonResponse
     */
    private function generateAndSendSms(Partner $partner)
    {
        $smsService = new SmsService($partner);

        $canSend = $smsService->checkLimit();
        if ($canSend !== true)
        {
            return $canSend;
        }

        return $smsService->sendSms(false);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postPhone(Request $request)
    {
        /**
         * Mobile number validation
         */
        $validation = ValidatorService::validateRequest($request->only('mobile_phone'), AuthRequests::PHONE_REQUEST);

        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check phone number in Partners
         */
        $mobilePhone = trim($request->input('mobile_phone'), '+');
        $partner = Partner::withoutTrashed()->where('mobile_phone', $mobilePhone)->first();
        if (!$partner) {
            $contact = Contact::withoutTrashed()->withCount('tradepoint')
                ->where('mobile_phone', $mobilePhone)
                ->first();
            /**
             * No such phone at all or no tradepoint
             */
            if (!$contact || !$contact->tradepoint_count) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'phone_does_not_exist'
                ], 403);
            }

            /**
             * Create Partner
             */
            $partner = new Partner($contact->only(['mobile_phone']));
            $partner->save();
        }

        /**
         * Fix for already registered w/o tradepoint
         */
        if (count($partner->tradepointsArray()) == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'phone_does_not_exist'
            ], 403);
        }

        /**
         * Check if mobile verified
         */
        if ($partner->phone_verified_at && $partner->password) {

            //Auth blocked
            if ($partner->auth_blocked_till && Carbon::parse($partner->auth_blocked_till)->isFuture())
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'auth_blocked',
                    'auth_blocked_till' => $partner->auth_blocked_till,
                    'auth_fail_count' => $partner->failed_auth,
                    'mobile_phone' => $partner->mobile_phone
                ], 403);
            }

            return response()->json([
                'status' => 'ok',
                'message' => 'need_password'
            ]);
        }

        /**
         * Generate and send sms code
         */
        return $this->generateAndSendSms($partner);
    }

    /**
     * @param Request $request
     * @param bool $reset
     * @return array|bool|JsonResponse
     */
    public function postSmsCode(Request $request, bool $reset = false)
    {
        /**
         * Sms code validation
         */
        $validation = ValidatorService::validateRequest($request->only(['mobile_phone', 'sms_code']), AuthRequests::SMSCODE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check if code exists, correct and not expired
         */
        $partner = Partner::withoutTrashed()->where([
            ['mobile_phone', trim($request->input('mobile_phone'), '+')],
            ['sms_code', $request->input('sms_code')],
            ['sms_code_sent_at', '>=', Carbon::now()->subMinutes(config('project.sms_code_lifetime', 2))]
        ])->first();

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'sms_code_expired_or_invalid'
            ], 403);
        }

        $params = [
            'sms_code' => null,
            'sms_code_sent_at' => null,
        ];

        if (!$reset)
        {
            $params['phone_verified_at'] = now();
        }

        $partner->update($params);

        return response()->json([
            'status' => 'ok',
            'message' => 'need_new_password',
            'mobile_phone' => $partner->mobile_phone
        ]);
    }

    /**
     * @param Request $request
     * @param bool $reset
     * @return array|bool|JsonResponse
     */
    public function postCreatePassword(Request $request, bool $reset = true)
    {
        /**
         * Passwords validation
         */
        $validation = ValidatorService::validateRequest($request->only(['mobile_phone', 'password', 'password_check']), AuthRequests::CREATE_PASSWORD_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check if password can be created
         */
        $where = $reset ? [
            ['mobile_phone', trim($request->input('mobile_phone'), '+')],
            ['phone_verified_at', '!=', null]
        ] : [
            ['mobile_phone', trim($request->input('mobile_phone'), '+')],
            ['phone_verified_at', '>=', Carbon::now()->subMinutes(config('project.create_password_lifetime', 10))],
            ['password', null]
        ];
        $partner = Partner::withoutTrashed()->where($where)->first();
        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'password_creation_expired_or_not_allowed'
            ], 403);
        }

        $locale = $request->input('locale', 'ru');
        if ($locale == 'kk')
        {
            $locale = 'kz';
        }
        if (!in_array($locale, config('project.locales', ['ru', 'kz'])))
        {
            $locale = 'ru';
        }
        /**
         * Set password and authorize
         */
        $partner->update([
            'password' => $request->input('password'),
            'platform' => Browser::platformFamily(),
            'locale' => $locale
        ]);
        $token = auth('partners')->login($partner);

        return $this->setTradePoint($partner, $token);
    }

    /**
     * @param Request $request
     * @return array|bool|JsonResponse
     */
    public function postLogin(Request $request)
    {
        $credentials = $request->only(['mobile_phone', 'password']);

        $credentials['mobile_phone'] = trim($credentials['mobile_phone'], '+');
        /**
         * Auth validation
         */
        $validation = ValidatorService::validateRequest($credentials, AuthRequests::LOGIN_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        $partner = Partner::withoutTrashed()->where('mobile_phone', $credentials['mobile_phone'])->first();
        if (!$partner)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'phone_does_not_exist'
            ], 403);
        }
        //Auth blocked
        if ($partner->auth_blocked_till && Carbon::parse($partner->auth_blocked_till)->isFuture())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'auth_blocked',
                'auth_blocked_till' => $partner->auth_blocked_till,
                'auth_fail_count' => $partner->failed_auth,
                'mobile_phone' => $partner->mobile_phone
            ]);
        }

        /**
         * Auth attempt
         */
        if (!$token = auth('partners')->attempt($credentials)) {
            $partner->failed_auth++;
            if ($partner->failed_auth >= 5)
            {
                $partner->auth_blocked_till = Carbon::now()->addMinutes(config('project.failed_auth_block_time', 10));
            }
            $partner->save();

            Log::channel('logstash')->info('Неверный пароль. Количество попыток: ' . $partner->failed_auth .
                ' IP: ' . $_SERVER['REMOTE_ADDR'] .
                ' Номер телефона: ' . $partner->mobile_phone);

            return response()->json([
                'status' => 'error',
                'message' => 'wrong_password',
                'auth_fail_count' => $partner->failed_auth,
            ], 403);
        }

        $locale = $request->input('locale', 'ru');
        if ($locale == 'kk')
        {
            $locale = 'kz';
        }
        if (!in_array($locale, config('project.locales', ['ru', 'kz'])))
        {
            $locale = 'ru';
        }
        $partner->update([
            'failed_auth' => 0,
            'auth_blocked_till' => null,
            'platform' => Browser::platformFamily(),
            'locale' => $locale
        ]);

        return $this->setTradePoint($partner, $token);
    }

    /**
     * @param Request $request
     * @return array|bool|JsonResponse
     */
    public function postPhoneReset(Request $request)
    {
        /**
         * Mobile number validation
         */
        $validation = ValidatorService::validateRequest($request->only('mobile_phone'), AuthRequests::PHONE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }
        /**
         * Check phone number in Partners
         */
        $partner = Partner::withoutTrashed()->where([
            ['mobile_phone', trim($request->input('mobile_phone'), '+')],
            ['password', '!=', null],
            ['phone_verified_at', '!=', null]
        ])->first();

        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'phone_does_not_exist_or_not_verified'
            ], 403);
        }

        //Auth blocked
        if ($partner->auth_blocked_till && Carbon::parse($partner->auth_blocked_till)->isFuture())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'auth_blocked',
                'auth_blocked_till' => $partner->auth_blocked_till,
                'auth_fail_count' => $partner->failed_auth,
                'mobile_phone' => $partner->mobile_phone
            ]);
        }

        return $this->generateAndSendSms($partner);
    }

    /**
     * @param Request $request
     * @return array|bool|JsonResponse
     */
    public function postSmsCodeReset(Request $request)
    {
        return $this->postSmsCode($request, true);
    }

    /**
     * @param Request $request
     * @return array|bool|JsonResponse
     */
    public function postCreatePasswordReset(Request $request)
    {
        return $this->postCreatePassword($request, true);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function postSetTradepoint(Request $request)
    {
        $invalidPoint = response()->json([
            'status' => 'error',
            'message' => 'tradepoint_not_found_or_invalid'
        ], 403);

        /** @var Partner $partner */
        $partner = auth('partners')->user();
        $accountCode = $request->input('account_code');
        $tradePoints = $partner->tradepointsArray();
        if (!isset($tradePoints[$accountCode]))
        {
            return $invalidPoint;
        }
        $tradePointContact = TradePointContact::withoutTrashed()
            ->where('account_code', $accountCode)
            ->where('contact_uid', $tradePoints[$accountCode]['contact_uid'])
            ->first();
        if (!$tradePointContact)
        {
            return $invalidPoint;
        }

        $partner->current_tradepoint = $accountCode;
        $partner->current_uid = $tradePointContact->contact_uid;
        $partner->save();
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        $this->storeLogin($partner->id, $accountCode, $tradePointContact->contact_uid, $ipAddress);

        return response()->json([
            'status' => 'ok',
            'message' => 'tradepoint_set',
            'tradepoint' => $tradePoints[$accountCode],
            'account' => $partner->current_contact->only(['first_name', 'last_name', 'middle_name', 'mobile_phone']) ?? [],
            'tradeagent' => $partner->current_contact->tradepoint->trade_agent->only(['employee_name', 'phone']) ?? [],
            'ip' => $ipAddress,
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function logout()
    {
        auth('partners')->logout();
        return response()->json(['status' => 'ok']);
    }


    /**
     * Refresh a token.
     *
     * @return JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('partners')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'token' => $token,
            'token_ttl' => auth('partners')->factory()->getTTL() * 60
        ]);
    }

    /**
     * @param Partner $partner
     * @param $token
     * @return JsonResponse
     */
    private function setTradePoint(Partner $partner, $token)
    {
        /**
         * Check tradepoints
         */
        $tradepoints = $partner->tradepointsArray();
        if (count($tradepoints) == 0)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'no_tradepoint',
            ], 403);
        }
        if (count($tradepoints) > 1)
        {
            return response()->json([
                'status' => 'ok',
                'token' => $token,
                'token_ttl' => auth('partners')->factory()->getTTL() * 60,
                'message' => 'need_tradepoint',
                'tradepoints' => array_values($tradepoints)
            ]);
        }
        $tpAcc = array_key_first($tradepoints);
        $ipAddress = $_SERVER['REMOTE_ADDR'];
        $partner->update(['current_tradepoint' => $tpAcc, 'current_uid' => $tradepoints[$tpAcc]['contact_uid']]);

        $this->storeLogin($partner->id, $tpAcc, $tradepoints[$tpAcc]['contact_uid'], $ipAddress);

        return response()->json([
            'status' => 'ok',
            'message' => 'authorized',
            'tradepoint' => array_pop($tradepoints),
            'account' => $partner->current_contact->only(['first_name', 'last_name', 'middle_name', 'mobile_phone']) ?? [],
            'tradeagent' => $partner->current_contact->tradepoint->trade_agent->only(['employee_name', 'phone']) ?? [],
            'token' => $token,
            'token_ttl' => auth('partners')->factory()->getTTL() * 60
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setPushToken(Request $request)
    {
        /** @var Partner $partner */
        $partner = auth('partners')->user();
        $partner->onesignal_token = $request->input('push_token', null);
        $partner->save();
        return response()->json(['status' => 'ok']);
    }

    /**
     * @param $id
     * @param $tpAcc
     */
    private function storeLogin($id, $tpAcc, $uid, $ipAddress)
    {
        $currentTime = now();
        PartnerAuth::query()->updateOrCreate(['partner_id' => $id, 'account_code' => $tpAcc, 'contact_uid' => $uid],
            ['login' => $currentTime, 'last_seen' => $currentTime, 'os' => Browser::platformName(), 'ip' => $ipAddress]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function setLocale(Request $request)
    {
        $locale = $request->input('locale', 'ru');
        if ($locale == 'kk')
        {
            $locale = 'kz';
        }
        if (!in_array($locale, config('project.locales', ['ru', 'kz'])))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid_locale',
            ], 403);
        }
        /** @var Partner $partner */
        $partner = auth('partners')->user();
        $partner->locale = $locale;
        $partner->save();
        return response()->json(['status' => 'ok']);
    }

}
