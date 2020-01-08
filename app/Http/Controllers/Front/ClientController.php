<?php

namespace App\Http\Controllers\Front;

use App\Models\CustomerPhoneVerification;
use App\Http\Controllers\Controller;
use App\Http\Requests\Front\CustomerRequests;
use App\Services\SmsService\SmsService;
use App\Services\ValidatorService\ValidatorService;
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
}
