<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests;
use App\Models\Contact;
use App\Models\Partner;
use App\Models\TradePoint;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /**
     * @param array $request
     * @param array $rules
     * @return array|bool|JsonResponse
     */
    private function validateRequest(array $request, array $rules)
    {
        $validator = Validator::make($request, $rules);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'validation_failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
        return true;
    }

    /**
     * @param Partner $partner
     * @return JsonResponse
     */
    private function generateAndSendSms(Partner $partner)
    {
        if ($partner->sms_code_sent_at)
        {
            $nextSend = Carbon::parse($partner->sms_code_sent_at)->addMinutes(2);
            if ($nextSend->isFuture())
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'sms_send_limit',
                    'can_send_at' => $nextSend
                ], 403);
            }
        }
        $smsCode = rand(0, 9999);
        while (strlen($smsCode) < 4) {
            $smsCode = '0' . $smsCode;
        }
        $partner->sms_code = $smsCode;

        /**
         * Send sms if in production
         */
        $inProd = app()->environment() === 'production';
        if ($inProd) {
            try {
                JtiApiProvider::sendSms($partner->mobile_phone, $smsCode);
            } catch (Exception $e) {
                LogService::logException($e);
                return response()->json([
                    'status' => 'error',
                    'message' => 'sms_not_sent'
                ], 500);
            }
        }
        $partner->sms_code_sent_at = now();
        $partner->save();
        $responseData = [
            'status' => 'ok',
            'message' => 'need_otp',
            'mobile_phone' => $partner->mobile_phone,
            'sms_code_sent_at' => $partner->sms_code_sent_at
        ];
        /**
         * Return OTP if not in prod
         */
        if (!$inProd) {
            $responseData['sms_code'] = $partner->sms_code;
        }
        return response()->json($responseData);
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
        $validation = $this->validateRequest($request->only('mobile_phone'), AuthRequests::PHONE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check phone number in Partners
         */
        $partner = Partner::withoutTrashed()->where('mobile_phone', $request->input('mobile_phone'))->first();
        if (!$partner) {
            $contact = Contact::withoutTrashed()->select('mobile_phone')
                ->where('mobile_phone', $request->input('mobile_phone'))
                ->first();
            /**
             * No such phone at all
             */
            if (!$contact) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'phone_does_not_exist'
                ], 403);
            }
            /**
             * Create Partner
             */
            $partner = new Partner($contact->toArray());
            $partner->save();
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
     * @return array|bool|JsonResponse
     */
    public function postSmsCode(Request $request, bool $reset = false)
    {
        /**
         * Sms code validation
         */
        $validation = $this->validateRequest($request->only(['mobile_phone', 'sms_code']), AuthRequests::SMSCODE_REQUEST);
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
     * @return array|bool|JsonResponse
     */
    public function postCreatePassword(Request $request, bool $reset = false)
    {
        /**
         * Passwords validation
         */
        $validation = $this->validateRequest($request->only(['mobile_phone', 'password', 'password_check']), AuthRequests::CREATE_PASSWORD_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check if password can be created
         */
        $where = $reset ? [
            ['mobile_phone', $request->input('mobile_phone')],
            ['phone_verified_at', '!=', null],
            ['password', '!=', null]
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
        Auth::guard('partners')->login($partner);

        return response()->json([
            'status' => 'ok',
            'message' => 'authorized'
        ]);
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
        $validation = $this->validateRequest($credentials, AuthRequests::LOGIN_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        $partner = Partner::withoutTrashed()->where('mobile_phone', $credentials['mobile_phone'])->first();

        //Auth blocked
        if ($partner->auth_blocked_till && Carbon::parse($partner->auth_blocked_till)->isFuture())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'auth_blocked',
                'auth_blocked_till' => $partner->auth_blocked_till,
                'mobile_phone' => $partner->mobile_phone
            ]);
        }

        /**
         * Auth attempt
         */
        if (!Auth::guard('partners')->attempt($credentials)) {
            $partner->failed_auth++;
            if ($partner->failed_auth >= 5)
            {
                $partner->auth_blocked_till = Carbon::now()->addMinutes(30);
            }
            $partner->save();
            return response()->json([
                'status' => 'error',
                'message' => 'wrong_password'
            ], 403);
        }

        $partner->update(['failed_auth' => 0, 'auth_blocked_till' => null]);

        /**
         * Check tradepoints
         */
        $tradepoints = $partner->tradepointsArray();
        if (count($tradepoints) > 1)
        {
            return response()->json([
                'status' => 'ok',
                'message' => 'need_tradepoint',
                'tradepoints' => $tradepoints
            ]);

        }

        $partner->update(['current_tradepoint' => array_key_first($tradepoints)]);

        return response()->json([
            'status' => 'ok',
            'message' => 'authorized'
        ]);
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
        $validation = $this->validateRequest($request->only('mobile_phone'), AuthRequests::PHONE_REQUEST);
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
        $partner = Auth::guard('partners')->user();
        $accountCode = $request->input('account_code');
        $tradePoint = TradePoint::withoutTrashed()->where('account_code', $accountCode)->first();
        if (!$tradePoint || !in_array($accountCode, array_keys($partner->tradepointsArray())))
        {
            return response()->json([
                'status' => 'error',
                'message' => 'tradepoint_not_found_or_invalid'
            ], 403);
        }
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
        Auth::guard('partners')->logout();
        return response()->json(['status' => 'ok']);
    }

}
