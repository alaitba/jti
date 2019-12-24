<?php

namespace App\Http\Controllers\Front;


use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests;
use App\Models\Contact;
use App\Models\Partner;
use App\Services\LogService\LogService;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function postPhone(Request $request)
    {
        /**
         * Mobile number validation
         */
        $validator = Validator::make($request->only('mobile_phone'), AuthRequests::PHONE_REQUEST);
        if ($validator->fails())
        {
            return response()->json([
                'status' => 'error',
                'message' => 'validation_failed',
                'errors' => $validator->errors()->toArray()
            ], 422);
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
        if ($partner->phone_verified_at)
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
        try {
            $client = new Client();
            $response = $client->request(
                'POST',
                config('jti_api.sms_url'),
                [
                    'body' => json_encode([
                        'data' => [
                            'mobilePhone' => $partner->mobile_phone,
                            'smsText' => $smsCode
                        ],
                        'identity' => [
                            'userName' => '',
                            'locale' => ''
                        ]
                    ], true)
                ]
            );
        } catch(\Exception $e){
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'sms_not_sent'
            ], 500);
        }
        $partner->sms_code_sent_at = now();
        $partner->save();
        return response()->json([
            'status' => 'ok',
            'message' => 'need_otp',
            'sms_code_sent_at' => $partner->sms_code_sent_at
        ]);
    }

    public function logout() {
        Auth::guard( 'partners' )->logout();
        return response()->json('ok');
    }
}
