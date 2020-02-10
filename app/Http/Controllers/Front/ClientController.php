<?php

namespace App\Http\Controllers\Front;

use App\Models\CustomerPhoneVerification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CustomerRequests;
use App\Models\TobaccoProduct;
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
                switch ((int) $result['message']['code'])
                {
                    case 2:
                        $message = 'is_seller';
                        break;
                    case 3:
                    case 5:
                        $message = 'already_filled';
                        break;
                    case 4:
                        $message = 'paper';
                        break;
                    case 6:
                        $message = 'seller_not_registered';
                        break;
                    case 7:
                        $message = 'wrong_mobile_format';
                        break;
                    case 8:
                        $message = 'leads_limit_exceeded';
                        break;
                    case 9:
                        $message = 'wrong_seller_id';
                        break;
                    case 10:
                        $message = 'blacklisted';
                        break;
                    case 11:
                        $message = 'seller_not_active';
                        break;
                    default:
                        $message = 'unknown_error';
                }
                return response()->json([
                    'status' => 'error',
                    'message' => $message,
                    'crm_message' => $result['message']['messageText'] ?? ''
                ], 403);
            }
        } catch (Exception $e) {
            LogService::logInfo('Seller: ' . auth('partners')->user()->current_uid);
            LogService::logException($e);
            LogService::logInfo($e->getCode());
            return response()->json([
                'status' => 'error',
                'message' => 'phone_not_checked'
            ], 500);
        }

        $customerPhoneVerification = CustomerPhoneVerification::query()->firstOrCreate(['mobile_phone' => trim($mobilePhone, '+')]);

        $smsService = new SmsService($customerPhoneVerification);
        if ($mobilePhone != '+77777777771')
        {
            $canSend = $smsService->checkLimit();
            if ($canSend !== true)
            {
                return $canSend;
            }
        } else {
            $smsService->setCodeType(SmsService::TEST);
        }

        $smsService->setUserType(SmsService::CUSTOMER);
        if ($result['resultObject'])
        {
            $result['resultObject']['birthDate'] = Carbon::parse($result['resultObject']['birthDate'])->format('d.m.Y');
            $result['resultObject']['brand'] = TobaccoProduct::withoutTrashed()
                ->where('product_code', $result['resultObject']['regularProductCode'])
                ->first()->brand ?? '';
            unset($result['resultObject']['regularProductCode']);
            unset($result['resultObject']['mobilePhone']);
            $smsService->setUserData($result['resultObject']);
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
        $customerPhoneVerification = CustomerPhoneVerification::query()->where([
            ['mobile_phone', trim($request->input('mobile_phone'), '+')],
            ['sms_code', $request->input('sms_code')],
            ['sms_code_sent_at', '>=', Carbon::now()->subMinutes(config('project.sms_code_lifetime', 3))]
        ])->first();

        if (!$customerPhoneVerification) {
            return response()->json([
                'status' => 'error',
                'message' => 'sms_code_expired_or_invalid'
            ], 403);
        }

        $customerPhoneVerification->status = 1;
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

        $mobilePhone = $request->input('mobile_phone');

        /**
         * Check if mobile verified
         */
        $verified = CustomerPhoneVerification::query()->where(['mobile_phone' => trim($mobilePhone, '+'), 'status' => true])->first();
        if (!$verified)
        {
            return response()->json([
                'status' => 'error',
                'message' => 'mobile_phone_not_verified'
            ], 403);
        }
        /**
         * avoid duplicate queries
         */

        if ($verified->status == 2) {
            return response()->json([
                'status' => 'error',
                'message' => 'lead_already_sent_to_crm'
            ], 403);
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
                'mobilePhone' => $mobilePhone,
                'fillingDate' => Carbon::now()->toISOString(),
                'internalId' => Str::random()
            ];
            if (!$request->input('self', 0) && $mobilePhone != '+77777777771')
            {
                $data = array_merge($data, [
                    'birthDate' => Carbon::parse($request->input('birthdate'))->toISOString(),
                    'regularProductCode' => $request->input('product_code'),
                    'annotation' => $request->input('signature'),
                    'firstName' => $request->input('firstname'),
                    'lastName' => $request->input('lastname'),
                ]);
            }
            $verified->status = 2;
            $verified->save();
            $result = JtiApiProvider::createLead($data)->getBody();
            $result = json_decode($result, true);
            if (!$result['result'])
            {
                LogService::logInfo('Seller: ' . auth('partners')->user()->current_uid);
                LogService::logInfo($result);
                return response()->json([
                    'status' => 'error',
                    'message' => 'already_filled',
                    'crm_message' => $result['message']['messageText'] ?? ''
                ], 403);
            }
            return response()->json([
                'status' => 'ok',
                'message' => 'created'
            ]);
        } catch (Exception $e) {
            LogService::logInfo('Seller: ' . auth('partners')->user()->current_uid);
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'not_created'
            ], 500);
        } finally {
            $verified->status = 1;
            $verified->save();
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
            LogService::logInfo('Seller: ' . auth('partners')->user()->current_uid);
            LogService::logException($e);
            return response()->json([
                'status' => 'error',
                'message' => 'api_failed'
            ], 500);
        }
    }
}
