<?php

namespace App\Http\Controllers\Front;

use App\Models\CustomerPhoneVerification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CustomerRequests;
use App\Services\SmsService\SmsService;
use App\Services\ValidatorService\ValidatorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    /**
     * @param Request $request
     * @return bool|JsonResponse
     */
    public function sendSMS(Request $request)
    {
        $validation = ValidatorService::validateRequest($request->only('mobile_phone'), CustomerRequests::PHONE_REQUEST);
        if ($validation !== true) {
            return $validation;
        }

        $customerPhoneVerification = CustomerPhoneVerification::firstOrCreate(['mobile_phone' => $request->input('mobile_phone')]);

        $smsService = new SmsService($customerPhoneVerification);

        $canSend = $smsService->checkLimit();
        if ($canSend !== true)
        {
            return $canSend;
        }

        return $smsService->sendSms();
    }

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

        return response()->json([
            'status' => 'ok',
            'mobile_phone' => $customerPhoneVerification->mobile_phone
        ]);
    }
}
