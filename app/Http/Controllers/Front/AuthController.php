<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests;
use App\Models\Contact;
use App\Models\Partner;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * @param array $request
     * @param array $rules
     * @return array|bool|\Illuminate\Http\JsonResponse
     */
    private function validateRequest(array $request, array $rules)
    {
        $validator = Validator::make($request, $rules);
        if ($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'validation_failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
        }
        return true;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postPhone(Request $request)
    {
        /**
         * Mobile number validation
         */
        $validation = $this->validateRequest($request->only('mobile_phone'), AuthRequests::PHONE_REQUEST);
        if ($validation !== true)
        {
            return $validation;
        }

        /*
         * Check phone number in Partners
         */
        $partner = Partner::withoutTrashed()->where('mobile_phone', $request->input('mobile_phone'))->first();
        if (!$partner)
        {
            $contact = Contact::withoutTrashed()->select('mobile_phone')
                ->where('mobile_phone', $request->input('mobile_phone'))
                ->first();
            /**
             * No such phone at all
             */
            if (!$contact)
            {
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
        if ($partner->phone_verified_at && $partner->password)
        {
            return response()->json([
                'status' => 'ok',
                'message' => 'need_password',
                'phone' => $partner->mobile_phone
            ]);
        }

        /**
         * Generate and send sms code
         */
        $smsCode = rand(0, 1000);
        while (strlen($smsCode) < 4)
        {
            $smsCode = '0' . $smsCode;
        }

        $partner->sms_code = $smsCode;
        /**
         * Send sms if in production
         */
        $inProd = app()->environment() === 'production';
        if ($inProd)
        {
            try {
                JtiApiProvider::sendSms($partner->mobile_phone, $smsCode);
            } catch(\Exception $e){
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
            'sms_code_sent_at' => $partner->sms_code_sent_at
        ];
        /**
         * Return OTP if not in prod
         */
        if (!$inProd)
        {
            $responseData['sms_code'] = $partner->sms_code;
        }
        return response()->json($responseData);
    }

    /**
     * @param Request $request
     * @return array|bool|\Illuminate\Http\JsonResponse
     */
    public function postSmsCode(Request $request)
    {
        /**
         * Sms code validation
         */
        $validation = $this->validateRequest($request->only(['mobile_phone', 'sms_code']), AuthRequests::SMSCODE_REQUEST);
        if ($validation !== true)
        {
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
        if (!$partner)
        {
            return response()->json([
              'status' => 'error',
                'message' => 'sms_code_expired_or_invalid'
            ], 403);
        }

        $partner->update([
            'sms_code' => null,
            'sms_code_sent_at' => null,
            'phone_verified_at' => now()
        ]);
        return response()->json([
            'status' => 'ok',
            'message' => 'need_new_password'
        ]);
    }

    /**
     * @param Request $request
     * @return array|bool|\Illuminate\Http\JsonResponse
     */
    public function postCreatePassword(Request $request)
    {
        /**
         * Passwords validation
         */
        $validation = $this->validateRequest($request->only(['mobile_phone', 'password', 'password_check']), AuthRequests::CREATE_PASSWORD_REQUEST);
        if ($validation !== true)
        {
            return $validation;
        }

        /**
         * Check if password can be created
         */
        $partner = Partner::withoutTrashed()->where([
            ['mobile_phone', $request->input('mobile_phone')],
            ['phone_verified_at', '>=', Carbon::now()->subMinutes(config('project.create_password_lifetime', 2))],
            ['password', null]
        ])->first();
        if (!$partner)
        {
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        Auth::guard( 'partners' )->logout();
        return response()->json(['status' => 'ok']);
    }
}
