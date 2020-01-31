<?php

namespace App\Http\Controllers\Front;

use App\Models\CustomerPhoneVerification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CustomerRequests;
use App\Providers\JtiApiProvider;
use App\Services\LogService\LogService;
use App\Services\SmsService\SmsService;
use App\Services\ValidatorService\ValidatorService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Class ClientController
 * @package App\Http\Controllers\Front
 */
class ClientController extends Controller
{

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function sendSMS(Request $request)
    {
        $mobilePhone = $request->input('mobile_phone');
        $validation = ValidatorService::validateRequest($request->only(['mobile_phone', 'legal_age']), CustomerRequests::PHONE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * А можно ли ему заполнять анкету?
         */

        try {
            $result = JtiApiProvider::checkConsumer($mobilePhone, auth('partners')->user()->current_uid)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                LogService::logInfo($result);
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


        $customerPhoneVerification = CustomerPhoneVerification::query()->firstOrCreate(['mobile_phone' => $mobilePhone]);

        $smsService = new SmsService($customerPhoneVerification);

        $canSend = $smsService->checkLimit();
        if ($canSend !== true)
        {
            return $canSend;
        }

        $smsService->setUserType(SmsService::CUSTOMER);

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
        $customerPhoneVerification = CustomerPhoneVerification::query()->where([
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createLead(Request $request)
    {
        $validation = ValidatorService::validateRequest($request->only([
            'mobile_phone',
            'firstname',
            'lastname',
            'birthdate',
            'product_code',
            'signature',
            'self'
        ]), CustomerRequests::CREATELEAD_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        /**
         * Check if mobile verified
         */
        $verified = CustomerPhoneVerification::query()->where(['mobile_phone' => $request->input('mobile_phone'), 'status' => true])->first();
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
            $data = [
                'isMobilePhoneVerified' => true,
                'verificationCode' => $verified->sms_code,
                'confirmationCode' => $verified->sms_code,
                'sellerId' =>  auth('partners')->user()->current_uid,
                'mobilePhone' => $request->input('mobile_phone'),
                'fillingDate' => Carbon::now()->toISOString(),
                'internalId' => Str::random()
            ];
            if (!$request->input('self', 0))
            {
                $data = array_merge($data, [
                    'birthDate' => Carbon::parse($request->input('birthdate'))->toISOString(),
                    'regularProductCode' => $request->input('product_code'),
                    'annotation' => $request->input('signature'),
                    'firstName' => $request->input('firstname'),
                    'lastName' => $request->input('lastname'),
                ]);
            }
            $result = JtiApiProvider::createLead($data)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                LogService::logInfo($result);
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

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getLeadHistory(Request $request)
    {
        try {
            $result = JtiApiProvider::getLeadHistory(
                auth('partners')->user()->current_uid,
                $request->input('perpage', 100),
                $request->input('page', 1)
            )->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                return response()->json([
                    'status' => 'error',
                    'message' => 'no_data'
                ], 404);
            }
            return response()->json([
                'status' => 'ok',
                'message' => 'got_leads',
                'data' => $result['resultObject']
            ]);
        } catch (Exception $e) {
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'api_failed'
            ], 500);
        }
    }
}
