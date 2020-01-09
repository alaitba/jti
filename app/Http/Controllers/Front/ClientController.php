<?php

namespace App\Http\Controllers\Front;

use App\Models\Contact;
use App\Models\CustomerPhoneVerification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CustomerRequests;
use App\Models\TradePointContact;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use App\Services\SmsService\SmsService;
use App\Services\ValidatorService\ValidatorService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ClientController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendSMS(Request $request)
    {
        $mobilePhone = $request->input('mobile_phone');
        $validation = ValidatorService::validateRequest($request->only('mobile_phone'), CustomerRequests::PHONE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * А можно ли ему заполнять анкету?
         */

        try {
            $tradePointContact = TradePointContact::withoutTrashed()
                ->where('account_code', auth('partners')->user()->current_tradepoint)
                ->first();
            if (!$tradePointContact)
            {
                return response()->json(['status' => 'error', 'message' => 'tradepoint_not_set'], 403);
            }
            $result = JtiApiProvider::checkConsumer('+' . $mobilePhone, $tradePointContact->contact_uid)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'already_filled'
                ], 403);
            }
        } catch (Exception $e) {
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'phone_not_checked'
            ], 500);
        }


        $customerPhoneVerification = CustomerPhoneVerification::firstOrCreate(['mobile_phone' => $mobilePhone]);

        $smsService = new SmsService($customerPhoneVerification);

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
    public function checkSms(Request $request)
    {
        $validation = ValidatorService::validateRequest($request->only(['mobile_phone', 'sms_code']), CustomerRequests::SMSCODE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check if code exists, correct and not expired
         */
        $customerPhoneVerification = CustomerPhoneVerification::where([
            ['mobile_phone', $request->input('mobile_phone')],
            ['sms_code', $request->input('sms_code')],
            ['sms_code_sent_at', '>=', Carbon::now()->subMinutes(config('project.sms_code_lifetime', 2))]
        ])->first();

        if (!$customerPhoneVerification) {
            return response()->json([
                'status' => 'error',
                'message' => 'sms_code_expired_or_invalid'
            ], 403);
        }

        $customerPhoneVerification->status = true;
        $customerPhoneVerification->save();

        return response()->json([
            'status' => 'ok',
            'mobile_phone' => $customerPhoneVerification->mobile_phone
        ]);
    }

    public function createLead(Request $request)
    {
        $validation = ValidatorService::validateRequest($request->only([
            'mobile_phone',
            'firstname',
            'lastname',
            'birthdate',
            'product_code',
            'signature'
        ]), CustomerRequests::CREATELEAD_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check if mobile verified
         */
        $verified = CustomerPhoneVerification::where(['mobile_phone' => $request->input('mobile_phone'), 'status' => true])->first();
        if (!$verified)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'mobile_phone_not_verified'
            ]);
        }

        /**
         * Post to JTI
         */
        try {
            $tradePointContact = TradePointContact::withoutTrashed()
                ->where('account_code', auth('partners')->user()->current_tradepoint)
                ->first();
            if (!$tradePointContact)
            {
                return response()->json(['status' => 'error', 'message' => 'tradepoint_not_set'], 403);
            }
            $data = [
                'isMobilePhoneVerified' => true,
                'verificationCode' => $verified->sms_code,
                'confirmationCode' => $verified->sms_code,
                'birthDate' => Carbon::parse($request->input('birthdate'))->toISOString(),
                'regularProductCode' => $request->input('product_code'),
                'sellerId' => $tradePointContact->contact_code,
                'annotation' => $request->input('signature'),
                'mobilePhone' => '+' . $request->input('mobile_phone'),
                'firstName' => $request->input('firstname'),
                'lastName' => $request->input('lastname'),
                'fillingDate' => Carbon::now()->toISOString(),
                'internalId' => Str::random()
            ];
            $result = JtiApiProvider::createLead($data)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'already_filled'
                ], 403);
            }
            return response()->json([
                'status' => 'ok',
                'message' => 'created'
            ]);
        } catch (Exception $e) {
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'not_created'
            ], 500);
        }
    }
}
