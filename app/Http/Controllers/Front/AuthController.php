<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\TradePointContact;
use App\Services\SmsService\SmsService;
use App\Services\ValidatorService\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return $smsService->sendSms();
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
        $mobilePhone = $request->input('mobile_phone');
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
                'message' => 'need_password',
                'mobile_phone' => $partner->mobile_phone
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
            ['mobile_phone', $request->input('mobile_phone')],
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
    public function postCreatePassword(Request $request, bool $reset = false)
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
            ['mobile_phone', $request->input('mobile_phone')],
            ['phone_verified_at', '!=', null]
        ] : [
            ['mobile_phone', $request->input('mobile_phone')],
            ['phone_verified_at', '>=', Carbon::now()->subMinutes(config('project.create_password_lifetime', 2))],
            ['password', null]
        ];
        $partner = Partner::withoutTrashed()->where($where)->first();
        if (!$partner) {
            return response()->json([
                'status' => 'error',
                'message' => 'password_creation_expired_or_not_allowed'
            ], 403);
        }

        /**
         * Set password and authorize
         */
        $partner->update([
            'password' => $request->input('password'),
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
                $partner->auth_blocked_till = Carbon::now()->addMinutes(30);
            }
            $partner->save();
            return response()->json([
                'status' => 'error',
                'message' => 'wrong_password',
                'auth_fail_count' => $partner->failed_auth,
            ], 403);
        }

        $partner->update(['failed_auth' => 0, 'auth_blocked_till' => null]);

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
            ['mobile_phone', $request->input('mobile_phone')],
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
        $partner = auth('partners')->user();
        $accountCode = $request->input('account_code');
        $tradePointContact = TradePointContact::withoutTrashed()->where('account_code', $accountCode)->first();
        if (!$tradePointContact || !in_array($accountCode, array_keys($partner->tradepointsArray())))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'tradepoint_not_found_or_invalid'
            ], 403);
        }

        $user = auth('partners')->user();
        $user->current_tradepoint = $accountCode;
        $user->current_uid = $tradePointContact->contact_uid;
        $user->save();

        return response()->json([
            'status' => 'ok',
            'message' => 'tradepoint_set'
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
                'tradepoints' => $tradepoints
            ]);
        }
        $tpAcc = array_key_first($tradepoints);
        $partner->update(['current_tradepoint' => $tpAcc, 'current_uid' => $tradepoints[$tpAcc]['contact_uid']]);

        return response()->json([
            'status' => 'ok',
            'message' => 'authorized',
            'tradepoint' => array_pop($tradepoints),
            'token' => $token,
            'token_ttl' => auth('partners')->factory()->getTTL() * 60
        ]);
    }
}
