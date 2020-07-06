<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Requests\Front\AuthRequests;
use App\Models\Contact;
use App\Models\LdCoupon;
use App\Models\Partner;
use App\Services\ValidatorService\ValidatorService;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    public function getLdCoupon(Request $request)
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
            /**
             * No such phone in Partners
             */
            return response()->json([
                'status' => 'error',
                'message' => 'phone_does_not_exist',
                'coupons' => 0
            ], 403);
        }

        $coupon = LdCoupon::where('seller_id', $partner->current_uid )->first();

        if (!$coupon) {
            /**
             * No data
             */
            return response()->json([
                'status' => 'error',
                'message' => 'coupons_does_not_exist',
                'coupons' => 0
            ], 403);
        }

        return response()->json([
            'status' => 'ok',
            'message' => 'coupons_exist',
            'coupons' => $coupon->qty
        ], 200);
    }
}
