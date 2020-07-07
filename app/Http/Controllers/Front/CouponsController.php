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
    public function getLdCoupon()
    {
        $coupon = LdCoupon::where('seller_id', auth('partners')->user()->current_uid )->first();

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
